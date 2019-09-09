<?php
namespace HtmlBuilder;

use HtmlBuilder\Layouts\Box;
use HtmlBuilder\Layouts\Columns;
use HtmlBuilder\Layouts\Tabs;

class Layouts extends Element{

    public static function columns():Columns{
        return new Columns();
    }
    public static function box($body, $title='', $footer=''):Box{
        return new Box($body, $title, $footer);
    }
    public static function tabs():Tabs{
        return new Tabs();
    }
}