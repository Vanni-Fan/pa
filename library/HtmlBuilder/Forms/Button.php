<?php

namespace HtmlBuilder\Forms;

use HtmlBuilder\Element;

class Button extends Element
{
    public function __construct()
    {
        parent::__construct('button');
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