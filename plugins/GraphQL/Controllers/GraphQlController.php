<?php
namespace plugins\GraphQL\Controllers;
use Power\Controllers\ApiController;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\InputObjectType;

//use GraphQL\Type\Definition\InterfaceType;

class GraphQlController extends ApiController
{
    private $query;
    public function initialize()
    {
        parent::initialize();
        $this->query = '
        query{
            #logs(id:12, name:"ABC", name_lt:123){
            logs(filter:{}){
            #logs{
                id(eq:1,lt:2)
                name
            }
            users{
                name
            }
            desc
            #abc
        }';
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
        $filters = new InputObjectType(
            [
               'name' => 'StoryFiltersInput',
               'fields' => [
                   'author' => [
                       'type' => Type::id(),
                       'description' => 'Only show stories with this author id'
                   ],
                   'popular' => [
                       'type' => Type::boolean(),
                       'description' => 'Only show popular stories (liked by several people)'
                   ],
                   'tags' => [
                       'type' => Type::listOf(Type::string()),
                       'description' => 'Only show stories which contain all of those tags'
                   ]
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