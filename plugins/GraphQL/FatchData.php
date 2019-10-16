<?php
namespace plugins\GraphQL;
use ArrayAccess;
use GraphQL\Executor\Executor;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\InputObjectType;
use Power\Models\Logs;
use PowerModelBase;

class FatchData{
    private static $data;
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
    
        echo "\n++++++【 $field 】+++++\n";
        echo "到这里来的都是子查询，比如 Users.Logs \n";
        echo '参数：',print_r($args,1);
        echo '来源：',print_r($source->toArray(),1),get_class($source),"\n";
        echo '期望返回：', $info->returnType, "\n";
        echo "+++++++++++\n\n\n";
    
        $params = [];
        if($type instanceof ListOfType){
            if(!($type->ofType instanceof ModelType)) throw new \Exception('未实现此类型');
            $model = call_user_func([$type->ofType->model,'getInstance']);
            $func = 'find';
            $params['offset'] = $args['filter']['limit'][0];
            $params['limit']  = $args['filter']['limit'][1];
        }else{
            if(!($type instanceof ModelType)) throw new \Exception('未实现此类型');
            $model = call_user_func([$type->model,'getInstance']);
            $func = 'findFirst';
        }

        self::parseWhere(call_user_func([$model,'getInstance']), $args['filter'], $params);
//        echo "查询条件：",print_r($params,1);
        
        return call_user_func([$model, $func], $params);
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
        
        $class = '\\Power\\Models\\'.ucfirst($info->fieldName);
        $model = call_user_func([$class,'find'],['limit'=>10]);
        return $model;//->toArray();
    }
    
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
    
    private static function setParamsAndReturnWhere(array $conditions, array &$params, array &$fields): string
    {
        if(!array_key_exists($conditions['key'], $fields)) throw new \Exception('Field:['.$conditions['key'].'] not exists!');
        $bind_index = count($params['bind']);
        $params['bind'][$bind_index] = $conditions['val'];
        $params['bindTypes'][$bind_index] = $fields[$conditions['key']];
        return $conditions['key'] . ' ' . ($conditions['op']??'=') . ' ?'.$bind_index;
    }
}
