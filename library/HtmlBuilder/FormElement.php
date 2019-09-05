<?php

namespace HtmlBuilder;

class FormElement extends Element {

    /**
     * @var string 模板
     */
    public $template = '';
    /**
     * 设置模板
     * @param string $template
     * @return Element
     */
    public function template(string $template):FormElement{
        $this->template = file_get_contents(__DIR__."/elements/$template.inc");
        return $this;
    }

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

    /**
     * @param string $name
     * @param array  $arguments
     * @return Element
     * @throws \Exception
     */
    public static function __callStatic(string $name, array $arguments):FormElement{
        if(!in_array($name, ['input','checkbox','radio','select','button','file','textarea'])) {
            throw new \Exception('不支持的元素:'.$name);
        }
        return static::create($name,current($arguments))->template($name);
    }
    
    public static function form(string $name):FormElement{
        return static::create('form','a')->template('form');
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