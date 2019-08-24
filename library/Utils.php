<?php
/**
 * @author vanni.fan
 */
class Utils{
    /*{{{*/ # 全部支持的比较操作符
    const CONDITIONS_OPERATORS = [
        '='           => '等于',
        '==='         => '完全相等',
        '>'           => '大于',
        '<'           => '小于',
        '>='          => '大于或等于', 
        '<='          => '小于或等于', 
        '!='          => '不等于',
        '^'           => '前缀匹配',
        '!^'          => '前缀不匹配',
        '$'           => '后缀匹配',
        '!$'          => '后缀不匹配',
        'and'         => '并且',
        'or'          => '或者',
        'in'          => '包含',
        'not in'      => '不包含',
        'range'       => '在范围内',
        'between'     => '在范围内',
        'not range'   => '不在范围内',
        'not between' => '不在范围内',
        'match'       => '正则匹配',
        'not match'   => '正则不匹配',
    ];/*}}}*/

    public static function getDispatchParamsByKey(\Phalcon\Mvc\Dispatcher $dispatcher, $key=null){
        $params = [];
        $current_key = null;
        foreach($dispatcher->getParams() as $_key=>$param){
            if(is_int($_key)){
                if($current_key){
                    $params[$current_key] = $param;
                    $current_key  = null;
                } else {
                    $current_key  = $param;
                    $params[$current_key] = null;
                }
            }else{
                $params[$_key] = $param;
            }
        }
        return $key ? ($params[$key]??null) : $params;
    }
    public static function isCli(){
        return strpos(php_sapi_name(),'cli') !== false;
    }
    public static function getConditionsOptions(){
        return self::CONDITIONS_OPERATORS;
    }
    /*{{{*//** 获取客户端IP
     *
     */
    public static function ip(){
        return $_SERVER['REMOTE_ADDR'];
    }/*}}}*/

    /*{{{*/ /** 分析绑定的字符模板，ext：parseBind('3.aa.bb.cc|dd|ee=_,2', $data)
     * 格式：  字段 | 过滤器
     *     比如 parseBind('3.index_aa.index_bb.cc|func_a|func_b=param2', $data)
     *
     * 注意，如果 $str 中没有 . 和 | 字符，则原始返回。如果需要顶层替换，添加第四个参数
     *     比如 parseBind('mykey', $data, null, true)
     *
     * @param string $str 绑定的字符串格式
     * @param array &$bind_data 用于查询绑定值的原始数据源
     * @param mixed $default null 默认值
     * @param bool $match_top 是否顶层绑定，即 vpath 中没有点号，也没有竖线，也会替换（如果找到的话）
     * @return mixed
     *//*}}}*/
    public static function parseBind(string $str, array &$bind_data, $default=null, bool $match_top=false){/*{{{*/
        # 找出字段和过滤器的位置
        $dot_index = strpos($str,'.');
        $func_index = strpos($str, '|');
        if(!$match_top && !$dot_index && !$func_index) return $str; // 既没有 . 也没有 | 直接返回·
        
        if($func_index === false){ # 没有func的话，直接找 vpath 即可
            return self::vpath($str, $bind_data, $default ?? $str);
        }else{ # 有 func 的话，需要切出index和func，然后单独调用
            $index = substr($str, 0, $func_index);
            $func  = substr($str, $func_index+1);
//            $value = self::vpath($index, $bind_data, $str);
            $value = self::vpath($index, $bind_data, $default ?? $index);
//            echo " $func ( $index ) = $value \n";
            return self::funcall($func, $value);
        }
    }/*}}}*/

