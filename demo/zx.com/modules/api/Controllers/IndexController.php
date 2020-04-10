<?php

namespace api\Controllers;
use api\Models\MyTable;

class IndexController extends \Phalcon\Mvc\Controller{
    function indexAction(){
        \MyServer::log();
        \MyLib\MyServer::log();

        $model = new MyTable();
        $rs = $model->find();
        print_r($rs->toArray());
        return "Hello World";
    }
}