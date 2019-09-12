<?php

namespace HtmlBuilder\Forms;

use HtmlBuilder\Element;

class Select extends Element
{
    /**
     * @var bool 是否为 tags ，即可以输入不存在的值
     */
    public $isTags = false;
    /**
     * @var bool 是否可以多选
     */
    public $multiple = false;
    /**
     * @var int 行高
     */
    public $rows = 1;
    /**
     * @var string 下拉选择的样式，select, select2
     */
    public $subtype = 'select'; // select2,
    /**
     * @var array 可选项 [ ['text'=>'文本', 'value'=>'值'], ...]
     */
    public $choices = []; // 选择的元素
    /**
     * @var array 可选项是否来自一个URL [ 'url'=>'获得选项的URL', 'path'=>'返回体中的 xpath', 'textName'=>'名称字段', 'valueName'=>'值字段']
     */
    public $choicesByUrl = [];
    /**
     * @var string|Element 是否允许输入其他选项
     */
    public $other = ''; // 允许输入其他项目
    
    /**
     * Select constructor.
     * @param string $label 标签
     * @param string $name 表单名称
     * @param string|array $value 默认值
     * @param string $subtype 子类型
     */
    public function __construct(string $label, string $name, $value, string $subtype = 'select')
    {
        parent::__construct('select');
        $this->label = $label;
        $this->name = $name;
        $this->subtype = $subtype;
        $this->value($value);
    }
    
    /**
     * 设置选项的来源为一个URL的输出
     * @param string $url
     * @param string $path
     * @param string $textName
     * @param string $valueName
     * @return $this
     */
    public function choicesByUrl(string $url, string $path, string $textName='', string $valueName=''){
        return $this;
    }
    
    /**
     * 设置可选项
     * @param array $choices
     * @return $this
     */
    public function choices(array $choices){
        $this->choices = $choices;
        return $this;
    }
    
    /**
     * 设置允许输入其他选项，并制定文本
     * @param string|Element $other 文本或子元素
     * @return $this
     */
    public function other($other='其他'){
        $this->other = $other;
        return $this;
    }
    
    /**
     * @param mixed $value
     * @return $this|Element
     */
    public function value($value){
        $this->value = is_array($value) ? $value : [$value];
        return $this;
    }
    
    /**
     * 设置是否为 tags 方式
     * @param bool $isTags
     * @return $this
     */
    public function isTags(bool $isTags){
        $this->isTags = $isTags;
        return $this;
    }
    
    /**
     * 设置是否可以多选
     * @param bool $multiple
     * @return $this
     */
    public function multiple(bool $multiple){
        $this->multiple = $multiple;
        return $this;
    }
}