    /*{{{*/ /** 计算条件值，格式如下，ext: conditions(['0.a.b','=','1.c.d'], $data)
     * 用于比较的数组一定是3个元素： [ 表达式1,  比较符,  表达式2 ]
     * 比较符支持： > , < , >=, <=, =, !=, &&, ||, and, or, ==, === 【参考：self::CONDITIONS_OPERATORS】
     * 表达式支持： Bind语法 和 简易语法
     *   Bind语法【参考 self::replaceBindFields 方法】： [ 'bind'=> '0 or 1', 'value'=>'值,支持绑定和函数调用', 'func'=>'外围函数']
     *   简易语法，[ '外围函数', '参数1', '参数2', '参数3'.... ]
     * 例子
     * $sources = [
     *     0 => [ # 0 表示通知源
     *         'user_id'     => 123,
     *         'user_name'   => 'vanni',
     *     ],
     *     2 => [
     *         'field_a' => 'aaa',
     *         'field_b' => 'bbb',
     *         'field_c' => [ 1,2,3 ],
     *         'field_d' => [ 'a' => 11, 'b' => 22 ]
     *     ],
     *     3=>[
     *         ['user_id'=>1],
     *         ['user_id'=>12],
     *         ['user_id'=>3],
     *         ['user_id'=>4],
     *     ]
     * ];
     * # 简单判断
     * $conditions = [11,'===','11'];
     * $rs[] = Utils::conditions($conditions, $sources);
     *
     * # 绑定语法
     * $conditions = [
     *     ['bind'=>1, 'value'=>'sprintf=Y-m-d,_|date'],    // 绑定语法，有 bind , value or func 的数组下标
     *     '>=',
     *     '2019-12-21'
     * ];
     *
     * # 组个判断
     * $conditions = [
     *     [222,'>=','0.user_id|Tool::exp=*2'], // 222 >= 123*2
     *     'or',
     *     ['2.field_c.1', '<', '2.field_d.a|Tool::exp=/2'] // 2 < 11/2
     * ];
     * $rs[] = Utils::conditions($conditions, $sources);
     *
     * # 多重嵌套
     * $conditions = [
     *     [11,'==',11],
     *     'and',
     *     [
     *         [222, '<', '0.user_id'],
     *         'or',
     *         [
     *             ['2.field_d.b', '<', '0.user_id'],
     *             'and',
     *             ['0.user_id', '=', 123]
     *         ]
     *     ]
     * ];
     *
     * $rs[] = Utils::conditions($conditions, $sources);
     *
     * # 多重函数调用，函数接受一个数组参数
     * $conditions  = [
     *     ['array_sum=_', '2.field_c.1', '2.field_d.a'], '>=', '10'   // 简易语法，第一个参数为函数
     * ];
     * $rs[] = Utils::conditions($conditions, $sources);
     *
     * # 多重函数调用，函数接受顺序接受参数
     * $conditions = [true,'=',['in_array', '0.user_id', '3.*.user_id']];
     * $rs[] = Utils::conditions($conditions, $sources);
     * print_r($rs);
     *
     * @param array $conditions 判断数组
     * @param array &$bind_data 绑定的数据
     * @return bool
     *//*}}}*/
    public static function conditions(array $conditions,array &$bind_data):bool{/*{{{*/
//        [$exp1, $co, $exp2] = $conditions;
        list($exp1, $co, $exp2) = $conditions;
        Logger\Logger::debug(['conditions','original', $exp1, $co, $exp2]); // 不要删除此行，改设Logger::level来屏蔽
        # 数字开头:字段|函数
        #$rex = '/^(\d+|\$[a-z_]+)\.[a-z_\*\.]+/i';
        $rex = '/\.|\|/';

        # 表达式是否为子条件
        $result1 = $result2 = null;
        foreach(['exp1'=>'result1','exp2'=>'result2'] as $exp=>$result){
            $_exp = $$exp;       # 表达式
            if(is_array($_exp)){ # 如果表达式是数组，说明要么需要合并，要么是子查询
//                echo "\n",print_r($_exp,1);
                if($_exp['bind'] ?? $_exp['value'] ?? $_exp['func'] ?? false){ // 非绑定语法
//                    echo "是绑定语法\n";
                    $tmp = self::replaceBindFields([$_exp], $bind_data);
                    $$result = $tmp[0];
                }elseif(array_key_exists($_exp[1], self::CONDITIONS_OPERATORS)){ # 是子查询
//                    echo "是子查询\n";
                    $$result = self::conditions($_exp, $bind_data);
                }else{ # 简易语法的合并数据
//                    echo "简易语法的数组合并\n";
                    #$func = array_pop($_exp);
                    $func = array_shift($_exp);
                    $$result = self::funcall($func, array_map(function($v)use($rex,&$bind_data){
                        return preg_match($rex,$v) ? self::parseBind($v,$bind_data) : $v;
                    }, $_exp));
                }
            }else{
//                echo "\n固定值:$_exp\n";
                $$result = preg_match($rex, $_exp) ? self::parseBind($_exp, $bind_data) : $_exp;
            }
        }
        Logger\Logger::debug(['conditions','actual', $result1, $co, $result2]);// 不要删除此行，改设Logger::level来屏蔽
        //echo "比较： 【 $result1 】 $co 【 $result2 】\n";
        switch($co){ # 不使用 eval ，IDE或一些代码检查器会报有风险
            case '=':
            case '==':  return $result1 ==  $result2;
            case '===': return $result1 === $result2;
            case '&&':
            case 'and': return $result1 &&  $result2;
            case '>':   return $result1 >   $result2; 
            case '<':   return $result1 <   $result2; 
            case '>=':  return $result1 >=  $result2; 
            case '<=':  return $result1 <=  $result2; 
            case '!=':  return $result1 !=  $result2; 
            case '^':   return strpos($result1, $result2)===0; # 字符串开始
            case '!^':  return !strpos($result1, $result2)===0;# 不以某字符串开始
            case '||':
            case 'or':  return $result1 ||  $result2; 
            case 'in': # 包含
                if(is_array($result2)){
                    return in_array($result2, $result1);
                }else{
                    return stripos($result1, $result2)!==false;
                }
            case 'not in': # 不包含
                if(is_array($result2)){
                    return !in_array($result1, $result2); 
                }else{
                    return stripos($result1, $result2)===false;
                }
            case '$':# 字符串结束
                return substr($result1, -1 * strlen($result2)) === $result2;
//                $found = strrpos($result1, $result2);
//                if($found===false) return false;
//                else return (strlen($result2) - $found) == strlen($result1);
            case '!$':# 不以某字符串结束
                return substr($result1, -1 * strlen($result2)) !== $result2;
//                $found = strrpos($result1, $result2);
//                if($found!==false) return true;
//                else return (strlen($result2) - $found) != strlen($result1);
            case 'range': 
            case 'between': # 区间范围包含
                if(!is_array($result2)) return false;
                if(count($result2)!==2) return false;
                $start = min($result2);
                $end   = max($result2);
                $value = is_numeric($result1) ? $result1 : strtotime($result1);
                $start = is_numeric($start)   ? $start   : strtotime($start);
                $end   = is_numeric($end)     ? $end     : strtotime($end);
                return $value >= $start && $value <= $end;
            case 'not range': 
            case 'not between': # 不在区间范围以内
                if(!is_array($result2)) return false;
                if(count($result2)!==2) return false;
                $start = min($result2);
                $end   = max($result2);
                $value = is_numeric($result1) ? $result1 : strtotime($result1);
                $start = is_numeric($start)   ? $start   : strtotime($start);
                $end   = is_numeric($end)     ? $end     : strtotime($end);
                return $value < $start && $value > $end;
            case 'match': # 正则匹配
                return @preg_match("\x01".$result2."\x01", $result1)===1;
            case 'not match': # 正则不匹配
                return @preg_match("\x01".$result2."\x01", $result1)!==1;
        }
    }/*}}}*/

