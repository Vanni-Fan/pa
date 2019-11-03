<?php

namespace HtmlBuilder\Forms;

use HtmlBuilder\Element;

class Button extends Element
{
    /**
     * @var bool 是否为方形
     */
    public $flat=false;
    /**
     * @var bool 是否单独占一行
     */
    public $block=false;
    /**
     * @var string 按钮的样式：default, primary, success, info, danger, warning
     */
    public $style='info';
    /**
     * @var string|Element 按钮前部分的图标、文字或者Element原始
     */
    public $btnBeforeIcon;
    /**
     * @var string|Element 按钮后部分的图标、文字或者Element原始
     */
    public $btnAfterIcon;
    /**
     * @var string 按钮尾部角标的背景颜色，可选：maroon, purple, orange, navy, olive
     */
    public $badgeColor='maroon';// maroon, purple, orange, navy, olive
    /**
     * @var string 按钮尾部上方的角标
     */
    public $badge='';
    /**
     * @var string 按钮的点击事件，button:普通按钮,  reset:重置表单， submit:提交表单
     */
    public $action='button'; // reset, submit
    /**
     * @var bool 组合按钮时，是否垂直排列
     */
    public $vertical=false;
    /**
     * @var string 子类型，input:带输入框的按钮组合； group:按钮组合； default:单个按钮
     */
    public $subtype = 'default';// input, group, default

    /**
     * Button constructor.
     * @param string $name
     * @param string $label 按钮的文字
     * @param null $value
     * @param string $subtype
     */
    public function __construct(string $name='', string $label='', $value=null, string $subtype='default')
    {
        parent::__construct('button', $name);
        $this->label = $label;
        $this->subtype = $subtype;
        if(null !== $value) $this->value = $value;
    }
    
    /**
     * 设置按钮为扁平按钮
     * @param bool $flat
     * @return $this
     */
    public function flat(bool $flat=true){
        $this->flat = $flat;
        return $this;
    }
    
    /**
     * 设置按钮为单行按钮
     * @param bool $block
     * @return $this
     */
    public function block(bool $block=true){
        $this->block = $block;
        return $this;
    }
    
    /**
     * 设置按钮的样式，六种之一：default, primary, success, info, danger, warning
     * @param string $style
     * @return $this
     */
    public function style(string $style){
        $this->style = $style;
        return $this;
    }
    
    /**
     * 设置按钮前部分的文字、图标或者子Element元素
     * @param $icon_or_button
     * @return $this
     */
    public function btnBeforeIcon($icon_or_button){
        $this->btnBeforeIcon = $icon_or_button;
        return $this;
    }
    
    /**
     * 设置按钮后部分的文字、图标或者子Element元素
     * @param $icon_or_button
     * @return $this
     */
    public function btnAfterIcon($icon_or_button){
        $this->btnAfterIcon = $icon_or_button;
        return $this;
    }
    
    /**
     * 设置按钮尾部角标的颜色,可选：maroon, purple, orange, navy, olive
     * @param string $color
     * @return $this
     */
    public function badgeColor(string $color){
        $this->badgeColor = $color;
        return $this;
    }
    
    /**
     * 设置按钮尾部的角标文本
     * @param string $text
     * @return $this
     */
    public function badge(string $text){
        $this->badge = $text;
        return $this;
    }
    
    /**
     * 设置按钮的点击动作：button:普通按钮,  reset:重置表单， submit:提交表单
     * @param string $action
     * @return $this
     */
    public function action(string $action){
        $this->action = $action;
        return $this;
    }
    
    /**
     * 设置按钮组是否为垂直排列
     * @param bool $vertical
     * @return $this
     */
    public function vertical(bool $vertical=true){
        $this->vertical = $vertical;
        return $this;
    }
}