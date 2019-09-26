<?php
namespace HtmlBuilder;

use HtmlBuilder\Components\MultiSelect;
use HtmlBuilder\Components\TimeRange;

/**
 *
 * Class Components
 * @package HtmlBuilder
 */

class Components extends Element
{
    public static function multiselect(string $style='single')
    {
        return new MultiSelect($style);
    }

    public static function timerange(string $name)
    {
        return new TimeRange($name);
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