    /*{{{*/ /** 递归的替换数组中的模板字符串，实际上递归的调用 replaceTemplateVal 方法
     * 
     * @param array $source 模板数据源，包含有模板字符串的数组
     * @param array &$data 用于替换模板的数据
     * @param array $tags 模板的开始和结束标签
     * @return array
     *//*}}}*/
    public static function tmpValArray(array $source, array &$data, array $tags=['[',']']):array{/*{{{*/
        $out = [];
        foreach($source as $k=>$v){
            $out[$k] = is_array($v) ? self::tmpValArray($v, $data, $tags) : (empty($v)?$v:self::replaceTemplateVal($v, $data, $tags));
        }
        return $out;
//        return array_map(function($v) use (&$data, $tags){
//            if(is_array($v)) $v = self::tmpValArray($v, $data, $tags);
//            else{
//                if(empty($v)) return $v;
//                $v = self::replaceTemplateVal($v, $data, $tags);
//            }
//            return $v;
//        }, $source);
    }/*}}}*/

    /*{{{*/ /** 替换模板中的变量，ext： replaceTemplateVal('aaa[aaa.bb.cc]bbb',$data)
     *
     * $b = new stdclass;
     * $b->c = 3333;
     * $a = [
     *   'aaa'=>1111,
     *   'bbb'=>2222,
     *   'ccc'=>['dd'=>['e'=>$b]]
     * ];
     * echo Utils::replaceTemplateVal('aaa:{aaa},bbb:{bbb|Utils::test},ccc:{ccc.dd.e.c|Utils::test}', $a, ['{','}']);
     *
     * @param string $template_string 模板字符串
     * @param array &$data 变量列表
     * @param array $template_tags 模板标签，默认是 ['{','}']，可以指定标签，但如果是正则的特殊字符，只能是 [] {} ()
     * @return string
     *//*}}}*/
    public static function replaceTemplateVal(string $template_string, array &$data, array $template_tags=['[',']']){/*{{{*/
        # 将模板的变量进行替换，以便用于正则
        $t = array_map(function($v){
            return str_replace(['[','{','(',')','}',']'],['\[','\{','\(','\)','\}','\]'],$v);
        }, $template_tags);
        # 返回替换
        return preg_replace_callback("#".$t[0].'((?!'.$t[1].').+)'.$t[1]."#iU", function($match)use(&$data){
            # $match[0] 带 tags 的文本
            # $match[1] 不带 tags 的文本
            #echo "匹配到的魔板变量：",print_r($match,1);
            $match[1] = trim($match[1]);
            $return   = $match[0]; // 默认原样返回

            if($index = strpos($match[1],'|')){
                $path = trim(substr($match[1],0,$index));
                $func = trim(substr($match[1],$index+1));
            }else{
                $path = $match[1];
                $func = null;
            }

            if(strpos($path,'.')){ // 如果包含.号，需要查询下标
                $return = self::vpath($path, $data, $func?$path:$return);// 找不到则原路返回
            }else{
                $return = $data[$path] ?? ($func?$path:$return); // 如果没有则返回原始字符串
            }
            if($func) $return = self::funcall($func, $return);
            if(is_array($return)){
                Logger\Logger::error(['模板字符:',$match,'结果:',$return,'搜索的对象为：',$data]);
                throw new Exception("模板字符：【{$match[1]}】的替换结果应该是字符串！但是获得的是数组【".json_encode($return)."】。\n用于查找的数组为：".json_encode($data));
            }
            return $return;
        }, $template_string);
    }/*}}}*/

