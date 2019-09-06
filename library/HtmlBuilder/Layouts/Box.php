<?php

namespace HtmlBuilder\Layouts;

use HtmlBuilder\Element;

class Box extends Element{
    public $header=['type'=>'','text'=>'','elements'=>[]];
    public $body=['type'=>'','text'=>'','elements'=>[]];
    public $footer=['type'=>'','text'=>'','elements'=>[]];
    public $badges='';
}