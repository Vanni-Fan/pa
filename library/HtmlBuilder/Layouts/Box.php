<?php

namespace HtmlBuilder\Layouts;

use HtmlBuilder\Element;

class Box extends Element{
    public $header;
    public $body;
    public $footer;
    /**
     * @var string 盒子样式：box-danger,box-primary, box-info, box-success, box-warning, box-danger, bod-gray
     */
    public $style='box-info';
    public $canClose=false;
    public $canMove=false;
    public $canMini=false;
    public function __construct($body, $header=null, $footer=null){
        parent::__construct('box');
        $this->body   = (object)['text'=>null,'element'=>null];
        $this->header = (object)['text'=>null,'element'=>null];
        $this->footer = (object)['text'=>null,'element'=>null];
    
        if($body)   $this->body($body);
        if($header) $this->header($header);
        if($footer) $this->footer($footer);
    }
    // 三款样式选择 : box-danger,Primary, Info, Success, Warning, Danger, Gray
    public function style($style):self{
        $this->style = 'box-'.strtolower($style);
        return $this;
    }
    public function canClose(bool $close=true):self{
        $this->canClose = $close;
        return $this;
    
    }
    public function canMini(bool $mini=true):self{
        $this->canMini = $mini;
        return $this;
    }
    public function canMove(bool $move=true):self{
        $this->canMove = $move;
        return $this;
    }
    
    public function body($body):self {
        if($body instanceof Element){
            $this->body->element = $body;
        }else{
            $this->body->text = $body;
        }
        return $this;
    }
    
    public function header($header):self{
        if($header instanceof Element){
            $this->header->element = $header;
        }else{
            $this->header->text = $header;
        }
        return $this;
    }
    
    public function footer($footer):self{
        if($footer instanceof Element){
            $this->footer->element = $footer;
        }else{
            $this->footer->text = $footer;
        }
        return $this;
    }
}