    /*{{{*//** 替换绑定的字段，ext：replaceBindFields(['a.b.c','b.d'], $data)
     * 参数替换规则如下：
     * [
     *   '参数字段' => '固定值',
     *   '参数字段' => [
     *       'bind'  => 'bind = 0 表示通知源, 否则为 activity_sources 的 id', 
     *       'value' => '用于绑定关系的vpath值，比如aaa.bbb.ccc.eee;可以用点号表示多维数组关系',
     *       'func'  => '转换函数，如果方法为类，那么必须为静态方法，比如 \namespace_a\class_b::func_c'
     *       'default'=>'如果绑定的值找不到的时候，使用此值作为默认值使用'
     *   ],
     *
     *   // 举例
     *   '参数字段' => [  # 绑定到 1 的 aaa.bbb.ccc.eee 字段上
     *       'bind'  => 1,
     *       'value' => 'aaa.bbb.ccc.eee',
     *   ],
     *   '参数字段' => [  # 绑定到 1 的 aaa.bbb.ccc.eee 字段上，并转换成 Unixtime 时间戳
     *       'bind'  => 1,
     *       'value' => 'aaa.bbb.ccc.eee',
     *       'func'  => 'strtotime'
     *   ],
     *   '参数字段' => [  # 绑定到 1 的 aaa.bbb.ccc.eee 字段上，并转换成天
     *       'bind'  => 1,
     *       'value' => 'aaa.bbb.ccc.eee',
     *       'func'  => 'strtotime|date=Y-m-d,_'
     *   ],
     *   '参数字段' => [  # 当前日期,无参数
     *       'func'  => 'date=Y-m-d'
     *   ],
     *   '参数字段' => [  # 将11111111转换成当前日期
     *       'value' => 11111111, 
     *       'func'  => 'date=Y-m-d,_'
     *   ],
     * ]
     *
     * 举例
     * $sources = [
     *     0 => [ # 0 表示通知源
     *         'user_id'     => 123,
     *         'user_name'   => 'vanni',
     *     ],
     *     2 => [
     *         'field_a' => 'aaa',
     *         'field_b' => 'bbb',
     *         'field_c' => [ 1,2,3 ],
     *         'field_d' => [ 'a' => 11, 'b' => 22 ]
     *     ]
     * ];
     * 
     * $a = [
     *     'fixed_value'      => 1111,
     *     'array_index'      => [
     *         'bind'         => 2,
     *         'value'        => 'field_c.0',
     *     ],
     *     'array_index_fun'  => [
     *         'bind'         => 2,
     *         'value'        => 'field_c.1',
     *         'func'         => 'pow=3'
     *     ],
     *     'object_attribute' => [
     *         'bind'         => 2,
     *         'value'        => 'field_d.b',
     *         'func'         => 'Utils::test'
     *     ],
     *     'no_bind'          => [
     *         'func'         => 'date=Y-m-d'
     *     ],
     *     'bind_0'           => [
     *         'bind'         => 0,
     *         'value'        => 'user_name',
     *         'func'         => 'strtoupper'
     *     ]
     * ];
     * 
     * $b = Utils::replaceBindFields($a, $sources);
     * print_r($sources);
     * print_r($b);
     *
     * @param array $data 包含替换的数组
     * @param &array $sources 其他数据源
     * @return array
     *//*}}}*/
    public static function replaceBindFields(array $data, array &$sources):array{/*{{{*/
        #echo "\n\n\n要批量替换:",print_r($data,1),",全局源为:",print_r($sources,),"\n\n\n";
        $out = [];
        uasort($data, function($a, $b){ // 让有 $this 的排在最后
            $a_has_this = (int)self::hasThisBind($a);
            $b_has_this = (int)self::hasThisBind($b);
            return ($a_has_this < $b_has_this) ? -1 : 1;
        });
        foreach($data as $field=>$value){
            if(is_array($value)){ // key: type, value, func
                $_value = $value['value'] ?? null;
                if(!empty($value['bind'])){
                    if(is_array($_value)){
                        #echo "\n需要替换的数组：", print_r($_value,1);
                        #echo "\n待查的数组：",print_r($sources,1);
                        $_value = array_map(function($i)use($out, &$sources){
                            if(strpos($i,'$this.')!==false) return self::parseBind(substr($i,6), $out, $value['default']??null, true);// ?: $i; # 去掉 $this.
                            return self::parseBind($i, $sources, $value['default']??null, true);// ?: $i; # 找到其他源中绑定的值
                        },$_value);
                        #echo "数组返回", print_r($_value,1),"\n\n\n";
                    }else {
                        if(strpos($value['value'],'$this.')!==false) $_value = self::parseBind(substr($value['value'],6), $out, $value['default']??null, true);// ?: $value['value']; # 去掉 $this.
                        $_value = self::parseBind($value['value'], $sources,$value['default']??null, true);// ?: $value['value']; # 找到其他源中绑定的值
                    }
                }
                # 执行过滤函数
                $value['func'] = array_key_exists('func', $value) ? trim($value['func']) : null;
                if(!empty($value['func'])) $_value = self::funcall($value['func'], $_value); 
                $out[$field] = $_value;
            }else{
                $out[$field] = $value;
            }
        }
        return $out;
    }/*}}}*/

    private static function hasThisBind($v):bool{
        if(is_array($v['value'])){
            foreach($v['value'] as $_v){
                if(strpos($_v,'$this.')===0) return true;
            }
            return false;
        }else{
            return strpos($v['value'],'$this.')===0;
        }
    }
    
    /*{{{*/ /** 函数调用，ext：funcall('funa|funb|func=2,_,3','value')
     * 和 ThinkPHP 的模板函数调用一致，下划线 _ 表示参数1的占位符，如果没有则固定放在第一个参数
     * Fun_A | Fun_B=参数_2,参数_3 | Fun_C | Fun_D=参数2,_,参数3
     *
     * $a = Utils::funcall('strtotime|strval|intval|date=Y-m-d,_|Utils::test', '2019-11-12');
     * var_dump($a);
     *
     * @param string $func 函数调用的描述字符串
     * @param mixed $value 调用函数时提供的值
     * @param mixed
     */ /*}}}*/
    public static function funcall(string $func, $value=null){/*{{{*/
        $funs = preg_split('/\s*\|\s*/', $func);
        foreach($funs as $index => $fun){
            $position = strpos($fun, '=');
            $func     = $fun;
            if(!$func) continue;
            # 参数初始化
            if($position){
                $func = trim(substr($fun, 0, $position));
                $params = preg_split('/\s*,\s*/', substr($fun,$position+1));
            }else $params = [];

            # 第一个参数如果没有传入值，不要传入空值
            if($index > 0 || ($index==0 && isset($value))){
                # 占位符
                $placeholder = array_search('_',$params);
                if($placeholder!==false){
                    $params[$placeholder] = $value;
                }else{
                    if(is_array($value)){
                        $params = $value;
                    }else{
                        array_unshift($params, $value);
                    }
                }
            }
            $value = call_user_func_array($func, $params);
        }
        return $value;
    }/*}}}*/

