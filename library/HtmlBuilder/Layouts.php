<?php
namespace HtmlBuilder;

use HtmlBuilder\Layouts\Box;
use HtmlBuilder\Layouts\Columns;
use HtmlBuilder\Layouts\Tabs;

class Layouts extends Element{
    
    /**
     * 创建一个列对象
     * @return Columns
     */
    public static function columns():Columns{
        return new Columns();
    }
    
    /**
     * 创建一个盒子对象
     * @param string|Element $body
     * @param string $title
     * @param string $footer
     * @return Box
     */
    public static function box($body, $title='', $footer=''):Box{
        return new Box($body, $title, $footer);
    }
    
    /**
     * 创建一个Tab对象
     * @return Tabs
     */
    public static function tabs():Tabs{
        return new Tabs();
    }
}