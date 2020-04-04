<?php

namespace web\Controllers;
class IndexController extends \Phalcon\Mvc\Controller{
    function indexAction(){
        return "<h1>Hello World</h1>";
    }
}