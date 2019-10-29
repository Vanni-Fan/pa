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
     * @param string $name
     * @param string $label
     * @param string $value
     * @param string $subtype
     * @param int    $rows
     */
    public function __construct(string $name='', string $label='', string $value=null, string $subtype='simple', int $rows = 3)
    {
        parent::__construct('textarea', $name);
        $this->rows = $rows;
        $this->label = $label;
        if(null !== $value) $this->value = $value;
        $this->subtype = $subtype;
    }
}