    /*{{{*//** 根据 vpath 来获取对象或数组的值，ext：vpath('aa.bb.*.cc', $data, $default)
     * 如果中间有 * 号，则返回数组，如果不存在返回默认值
     * $b = new stdclass;
     * $b->c = 1;
     * $a = [
     *       'aa'=> $b,
     *       'bb'=> ['cc'=>['dd'=>$b]]
     *      ];
     * $b->a = json_decode('[
     *   {"c":[{"d":1,"e":2,"f":3},{"d":1.1,"e":2}],"i":11},
     *   {"c":[{"d":2,"e":2,"f":3},{"d":2.1,"e":2}],"i":22},
     *   {"c":[{"d":3,"e":2,"f":3},{"e":2}],"i":33},
     *   {"c":[{"d":4,"e":2,"f":3},{"d":4.1,"e":2}],"i":44},
     *   {"i":44}
     * ]',1);
     * $c[] = Utils::vpath('bb.cc.dd.c', $a);                    // 指定数组下标，返回对应的值
     * $c[] = Utils::vpath('bb.cc.dd.not_exists', $a, 'mytext'); // 不存在时，返回默认值
     * $c[] = Utils::vpath('a.*.c.*.d',      $b);                // 两个 * 返回2维数组
     * $c[] = Utils::vpath('a.*.c.*.d',      $b, []);            // 两个 * 返回2维数组
     * $c[] = Utils::vpath('a.*.i',          $b);                // 一个 * 返回1维数组
     * $c[] = Utils::vpath('a.*.c.*.d',      $b, '我为空');      // 指定数组下标，返回对应的值，不存在时，返回默认值
     * $c[] = Utils::vpath('a.*.c.*.{d,e}',  $b, '我为空');      // 指定数组下标，返回对应的值，不存在时，返回默认值
     * $c[] = Utils::vpath('bb.cc.dd.ee?0',$a);                 // 指定下标，如果没有就用0默认值
     * $c[] = Utils::vpath('bb.cc.dd.ee?abc',$a);               // 指定下标，如果没有就用abc默认值
     * @param string $str 路径信息
     * @param array|object $variable 数组或者对象
     * @param mixed $default 默认值
     * @return mixed
     *//*}}}*/
    public static function vpath(string $path, &$variable, $default=null){/*{{{*/
        #echo "Find:$path in",print_r($variable,1),"\n\n";
        if(strpos($path,'?')){
            $parts = explode('?', $path);
            if(is_null($default)) $default = array_pop($parts);
            do{
                $part = array_shift($parts);
                $unid = uniqid();
                $val  = self::vpath(trim($part), $variable, $unid);
                $found = true;
                if(is_array($val)){
                    array_walk_recursive($val, function($v)use($unid, &$found){if($v==$unid)$found = false;});
                }else{
                    $found = $unid !== $val;
                }
                if($found) return $val;
            }while($parts);
            return $default;
        }
        
        if(strpos($path, '.')){

            # 如果有 * 号，表示要重建数据，不能用引用
            if(strpos($path,'*') !== false){ 
                $refval = is_object($variable) ? clone $variable : $variable;
            }else $refval = &$variable;

            $params = explode('.',$path);
            do{
                $item = array_shift($params);
                if(is_array($refval)){
                    if(!isset($refval[$item])){
                        if($item == '*'){ # 如果为 * 号，那么就返回数组，而且需要重建数组 temp_out
                            $temp_out = [];
                            foreach($refval as $_){
                                $next_key = implode('.',$params);
                                if($next_key){
                                    if($next_key{0} == '{' && substr($next_key,-1)=='}'){ // 是 a.*.{user_id, key:a.b.user_name} 这种格式
                                        $tmps = [];
                                        foreach(explode(',', substr($next_key,1,-1)) as $key){
                                            $keys = explode(':',$key);
                                            $keys[1] = $keys[1] ?? $keys[0];
                                            $tmps[trim($keys[0])] = self::vpath(trim($keys[1]), $_, $default);
                                        }
                                        $temp_out[] = $tmps;
                                    }else{
                                        $temp_out[] = self::vpath($next_key, $_, $default);
                                    }
                                }else $temp_out[] = $_;
                            }
                            return $temp_out; # 重建后直接返回，跳出当前循环
                        }else return $default;
                    }
                    $refval = &$refval[$item];
                }else{
                    if(!isset($refval->$item)) return $default;
                    $refval = &$refval->$item;
                }
            }while($params);
            return $refval;
        }else{
            if(is_array($variable)){
                return $variable[$path] ?? $default;
            }else{
                return $variable->$path ?? $default;
            }
        } 
    }/*}}}*/

    /*{{{*//** 使用 vpath 初始化数组，ext：path2arr('a.b.*.c','default_value',$out_arr); var_dump($out_arr);
     *
     * 举例：
     * $a = $b = [];
     * Utils::path2arr('a.b.c', 1, $a);
     * Utils::path2arr('b.*.c', 1, $a);
     * Utils::path2arr('c.*', 1, $a);
     * Utils::path2arr('d.*.e.*', 1, $a);
     * Utils::path2arr('d.*.k', 1, $a);
     * Utils::path2arr('i.j.k.*.m.k.*.k', 1, $a);
     * Utils::path2arr('*.j.k.*.m.k.*.k', 1, $b);
     * print_r($a);
     * print_r($b);
     *
     * @param string $path vpath
     * @param mixed $default 默认值
     * @param &array $arr 需要设置的数组
     *//*}}}*/
    public static function path2arr(string $path, $default, array &$arr){/*{{{*/
        $ref = &$arr;
        foreach(explode('.',$path) as $p) $ref = &$ref[$p=='*' ? 0 : $p];
        $ref = $default;
    }/*}}}*/

