<?php

namespace api\Controllers;
use api\Models\MyTable;

class IndexController extends \Phalcon\Mvc\Controller{
    function indexAction(){
        echo "<pre>";
        print_r(\PA::$loader);
        \MyServer::log();
        \MyLib\MyServer::log();

        $model = new MyTable();
        $model->find();
        return "Hello World";
    }
}