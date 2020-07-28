<?php
namespace plugins\Proxy\Controllers;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View\Simple;

class ProxyController extends Controller
{
    public function indexAction(){
        \Logger\Logger::debug($_SERVER);
        \Logger\Logger::debug(getallheaders());
        $url = str_replace('/_proxy','',$_SERVER['REQUEST_URI']);
        $url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_X_FORWARDED_HOST'].$url;

        if(!isset($_SERVER['PHP_AUTH_USER'])){
//            header('WWW-Authenticate: Basic realm="Please enter your account and password on PA platform"');
//            header('HTTP/1.0 401 Unauthorized');
//            $this->view->getViewsDir(POWER_BASE_DIR.'plugins/Proxy/views/templates');

            $view = new Simple();
            $view->setViewsDir(POWER_BASE_DIR.'plugins/Proxy/views/templates');
            $view->pa_website = 'http://oa.5ums.com:8888';

            echo $view->render('policy');

//            exit ("对不起您无权访问");
        }else{
            $this->view->render('Proxy','policy');
//            $user = $_SERVER['PHP_AUTH_USER'];
//            $password = $_SERVER['PHP_AUTH_PW'];

//            echo file_get_contents($url);
        }
    }
}
