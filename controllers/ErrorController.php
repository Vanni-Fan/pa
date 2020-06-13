<?php
namespace Power\Controllers;
use Logger\Logger;
use PA;
use Phalcon\Mvc\Dispatcher;
use Power\App;

class ErrorController extends AdminBaseController{
    public function initialize(){
        \Utils::isCli() || header('x-powered-by: '.PA::$config['site.domain.logogram'].'/'.PA::$config['site.version']);
    }
    
    public static function handlerError($code,$msg,$file,$line){
        $error = "ERR:[$code,$msg]@[$file:$line]";
        Logger::debug("发生PHP错误：$error.\n栈信息：".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS),1));
        throw new \Exception($error);
    }
    
    public static function handlerException($exception){
        $module   = App::getModuleName();
        $dispatch = PA::$app->dispatcher;
        $forward  = [];
        if($mappings = PA::$config->path('error.mappings')){
            $modules = array_filter(array_keys($mappings->toArray()), function($v)use($module){
                return strpos(",$v,", ",$module,") !== false;
            });
            if(isset($modules[0])){
                $forward = $mappings->get($modules[0])->toArray();
            }elseif($mappings->has('*')){
                $forward = $mappings->get('*')->toArray();
            }
        }
        if(empty($forward)){
            $forward = [
                'controller' => 'error',
                'action'     => 'index',
                'namespace'  => __NAMESPACE__
            ];
        }
        $forward['params'] = [$exception, $module, $dispatch];
        $dispatch->forward($forward);
        $dispatch->dispatch();
    }
    
    public function indexAction(\Throwable $exception, string $module, Dispatcher $dispatch){
        if(\Utils::isCli()){
            $msg = "\n发生错误:".$exception->getMessage()."\n".print_r($exception->getTraceAsString(),1)."\n";
            echo \Shell::getColorText($msg,31,43);
            echo \Shell::getColorText("\n",37,40);
        }else{
            if(PA::$config['debug']) {
                $this->view->title   = '发生错误';
                $this->view->message = $exception->getMessage();
                $this->view->trace   = $exception->getTraceAsString();
            }else{
                $this->view->title   = '异常访问';
                $this->view->message = '网站无法为您提供服务，抱歉！';
                $this->view->trace   = '';
            }
            $this->render('datasource/index',1);
        }
    }
}
