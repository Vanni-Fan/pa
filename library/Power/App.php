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
use Phalcon\Db\Adapter\PdoFactory as DB;
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
        // PA::$em       = new \Phalcon\Events\Manager();
        // Model::setup(['exceptionOnFailedSave' => true]); # todovar_dump()
    }

    public function setErrorHandler($config){
        if($error_handler = $config->path('error.handler')) set_error_handler($error_handler);
        if($config->trace){
            (new \Phalcon\Debug())->listen();
        }else{
            if($exception_handler = $config->path('error.exception')) set_exception_handler($exception_handler);
        }
    }
    public function init2($config=null){
        # 1、PA初始化：Loader目录注册,Loader名字空间注册,常量设置
        #define('BASE_DIR', isset(PA::$config['application']) ? PA::$config['application'] : POWER_BASE_DIR);
        #define('PA_URL_PATH', '/'.trim(PA::$config['pa_url_path'],'/').'/');
        #define('VIEW_DIR', BASE_DIR.'modules/');
        # 设置基础环境
        PA::$loader->registerDirs([POWER_BASE_DIR,POWER_BASE_DIR.'library']);
        PA::$loader->registerNamespaces(
            [
                'Power\\Controllers' => POWER_BASE_DIR.'controllers',
                'Power\\Models'      => POWER_BASE_DIR.'models',
                'Power\\Views'       => POWER_BASE_DIR.'views',
            ],
            true
        );
        PA::$loader->register();

        # 2、加载 PA 的 vendors，如果有的话
        $pa_vendor = realpath(POWER_BASE_DIR.'library/vendor/autoload.php');
        if($pa_vendor) require $pa_vendor;

        # 3、设置 PA 自己的错误处理机制和方法
        $this->setErrorHandler(PA::$config);

        # 4、加载项目的配置文件 $config，并合并
        if($config){
            if(!($config instanceof \Phalcon\Config)){
                $config = is_array($config) ? (new \Phalcon\Config($config)) : (new Php($config));
            }
            # 用项目的错误处理来替代PA的错误处理
            if($config->error) $this->setErrorHandler($config);
            # 加载项目的 vendors
            if($config->application){
                $project_vendor = realpath($config->application.'/library/vendor/autoload.php');
                if($project_vendor) require $project_vendor;
            }
            # 合并配置文件
            PA::$config->merge($config);
        }

        # 5、加载数据的配置和路由
        $db_routers = []; // [$module=>[[具体路由配置],[具体路由配置]]];
        if(PA::$config->load_db_config){
            # 初始化 PA 的数据库
            if(!PA::$config->pa_db) throw new \Exception('请配置 pa_db 选项，以便加载数据库的配置');
            $db_connect = PA::$config['pa_db']->toArray();
            $db_adapter = $db_connect['adapter'];
            if($db_adapter == 'sqlite'){ // sqlite 不支持的配置项目
                unset($db_connect['adapter'], $db_connect['host'], $db_connect['prefix']);
            }
            PA::$db = (new DB())->newInstance($db_adapter, $db_connect);
            # 配置文件
            // $db_config = array_column(PA::$db->fetchAll('SELECT var_name as name,var_default as value FROM '.PA::$config->path('pa_db.prefix').'configs'),'value','name');
            $db_config = array_column(\Power\Models\Configs::find()->toArray(),'var_name','var_default');
            PA::$config->merge(new \Phalcon\Config($db_config));// 数据配置
            # 路由
            foreach(\Power\Models\Menus::find(['url_suffix is not null','columns'=>'url_suffix,router']) as $_router){
                $db_routers[$_router->module]['*'][$_router->url_suffix] = json_decode($_router->router,1);
            }
        }

        # 6、分析匹配的子模块并加载
        $modules = $routers = []; # 所有的模块和路由
        if(!PA::$config['domain_bind']){ # 没有子模块域名绑定
            if(PA::$config['modules']){
                # 返回添加成功的模块 todo
                $modules = $this->registerModules(PA::$config['modules'], '*', $routers);
            }
            $this->loadAdminRouter($routers);
        }
        # 有配置模块
        else{ 
            # 没有配置 modules 目录
            if(!PA::$config['modules']){ 
                if(count(PA::$config['domain_bind'])===1 && current(PA::$config['domain_bind'])==='admin'){
                    $this->loadAdminRouter($routers);
                }else throw new \Exception('请配置 modules 参数');
            }
            # 有配置 modules 目录
            else{
                $domain_base  = PA::$config['root_domain'] ?: (substr($_SERVER['SERVER_NAME'], strpos($_SERVER['SERVER_NAME'],'.')+1));
                foreach(PA::$config['domain_bind'] as $domain=>$_module){
                    $domain = strpos($domain, '.')!==false ? $domain : "$domain.$domain_base";
                    if(strpos($_SERVER['HTTP_HOST'], $domain)!==0) continue;
                    $modules = $this->registerModules(PA::$config['modules'], $_module, $routers);
                }
            }
        }
        if($modules) PA::$app->registerModules($modules);

        # 8、加载插件，调用 plugin的 autoload 方法。具体操作请在 autoload 方法里面实现
        foreach(PA::$config->plugins ?? [] as $plugin){
            $path = POWER_BASE_DIR . 'plugins/'.$plugin;
            if(file_exists($path) && is_dir($path)){
                $class = '\\plugins\\'.$plugin.'\\Settings';
                if(method_exists($class,'autoload')) {
                    call_user_func([$class, 'autoload']);
                }
            }
        }

        # 9、设置 em, view 等
        # 设置错误异常
        Model::setup(['exceptionOnFailedSave' => PA::$config->exception_on_failed_save]);
        # 设置视图
        if(PA::$config->view){
            PA::$di->setShared('view', function () {
                $class = '\\'.PA::$config->view->class;
                $view = new $class();
                $view->disableLevel(
                    [
                        View::LEVEL_LAYOUT      => PA::$config->view->disable_layout,
                        View::LEVEL_MAIN_LAYOUT => PA::$config->view->disable_main_layout,
                    ]
                );
                return $view;
            });
        }

        # 设置事件监听
        if(PA::$config->event ?? null){
            PA::$em = new \Phalcon\Events\Manager();
            foreach(PA::$config->event->events ?? [] as $type){
                PA::$em->attach($type, function()use($config){${$config->event->handler}();});
                $obj_name = explode(':',$type)[0];
                $obj_name = $obj_name == 'application' ? 'app' : $obj_name;
                if(isset(PA::$$obj_name)) PA::${$obj_name}->setEventsManager(PA::$em);
            }
        }

        # 10、排序 router，并注册 router
        # 为路由设置权重
        echo "<pre>";
        foreach($routers as $method => &$rule){
            $sort = [];
            array_walk($rule, function(&$v, $k)use(&$sort){
                $v['priority'] = $v['priority'] ?? 0;
                $sort[] = $v['priority'];
            });
            array_multisort($sort, $rule);
            echo "\n要添加的 $method 有",print_r($rule);
            foreach($rule as $url => $param){
                unset($param['priority']);
                if($method === '*' ) {
                    PA::$router->add($url, $param);
                }else{
                    PA::$router->{'add' . $method}($url, $param);
                }
            }
        }
        // print_r(PA::$router);
        #print_r(PA::$loader);
        print_r($routers);
        exit;
        # 重新赋值会到配置中
        PA::$config['routers'] = $routers;
        # 设置路由

        return $this;
    }
    
    public function registerModule($module, $path){
        $namespace = [
            $module.'\\Controllers' => $path.'/controllers',
            $module.'\\Models'      => $path.'/models',
            $module.'\\Views'       => $path.'/views',
        ];
        PA::$loader->registerNamespaces($namespace, true);
    }

    # 加载 admin 模块，返回[plugin:[...], db:[...]]
    # 1、 初始化PA的数据库连接
    # 2、 获得所有 DB 中配置的路由信息
    # 3、 获得所有 Plugin 中的配置信息
    # 4、 返回
    public static function loadAdminRouter(&$routers){
        static $loaded = false;
        if(!$loaded){
            $pa_routers = include POWER_BASE_DIR.'data/routers.php';
            $routers = array_merge_recursive($routers, $pa_routers);
        }
    }
    /**
     * 根据一个目录注册模块，包括名字空间，和路由规则
     * @param $module_dir 模块目录，里面的每个文件夹都被解析成一个模块
     * @param $include 只分析指定的某几个目录，如果有条目大于1或等于空，表示要模块前缀
     * @param &$routers 被设置的路由数组
     */
    public static function registerModules(string $module_dir, string $include, array &$routers):array{
        static $modules = []; // [ $module_name => $module_path, ... ]
        # 只分析一次
        if(!$modules){
            foreach(new \DirectoryIterator($module_dir) as $file){
                if($file->isDot() || !$file->isDir()) continue;
                $modules[$file->getBasename()] = $file->getPathname();
            }
        }
        # 需要包含的模块
        $include = preg_split('/[, ]+/',$include);
        if(array_search('*', $include) !== false) $include = array_merge(array_keys($modules),['admin']);

        $need_module_url_prefix = count($include) !== 1;
        $have_admin_module = array_search('admin', $include);
        if($have_admin_module !== false){
            unset($include[$have_admin_module]);
            self::loadAdminRouter($routers);
        }

        $registered_modules = []; # 
        foreach($modules as $module=>$path){
            if(!in_array($module,$include)) continue;
            
            $registered_modules[$module] = function(){}; // todo ? 是否Work？
            $prefix = $need_module_url_prefix ? $module : '';
            
            $dispatch = ['namespace'=>$module.'\\Controllers', 'module'=>$module, 'controller'=>1];
            $routers['*'][$prefix.'?([\w0-9\_\-]+)?/?']=$dispatch;
    
            $dispatch['action']=2;
            $routers['*'][$prefix.':controller/:action']=$dispatch;
    
            $dispatch['params']=3;
            $routers['*'][$prefix.':controller/:action/:params']=$dispatch;
            // 添加名字空间
            PA::$loader->registerNamespaces([
                $module.'\\Controllers' => $path.'/controllers',
                $module.'\\Models'      => $path.'/models',
                $module.'\\Views'       => $path.'/views',
            ], true);
        }

        return $registered_modules;
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
        foreach($vendors as $vendor) require_once $vendor;
        
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
        // PA::$db = DB::load(PA::$config['pa_db']);
        // exit("<pre>".print_r(PA::$config->toArray(),1));
        $db_connect = PA::$config['pa_db']->toArray();
        $db_adapter = $db_connect['adapter'];
        if($db_adapter == 'sqlite'){ // sqlite 不支持的配置项目
            unset($db_connect['adapter'], $db_connect['host'], $db_connect['prefix']);
        }
        //PA::$db = (new DB())->newInstance(PA::$config['pa_db']['adapter'], PA::$config['pa_db']->toArray());
        PA::$db = (new DB())->newInstance($db_adapter, $db_connect);

        # 加载用户配置的路由
        $routers = PA::$config['routers'] ? PA::$config['routers']->toArray() : [];

        # 子模块处理
        $need_admin_router = false;
        $domain_base  = PA::$config['root_domain'] ?: (substr($_SERVER['SERVER_NAME'], strpos($_SERVER['SERVER_NAME'],'.')+1));
        $bind_modules = []; // 绑定的模块数 [域名=>[模块1,模块2]]
        if(PA::$config['domain_bind']){
            foreach(PA::$config['domain_bind'] as $domain=>$module){
                if(preg_match('/\*|\badmin\b/i',$module)){
                    $need_admin_router = true;
                }
                $domain = strpos($domain, '.')!==false ? $domain : "$domain.$domain_base";
                $bind_modules[$domain] = $bind_modules[$domain] ?? [];
                $_modules = [];
                if(strpos($module,'*')){ // 全部模块
                    if(PA::$config['modules']) foreach(new \DirectoryIterator(PA::$config['modules']) as $file){
                        if($file->isDot() || !$file->isDir()) continue;
                        $_modules[] = $file->getBasename();
                    }else{
                        $_modules[] = 'admin';
                    }
                }else{
                    $_modules = preg_split('/[, ]+/',$module);
                }
                $bind_modules[$domain] = array_merge($bind_modules[$domain], $_modules);
            }
        }
        // echo "<pre>";
        // print_r(PA::$db);
        $db_router = \Power\Models\Menus::find(['url_suffix is not null','columns'=>'url_suffix,router']);
        foreach($db_router as $_router){
            if($need_admin_router && preg_match('/\*|\badmin\b/i',$_router->modules)){
                $routers['*'][$_router->url_suffix] = json_decode($_router->router,1);
            }
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
        if($modules) PA::$app->registerModules($modules);



        /*
        $mutil_module = false;
        define('VIEW_DIR', BASE_DIR.'modules/');
        # 单模块（一个域名对应一个模块）
        if(PA::$config['modules'] && PA::$config['domain_bind']){ // 如果有绑定域名
            $domains = PA::$config['domain_bind']->toArray();
            uksort($domains, function($a,$b){
                return ($a==='*' ? 99 : strpos($a,'.')) > ($b==='*' ? 99 : strpos($b,'.'));
            });
            $domain_base = PA::$config['root_domain'] ?: (substr($_SERVER['SERVER_NAME'], strpos($_SERVER['SERVER_NAME'],'.')+1));
            $module = '';
            foreach($domains as $domain=>$_module){
                $domain = strpos($domain, '.')!==false ? $domain : "$domain.$domain_base";
                if($domain==='*' || strpos($_SERVER['HTTP_HOST'], $domain)===0){
                    $module = $_module;
                    break;
                }
            }
            if($module){
                $dispatch = ['namespace'=>$module.'\\Controllers', 'controller'=>1, 'module'=>$module];
                $routers['*']['#^/?([\w0-9\_\-]+)?$#u']=$dispatch;
    
                $dispatch['action']=2;
                $routers['*']['/:controller/:action']=$dispatch;
    
                $dispatch['params']=3;
                $routers['*']['/:controller/:action/:params']=$dispatch;
    
            }
        }
        # 多模块（一个域名对应多个模块）
        elseif($bind_modules){
        // elseif(PA::$config['modules']){
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
        */

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
                //$view->start();
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

        PA::$config['routers'] = $routers;

        # 设置路由
        PA::$di->setShared('router', PA::$router);

        # 数据库查询SQL监听
        PA::$em->attach('db', function ($event, $connection){
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
        echo PA::$app->handle($_SERVER["REQUEST_URI"])->getContent();
    //    $response = PA::$app->handle('/');
    //    echo "<pre>";
    //    echo "是否已经发送:".$response->isSent();
    //    echo "内容:[".print_r($response->getContent(),1).']';
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