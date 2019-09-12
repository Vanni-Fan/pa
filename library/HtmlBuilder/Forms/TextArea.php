<?php

namespace HtmlBuilder\Forms;

use HtmlBuilder\Element;

class TextArea extends Element
{
    /**
     * @var int 显示多少行
     */
    public $rows;
    /**
     * @var string 富编辑器样式： simple,  ckeditor, wysihtml5
     */
    public $subtype='simple';// ckeditor, wysihtml5
    
    /**
     * TextArea constructor.
     * @param string $label
     * @param string $name
     * @param string $value
     * @param int    $rows
     */
    public function __construct(string $label='', string $name='', string $value='', $rows = 3)
    {
        parent::__construct('textarea');
        $this->rows = $rows;
        $this->label = $label;
        $this->name = $name;
        $this->value = $value;
    }
}