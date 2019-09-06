<?php
namespace HtmlBuilder;

/**
 *
 * Class Components
 * @package HtmlBuilder
 */

class Components extends Element{
    public static function multiselect():self{return static::create('','');}
    public static function datetime():self{return static::create('','');}
    public static function daterange():self{return static::create('','');}
    public static function timerange():self{return static::create('','');}
    public static function tags():self{return static::create('','');}
    public static function color():self{return static::create('','');}
    public static function map():self{ return static::create('','');}
    public static function table():self{ return static::create('','');}
}