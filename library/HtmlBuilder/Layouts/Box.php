<?php

namespace HtmlBuilder\Layouts;

use HtmlBuilder\Element;

class Box extends Element{
    /**
     * @var string|Element 盒子的头部
     */
    public $header;
    /**
     * @var string|Element 盒子的内容
     */
    public $body;
    /**
     * @var string|Element 盒子的尾部
     */
    public $footer;
    /**
     * @var string 盒子样式：box-danger,box-primary, box-info, box-success, box-warning, box-danger, bod-gray
     */
    public $style='box-info';
    /**
     * @var bool 盒子是否有关闭按钮
     */
    public $canClose=false;
    /**
     * @var bool 盒子是否可以拖拽移动
     */
    public $canMove=false;
    /**
     * @var bool 盒子是否可以最小化
     */
    public $canMini=false;
    
    /**
     * Box constructor.
     * @param      $body
     * @param null $header
     * @param null $footer
     */
    public function __construct($body, $header=null, $footer=null){
        parent::__construct('box');
        $this->body   = (object)['text'=>null,'element'=>null];
        $this->header = (object)['text'=>null,'element'=>null];
        $this->footer = (object)['text'=>null,'element'=>null];
    
        if($body)   $this->body($body);
        if($header) $this->header($header);
        if($footer) $this->footer($footer);
    }
    
    /**
     * 设置盒子的样式，六种之一：Primary, Info, Success, Warning, Danger, Gray
     * @param string $style
     * @return $this
     */
    public function style(string $style){
        $this->style = 'box-'.strtolower($style);
        return $this;
    }
    
    /**
     * 设置盒子是否能关闭
     * @param bool $close
     * @return $this
     */
    public function canClose(bool $close=true){
        $this->canClose = $close;
        return $this;
    
    }
    
    /**
     * 设置盒子是否能最小化
     * @param bool $mini
     * @return $this
     */
    public function canMini(bool $mini=true){
        $this->canMini = $mini;
        return $this;
    }
    
    /**
     * 设置盒子是否能拖拽移动
     * @param bool $move
     * @return $this
     */
    public function canMove(bool $move=true){
        $this->canMove = $move;
        return $this;
    }
    
    /**
     * 设置盒子的内容体
     * @param string|Element $body
     * @return $this
     */
    public function body($body) {
        if($body instanceof Element){
            $this->body->element = $body;
        }else{
            $this->body->text = $body;
        }
        return $this;
    }
    
    /**
     * 设置盒子的头部
     * @param string|Element $header
     * @return $this
     */
    public function header($header){
        if($header instanceof Element){
            $this->header->element = $header;
        }else{
            $this->header->text = $header;
        }
        return $this;
    }
    
    /**
     * 设置盒子的尾部
     * @param string|Element $footer
     * @return $this
     */
    public function footer($footer){
        if($footer instanceof Element){
            $this->footer->element = $footer;
        }else{
            $this->footer->text = $footer;
        }
        return $this;
    }
}