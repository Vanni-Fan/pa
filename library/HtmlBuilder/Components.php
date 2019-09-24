<?php
namespace HtmlBuilder;

use HtmlBuilder\Components\TimeRange;

/**
 *
 * Class Components
 * @package HtmlBuilder
 */

class Components extends Element
{
    public static function multiselect()
    {
        return static::create('', '');
    }

    public static function timerange()
    {
        return new TimeRange();
    }

    public static function map()
    {
        return static::create('', '');
    }

    public static function table()
    {
        return static::create('', '');
    }
}