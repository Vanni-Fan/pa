<?php
namespace HtmlBuilder;

/**
 *
 * Class Components
 * @package HtmlBuilder
 */

class Components extends Element{
    public static function multiselect(){return static::create('','');}
    public static function datetime(){return static::create('','');}
    public static function daterange(){return static::create('','');}
    public static function timerange(){return static::create('','');}
    public static function tags(){return static::create('','');}
    public static function color(){return static::create('','');}
    public static function map(){ return static::create('','');}
    public static function table(){ return static::create('','');}
}