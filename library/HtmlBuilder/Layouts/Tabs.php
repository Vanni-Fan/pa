<?php

namespace HtmlBuilder\Layouts;

use HtmlBuilder\Element;

class Tabs extends Element
{
    public function __construct()
    {
        parent::__construct('tabs');
    }
    
    /**
     * 添加一个Tab
     * @param string  $name
     * @param Element $element
     * @param bool    $visible
     * @return Tabs
     */
    public function tab(string $name, Element $element, $visible=false): self
    {
        $tab = (new static())->add($element);
        $tab->name = $name;
        $tab->visible = $visible;
        $this->add($tab);
        return $this;
    }
}