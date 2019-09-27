<?php
namespace HtmlBuilder;

use HtmlBuilder\Components\MultiSelect;
use HtmlBuilder\Components\Table;
use HtmlBuilder\Components\TimeRange;

/**
 *
 * Class Components
 * @package HtmlBuilder
 */

class Components extends Element
{
    public static function multiselect(string $style='single'):MultiSelect
    {
        return new MultiSelect($style);
    }

    public static function timerange(string $name):TimeRange
    {
        return new TimeRange($name);
    }

    public static function map()
    {
        return static::create('', '');
    }

    public static function table($name):Table
    {
        return new Table($name);
    }
}