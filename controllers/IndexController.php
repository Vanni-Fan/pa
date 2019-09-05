<?php
namespace Power\Controllers;

use HtmlBuilder\Element;
use HtmlBuilder\FormElement;

class IndexController extends AdminBaseController{
    public function indexAction(){
        $this->title = 'The PermissionAdmin System.';

        $a = FormElement::form('user_name')->add(
            FormElement::input('user_name')->class("SSS"),
            FormElement::input('sex'),
            FormElement::create('input','aa')->template('input')
        );

        exit(" this is [$a] ");

//        $this->render();
    }
}
