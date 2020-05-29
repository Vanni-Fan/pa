<?php
/**
 * @author vanni
 * @version 1.0
 */
class Shell{
    static private array $args = [];
    static private array $rules = [];
    static private array $description = [];

    /**
     * 设置需要分析的原始数组，CLI 的话，通常是 $argv
     * @param array $args
     * @return array
     */
    static public function args(array $args=null):array{
        if(is_null($args)) return self::$args;
        return self::$args = $args;
    }

    /**
     * 设置各个参数的规则
     * $rules 的格式如下 :
     *   [
     *      参数名   => 配置项
     *      参数名2  => 配置项2
     *      ....
     *   ]
     *   配置项是以【;】号分隔的字符串，
     *      第一段固定为配置匹配符号，用|线分隔多个匹配项，比如 c|config|configure
     *      stdin: 表示此值可以从标准输入获得
     *      bool: 表示此值是布尔值，只要有则为真
     *      default:xxx   表示使用 xxx 作为默认值
     *      multi: 表示运行有多个项，如果配置，那么值为数组
     *  比如：
     *   [
     *      'config' => 'c|config;default:/var/www/pa/config.php',  # 默认值
     *      'paths'  => 'p|path;multi',                             # 多值
     *      'debug'  => 'd|debug;bool;default:1',                   # 布尔值，默认1
     *      'txt'    => 'c|text|contents;stdin',                    # 内容支持 stdin 输入
     *      'other'  => 'o|other;default:no;stdin'                  # 默认为 no ，支持 stdin 输入
     *   ];
     *
     *
     * @param array $rules
     * @return array
     */
    static public function rules(array $rules=null):array{
        if(is_null($rules)) return self::$rules;
        return self::$rules = $rules;
    }

    /**
     * 设置各个参数的描述信息
     * $description 的格式为 [ 参数名 => 参数描述, 参数名2 => 参数描述2, ... ]
     * @param array $description
     * @return array
     */
    static public function description(array $description=null):array{
        if(is_null($description)) return self::$description;
        return self::$description = $description;
    }

    /**
     * 分析参数
     * @return array
     */
    static function parse(bool $include_undefined_param=false):array {
//        array_shift($arguments); # 第一个是文件名
        $arguments = self::$args;
        $rules = self::$rules;
        # 分析规则配置
        $use_stdin = $is_booleans = $not_booleans = $default = $multiple = $all_rules = [];
        foreach($rules as $key=>$rule){
            $rule_items = explode(';',$rule);

            # 处理特殊关键字，找到则删除
            $find = [
                'stdin'      => &$use_stdin,
                'bool'       => &$is_booleans,
                'multi'      => &$multiple,
                'default:.+' => &$default
            ];
            foreach($rule_items as $index=>$item){
                foreach($find as $type => &$variable){
                    if(preg_match("/$type/", $item)){
                        if($type == 'default:.+') $variable[$key] = substr($item, 8);
                        else                      $variable[$key] = true;
                        unset($rule_items[$index]);
                        break;
                    }
                }
            }

            reset($rule_items);
            $all_rules[$key] = explode('|',current($rule_items));

            if(isset($use_stdin[$key]))    $use_stdin[$key]    = $all_rules[$key][0];
            if(isset($is_booleans[$key]))  $is_booleans[$key]  = $all_rules[$key][0];
            else                           $not_booleans[$key] = $all_rules[$key][0];
        }
        #var_dump($use_stdin , $is_booleans , $not_booleans , $default , $multiple , $all_rules);
        #exit;

        # 生成匹配的正则表达式
        $args_rule = [];
        foreach($all_rules as $key=>$rules){
            $match_items = [];  # 用于保存匹配的数组

            # 先将自己规则从长到短排序
            usort($rules, function($a, $b){ $la=strlen($a); $lb=strlen($b); if($la==$lb) return 0; return ($la>$lb)?-1:1;});
            # 将有单字母的选择单独处理
            $single = (strlen(end($rules))==1) ? array_pop($rules) : null;
            # 其它的项目则直接匹配
            foreach($rules as $rule) $match_items[] = $rule;

            # 单字母规则
            $is_bool = isset($is_booleans[$key]);
            if($single){
                if($is_bool){ # 加入其它布尔值以及其它非布尔值
                    $items = $is_booleans;
                    unset($items[$key]);
                    $match_times   = count($items)+1;
                    foreach($not_booleans as $_key=>$item){
                        if(strlen($item)>1) continue;
                        $_items = array_merge($items, [$item]);
                        $match_str = '['. implode('',$_items).']{0,'.$match_times.'}';
                        $match_items[] = $match_str . $single . $match_str;
                    }
                }else{ # 加入所有布尔值
                    $items = $is_booleans;
                    $items = array_filter($items, function($v){ return strlen($v)==1; }); // 去掉长度大于1的
                    $match_times   = count($items);
//                    echo "\n[",$match_times,$single,"]\n";
                    if($match_times) {
                        $match_str = '[' . implode('', $items) . ']{0,' . $match_times . '}';
                        $match_items[] = $match_str . $single . $match_str;
                    }else{
                        $match_items[] = $single;
                    }
                }
            }
            $rule_str = implode('|',$match_items);
            $args_rule[$key] = '/^(?:\-{1,2})(?:'.$rule_str.')$/';
        }
//        print_r($args_rule);

        # 最终的输出参数
        $params = [];
        $total  = count($arguments);

        # 命令行输入
        $stdin  = array(STDIN); $w = $e = null;
        $has_stdin = (bool)stream_select($stdin,$w,$e,0);
        $stdin_str = $has_stdin ? file_get_contents('php://stdin') : null;

        # 填充最终输出的参数
        foreach($args_rule as $key => $pattern){
            $found = 0;
            for($i=0; $i<$total; $i++){
                @list($param, $value) = explode('=', $arguments[$i]);

//                echo "$key => $pattern  |  $param  |  $value \n";
                if(preg_match($pattern, $param)){
//                    echo "FOUND!!!\n\n";
                    $found = 1;
                    if(is_null($value) && isset($arguments[$i+1]) && substr($arguments[$i+1],0,1)!='-') $value = $arguments[++$i]; # 是否有 var=value 的类型
                    if(is_null($value) && isset($use_stdin[$key]))                             $value = $stdin_str;                            # 是否从标准输入读取
                    if(is_null($value) && isset($default[$key]))                               $value = $default[$key];                        # 是否有提供默认值
                    if(isset($is_booleans[$key]))                                              $value = is_null($value) ? true : (bool)$value; # 如果是布尔型，强制转换

                    if(isset($multiple[$key])){
                        if(!isset($params[$key])) $params[$key] = [$value];
                        else $params[$key][] = $value;
                    }else{
                        $params[$key] = $value;
                    }
                }
            }
//            echo "\n $key => $found $value \n";
            if(!$found){
                $value = null;
                if(isset($default[$key]))      $value = $default[$key];
                if(isset($is_booleans[$key]))  $value = (bool) $value;
                if(isset($multiple[$key])){
                    $params[$key] = $value ? [$value] : [];
                }else{
                    $params[$key] = $value;
                }
            }
        }

        # 获取其他参数
        if($include_undefined_param){
            for($i=0; $i<$total; $i++){
                $key_prefix = substr($arguments[$i],0,1);
                $val_prefix = substr($arguments[$i+1]??'',0,1);
                if($key_prefix === '-'){
                    $key = ltrim($arguments[$i],'-');
                    if($val_prefix === '-'){
                        [$key, $val] = explode('=', $key);
                        $val = $val ?? true;
                    }else{
                        $val = $arguments[++$i]??'';
                    }
                    $params[$key] = $val;
                }
            }
        }
        return $params;
    }

