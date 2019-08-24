<?php
namespace Power\Controllers;

class IndexController extends AdminBaseController{
    public function indexAction(){
        $this->title = 'The PermissionAdmin System.';
        $this->render();
    }
}
