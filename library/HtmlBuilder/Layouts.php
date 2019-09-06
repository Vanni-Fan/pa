<?php
namespace HtmlBuilder;

class Layouts extends Element{
    public static function columns():self{return static::create('','');}
    public static function box():self{return static::create('','');}
    public static function tabs():self{ return static::create('','');}
    public static function image():self{ return static::create('','');}
}