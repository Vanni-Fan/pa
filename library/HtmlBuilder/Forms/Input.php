<?php

namespace HtmlBuilder\Forms;

use HtmlBuilder\Element;

class Input extends Element{
    public $inputMask="99:99";
    public $statistics=false;    // 显示在右下角，字符长度，单词个数
    public $inputAfterIcon="";      // 后面的图标
    public $inputBeforeIcon="";      // 前面的图标
    public function __construct($name, $label=''){
        parent::__construct('input',$name);
        $this->subtype = 'text';
        if($label) $this->label = $label;
    }
    
    public function inputAfterIcon($icon){
        $this->inputAfterIcon = $icon;
        return $this;
    }
    public function inputBeforeIcon($icon){
        $this->inputBeforeIcon = $icon;
        return $this;
    }
    public function inputMask($mask){
        $this->inputMask = $mask;
        return $this;
    }
    public function statistics(bool $enable=true){
        $this->statistics = $enable;
        return $this;
    }
}