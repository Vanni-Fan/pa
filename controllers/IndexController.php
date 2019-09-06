<?php
namespace Power\Controllers;

use HtmlBuilder\Components;
use HtmlBuilder\Forms;
use HtmlBuilder\Layouts;
use HtmlBuilder\Parser\AdminLte\Parser;

class IndexController extends AdminBaseController{
    public function indexAction(){
        $this->title = 'The PermissionAdmin System.';
    
        $a = new Parser(
            Layouts::columns(2)->add(
                Layouts::box('右边搜索栏')->add(
                    Forms::form('search')->add(
                        Forms::input('text'),
                        Forms::button('Search')
                    ),
                ),
                Layouts::box('左边注册栏')->add(
                    Forms::form('register')->add(
                        Layouts::image('register_images.jpg'),
                        Forms::input('username'),
                        Components::data('expire_data'),
                        Forms::button('Register','s')->attr('sdf','')->enabled()->value('ss')
                            ->labelPosition('')
                            ->label(''),
                        Forms::file(),
                    )
                )
            )
        );
        print_r($a->parse());
//        $a = FormElement::form('user_name')->add(
//            FormElement::input('user_name')->class("SSS"),
//            FormElement::input('sex'),
//            FormElement::create('input','aa')->template('input')
//        );
//
//        Element::create('form','');
//        FormElement::create('input','user_name');
//
//        Element::form('abc');
//        FormElement::input('user_name');
//
//        $b = AdminLteParser::parse($a);
//
//        $b = AdminLteParser::parse(
//            Element::form('post')->add(
//                FormElement::input('',''),
//                FormElement::checkbox('',''),
//                FormElement::radio(''),
//                LayoutElement::col3()->add(
//                    FormElement::input(''),
//                    FormElement::checkbox()
//                ),
//                ComponentElement::date()
//            )
//        );
//        $b = [
//            'css_files' => [],
//            'styles'    => '',
//            'js_files'  => [],
//            'js_init'   => '',
//            'scripts'   => '',
//        ];
//
//        exit(" this is [$a] ");

//        $this->render();
    }
}
