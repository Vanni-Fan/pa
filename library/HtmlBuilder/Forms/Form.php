<?php

namespace HtmlBuilder\Forms;

use HtmlBuilder\Element;

class Form extends Element
{
    /**
     * @var string 提交地址
     */
    public $action;
    /**
     * @var string 提交方式，post or get
     */
    public $method;
    public function __construct($action, $method)
    {
        parent::__construct('form');
        $this->action = $action;
        $this->method = $method;
    }
    
    /**
     * 设置提交的地址
     * @param string $action 提交地址
     * @return $this
     */
    public function action(string $action){
        $this->action = $action;
        return $this;
    }
    
    /**
     * 设置提交方式
     * @param string $method 提交方式
     * @return $this
     */
    public function method(string $method){
        $this->method = $method;
        return $this;
    }
}