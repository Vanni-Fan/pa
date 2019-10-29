<?php

namespace HtmlBuilder\Forms;

use HtmlBuilder\Element;

class Check extends Element
{
    /**
     * @var string iCheck的样式，10种之一：black,red,green,blue,aero,grey,orange,yellow,pink,purple
     */
    public $style = 'purple';//black,red,green,blue,aero,grey,orange,yellow,pink,purple
    /**
     * @var string 是扁平(flat)还是圆角(square)
     */
    public $flat = 'flat'; // flat or square
    /**
     * @var int 每行显示多少个元素
     */
    public $colCount = 3; // 每行几列
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
     * @var string|Element 是否允许选择全部
     */
    public $selectAll = ''; // 允许选择所有
    /**
     * @var string 是否允许空选
     */
    public $none = ''; // 允许不选,
    
    /**
     * Check constructor.
     * @param string $name 表单中的名称
     * @param string $label 标签名称
     * @param  mixed $value 表单的默认值
     * @param string $subtype 样式
     */
    public function __construct(string $name='', string $label='', $value=null, string $subtype='checkbox')
    {
        parent::__construct('check');
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
}