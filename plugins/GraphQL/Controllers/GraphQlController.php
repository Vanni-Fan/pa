<?php
namespace plugins\GraphQL\Controllers;
use Power\Controllers\ApiController;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\EnumType;

//use GraphQL\Type\Definition\InterfaceType;
class MyType{
    static $condition = null;
    static public function cond(){
        return self::$condition ?: (self::$condition = new MyCondition());
    }
}

class MyCondition extends InputObjectType{
    public function __construct()
    {
        parent::__construct(
            [
                'name'=>'Filter',
                'fields'=>function(){
                    return [
                        'key' => Type::string(),
                        'op'  => Type::string(),
                        'val' => Type::string(),
                        'sub' => Type::listOf(MyType::cond())
                    ];
                }
            ]
        );
    }
}


class GraphQlController extends ApiController
{
    private $query;
    public function initialize()
    {
        parent::initialize();

        $muilt_filter = '
        {
          op:"or",
          sub:[
            {
              op:"and",
              sub:[
                {key:"A",op:">",val:"11"},
                {key:"B",op:"<",val:"11"}
              ],
          },{key:"B",op:"=",val:"11"}]
        }';
        $this->query = '
        query{
            #logs(id:12, name:"ABC", name_lt:123){
            #logs(filter:{where:{key:"abc",op:"=",val:"sdf"}}){
            logs(filter:{where:'.$muilt_filter.'}){
            #logs(filter:{where:OTHER}){
            #logs(filter:{where:["AA",">","BB"]}){
            #logs{
                id(eq:1,lt:2)
                name
            }
            users{
                name
            }
            desc
            #abc
        }
        ';
    }
    public function getLog($value, $args, $context, $info){
        print_r($args);
        switch($info->fieldName){
            case 'id':
                return random_int(1,20);
            case 'name':
                return ['AA','BB','CC'][random_int(0,2)];
        }
    }
    public function getObjs($value, $args, $context, $info){
        print_r($args);
        if($info->fieldName == 'desc') return '这个是描述';
        return ['id'=>random_int(1,20),'name'=>['AA','BB','CC'][random_int(0,2)]];
    }

    public function indexAction(){

        //Input types (or argument types) are: Scalar, Enum, InputObject, NonNull and List
        $filters = new InputObjectType(
            [
               'name' => 'Filters',
               'fields' => [
                   'where' => MyType::cond(), // 复杂查询
                   'sort'  => Type::listOf(Type::string()), // sort:["abc desc"]
                   'key'   => Type::string(), // 简单查询 key:abc, val:123 =>  abc = 123
                   'val'   => Type::string(),
                   'op'    => Type::string(), // 简单查询 key:abc, val:123, op:>  => abc > 123
                   'limit' => Type::listOf(Type::int()), // limit:[1,2]
               ]
        ]);

        $logs_type = new ObjectType(
            [
                'name' => 'Logs',
                'fields' => [
                    'id'   => [
                        'type'=>Type::int(),
                        'args'=>[
                            'eq' => Type::int(),
                            'lt' => Type::int()
                        ]
                    ],
                    'name' => Type::string(),
                ],
                'resolveField'=>[$this,'getLog']
            ]
        );
        $users_types = new ObjectType(
            [
                'name' => 'Users',
                'fields' => [
                    'id'   => [
                        'type'=>Type::int(),
                        'args'=>[
                            'eq' => Type::int(),
                            'lt' => Type::int()
                        ]
                    ],
                    'name' => Type::string(),
                ],
                'resolveField'=>[$this,'getLog']
            ]
        );
        
        $schema = new Schema(
            ['query'=>new ObjectType(
                [
                    'name' => 'AllTypes',
                    'fields' => [
                        'logs'  => [
                            'type' => $logs_type, // 对应一个对象
                            'args' => [
                                'id'   => Type::int(),
                                'name' => Type::string(),
                                'name_lt' => Type::int(),
                                'filter'=>[
                                    'type' => Type::nonNull($filters),
                                    'defaultValue' => [
                                        'popular' => true
                                    ]
                                ]
                            ]
                        ],
                        'users' => Type::listOf($users_types), // 对应一个数组
                        'desc'  => Type::string(),
                    ],
                    'resolveField' => [$this,'getObjs']
                ]
            )]
        );
        $rs = GraphQL::executeQuery($schema, $this->query);
        $this->view->data  = $rs->data;
        $this->view->error = $rs->errors;
    }
}