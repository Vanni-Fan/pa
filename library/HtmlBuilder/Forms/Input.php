<?php

namespace HtmlBuilder\Forms;

use HtmlBuilder\Element;

class Input extends Element{
    public $inputMask="99:99";
    public $statistics=true;    // 显示在右下角，字符长度，单词个数
    public $appendIcon="";      // 后面的图标
    public $prependIcon="";      // 前面的图标
}