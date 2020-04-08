<?php

namespace web\Controllers;
class IndexController extends \Phalcon\Mvc\Controller{
    function indexAction(){
        $module = $this->dispatcher->getModuleName();
        $view_path = \PA::$config->module_path.'/'.$module.'/views/';

        $this->view->setViewsDir($view_path);
        $this->view->mylist = [
            'aa'=>1,
            'bb'=>2
        ];
        $this->view->render("index", "list");
    }
}