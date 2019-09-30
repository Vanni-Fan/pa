<?php
namespace HtmlBuilder;

/**
 * 基础元素
 * Class Element
 * @package FormBuilder
 */
class Element{
    /**
     * @var string DOM ID
     */
    public $id = '';
    /**
     * @var string 类型
     */
    public $type;
    /**
     * @var string 字段名称
     */
    public $name   = '';
    /**
     * @var string 子分类
     */
    public $subtype = '';
    /**
     * @var string 标签名称
     */
    public $label  = '';
    /**
     * @var string 标签位置： top,bottom,left,right,left-right,right-left
     */
    public $labelPosition = 'top';
    /**
     * @var int 标签占的宽度 12分之几
     */
    public $labelWidth = 0;
    /**
     * @var bool 是否可用
     */
    public $enabled=true;
    /**
     * @var bool 是否可视
     */
    public $visible=true;
    /**
     * @var bool 是否必须
     */
    public $required=false;
    /**
     * @var mixed 默认值
     */
    public $value='';
    /**
     * @var string 占位符
     */
    public $placeHolder='';
    /**
     * @var string 在标签后面的提示信息，用问号显示，鼠标移动上去，则显示此内容
     */
    public $tooltip='';
    /**
     * @var string 说明信息，位于组件下方
     */
    public $description='';
    /**
     * @var array HTML标签的扩展属性
     */
    public $attributes=[];
    /**
     * @var array 验证器
     */
    public $validators=[];
    /**
     * @var string 在标签前面添加的图标
     */
    public $labelIcon='';
    /**
     * @var string 在输入框后面条件的图标
     */
    public $badgeIcon='';
    /**
     * @var array 子元素
     */
    public $elements=[];
    
    /**
     * Element constructor.
     * @param string $type 标签名称
     * @param string $name
     */
    public function __construct(string $type, string $name=''){
        $this->type = $type;
        $this->name = $name;
    }

    /**
     * 设置子类型
     * @param string $subtype
     * @return self
     */
    public function subtype(string $subtype){
        $this->subtype = $subtype;
        return $this;
    }
    
    /**
     * 创建一个元素
     * @param $params
     * @return mixed
     */
    public static function create(...$params){
        return new static(...$params);
    }
    
    /**
     * 设置标签
     * @param string $label
     * @return self
     */
    public function label(string $label){
        $this->label = $label;
        return $this;
    }
    
    /**
     * 设置标签的位置
     * @param string $position
     * @return self
     */
    public function labelPosition(string $position){
        $this->labelPosition = $position;
        return $this;
    }

    /**
     * 设置标签占的宽度，12分之几
     * @param int $labelWidth
     * @return self
     */
    public function labelWidth(int $labelWidth){
        $this->labelWidth = $labelWidth;
        return $this;
    }
    
    /**
     * 设置是否可用
     * @param bool $enabled=true
     * @return self
     */
    public function enabled(bool $enabled=true){
        $this->enabled = $enabled;
        return $this;
    }
    
    /**
     * 设置是否可视
     * @param bool $visible=true
     * @return self
     */
    public function visible(bool $visible=true){
        $this->visible = $visible;
        return $this;
    }
    
    /**
     * 设置是否必须
     * @param bool $required=true
     * @return self
     */
    public function required(bool $required=true){
        $this->required = $required;
        return $this;
    }
    
    /**
     * 设置默认值
     * @param mixed $value
     * @return self
     */
    public function value($value){
        $this->value = $value;
        return $this;
    }
    
    /**
     * 设置占位符
     * @param string $placeHolder
     * @return self
     */
    public function placeHolder(string $placeHolder){
        $this->placeHolder = $placeHolder;
        return $this;
    }
  
    /**
     * 设置在标签后面的提示信息，用问号显示，鼠标移动上去，则显示此内容
     * @param string $tooltip
     * @return self
     */
    public function tooltip(string $tooltip){
        $this->tooltip = $tooltip;
        return $this;
    }
 
    /**
     * 设置说明信息，位于组件下方
     * @param string $description
     * @return self
     */
    public function description(string $description){
        $this->description = $description;
        return $this;
    }
    
    /**
     * 添加额外的属性
     * @param string $name
     * @param $values
     * @return self
     */
    public function attr(string $name, $values){
        $this->attributes[$name] = $values;
        return $this;
    }
    
    /**
     * 添加验证器
     * @param string $validate
     * @return self
     */
    public function validate(Validate ... $validate){
        foreach($validate as $v) $this->validators[] = $v;
        return $this;
    }
    
    /**
     * 设置说明信息，位于组件下方
     * @param string $labelIcon
     * @return self
     */
    public function labelIcon(string $labelIcon){
        $this->labelIcon = $labelIcon;
        return $this;
    }
    
    /**
     * 设置说明信息，位于组件下方
     * @param string $badgeIcon
     * @return self
     */
    public function badgeIcon(string $badgeIcon){
        $this->badgeIcon = $badgeIcon;
        return $this;
    }
  
    /**
     * 添加元素到子元素列表
     * @param Element ...$elements
     * @return self
     */
    public function add(Element ... $elements){
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
    
    /**
     * @param string $name
     * @param $value
     */
    public function __set(string $name, $value)
    {
        $this->$name = $value;
    }
    
    public function __get(string $name){
        return $this->$name;
    }
    
    /**
     * @param string $name
     * @param  array $arguments
     * @return Element
     */
    public function __call(string $name, array $arguments)
    {
//        $this->attributes[$name] = current($arguments);
        $this->$name = current($arguments);
        return $this;
    }
    
    /**
     * 转换成数组
     * @return array
     */
    public function toArray():array{
        return json_decode(json_encode($this),1);
    }
}