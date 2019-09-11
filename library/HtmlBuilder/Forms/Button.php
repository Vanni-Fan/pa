<?php

namespace HtmlBuilder\Forms;

use HtmlBuilder\Element;

class Button extends Element
{
    public $flat='';
    public $block=false;
    public $style='info';
    public $btnBeforeIcon='';//fa fa-users';
    public $btnAfterIcon='';//fa fa-info';
    public $badgeColor='maroon';// maroon, purple, orange, navy, olive
    public $badge='';
    public $action='button'; // reset, submit
    public $vertical=false;
    public $subtype = 'default';// input, group, default

    public function __construct(string $label='')
    {
        parent::__construct('button');
        $this->label = $label;
    }
    public function flat(bool $flat=true){
        $this->flat = $flat;
        return $this;
    }
    public function block(bool $block=true){
        $this->block = $block;
        return $this;
    }
    public function style(string $style){
        $this->style = $style;
        return $this;
    }
    public function btnBeforeIcon($icon_or_button){
        $this->btnBeforeIcon = $icon_or_button;
        return $this;
    }
    public function btnAfterIcon($icon_or_button){
        $this->btnAfterIcon = $icon_or_button;
        return $this;
    }
    public function badgeColor(string $color){
        $this->badgeColor = $color;
        return $this;
    }
    public function badge(string $text){
        $this->badge = $text;
        return $this;
    }
    public function action(string $action){
        $this->action = $action;
        return $this;
    }
    public function vertical(bool $vertical=true){
        $this->vertical = $vertical;
        return $this;
    }
}