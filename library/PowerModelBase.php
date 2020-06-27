<?php
use Phalcon\Mvc\Model;
/**
 * 基础模型
 * @author vanni.fan
 */
abstract class PowerModelBase extends Model{
    private static $instances = [];
    private static $describe = [];
    private static $fields = [];
    public function initialize(){
        PA::$di->set('db',PA::$db);
        $this->setDi(PA::$di);
        $this->setSource(PA::$config->path('pa_db.prefix').$this->getSource());
    }
    
    /**
     * 获得字段信息，1维数组
     * @return array
     */
    public static function fields():array{
        if(empty(self::$fields[static::class])) {
            $obj = self::getInstance();
            self::$fields[static::class] = $obj->getModelsMetaData()->getDataTypes($obj);
        }
        return self::$fields[static::class];
    }
    
    /**
     * 获得字段详情，2维数组
     */
    public static function describe():array{
        if(empty(self::$describe[static::class])) {
            $obj = self::getInstance();
            self::$describe[static::class] = $obj->getReadConnection()->describeColumns($obj->getSource());
        }
        return self::$describe[static::class];
    }
    
    /**
     * 获得模型的单例对象，如果需要使用 $model->save() 的时候，请谨慎使用单例
     * @param mixed ...$params
     * @return mixed|PowerModelBase
     */
    public static function getInstance(...$params){
        $key = md5(json_encode($params).static::class);
        return self::$instances[$key] ?? (self::$instances[$key] = new static(...$params));
    }
    
    /**
     * 根据对象模型，设置查询差数，并返回预编译的SQL部分
     * @param array  $conditions 嵌套的查询条件
     * @param array &$params     分析的填充参数用的查询数组
     * @param string $op         首层的条件关系
     * @param bool   $checkFields 是否检查字段，默认为检查即条件中的字段必须是当前模型的
     * @return array
     * @throws Exception
     *
     * @example 构建常规的数据结构查询
     *      parseWhere(['where' => ['key' => 'val']]);      // where key = val;
     *
     * @example 构建表格组件的数据结构查询
     *      parenWhere(['where' => $_POST['filters']]);     // where $filter
     *
     * @example 构建混合模式的数据结构查询
     *      $_POST['filters']['key'] = 'val';
     *      parenWhere(['where' => $_POST['filters']]);     // where key = val and $filter
     *
     * @example 常规数据结构添加特殊检索条件
     *      $_POST['filters']['key|>'] = 'val';
     *      parenWhere(['where' => $_POST['filters']]);     // where key > val and $filter
     */
    public static function parseWhere(array $conditions, ?array &$params = [], $op='AND', bool $checkFields=true):array {
//        print_r($conditions);
//        if(!isset($conditions['where']) && !isset($conditions['key'], $conditions['val'])) return;
        if(empty($conditions['where']) && empty($conditions['key']) && empty($conditions['val']))
            return $params;

        empty($params['conditions']) && $params['conditions'] = $params[0] ?? '';
        $where_str = $params['conditions'];
        
//        if(isset($conditions['key'], $conditions['val'])){
//            $sub_where = ['key'=>$conditions['key'], 'op'=>$conditions['op']??'=', 'val'=>$conditions['val']];
//            if(!empty($conditions['where']) && is_array($conditions['where'])){
//                $conditions['where'] = ['op'=>$op, 'sub'=>[$sub_where,$conditions['where']]];
//            }else{
//                $conditions['where'] = $sub_where;
//            }
//        }

        $conditions = self::parseMuiltWhere($conditions, $op);
        if($conditions['where']) $params['bind'] = $params['bind'] ?? [];
        $sub_where_str = self::getParseWhereSql($conditions['where'],$params, $checkFields);
        $where_str = $where_str ? "$where_str $op $sub_where_str" : $sub_where_str;
        $params['conditions'] = $where_str;
        return $params;
    }

    /**
     * 构建多种条件查询数据
     * @param $conditions
     * @param string $op
     * @return array|array[]
     *
     * @author 刘文岳
     * @date 2020-06-15 11:23
     */
    protected static function parseMuiltWhere($conditions, $op='AND'): array
    {
        if (isset($conditions['key'], $conditions['val'])) {
            $new_conditions = ['key' => $conditions['key'], 'op' => $conditions['op']??'=', 'val' => $conditions['val']];
            if (isset($conditions['where']) && is_array($conditions['where'])) {
                $conditions['where'] = ['op' => $op, 'sub' => [$new_conditions, $conditions['where']]];
            } else {
                $conditions['where'] = $new_conditions;
            }
            unset($conditions['key'], $conditions['val']);
        }
        $subWhere = [];
        foreach ($conditions['where'] as $key => $val) {
            if (in_array($key, ['op', 'sub'])) {
                continue;
            }
            list($realKey, $realOp) = explode('|', $key . '|=');
            $subWhere[] = ['key' => $realKey, 'op' => $realOp, 'val' => $val];
            unset($conditions['where'][$key]);
        }
        if (isset($conditions['where'], $conditions['where']['sub'])) {
            $conditions['where']['sub'] = array_merge($conditions['where']['sub'], $subWhere);
        } else {
            if (isset($conditions['where']) && $conditions['where']) {
                $subWhere[] = $conditions['where'];
            }
            $conditions['where'] = ['op' => $op, 'sub' => $subWhere];
        }
        return $conditions;
    }
    
