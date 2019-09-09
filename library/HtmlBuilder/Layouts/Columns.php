<?php

namespace HtmlBuilder\Layouts;

use HtmlBuilder\Element;

class Columns extends Element{
    public $width;
    public $offset;
    public $push;
    public $pull;
    public function __construct()
    {
        parent::__construct('columns');
    }
    
    public function column(Element $element,int $width, int $offset=0, int $push=0, int $pull=0):self{
        $this->add((new static())->width($width)->offset($offset)->pull($pull)->push($push)->add($element));
        return $this;
    }
}

