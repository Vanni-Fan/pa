<?php

namespace HtmlBuilder\Forms;

use HtmlBuilder\Element;

class TextArea extends Element
{
    public $rows;
    public $subtype='simple';// ckeditor, wysihtml5
    public function __construct(string $label='', string $name='', string $value='', $rows = 3)
    {
        parent::__construct('textarea');
        $this->rows = $rows;
        $this->label = $label;
        $this->name = $name;
        $this->value = $value;
    }
}