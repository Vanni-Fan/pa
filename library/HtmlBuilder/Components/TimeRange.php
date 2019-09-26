<?php

namespace HtmlBuilder\Components;

use HtmlBuilder\Element;

class TimeRange extends Element
{

    public function __construct(string $name, string $label='')
    {
        parent::__construct('timerange',$name);
        $this->label = $label;
    }

}