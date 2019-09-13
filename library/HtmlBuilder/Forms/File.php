<?php

namespace HtmlBuilder\Forms;

use HtmlBuilder\Element;

class File extends Element
{
    public $subtype = 'single';
    public $statistics = 1;//true;
    public $accept = '*/*';//image/*';
    public function __construct($name)
    {
        parent::__construct('file');
        $this->name = $name;
    }
}