<?php

namespace HtmlBuilder;

class Forms extends Element {

    /**
     * @var string 模板
     */
    public $template = '';
    /**
     * 设置模板
     * @param string $template
     * @return self
     */
    public function template(string $template):self{
        $this->template = $template;//file_get_contents(__DIR__."/elements/$template.inc");
        return $this;
    }
    
    public static function input():self{return static::create('','');}
    public static function checkbox():self{return static::create('','');}
    public static function radio():self{ return static::create('','');}
    public static function select():self{ return static::create('','');}
    public static function button():self{ return static::create('','');}
    public static function file():self{ return static::create('','');}
    public static function textarea():self{ return static::create('','');}
    public static function form(string $name):self{ return static::create('form','a')->template('form');}
    
    public function render(): string {
        return preg_replace_callback('#\{\{([a-z][^\{]+)\}\}#i',function($match){
            $key = $match[1];
            if(!isset($this->{$key})) return '';
            $value  = $this->{$key};
            $return = '';
            if($key==='elements' || $key==='attributes'){
                foreach($value as $_k=>$_v) $return .= $_v;
            }elseif($key==='validators'){
                # todo ?
            }else{
                $return = $value;
            }
            return $return;
        },$this->template);
    }
//
//    /**
//     * @param string $name
//     * @param array  $arguments
//     * @return self
//     * @throws \Exception
//     */
    public static function __callStatic(string $name, array $arguments){
        if(!in_array($name, ['input','checkbox','radio','select','button','file','textarea'])) {
            throw new \Exception('不支持的元素:'.$name);
        }
        return static::create($name,current($arguments))->template($name);
    }
    
}

//FormElement::form()->add(
//    FormElement::input('aa')
//)->id('aaa');
//
//$b->add(
////    (new BaseElement('name'))->label('aa')
//    // Former::input('aaa')->label('')
//    // Former::checkbox('aaa[]')->label('')
//    // Former::radio()
//    Element::create('input','')->label('aaa')
//);
//$b->add(
//    Element::create('input','')->label('111'),
//    Element::create('checkbox',''),
//    Element::create('radio','')->label()->value()->required()->enabled()->validate()->sdf()
//);
//$b->add(Element::create('radio',''), Element::input('l'));
//
//
////$b = new BaseElement();
////$b->substr = [];
////$b->type = 'a';
////$b->name = '';
////$b->label= '';
//

/*
$a = new Builder();
$a->add('input',[
    'type'        => 'text',
    'substr'      => 'email',
    'name'        => 'user_name',
    'value'       => '默认值',
    'label'       => '用户名',
    'placeHolder' => '请输入用户名',
    'tooltip'     => '英文名称',
    'description' => '用户名如果不存在，将会支持',
    'validators'  => [
        [
            'type'   => 'text',
            'maxlen' => 12,
            'minlen' => 3,
            'text'   => '用户名最少3位，最大12位',
        ]
    ]
]);
*/