    /*{{{*//** 使用 SMPT 发送邮件，ext：sendMail('标题','内容')
     * 可以配置的选项 $options 有：
     *     debug       : bool         , 默认：false                    , 是否开启调试
     *     tls         : bool         , 默认：false                    , 是否加密传输
     *     isHTML      : bool         , 默认：true                     , 是否为HTML格式
     *     host        : string       , 默认：localhost                , SMTP 主机地址
     *     user        : string       , 默认：root                     , SMTP 验证的用户名
     *     password    : string       , 默认：空                       , SMTP 验证的秘密
     *     port        : int          , 默认：25                       , SMTP服务器端口
     *     from        : string|array , 默认：[system=>root@localhost] , 发送人，数组格式：[name=>address]
     *     to          : string|array , 默认：[system=>root@localhost] , 接受者地址，数组格式：[name=>address]
     *     reply       : string|array , 默认：[system=>root@localhost] , 回复地址，数组格式：[name=>address]
     *     cc          : string|array , 默认：空                       , 抄送地址，数组格式：[address1, name2=>address2, ...]
     *     bcc         : string|array , 默认：空                       , 暗抄地址，数组格式：[address1, name2=>address2, ...]
     *     attachments : string|array , 默认：空                       , 附件，数组格式：[file1, name2=>file2, ...]
     *     timegap     : int          , 默认：1800                     , 频率限制：相同 $options 的邮件发送间隔时间，默认半小时
     *
     * @param string $subject 邮件主题
     * @param string $body 邮件内容
     * @param array $options 邮件选项
     *//*}}}*/
    public static function sendMail(string $subject, string $body, array $options=[]):bool{/*{{{*/
        # 缓存发送频率
        $cache_time = $options['timegap'] ?? 30*60; // 相同配置的缓存时间, 半小时一次
        if($cache_time>0){
            $cache_key = md5(json_encode($options));
            if(self::cache($cache_key)) return true;
            self::cache($cache_key, 1, $cache_time); // 不管发送是否成功，都缓存
        }

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);                   // Passing `true` enables exceptions
        try {
            $mail->isSMTP();                                               // Set mailer to use SMTP
                                                                           // Server settings
            if(!empty($options['debug'])) $mail->SMTPDebug  = 2;           // Enable verbose debug output
            if(!empty($options['user']))  $mail->SMTPAuth   = true;        // Enable SMTP authentication
            if(!empty($options['tls']))   $mail->SMTPSecure = 'tls';       // Enable TLS encryption, `ssl` also accepted

            $mail->Host     = $options['host']     ?? 'localhost';         // Specify main and backup SMTP servers
            $mail->Username = $options['user']     ?? 'root';              // SMTP username
            $mail->Password = $options['password'] ?? '';                  // SMTP password
            $mail->Port     = $options['port']     ?? 25;                  // TCP port to connect to

            //Set from
            if(empty($options['from']))         $options['from'] = ['system'=>'root@localhost'];
            elseif(!is_array($options['from'])) $options['from'] = [$options['from']=>$options['from']];
            $mail->setFrom(current($options['from']), key($options['from']));

            //Set Reply
            if(!empty($options['reply'])){
                list($addr, $name) = is_array($options['reply']) ? $options['reply'] : [$options['reply'],$options['reply']];
                $mail->addReplyTo($addr, $name);
            }else{
                $mail->addReplyTo(current($options['from']), key($options['from']));
            }

            //Recipients
            $recipients = $options['to'] ?? [$options['from'][1]=>$options['from'][0]];
            $recipients = is_array($recipients) ? $recipients : [$recipients];
            foreach($recipients as $n=>$receiver){
                if(is_int($n)) $mail->addAddress($receiver);  // Add a recipient
                else $mail->addAddress($receiver, $n);        // Name is optional
            }

            //CCs
            if(!empty($options['cc'])){
                $options['cc'] = is_array($options['cc']) ? $options['cc'] : [$options['cc']];
                foreach($options['cc'] as $n=>$cc){
                    if(is_int($n)) $mail->addCC($cc);  // Add a recipient
                    else $mail->addCC($cc, $n);        // Name is optional
                }
            }

            //BCCs
            if(!empty($options['bcc'])){
                $options['bcc'] = is_array($options['bcc']) ? $options['bcc'] : [$options['bcc']];
                foreach($options['bcc'] as $n=>$bcc){
                    if(is_int($n)) $mail->addBCC($bcc);  // Add a recipient
                    else $mail->addBCC($bcc, $n);        // Name is optional
                }
            }

            //Attachments
            if(!empty($options['attachments'])){
                $options['attachments'] = is_array($options['attachments']) ? $options['attachments'] : [$options['attachments']];
                foreach($options['attachments'] as $n=>$a){
                    if(is_int($n)) $mail->addAttachment($a);// Add attachments
                    else $mail->addAttachment($a, $n);      // Optional name
                }
            }

            $html = $options['isHTML'] ?? true;
            if($html) $mail->isHTML(true);// Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->CharSet = "utf-8";
            $mail->send();
        } catch (Exception $e) {
            # 邮件是最好的屏障，如果邮件也发不出，那也没必要再抛异常了
            return false;
        }
        return true;
    }/*}}}*/

    /*{{{*//** 设置缓存内容 或 读取缓存内容，ext：cache('key','value')
     * 一个参数为获取，多个参数为设置
     * @param string $key 缓存的Key
     * @param mixed $val 需要被缓存的值
     * @param int $expire 缓存时间，如果为0表示不超时
     * @return mixed
     *//*}}}*/
    static function cache(string $key, $val=null, int $expire=0){/*{{{*/
        $front = new Phalcon\Cache\Frontend\Data(["lifetime" => 1 * 24 * 60 * 60]); // 1天
        $redis = PA::$config['redis'] ?: false;
        if($redis){
            $cache = new Phalcon\Cache\Backend\Redis($front,$redis->toArray());
        }else{
            $dir   = realpath(POWER_DATA).'/cache/';
            if(!file_exists($dir)) mkdir($dir,'0777');
            $cache = new Phalcon\Cache\Backend\File($front,['cacheDir'=>$dir]);
        }
        return func_num_args()===1 ? $cache->get($key) : $cache->save($key, $val, $expire);
    }
