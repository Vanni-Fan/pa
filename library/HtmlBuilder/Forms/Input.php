<?php
/*
- mail 邮件: 添加验证器
- url URL： 添加验证器
- tel 电话： 添加 mask、验证器
- mobile 手机：
- cre 货币： 添加前缀
- number 数字： 添加验证器
- password 密码： password
- hidden 隐藏元素： 设置 vis
- time 时间： 添加额外控件
- date 日期： 添加额外控件
- color 颜色： 添加额外控件
*/
namespace HtmlBuilder\Forms;

use HtmlBuilder\Element;
use HtmlBuilder\Validate;

class Input extends Element{
    /**
     * @var string 输入蒙版
     */
    public $inputMask="";
    /**
     * @var bool 是否在右下方显示统计，包含长度和单词个数
     */
    public $statistics=false;        // 显示在右下角，字符长度，单词个数
    /**
     * @var string 标签前部分的图标
     */
    public $inputAfterIcon="";       // 后面的图标
    /**
     * @var string 标签后部分的图标
     */
    public $inputBeforeIcon="";      // 前面的图标
    
    /**
     * Input constructor.
     * @param string $name 表单中的名称
     * @param string $label 标签
     * @param string $value 默认值
     * @param string $subtype 子类型：mail, url, tel, mobile, currency, number, hidden, password, time, date, color
     */
    public function __construct(string $name='', string $label='', string $value=null, string $subtype='text'){
        parent::__construct('input',$name);
        if(null !== $value) $this->value = $value;
        $this->subtype = $subtype;
        switch($subtype){
            case 'mail':
                $this->validate(Validate::mail('请输入正确的邮件地址'));
                break;
            case 'url':
                $this->validate(Validate::regex('请输入正确的URL地址','^https?://.+i'))->placeHolder('http[s]://');
                break;
            case 'tel':
                $this->inputMask("'mask':'(9999)-(99999999)'")->validate(Validate::regex('请输入正确的电话号码','^[0-9]{3,4}-[0-9]{6,8}$'));
                break;
            case 'mobile':
                $this->inputMask("'mask':'199 9999 9999'")->subtype('text')->validate(Validate::regex('请输入正确的手机号码','^1[0-9]{2} [0-9]{4} [0-9]{4}$'));
                break;
            case 'currency':
                $this->inputBeforeIcon('fa fa-yen')->validate(Validate::regex('请输入正确的金额','^[0-9]+(\.[0-9]{1,})$'));
                break;
            case 'number':
                $this->subtype('number');
                break;
            case 'hidden':
                $this->visible(false);
                break;
            case 'password':
                $this->subtype('password');
                break;
            case 'time':
                $this->subtype('time')->validate(Validate::regex('时间格式不对','^[0-9][0-9]:[0-9][0-9]$'));
                break;
            case 'date':
                $this->subtype('date');
                break;
            case 'color':
                $this->subtype('color');
                break;
        }
        if($label) $this->label = $label;
    }
    
    /**
     * 设置标签前部分的图标
     * @param $icon
     * @return $this
     */
    public function inputAfterIcon($icon){
        $this->inputAfterIcon = $icon;
        return $this;
    }
    
    /**
     * 设置标签后部分的图标
     * @param $icon
     * @return $this
     */
    public function inputBeforeIcon($icon){
        $this->inputBeforeIcon = $icon;
        return $this;
    }
    
    /**
     * 设置输入蒙版
     * @param $mask
     * @return $this
     */
    public function inputMask($mask){
        $this->inputMask = $mask;
        return $this;
    }
    
    /**
     * 开启输入统计
     * @param bool $enable
     * @return $this
     */
    public function statistics(bool $enable=true){
        $this->statistics = $enable;
        return $this;
    }
}