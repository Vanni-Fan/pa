<?php

namespace HtmlBuilder;

use HtmlBuilder\Forms\Button;
use HtmlBuilder\Forms\Check;
use HtmlBuilder\Forms\File;
use HtmlBuilder\Forms\Form;
use HtmlBuilder\Forms\Input;
use HtmlBuilder\Forms\Radio;
use HtmlBuilder\Forms\Select;
use HtmlBuilder\Forms\TextArea;

class Forms extends Element {

    public static function input(string $name, string $label='', $value='', string $subtype='text'):Input{
        return new Input($name, $subtype, $value, $label);
    }
    public static function button(string $label=''):Button{
        return new Button($label);
    }
    public static function textarea(string $label='', string $name='', string $value='', $rows = 3):TextArea{
        return new TextArea($label, $name, $value, $rows);
    }
    public static function checkbox($label, $name, $value):Check{
        return new Check($label, $name, $value,'checkbox');
    }
    public static function radio($label, $name, $value):Check{
        return new Check($label, $name, $value,'radio');
    }

    public static function select($label, $name, $value):Select{
        return new Select($label, $name, $value);
    }
    public static function file():File{
        return new File();
    }
    public static function form(string $name):Form{
        return new Form();
    }
}