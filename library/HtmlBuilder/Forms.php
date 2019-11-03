<?php

namespace HtmlBuilder;

use HtmlBuilder\Forms\Button;
use HtmlBuilder\Forms\Check;
use HtmlBuilder\Forms\File;
use HtmlBuilder\Forms\Form;
use HtmlBuilder\Forms\Input;
use HtmlBuilder\Forms\Select;
use HtmlBuilder\Forms\TextArea;

class Forms extends Element {
    /**
     * 创建一个 Form 对象
     * @param string $action
     * @param string $method
     * @return Form
     */
    public static function form(string $action, string $method='post'):Form{
        return new Form($action, $method);
    }
    
    /**
     * 创建一个 Input 对象
     * @param string $name
     * @param string $label
     * @param string $value
     * @param string $subtype
     * @return Input
     */
    public static function input(string $name, string $label='', $value='', string $subtype='text'):Input{
        return new Input($name, $label, $value, $subtype);
    }
    
    /**
     * 创建一个 Button 对象
     * @param string $label
     * @return Button
     */
    public static function button(string $name, string $label='', $value='', string $subtype='default'):Button{
        return new Button($name, $label, $value, $subtype);
    }
    
    /**
     * 创建一个 TextArea 对象
     * @param string $label
     * @param string $name
     * @param string $value
     * @param int    $rows
     * @return TextArea
     */
    public static function textarea(string $label='', string $name='', string $value='', $rows = 3):TextArea{
        return new TextArea($label, $name, $value, $rows);
    }
    
    /**
     * 创建一个复选框对象
     * @param $label
     * @param $name
     * @param $value
     * @return Check
     */
    public static function checkbox($label, $name, $value):Check{
        return new Check($label, $name, $value,'checkbox');
    }
    
    /**
     * 创建一个单选框对象
     * @param $label
     * @param $name
     * @param $value
     * @return Check
     */
    public static function radio($name, $label, $value):Check{
        return new Check($name, $label, $value,'radio');
    }
    
    /**
     * 创建一个下拉列表对象
     * @param $label
     * @param $name
     * @param $value
     * @param $subtype
     * @return Select
     */
    public static function select($name, $label, $value=null,$subtype='select'):Select{
        return new Select($name, $label, $value,$subtype);
    }
    
    /**
     * 创建一个 File 文件对象
     * @param $name
     * @param $label
     * @param $value
     * @param $subtype
     * @return File
     */
    public static function file(string $name, string $label='', $value='', string $subtype='single'):File{
        return new File($name,$label,$value,$subtype);
    }
}