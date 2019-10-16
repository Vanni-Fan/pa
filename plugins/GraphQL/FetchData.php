<?php
namespace plugins\GraphQL;
use ArrayAccess;
use GraphQL\Executor\Executor;
use GraphQL\Type\Definition\ListOfType;
use PowerModelBase;

class FetchData{
    public static function columns($source, $args, $context, \GraphQL\Type\Definition\ResolveInfo $info){ # context 用不上
        $field = $info->fieldName;
        $type  = $info->returnType;
        if(is_array($source) || $source instanceof ArrayAccess){
            if(array_key_exists($field, $source)){
                return $source[$field]??null;
            }
        }
        if(is_object($source)){
            $tmp = get_object_vars($source);
            if(array_key_exists($field, $tmp)){
                return $source->$field ?? null;
            }
        }
    
//        echo "\n++++++【 $field 】+++++\n";
//        echo "到这里来的都是子查询，比如 Users.Logs \n";
//        echo '参数：',print_r($args,1);
//        echo '来源：',print_r($source->toArray(),1),get_class($source),"\n";
//        echo '期望返回：', $info->returnType, "\n";
//        echo "+++++++++++\n\n\n";
    
        $params = [];
        if($type instanceof ListOfType){
//            echo "查多条\n";
            if(!($type->ofType instanceof ModelType)) throw new \Exception('未实现此类型');
            $model = call_user_func([$type->ofType->model,'getInstance']);
            $func = 'find';
            $params['offset'] = $args['filter']['limit'][0];
            $params['limit']  = $args['filter']['limit'][1];

            self::setRelateCondition($model, $source, $params);
            self::parseWhere(call_user_func([$model,'getInstance']), $args['filter'], $params);
//            echo "查询条件：",var_export($params,1);
        }else{
//            echo "查一条\n";
            if(!($type instanceof ModelType)) throw new \Exception('未实现此类型');
            $model = call_user_func([$type->model,'getInstance']);
            $func = 'findFirst';
        }

        $rs = call_user_func([$model, $func], $params);
        if($func == 'findFirst' && !$rs){ # 如果只有查一行的，就需要填充空
            $rs = array_fill_keys(array_keys($model->fields()), null);
        }

        return $rs;
    }

    /**
     * 根据参数，从数据库的表中查询多条记录，返回
     * 如果有子集，在此方法里面需要设置为 null 然后在字段数据中设置数据，才可以使用子集的参数
     * @param                                      $source
     * @param                                      $args
     * @param                                      $context
     * @param \GraphQL\Type\Definition\ResolveInfo $info
     * @return array|mixed|string|null
     * @throws \Exception
     */
    public static function rows($source, $args, $context, \GraphQL\Type\Definition\ResolveInfo $info){
        $rs = Executor::defaultFieldResolver($source, $args, $context, $info);
        if($rs !== null) return $rs;

//        echo '期望返回：', $info->returnType->ofType, "\n";
        $model = call_user_func([$info->returnType->ofType->model,'getInstance']);
        $params['offset'] = $args['filter']['limit'][0] ?? 0;
        $params['limit']  = $args['filter']['limit'][1] ?? 10;

        self::parseWhere($model, $args['filter'], $params);
        return call_user_func([$model,'find'],$params);
    }

    public static function setRelateCondition(PowerModelBase $current_model, PowerModelBase $reference_model, array &$params){
        if(isset($params['conditions'])){
            $where_str = &$params['conditions'];
        }else{
            $params[0] = $params[0]??'';
            $where_str = &$params[0];
        }
        if(!isset($params['bind'])) $params['bind'] = [];

//        $reference_fields = $reference_model->fields();
        $relate_params    = [];
        $current_class    = get_class($current_model);
//        $reference_class  = get_class($reference_model);

        foreach(self::getHasRelate($reference_model, $current_class) as $reference_field=>$current_field){
//            echo "$current_class.$current_field = $reference_class.$reference_field = ". $reference_model->{$reference_field} ."\n";
            $relate_params[]       = $current_field .'=?'.count($params['bind']);
            $params['bind'][]      = $reference_model->{$reference_field};
//            $params['bindTypes'][] = $reference_fields[$reference_field];
        }
        if($relate_params) $where_str = '(' . implode(' AND ', $relate_params) . ')';
    }

