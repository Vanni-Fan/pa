<?php
namespace Power;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Model;
use Phalcon\Config\Adapter\Php;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Db\Adapter\Pdo\Factory as DB;
use PA;
use Power\Models\Plugins;

class App{
    public function __construct()
    {
        PA::$di       = new FactoryDefault();
        PA::$app      = new Application(PA::$di);
        PA::$config   = new Php(POWER_BASE_DIR.'data/config.php');
        PA::$router   = new Router(false); # 不添加默认路由
        PA::$loader   = new Loader();
        PA::$dispatch = new Dispatcher();
        PA::$di->set('dispatcher', PA::$dispatch);
        PA::$em       = new \Phalcon\Events\Manager();
        Model::setup(['exceptionOnFailedSave' => true]);
    }
    
    public function registerModule($module, $path){
        $namespace = [
            $module.'\\Controllers' => $path.'/controllers',
            $module.'\\Models'      => $path.'/models',
            $module.'\\Views'       => $path.'/views',
        ];
        PA::$loader->registerNamespaces($namespace, true);
    }
    
    public function init($config_file=null){
        # 合并文件配置
        if($config_file){
            if(is_array($config_file)){
                PA::$config->merge(new \Phalcon\Config($config_file));
            }elseif($config_file instanceof \Phalcon\Config){
                PA::$config->merge($config_file);
            }else{
                PA::$config->merge(new Php($config_file));
            }
        }

        # 设置基础环境
        define('BASE_DIR', isset(PA::$config['application']) ? PA::$config['application'] : POWER_BASE_DIR);
        define('PA_URL_PATH', PA::$config['pa_url_path']);
        
        # 加载依赖库
        $vendors = array_unique(array_filter([
            realpath(POWER_BASE_DIR.'library/vendor/autoload.php'),
            realpath(BASE_DIR.'library/vendor/autoload.php')
        ]));
        foreach($vendors as $vendor) require $vendor;
        
        # 设置库和插件的加载目录
        PA::$loader->registerDirs(
            array_unique(array_filter([
                realpath(POWER_BASE_DIR),
                realpath(POWER_BASE_DIR.'library'),
                realpath(BASE_DIR),
                realpath(BASE_DIR.'library'),
            ]))
        );
        PA::$loader->registerNamespaces(
            [
                'Power\\Controllers' => POWER_BASE_DIR.'controllers',
                'Power\\Models'      => POWER_BASE_DIR.'models',
                'Power\\Views'       => POWER_BASE_DIR.'views',
            ],
            true
        );
        PA::$loader->register();
        
        # 错误捕获机制
        if($error_handler = PA::$config->path('error.handler')) set_error_handler($error_handler);
        if(PA::$config->trace){
            (new \Phalcon\Debug())->listen();
        }else{
            if($exception_handler = PA::$config->path('error.exception')) set_exception_handler($exception_handler);
        }
        # 创建数据库连接
        PA::$db = DB::load(PA::$config['pa_db']);

        # 加载数据库路由
        $routers = PA::$config['routers'] ? PA::$config['routers']->toArray() : [];
        $db_router = \Power\Models\Menus::find(['url_suffix is not null','columns'=>'url_suffix,router']);
        foreach($db_router as $_router){
            $routers['*'][$_router->url_suffix] = json_decode($_router->router,1);
        }
        
        # 合并数据库配置
        $db_config = array_column(PA::$db->fetchAll('SELECT var_name as name,var_default as value FROM '.PA::$config->path('pa_db.prefix').'configs'),'value','name');
        PA::$config->merge(new \Phalcon\Config($db_config));// 数据配置


        # 注册模块
        $modules = [];
        if(PA::$config['modules']){
            foreach(new \DirectoryIterator(PA::$config['modules']) as $file){
                if($file->isDot() || !$file->isDir()) continue;
                $module = $file->getBasename();
                $path = $file->getPathname();
                $modules[$module] = function()use($module, $path){
                    return call_user_func([$this, 'registerModule'], $module, $path);
                };
            }
        }
        PA::$app->registerModules($modules);


        # 设置路由
        $need_admin_router = false;
        if(PA::$config['modules'] && PA::$config['domain_bind']){ // 如果有绑定域名
            define('VIEW_DIR', BASE_DIR.'modules/');
            $domains = PA::$config['domain_bind']->toArray();
            uksort($domains, function($a,$b){
                return ($a==='*' ? 99 : strpos($a,'.')) > ($b==='*' ? 99 : strpos($b,'.'));
            });
            $domain_base = PA::$config['root_domain'] ?: (substr($_SERVER['SERVER_NAME'], strpos($_SERVER['SERVER_NAME'],'.')+1));
            foreach($domains as $domain=>$module){
                if($domain==='*' || "$domain.$domain_base"===$_SERVER['HTTP_HOST']) break;
            }
            if($module === 'admin') $need_admin_router = true;// 加载管理后台的路由配置

            $dispatch = ['namespace'=>$module.'\\Controllers', 'controller'=>1, 'module'=>$module];
            $routers['*']['#^/?([\w0-9\_\-]+)?$#u']=$dispatch;

            $dispatch['action']=2;
            $routers['*']['/:controller/:action']=$dispatch;

            $dispatch['params']=3;
            $routers['*']['/:controller/:action/:params']=$dispatch;
        }elseif(PA::$config['modules']){
            define('VIEW_DIR', BASE_DIR.'modules/');
            foreach(new \DirectoryIterator(PA::$config->modules) as $file){
                if($file->isDot() || !$file->isDir()) continue;
                $module = $file->getBasename();
                $prefix = '/'.$module.'/';
                
                #error_log("有模块: $module, $path, [$prefix] \n",3,'/tmp/vanni.log');
                $dispatch = ['namespace'=>$module.'\\Controllers', 'module'=>$module, 'controller'=>1];
                $routers['*'][$prefix.'?([\w0-9\_\-]+)?/?']=$dispatch;

                $dispatch['action']=2;
                $routers['*'][$prefix.':controller/:action']=$dispatch;

                $dispatch['params']=3;
                $routers['*'][$prefix.':controller/:action/:params']=$dispatch;
            }
        }else{
            define('VIEW_DIR', BASE_DIR.'views/templates/');
            if(!SINGLE_POWER){
                PA::$loader->registerNamespaces(
                    [
                        'Controllers' => BASE_DIR.'controllers',
                        'Models'      => BASE_DIR.'models',
                        'Views'       => BASE_DIR.'views',
                    ],
                    true
                );
            }
            $dispatch = ['namespace'=>'\\Controllers', 'controller'=>1];
            $routers['*']['/:controller']=$dispatch;
            
            $dispatch['action']=2;
            $routers['*']['/:controller/:action']=$dispatch;
            
            $dispatch['params']=3;
            $routers['*']['/:controller/:action/:params']=$dispatch;
            $need_admin_router = true;
        }

        if($need_admin_router){
            # 加载配置路由
            $admin_routers = include POWER_BASE_DIR.'data/routers.php'; // 加载路由
            foreach($admin_routers as $_method => $_routers){
                foreach($_routers as $_match=>$_router) $routers[$_method][$_match] = $_router;
            }
        }
        PA::$loader->register();
    
        # 设置模板输出
        PA::$di->setShared('view',
            function () {
                $view = new View();
                $view->disableLevel(
                    [
                        View::LEVEL_LAYOUT      => true, // 禁用 layouts 文件夹中 和控制器同名的 template
                        View::LEVEL_MAIN_LAYOUT => true, // 禁用 views 目录中的 index.phtml
                    ]
                );
//                $view->start();
                return $view;
            }
        );
        
        # 为路由设置权重
        foreach($routers as $method => &$rule){
            $sort = [];
            array_walk($rule, function(&$v, $k)use(&$sort){
                $v['priority'] = $v['priority'] ?? 0;
                $sort[] = $v['priority'];
            });
            array_multisort($sort, $rule);
            #echo "\n要添加的 $method 有",print_r($rule);
            foreach($rule as $url => $param){
                unset($param['priority']);
                if($method === '*' ) {
                    PA::$router->add($url, $param);
                }else{
                    PA::$router->{'add' . $method}($url, $param);
                }
            }
        }
//        print_r(PA::$router);
//        print_r($routers);
//        exit;
        PA::$config['routers'] = $routers;

        # 设置路由
        PA::$di->setShared('router', PA::$router);

        # 数据库查询SQL监听
        PA::$em->attach('db', function ($event, $connection){
//            echo "\n\n",$connection->getSQLStatement(),"\n\n";
        });
        PA::$db->setEventsManager(PA::$em);
        
        # 加载插件的 autoload
        $plubins = Plugins::find(['is_enabled=1','columns'=>'name']);
        if($plubins) foreach($plubins as $p){
            $class = '\\plugins\\'.$p->name.'\\Settings';
            if(method_exists($class,'autoload')) {
                call_user_func([$class, 'autoload']);
            }
        }
        
        return $this;
    }
    public function run($config_file=null){
        if($config_file) $this->init($config_file);
        echo PA::$app->handle()->getContent();
        
//        $response = PA::$app->handle();
//        echo "<pre>";
//        echo "是否已经发送:".$response->isSent();
//        echo "内容:[".print_r($response->getContent(),1).']';
//        PA::$app->
//        if(!$response->isSent())
//            $response->send();
//        echo $response->getContent();
//        exit;
//        if(!$response->isSent()){
//            $response->send();
//        }
//        PA::$app->handle();//->send();
//        echo PA::$app->handle()->getContent();//->send();
    }
    public function __call($name, $arguments){
        return call_user_func_array([PA::$app,$name], $arguments);
    }
}