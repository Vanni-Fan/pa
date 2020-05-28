<?php
namespace Power\Controllers;
use Phalcon\Mvc\Controller;
use Logger\Logger;
use PA;

class ErrorController extends Controller{
    public function initialize(){
        \Utils::isCli() || header('x-powered-by: '.PA::$config['site.domain.logogram'].'/'.PA::$config['site.version']);
    }
    
    public static function handlerError($code,$msg,$file,$line){
        $error = "ERR:[$code,$msg]@[$file:$line]";
        Logger::debug("发生PHP错误：$error.\n栈信息：".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS),1));
        throw new \Exception($error);
    }
    
    public static function handlerException($exception){
        $dispatch= PA::$app->dispatcher;
        $class   = explode('\\', PA::$config['error']['controller']);
        $forward = [
            'controller' => substr(end($class), 0, -10),
            'action'     => 'index',
            'params'     => [$dispatch,$exception]
        ];
        if (count($class) > 1) $forward['namespace'] = implode('\\', array_slice($class, 0, -1));
        else $forward['namespace'] = '\\';
        $dispatch->forward($forward);
        $dispatch->dispatch();
    }
    
    public function indexAction($dispatch,$exception){
        if(PA::$config['debug']){
            echo '<h1>发生错误</h1>';
            echo '<h2>'.$exception->getMessage().'</h2>';
            echo '<pre>'.$exception->getTraceAsString().'</pre>';
        }else{
            echo '<h1>网站有点问题。。。</h1>';
        }
        echo "<span>Power by PA</span>";
    }
}
