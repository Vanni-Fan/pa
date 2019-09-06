<?php
namespace HtmlBuilder;

/**
 * 基础元素
 * Class Element
 * @package FormBuilder
 */
class Element{
    /**
     * @var string 类型
     */
    public $type;
    /**
     * @var string 字段名称
     */
    public $name   = '';
    public function __construct($type, $name=''){
        $this->type = $type;
        $this->name = $name;
    }


    /**
     * @var string 子分类
     */
    public $subtype = '';
    /**
     * 设置子类型
     * @param $subtype
     * @return self
     */
    public function subtype($subtype):self{
        $this->subtype = $subtype;
        return $this;
    }
    
    /**
     * 创建一个元素
     * @param $type
     * @param $name
     * @return mixed
     */
    public static function create($type, $name=''):self{
        return new static($type, $name);
    }
    
    /**
     * @var string 标签名称
     */
    public $label  = '';
    /**
     * 设置标签
     * @param string $label
     * @return self
     */
    public function label(string $label):self{
        $this->label = $label;
        return $this;
    }

    
    /**
     * @var string 标签位置： top,bottom,left,right,left-right,right-left
     */
    public $labelPosition = 'top';
    /**
     * 设置标签的位置
     * @param string $position
     * @return self
     */
    public function labelPosition(string $position):self{
        $this->labelPosition = $position;
        return $this;
    }
    
    
    /**
     * @var int 标签占的宽度 12分之几
     */
    public $labelWidth = 0;
    /**
     * 设置标签占的宽度，12分之几
     * @param int $labelWidth
     * @return self
     */
    public function labelWidth(int $labelWidth):self{
        $this->labelWidth = $labelWidth;
        return $this;
    }
    
    
    /**
     * @var bool 是否可用
     */
    public $enabled=true;
    /**
     * 设置是否可用
     * @param bool $enabled=true
     * @return self
     */
    public function enabled(bool $enabled=true):self{
        $this->enabled = $enabled;
        return $this;
    }
    
    
    /**
     * @var bool 是否可视
     */
    public $visible=true;
    /**
     * 设置是否可视
     * @param bool $visible=true
     * @return self
     */
    public function visible(bool $visible=true):self{
        $this->visible = $visible;
        return $this;
    }
    
    
    /**
     * @var bool 是否必须
     */
    public $required=false;
    /**
     * 设置是否必须
     * @param bool $required=true
     * @return self
     */
    public function required(bool $required=true):self{
        $this->required = $required;
        return $this;
    }
    
    
    /**
     * @var mixed 默认值
     */
    public $value='';
    /**
     * 设置默认值
     * @param mixed $value
     * @return self
     */
    public function value($value):self{
        $this->value = $value;
        return $this;
    }
    
    
    /**
     * @var string 占位符
     */
    public $placeHolder='';
    /**
     * 设置占位符
     * @param string $placeHolder
     * @return self
     */
    public function placeHolder(string $placeHolder):self{
        $this->placeHolder = $placeHolder;
        return $this;
    }
  
  
    /**
     * @var string 在标签后面的提示信息，用问号显示，鼠标移动上去，则显示此内容
     */
    public $tooltip='';
    /**
     * 设置在标签后面的提示信息，用问号显示，鼠标移动上去，则显示此内容
     * @param string $tooltip
     * @return self
     */
    public function tooltip(string $tooltip):self{
        $this->tooltip = $tooltip;
        return $this;
    }
 
 
    /**
     * @var string 说明信息，位于组件下方
     */
    public $description='';
    /**
     * 设置说明信息，位于组件下方
     * @param string $description
     * @return self
     */
    public function description(string $description):self{
        $this->description = $description;
        return $this;
    }
   
    
    /**
     * @var array HTML标签的扩展属性
     */
    public $attributes=[];
    /**
     * 添加额外的属性
     * @param string $name
     * @param $values
     * @return self
     */
    public function attr(string $name, $values):self{
        $this->attributes[$name] = $values;
        return $this;
    }

    
    /**
     * @var array 验证器
     */
    public $validators=[];
    /**
     * 添加验证器
     * @param string $validate
     * @return self
     */
    public function validate($validate):self{
        $this->validators[] = $validate;
        return $this;
    }
    
    
    /**
     * @var string 在标签前面添加的图标
     */
    public $labelIcon='';
    /**
     * 设置说明信息，位于组件下方
     * @param string $labelIcon
     * @return self
     */
    public function labelIcon(string $labelIcon):self{
        $this->labelIcon = $labelIcon;
        return $this;
    }
    
    
    /**
     * @var string 在输入框后面条件的图标
     */
    public $badgeIcon='';
    /**
     * 设置说明信息，位于组件下方
     * @param string $badgeIcon
     * @return self
     */
    public function badgeIcon(string $badgeIcon):self{
        $this->badgeIcon = $badgeIcon;
        return $this;
    }
  
  
    /**
     * @var array 子元素
     */
    public $elements=[];
    /**
     * 添加元素到子元素列表
     * @param Element ...$elements
     * @return self
     */
    public function add(Element ... $elements):self{
        foreach($elements as $element){
            $this->elements[] = $element;
        }
        return $this;
    }


    /**
     * 直接输出JSON
     * @return string
     */
    public function __toString():string {
        return json_encode($this, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __call($name, $arguments)
    {
        $this->attributes[$name] = current($arguments);
    }

    public function toArray():array{
        return json_decode(json_encode($this),1);
    }
}