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
    public $inputMask="99:99";
    public $statistics=false;        // 显示在右下角，字符长度，单词个数
    public $inputAfterIcon="";       // 后面的图标
    public $inputBeforeIcon="";      // 前面的图标
    public function __construct($name, $subtype='text', $label=''){
        parent::__construct('input',$name);
        $this->subtype = $subtype;
        switch($subtype){
            case 'mail':
                $this->validate(Validate::mail('请输入正确的邮件地址'));
                break;
            case 'url':
                $this->validate(Validate::regex('请输入正确的URL地址','#^https?://.+#i'))->placeHolder('http[s]://');
                break;
            case 'tel':
                $this->inputMask("'mask':'(9999)-(99999999)'");
                break;
            case 'mobile':
                $this->inputMask("'mask':'(1)9999999999'")->subtype('number');
                break;
            case 'currency':
                $this->inputBeforeIcon('fa fa-yen')->validate(Validate::regex('请输入正确的金额','#^\d+(\.\d{1,})$#'));
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
                $this->subtype('time')->validate(Validate::regex('时间格式不对','#^\d\d:\d\d$'));
                break;
            case 'date':
                $this->subtype('date');
                break;
            case 'color':
                break;
        }
        if($label) $this->label = $label;
    }
    
    public function inputAfterIcon($icon):self{
        $this->inputAfterIcon = $icon;
        return $this;
    }
    
    public function inputBeforeIcon($icon):self{
        $this->inputBeforeIcon = $icon;
        return $this;
    }
    
    public function inputMask($mask):self{
        $this->inputMask = $mask;
        return $this;
    }
    
    public function statistics(bool $enable=true):self{
        $this->statistics = $enable;
        return $this;
    }
}