    /**
     * 以色块显示文本信息
     * format: \033[颜色代码;颜色代码;颜色代码m
     *
     * 颜色表
     * 前景             背景              颜色
     *    ---------------------------------------
     *    30                40               黑色
     *    31                41               紅色
     *    32                42               綠色
     *    33                43               黃色
     *    34                44               藍色
     *    35                45               紫紅色
     *    36                46               青藍色
     *    37                47               白色
     *
     * 代码              意义
     *    -------------------------
     *    0                 OFF
     *    1                 高亮显示
     *    4                 underline
     *    5                 闪烁
     *    7                 反白显示
     *    8                 不可见
     * @param string $text 原始文本
     * @param string|int $foreground 前景色
     * @param string|int $background 背景色
     * @return string
    **/
    static function getColorText(string $text,$foreground=32,$background=1):string {
        $after  = "\033[0m";
        $before = "\033[$foreground;{$background}m";
        return $before.$text.$after;
    }

    /**
     * 获得帮助信息
     * @return string
     */
    static public function getHelp():string {
        $rules = self::$rules;
        $description = self::$description;
        $s = "Usage: ".basename($_SERVER['argv'][0])." [".self::getColorText('OPTIONS',31)."]\n\n".self::getColorText('OPTIONS',31).":\n";
        $args_rule = $default = $use_stdin = $booleans = [];
        foreach($rules as $key=>$rule){
            $rule_items = explode(';',strtolower($rule));
            if(($index = array_search('stdin',$rule_items))!==false){
                unset($rule_items[$index]);
                $use_stdin[$key] = true;
            }
            if(($index = array_search('bool',$rule_items))!==false){
                unset($rule_items[$index]);
                $booleans[$key] = true;
            }
            if(($index = array_search('multi',$rule_items))!==false){
                unset($rule_items[$index]);
                $booleans[$key] = true;
            }
            $rule = implode(';',$rule_items);
            if($index = strpos($rule,';default:')){
                $default[$key] = substr($rule, $index+9);
                $rule = substr($rule,0,$index);
            }
            $args_rule[$key] = '-('.self::getColorText($rule,'32;1;4').')';
        }

        $or = self::getColorText(' | ',33);
        foreach($args_rule as $name => $item){
            $space = '    ';
            if(isset($booleans[$name])){
                $s .= $space.$item;
            }else{
//                $s .= $space.$item.self::getColorText($name,31).$or.$item.'='.self::getColorText($name,31).$or.$item.' '.self::getColorText($name,31);
                $s .= $space.$item.'='.self::getColorText($name,31).$or.$item.' '.self::getColorText($name,31);
            }
            if(isset($use_stdin[$name])){
                $s .= $or.$item.' < '.self::getColorText('$stdin',31);
            }
            $s .= "\n".'        '.$description[$name]."\n";
            if(isset($default[$name])) $s .= '        Default:'.$default[$name]."\n";
            $s .= "\n";
        }
        return $s;
    }

    /**
     * 获得部分隐藏输出密码
     * @param string $pass 要隐藏显示的数据
     * @param int $disp_percent 可视化的比例
     * @param string $hidden_char 替换的字符
     * @return string
     */
    static function getPass(string $pass,int $disp_percent, string $hidden_char='*'):string {
        $len = strlen($pass);
        $show_len = ceil($len * ((100-$disp_percent)/100));
        $showed   = [];
        while($show_len--){
            $index = mt_rand(1,$len)-1;
            if(isset($showed[$index])){
                $show_len++;
                continue;
            }
            $showed[$index] = 1;
            $pass[$index]   = $hidden_char;
        }
        return $pass;
    }

}
