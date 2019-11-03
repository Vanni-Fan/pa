<?php
namespace HtmlBuilder;

/**
 * Class Validate
 *
 *
 * @package HtmlBuilder
 */
class Validate{
    /**
     * @var string 验证器类型： number, text, regex, mail, expression
     */
    public $type;
    /**
     * @var string 验证不通过时的提示文本
     */
    public $text;
    /**
     * @var \stdClass 验证器规格
     */
    public $rule;
    
    /**
     * Validate constructor.
     * @param string $type 验证器类型
     * @param string $message 错误提示
     * @param array  $option 规则
     */
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
            case 'expression':
                $this->rule = (object)array_merge(['callback'=>'','expression'], $option);
                break;
            case 'mail':
                $this->rule = new \stdClass;
                break;
        }
    }
    
    /**
     * 创建一个数字验证器
     * @param  string $message 错误提示
     * @param int $min 最小值
     * @param int $max 最大值
     * @return Validate
     */
    public static function number(string $message, int $min=-2000000000, int $max=2000000000){
        return new static('number', $message, ['minValue'=>$min, 'maxValue'=>$max]);
    }
    
    /**
     * 创建一个文本验证器
     * @param string $message
     * @param int $minlen
     * @param int $maxlen
     * @return Validate
     */
    public static function text(string $message, int $minlen=1, int $maxlen=256){
        return new static('text', $message, ['minLength'=>$minlen, 'maxLength'=>$maxlen]);
    }
    
    /**
     * 创建一个基于正则的验证器
     * @param string $message
     * @param string $regex
     * @return Validate
     */
    public static function regex(string $message, string $regex){
        return new static('regex', $message, ['regex'=>$regex]);
    }
    
    /**
     * 创建一个邮件验证器
     * @param string $message
     * @return Validate
     */
    public static function mail(string $message){
        return new static('mail', $message);
    }

    public static function expression(string $message, string $expression){
        return new static('expression',$message, ['expression'=>$expression]);
    }
}
