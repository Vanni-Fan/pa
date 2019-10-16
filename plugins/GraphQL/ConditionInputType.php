<?php
namespace plugins\GraphQL;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\InputObjectType;

class ConditionInputType extends InputObjectType{
    public function __construct()
    {
        parent::__construct(
            [
                'name'=>'Filter',
                'fields'=> static function(){
                    return [
                        'key' => Type::string(),
                        'op'  => [
                            'type' => Types::op(),
                            'defaultValue' => '=',
                        ],
                        'val' => Type::string(),
                        'sub' => Type::listOf(Types::condition())
                    ];
                }
            ]
        );
    }
}
