<?php

namespace HtmlBuilder\Forms;

use HtmlBuilder\Element;

class Button extends Element
{
    public $flat='';
    public $block=true;
    public $style='info';
    public $btnBeforeIcon='fa fa-users';//fa fa-users';
    public $btnAfterIcon='fa fa-users';//fa fa-info';
    public $badgeColor='maroon';// maroon, purple, orange, navy, olive
    public $badge='9+';

    public function __construct()
    {
        parent::__construct('button');
        $this->subtype = 'default'; // input or group/ v-group
    }
    
    public function tab(string $name, Element $element, $visible=false): self
    {
        $tab = (new static())->add($element);
        $tab->name = $name;
        $tab->visible = $visible;
        $this->add($tab);
        return $this;
    }
}