<?php
namespace plugins\Proxy\Controllers;
use Phalcon\Mvc\Controller;

class ProxyController extends Controller
{
    public function indexAction(){
        \Logger\Logger::debug($_SERVER);
        \Logger\Logger::debug(getallheaders());
        if(!isset($_SERVER['PHP_AUTH_USER'])){
            header('WWW-Authenticate: Basic realm="Please enter your account and password on PA platform"');
            header('HTTP/1.0 401 Unauthorized');
            exit ("对不起您无权访问");
        }else{
            $request = explode(' ', $_SERVER['HTTP_X_REQUEST']);
            $url = $request[1];
            echo file_get_contents($url);
        }
    }
}
