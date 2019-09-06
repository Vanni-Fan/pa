<?php
namespace HtmlBuilder;

use HtmlBuilder\Layouts\Box;
use HtmlBuilder\Layouts\Columns;
use HtmlBuilder\Layouts\Tabs;

class Layouts extends Element{

    public static function columns(int $columns):Columns{
        return new Columns($columns);
    }
    public static function box():Box{
        return new Box('');
    }
    public static function tabs():Tabs{
        return new Tabs('');
    }
}