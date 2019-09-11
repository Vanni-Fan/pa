<?php

namespace HtmlBuilder\Forms;

use HtmlBuilder\Element;

class Select extends Element
{
    public $isTags = false;
    public $multiple = false;
    public $rows = 0;
    public $subtype = 'select'; // select2, menu
    public $choices = [
        ['text' => '',
            'value' => '']
    ]; // 选择的元素
    public $choicesByUrl = [
        'url' => '',
        'path' => '',
        'valueName' => '',
        'titleName' => ''
    ];
    public $other = ''; // 允许输入其他项目

    public function __construct($label, $name, $value, $subtype = 'select')
    {
        parent::__construct('select');
        $this->label = $label;
        $this->name = $name;
        $this->subtype = $subtype;
        $this->value($value);
    }

    public function choicesByUrl($url, $path, $titleName = '', $valueName = '')
    {
        return $this;
    }

    public function choices(array $choices)
    {
        $this->choices = $choices;
        return $this;
    }

    public function other($other)
    {
        $this->other = $other;
        return $this;
    }

    public function value($value)
    {
        $this->value = is_array($value) ? $value : [$value];
        return $this;
    }
}