    private static function getHasRelate(PowerModelBase $model, string $class_name){
        $referer = $model->getModelsManager()->getHasOneAndHasMany($model);
        $return = [];
        foreach($referer as $ref){
            if($ref->getReferencedModel() !== $class_name) continue;

            $keys = $ref->getFields();
            if(!is_array($keys)) $keys = [$keys];

            $values = $ref->getReferencedFields();
            if(!is_array($values)) $values = [$values];

            $return = array_merge($return, array_combine($keys, $values));
        }
//        echo "查询".get_class($model).'的外键定义'.print_r($return,1)."\n\n\n";
        return $return;
    }

    /**
     * 根据对象模型，设置查询差数，并返回预编译的SQL部分
     * @param PowerModelBase $model 对象模型
     * @param array $conditions 嵌套的查询条件
     * @param array &$params 分析的填充参数用的查询数组
     * @param string $op 首层的条件关系
     * @throws \Exception
     */
    public static function parseWhere(PowerModelBase $model, array $conditions, array &$params, $op='AND'){
        if(!isset($conditions['where']) && !isset($conditions['key'], $conditions['val'])) return;
        if(isset($params['conditions'])){
            $where_str = &$params['conditions'];
        }else{
            $params[0] = $params[0]??'';
            $where_str = &$params[0];
        }
        
        if(isset($conditions['key'], $conditions['val'])){
            $sub_where = ['key'=>$conditions['key'], 'op'=>$conditions['op']??'=', 'val'=>$conditions['val']];
            if(!empty($conditions['where']) && is_array($conditions['where'])){
                $conditions['where'] = ['op'=>$op, 'sub'=>[$sub_where,$conditions['where']]];
            }else{
                $conditions['where'] = $sub_where;
            }
        }
        if($conditions['where']) $params['bind'] = $params['bind'] ?? [];
//        print_r($conditions['where']);
//        return;
        $sub_where_str = self::getParseWhereSql($model, $conditions['where'],$params);
        $where_str = $where_str ? "$where_str $op $sub_where_str" : $sub_where_str;
    }

    /**
     * 根据对象模型，设置查询差数，并返回预编译的SQL部分
     * @param PowerModelBase $model
     * @param array $conditions
     * @param array $params
     * @return string
     * @throws \Exception
     */
    private static function getParseWhereSql(PowerModelBase $model, array $conditions, array &$params): string
    {
        $fields = $model->fields();
        if(!empty($conditions['sub']) && is_array($conditions['sub'])){
            return '('.implode(' '.$conditions['op'].' ', array_map(function($v) use($model, &$params, &$fields){
                if(!empty($v['sub']) && is_array($v['sub'])) return self::getParseWhereSql($model, $v, $params);
                return self::setParamsAndReturnWhere($v, $params, $fields);
            },$conditions['sub'])).')';
        }
        return self::setParamsAndReturnWhere($conditions, $params, $fields);
    }

    /**
     * 设置单个参数，并返回预编译的SQL部分
     * @param array $conditions
     * @param array $params
     * @param array $fields
     * @return string
     * @throws \Exception
     */
    private static function setParamsAndReturnWhere(array $conditions, array &$params, array &$fields): string
    {
        if(!array_key_exists($conditions['key'], $fields)) throw new \Exception('Field:['.$conditions['key'].'] not exists!');
        $bind_index = count($params['bind']);
        $params['bind'][$bind_index] = $conditions['val'];
//        $params['bindTypes'][$bind_index] = $fields[$conditions['key']];
        return $conditions['key'] . ' ' . ($conditions['op']??'=') . ' ?'.$bind_index;
    }
}
