<?php
namespace HtmlBuilder;

/**
 * Class Validate
 *
 *
 * @package HtmlBuilder
 */
class Validate{
    
    public $type;
    public $text;
    public $rule;
    public function __construct(string $type, string $message,array $option=[])
    {
        $this->type = $type;
        $this->text = $message;
        switch($type){
            case 'number':
                $this->rule = (object)array_merge(['minValue'=>-2000000000,'maxValue'=>2000000000], $option);
                break;
            case 'text':
                $this->rule = (object)array_merge(['minLength'=>1,'maxLength'=>256], $option);
                break;
            case 'regex':
                $this->rule = (object)array_merge(['regex'=>''], $option);
                break;
            case 'mail':
            case 'expression':
                $this->rule = new \stdClass;
                break;
        }
    }
    public static function number($message, $min=-2000000000, $max=2000000000){
        return new static('number', $message, ['minValue'=>$min, 'maxValue'=>$max]);
    }
    public static function text($message, $minlen=1, $maxlen=256){
        return new static('text', $message, ['minLength'=>$minlen, 'maxLength'=>$maxlen]);
    }
    public static function regex($message, $regex){
        return new static('regex', $message, ['regex'=>$regex]);
    }
    public static function mail($message){
        return new static('mail', $message);
    }
}
