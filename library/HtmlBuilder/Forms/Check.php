<?php

namespace HtmlBuilder\Forms;

use HtmlBuilder\Element;

class Check extends Element
{
    public $style = 'purple';//black,red,green,blue,aero,grey,orange,yellow,pink,purple
    public $flat = 'flat'; // flat or square
    public $colCount = 3; // 每行几列
    public $choices = [
        ['text'=>'',
        'value'=>'']
    ]; // 选择的元素
    public $choicesByUrl = [
        'url'=>'',
        'path'=>'',
        'valueName'=>'',
        'titleName'=>''
    ];

    public $other = ''; // 允许输入其他项目
    public $selectAll = ''; // 允许选择所有
    public $none = ''; // 允许不选,

    public function __construct($label, $name, $value, $subtype='checkbox')
    {
        parent::__construct('check');
        $this->label = $label;
        $this->name = $name;
        $this->value = $value;
        $this->subtype = $subtype;
    }

    public function choicesByUrl($url, $path, $titleName='', $valueName=''){
        return $this;
    }

    public function choices(array $choices){
        $this->choices = $choices;
        return $this;
    }
}