    /**
     * 根据对象模型，设置查询差数，并返回预编译的SQL部分
     * @param array $conditions
     * @param array $params
     * @param bool  $checkFields
     * @return string
     * @throws Exception
     */
    private static function getParseWhereSql(array $conditions, array &$params, bool $checkFields): string
    {
        if($checkFields) $fields = static::fields();
        else $fields = [];
        
        if(!empty($conditions['sub']) && is_array($conditions['sub'])){
            return '('.implode(' '.$conditions['op'].' ', array_map(function($v) use(&$params, &$fields, $checkFields){
                    if(!empty($v['sub']) && is_array($v['sub'])) return self::getParseWhereSql($v, $params, $checkFields);
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
     * @throws Exception
     */
    private static function setParamsAndReturnWhere(array $conditions, array &$params, array &$fields): string
    {
        if($fields && !array_key_exists($conditions['key'], $fields)) throw new \Exception('Field:['.$conditions['key'].'] not exists!');
        $bind_index = 'bind_' . count($params['bind']);
//        $params['bindTypes'][$bind_index] = $fields[$conditions['key']];
        $conditions['op'] = strtoupper($conditions['op']);
        if ($conditions['op'] === '%' || $conditions['op'] === 'LIKE') {
            $params['bind'][$bind_index] = '%'.$conditions['val'].'%';
            return $conditions['key'] . ' like ' . ':' . $bind_index . ':';
        } elseif ($conditions['op'] === 'IN') {
            $params['bind'][$bind_index] = $conditions['val'];
            return $conditions['key'] . ' IN ' . '({' . $bind_index . ':array})';
        } elseif ($conditions['op'] === 'BETWEEN') {
            $params['bind'][$bind_index . '_0'] = $conditions['val'][0];
            $params['bind'][$bind_index . '_1'] = $conditions['val'][1];
            return $conditions['key'] . ' BETWEEN ' . ':' . $bind_index . '_0: AND :' . $bind_index . '_1:';
        } elseif ($conditions['op'] === 'NOT IN') {
            $params['bind'][$bind_index] = $conditions['val'];
            return $conditions['key'] . ' NOT IN ' . '({' . $bind_index . ':array})';
        } elseif ($conditions['op'] === 'NOT BETWEEN') {
            $params['bind'][$bind_index . '_0'] = $conditions['val'][0];
            $params['bind'][$bind_index . '_1'] = $conditions['val'][1];
            return $conditions['key'] . ' NOT BETWEEN ' . ':' . $bind_index . '_0: AND :' . $bind_index . '_1:';
        }
        $params['bind'][$bind_index] = $conditions['val'];
        return $conditions['key'] . ' ' . ( $conditions['op'] ?? '=' ) . ' :' . $bind_index . ':';
    }

    public function beforeSave(){
        static $filled = false;
        if($filled) return;
        $filled = true;

        $fields = self::fields();
        $time = time();
        $date = date('Y-m-d H:i:s', $time);
        if(array_key_exists('updated_time', $fields)){
            if(empty($this->updated_time)) $this->updated_time = ($fields['updated_time'] === \Phalcon\Db\Column::TYPE_INTEGER ? $time: $date);
        }
        if(array_key_exists('created_time', $fields)){
            if(empty($this->created_time)) $this->created_time = ($fields['created_time'] === \Phalcon\Db\Column::TYPE_INTEGER ? $time : $date);
        }
        if(array_key_exists('updated_user', $fields)) {
            if (empty($this->updated_user)) $this->updated_user = PA::$user['user_id'] ?? null;
        }
        if(array_key_exists('created_user', $fields)) {
            if (empty($this->created_user)) $this->created_user = PA::$user['user_id'] ?? null;
        }
    }
    public function beforeCreate(){
        $this->beforeSave();
    }
    public function beforeUpdate(){
        $this->beforeSave();
    }
    public function prepareSave(){
        $this->beforeSave();
    }
}