/*}}}*/

    /*{{{*//** 解密
     * @param string $str 需要解密的原始字符串
     * @parma string $password 解密密码
     * @param array $options 选项：vi:默认=密码的md5值小写(长度符合method的要求), method:默认=AES-128-CBC, options:默认=0, gzip:默认=0 等
     * @return string
     *//*}}}*/
    public static function decrypt(string $str, string $password, array $options=[]):string{/*{{{*/
        $iv_len  = openssl_cipher_iv_length($options['method'] ?? 'AES-128-CBC');
        $options = array_merge([
            'iv'      => substr(md5($password),0, $iv_len),
            'method'  => 'AES-128-CBC',
            'options' => 0,
            'gzip'    => 0,
        ],$options);

        if(OPENSSL_ZERO_PADDING == ($options['options'] & OPENSSL_ZERO_PADDING)){
            if(OPENSSL_RAW_DATA != ($options['options'] & OPENSSL_RAW_DATA)) $options['options'] |= OPENSSL_RAW_DATA;
            $zero_padding = true;
        }
        $bin = openssl_decrypt($str, $options['method'], $password, $options['options'], $options['iv']);
        #echo "解密：",bin2hex($bin),"\n";
        if(isset($zero_padding)){
            $bin = rtrim($bin,"\0");
            #echo "去零：",bin2hex($bin),"\n";
        }
        if($options['gzip']){
            $bin = gzuncompress($bin);
            #echo "解压:", bin2hex($bin),"\n";
        }
        return $bin;
    }/*}}}*/

    /*{{{*//** 加密
     * @param string $str 需要加密的原始字符串
     * @parma string $password 加密密码
     * @param array $options 选项：vi:默认=密码的md5值小写(长度符合method的要求), method:默认=AES-128-CBC, options:默认=0, gzip:默认=0 等
     * @return string
     *//*}}}*/
    public static function encrypt(string $str, string $password, array $options=[]):string{/*{{{*/
        $iv_len  = openssl_cipher_iv_length($options['method'] ?? 'AES-128-CBC');
        $options = array_merge([
            'iv'      => substr(md5($password),0, $iv_len),
            'method'  => 'AES-128-CBC',
            'options' => 0,
            'gzip'    => 0,
        ],$options);

        if($options['gzip']){
            $str = gzcompress($str);
            #echo "压缩:",bin2hex($str),"\n";
        }
        if(OPENSSL_ZERO_PADDING == ($options['options'] & OPENSSL_ZERO_PADDING)){
            if(OPENSSL_RAW_DATA != ($options['options'] & OPENSSL_RAW_DATA)) $options['options'] |= OPENSSL_RAW_DATA;
            $str = self::data_padding('zero',$str, $iv_len);
            #echo "填零:",bin2hex($str),"\n";
        }
        $bin = openssl_encrypt($str, $options['method'], $password, $options['options'], $options['iv']);
        #echo "加密:",bin2hex($bin),"\n";
        return $bin;
    }/*}}}*/

    /*{{{*//** 私有方法，填充数据，以方便加解密
     * 字符串填充，用于加解密
     *//*}}}*/
    static private function data_padding($padding_type, $data, $block_size){/*{{{*/
        $mod = strlen($data) % $block_size;
        if($mod==0 && $padding_type!='pkcs7') return $data; // is soigne.

        $pad_num = $block_size - $mod;
        switch($padding_type){
        case 'pkcs5':
        case 'pkcs7':
        case 'pkcs':
            $pad_num = $mod ? $pad_num : $block_size; # 当 pkcs7 时，数据块虽然整齐，但依然要填充一个最大值
            $pad_str = chr($pad_num);
            break;
        case 'ssl':
        case 'ssl3':
            $pad_str = chr($pad_num-1);
            break;
        case 'zero':
            $pad_str = "\0";
            break;
        }
        return $data . str_repeat($pad_str, $pad_num);
    }/*}}}*/

    /*{{{*//** 根据格式，打包数组成二进制数据，
     * ext: 格式：[字段=>字节数],字节可以小于1，但只可以分出8段。字节数如果大于8，则表示字符串集合，小于8都会转换成数字
     * $f = ['m.a'=>1/8, 'm.b'=>3/8, 'm.c'=>2/8, 'k'=>1,   'd'=>2/8, 'j'=>10,    'l'=>3,   'n'=>'Q'];
     * $d = ['m.a'=>1,   'm.b'=>0,   'm.c'=>3,   'k'=>222, 'd'=>2,   'j'=>'abc', 'l'=>888, 'n'=>92342342342345];
     * $b = Utils::pack($f, $d);
     * $c = bin2hex($b); 
     *
     * ['field'=>'number[:type]']
     * [
     *  'a' => 1,    // 1字节
     *  'b' => 1/8,  // 1位 0b00000001, << 0
     *  'c' => 3/8,  // 3位 0b00001110, << 1
     *  'd' => 2/8,  // 2位 0b00110000  << 4
     *  'e' => 2/8,  // 2位 0b11000000  << 6
     *  'f' => 4,    // 4个字节，一般整型
     *  'i' => 8,    // 8个字节，大整型
     *  'j' => 255   // 255个字节, 255c
     *
     *  'k' => '4c', // 4字节,使用标准的 php.pack
     *  'h' => '4s', //
     * ]
     * 
     * @param array $format 格式：[字段=>字节数/或pack中的格式字符]
     * @param array $data 原始数据，需要和前面的字段对应上
     * @return string
     *//*}}}*/
    public static function pack(array $formats, array $data):string{/*{{{*/
        $pack_param = [''];
        $str_meger2arr = function($str, $len, &$arr){
            $value = str_pad($str,$len,"\x00", STR_PAD_LEFT); # 在字符串前面填充空字符串0x00
            foreach(preg_split("//",$value,-1,PREG_SPLIT_NO_EMPTY) as $c) $arr[] = ord($c);
        };
        $parts = ['0.125'=>1,'0.25'=>2,'0.375'=>3,'0.5'=>4,'0.625'=>5,'0.75'=>6,'0.875'=>7];
        $bit_start  = $bit_value  = 0;
        foreach($formats as $field=>$format){
            if(is_numeric($format) && $format < 1){ # 位运算
                $bits  = $parts[(string)$format]; # 占多少位
                # 值处理
                $value = $data[$field];
                $value &= (2**$bits-1);        // 去掉高位
                $value = $value << $bit_start; // 移动到指定的位置
                
                $bit_start += $bits;           // 下个位开始位置
            }else{
                if($bit_start){ # 结束位操作
                    $pack_param[0] .= 'C';
                    $pack_param[]   = $bit_value;
                    $bit_start = $bit_value = 0;
                }

                # 如果格式是字符串，那么直接使用php的pack格式
                if(is_string($format)){
                    $pack_param[0] .= $format;
                    $pack_param[]   = $data[$field];
                    continue;
                }

                # 如果大于8个字节，超出64为系统最大整数，将转换成对应的字节
                if($format>8){
                    $pack_param[0] .= $format.'C';
                    $str_meger2arr($data[$field], $format, $pack_param);
                    continue;
                }

                # 1 ~ 8 个字节的处理
                if(is_string($data[$field])){
                    $pack_param[0] .= str_repeat('C',$format);
                    $str_meger2arr($data[$field], $format, $pack_param);
                    continue;
                }

                # 1 ~ 8 个字节的处理
                $php_pack_format = [1=>'C',2=>'S',3=>'CCC',4=>'L',5=>'CCCCC',6=>'CCCCCC',7=>'CCCCCCC',8=>'Q'];
                $pack_param[0] .= $php_pack_format[$format];
                if(in_array($format, [3,5,6,7])){
                    $hex = str_pad(dechex($data[$field]), $format*2, '0', STR_PAD_LEFT);
                    $bin = hex2bin($hex);
                    foreach(preg_split("//",$bin,-1,PREG_SPLIT_NO_EMPTY) as $c) $pack_param[] = ord($c);
                }else{
                    $pack_param[]   = $data[$field];
                }
            }
        }
        if($bit_start){ # 结束位操作
            $pack_param[0] .= 'C';
            $pack_param[]   = $bit_value;
        }
        #print_r($pack_param);
        return call_user_func_array('pack',$pack_param);
    }/*}}}*/

    /*{{{*//** 根据格式，解包二进制数据
     *
     * $f = ['m.a'=>1/8, 'm.b'=>3/8, 'm.c'=>2/8, 'k'=>1,   'd'=>2/8, 'j'=>10,    'l'=>3,   'n'=>'Q'];
     * $d = ['m.a'=>1,   'm.b'=>0,   'm.c'=>3,   'k'=>222, 'd'=>2,   'j'=>'abc', 'l'=>888, 'n'=>92342342342345];
     * $b = Utils::pack($f, $d);
     * $c = bin2hex($b); 
     * var_dump($c);
     * $e = Utils::unpack($f, $b);
     * print_r($e);
     *//*}}}*/
    public static function unpack(array $formats, string $str):array{/*{{{*/
        $result = [];
        $unpack_format = '';
        $bit_started   = false;
        $bit_fields    = [];
        $bit_last_key  = '';
        foreach($formats as $field => $format){
            if(is_numeric($format) && $format < 1){
                if(!$bit_started){
                    $bit_last_key = uniqid();
                    $unpack_format .= 'C+'.$bit_last_key.'/';   # 需要扩展成多个 +
                    $bit_started = true;
                }
                $bit_fields[$bit_last_key][] = $field;
            }else{
                $bit_started = false;
                if(is_string($format)){
                    $unpack_format .= $format.$field.'/';
                    continue;
                }

                if($format>8){ # 需要拼接
                    $unpack_format .= 'C'.$format.'-'.$field.'-/'; # 需要合并成一个 -
                    continue;
                }

                $php_pack_format = [1=>'C',2=>'S',3=>'C3',4=>'L',5=>'C5',6=>'C6',7=>'C7',8=>'Q'];
                if(in_array($format, [3,5,6,7])){ # 需要拼接
                    $unpack_format .= $php_pack_format[$format].'-'.$field.'-/';
                }else{
                    $unpack_format .= $php_pack_format[$format]. $field.'/';
                }
            }
        }
        #var_dump($unpack_format);
        #print_r($bit_fields);

        # 解包
        $values = unpack(substr($unpack_format,0,-1), $str);

        # 合并/拆分成对应的数组
        $result = []; // 最终的数组结果
        $parts = ['0.125'=>1,'0.25'=>2,'0.375'=>3,'0.5'=>4,'0.625'=>5,'0.75'=>6,'0.875'=>7];
        $pending = []; // 后期需要处理的项目, 字段=>长度
        foreach($values as $field=>$value){
            $type  = $field{0};
            if($type == '+'){ #位扩展
                $bit_start = 0;
                foreach($bit_fields[substr($field,1)] as $_f){
                    $bits = $parts[(string)$formats[$_f]];
                    $mark = (2**$bits-1) << $bit_start;            // 去掉高位
                    $result[$_f] = ($value & $mark) >> $bit_start; // 去掉低位
                    #echo "蒙版字符串:".decbin($mark).",去掉高低后的值是:". decbin($result[$_f])."\n";
                    $bit_start += $bits;                // 下个位开始位置
                } 
            }elseif($type == '-'){
                $field = explode('-',$field)[1];
                if(!isset($result[$field])){
                    $result[$field]  = '';
                    $pending[$field] = $formats[$field];
                }
                # 小于8个字节的都是整数，否则按照字符串处理
                $result[$field] .= $formats[$field]>8 ? chr($value) : dechex($value);
            }else{
                $result[$field] = $value;
            }
        }
        # 对字符串的数据进行处理
        foreach($pending as $field=>$size) $result[$field] = $size>8 ? ltrim($result[$field],"\x00") : hexdec($result[$field]);
        return $result;
    }/*}}}*/
}
