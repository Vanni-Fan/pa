<?php
namespace plugins\GraphQL;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\InputObjectType;

class FiltersInputType extends InputObjectType{
    public function __construct()
    {
        parent::__construct(
            [
                'name' => 'Filters',
                'fields' => [
                    'where' => Types::condition(), // 复杂查询
                    'sort'  => Type::listOf(Type::string()), // sort:["abc desc"]
                    'key'   => Type::string(), // 简单查询 key:abc, val:123 =>  abc = 123
                    'val'   => Type::string(),
                    'op'    => [
                        'type'=>Types::op(), // 简单查询 key:abc, val:123, op:>  => abc > 123
                        'defaultValue'=>'='
                    ],
                    'limit' => [
                        'type'=>Type::listOf(Type::int()), // limit:[1,2]
                        'defaultValue'=>[1,10],
                    ]
                ]
            ]
        );
    }
}
