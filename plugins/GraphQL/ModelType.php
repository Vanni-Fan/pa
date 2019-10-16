<?php
namespace plugins\GraphQL;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Phalcon\Db\Column;
use Phalcon\Mvc\Model\Relation;

class ModelType extends ObjectType{
    public $model;
    public function __construct($name)
    {
        parent::__construct(
            [
                'name'=>ucfirst(basename($name)),
                'fields'=> static function() use ($name) {
                    $model = new $name();
                    # 查找关系
                    $fields = $relate = [];
                    array_map(
                        function($v) use(&$relate) {
                            $relate_fields = $v->getFields();
                            if(!is_array($relate_fields)) $relate_fields = [$relate_fields];
                            $r_type = '';
                            switch ($v->getType()){
                                case Relation::HAS_ONE:
                                case Relation::HAS_ONE_THROUGH:
                                    $r_type = 'one';  break;
                                case Relation::HAS_MANY:
                                case Relation::HAS_MANY_THROUGH:
                                    $r_type = 'many'; break;
                                default: return;
                            }
                            $option = $v->getOptions();
                            if($r_type) foreach($relate_fields as $_index => $r_field){
                                $relate[$r_field] = [
                                    'type'  => $r_type,
                                    'name'  => isset($option['alias']) ? ($option['alias'].($_index?:'')) : (ucfirst($r_field).($_index?:'')),
                                    'table' => '\\'.$v->getReferencedModel()
                                ];
                            }
                        },
                        $model->getModelsManager()->getHasOneAndHasMany($model)
                    );
                    # 设置字段
                    $meta = $model->getModelsMetaData()->getDataTypes($model);
                    foreach($meta as $field=>$type){
                        # 如果有关系，则直接设置对应的类型
                        if(array_key_exists($field, $relate)){
                            $args = [ // 可设置的参数
                                      'filter'=>[
                                          'type' => Types::filter(),
                                          'defaultValue' => ['op'=>'=']
                                      ]
                            ];
                            if($relate[$field]['type']==='many'){
                                $fields[$relate[$field]['name']] = [
                                    'type' => Type::listOf(Types::table($relate[$field]['table'])),
//                                    'description' => '有多个', // todo
                                    'args' => $args
                                ];
                            } else {
                                $fields[$relate[$field]['name']] = [
                                    'type' => Types::table($relate[$field]['table']),
//                                    'description' => '有一个，关联ID为：#Users.user_id = Logs.user_id#',  // todo
//                                    'args' => $args
                                ];
                            }
                        }
        
                        # 没有关系，安装类型来设置
                        switch($type){
                            case Column::TYPE_INTEGER:
                            case Column::TYPE_BIGINTEGER:
                            case Column::BIND_PARAM_INT:
                                $fields[$field] = ['type' => Type::int()];
                                break;
                            case Column::TYPE_DATE:
                            case Column::TYPE_VARCHAR:
                            case Column::TYPE_DATETIME:
                            case Column::TYPE_CHAR:
                            case Column::TYPE_TEXT:
                            case Column::TYPE_TINYBLOB:
                            case Column::TYPE_BLOB:
                            case Column::TYPE_MEDIUMBLOB:
                            case Column::TYPE_LONGBLOB:
                            case Column::TYPE_JSON:
                            case Column::TYPE_JSONB:
                            case Column::TYPE_TIMESTAMP:
                            case Column::BIND_PARAM_STR:
                            case Column::BIND_PARAM_BLOB:
                            case Column::BIND_PARAM_BOOL:
                            case Column::BIND_PARAM_NULL:
                            case Column::BIND_SKIP:
                                $fields[$field] = ['type' => Type::string()];
                                break;
                            case Column::TYPE_FLOAT:
                            case Column::TYPE_DOUBLE:
                            case Column::TYPE_DECIMAL:
                            case Column::BIND_PARAM_DECIMAL:
                                $fields[$field] = ['type' => Type::float()];
                                break;
                            case Column::TYPE_BOOLEAN:
                                $fields[$field] = ['type' => Type::boolean()];
                                break;
                        }
                    }
                    return $fields;
                },
                'resolveField'=>[FetchData::class, 'columns']
            ]
        );
        $this->model = $name;
    }
    
    public function toString()
    {
        return $this->model;
    }
}
