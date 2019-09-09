<?php

namespace HtmlBuilder\Forms;

use HtmlBuilder\Element;

class Radio extends Element
{
    public function __construct()
    {
        parent::__construct('radio');
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