<?php

namespace HtmlBuilder;

use HtmlBuilder\Forms\Button;
use HtmlBuilder\Forms\CheckBox;
use HtmlBuilder\Forms\File;
use HtmlBuilder\Forms\Form;
use HtmlBuilder\Forms\Input;
use HtmlBuilder\Forms\Radio;
use HtmlBuilder\Forms\Select;
use HtmlBuilder\Forms\TextArea;

class Forms extends Element {

    public static function input($name, $label='', $subtype='text'):Input{
        return new Input($name, $subtype, $label);
    }
    
    public static function checkbox():CheckBox{
        return new CheckBox();
    }
    public static function radio():Radio{
        return new Radio();
    }
    public static function select():Select{
        return new Select();
    }
    public static function button():Button{
        return new Button();
    }
    public static function file():File{
        return new File();
    }
    public static function textarea():TextArea{
        return new TextArea();
    }
    public static function form(string $name):Form{
        return new Form();
    }
}