<?php
namespace plugins\GraphQL\Controllers;
use plugins\GraphQL\FatchData;
use Power\Controllers\ApiController;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use plugins\GraphQL\Types;
use Power\Models\Configs;
use Power\Models\Extensions;
use Power\Models\Logs;
use Power\Models\Plugins;
use Power\Models\Roles;
use Power\Models\Rules;
use Power\Models\Users;


class GraphQlController extends ApiController
{
    private $query;
    public function initialize()
    {
        parent::initialize();

        # 无限子查询表达式
        $filter = '
        {
          op:OR,
          sub:[
            {
              op:AND,
              sub:[
                {key:"A",op:ELT,val:"11"},
                {key:"B",val:"11"}
              ],
            },
            {key:"B",op:AND,val:"11"}
          ]
        }';
        $this->query = '
        query{
            users(filter:{where:'.$filter.'}){
                user_id
                nickname
                password
                name
                Logs(filter:{where:{key:"A",val:"123",op:IN}}){
                    log_id
                    name
                }
                roles{
                    role_id
                    name
                }
            }
            roles{
                role_id
                name
               # router
            }
            logs(filter:{key:"AA",val:"234"}){
                log_id
                name
            }
        }
        ';
        
//        $this->query = '
//        query{
//          __schema{
//             users{
//                 user_id
//             }
//          }
//        }
//        ';
    }

    public function indexAction(){
        $schema = new Schema(
            ['query'=>new ObjectType(
                [
                    'name' => 'AllTypes',
                    'fields' => [
                        'logs'  => [
                            'type' => Type::listOf(Types::table('\\'.Logs::class)), // 对应一个对象
                            'args' => ['filter'=>['type' => Types::filter(),'defaultValue' => ['op'=>'=']]]
                        ],
                        'users' => [
                            'type' => Type::listOf(Types::table('\\'.Users::class)), // 对应一个数组
                            'args' => ['filter'=>['type' => Types::filter(),'defaultValue' => ['op'=>'=']]]
                        ],
                        'configs' => [
                            'type' => Type::listOf(Types::table('\\'.Configs::class)), // 对应一个数组
                            'args' => ['filter'=>['type' => Types::filter(),'defaultValue' => ['op'=>'=']]]
                        ],
                        'extensions' => [
                            'type' => Type::listOf(Types::table('\\'.Extensions::class)), // 对应一个数组
                            'args' => ['filter'=>['type' => Types::filter(),'defaultValue' => ['op'=>'=']]]
                        ],
                        'plugins' => [
                            'type' => Type::listOf(Types::table('\\'.Plugins::class)), // 对应一个数组
                            'args' => ['filter'=>['type' => Types::filter(),'defaultValue' => ['op'=>'=']]]
                        ],
                        'roles' => [
                            'type' => Type::listOf(Types::table('\\'.Roles::class)), // 对应一个数组
                            'args' => ['filter'=>['type' => Types::filter(),'defaultValue' => ['op'=>'=']]]
                        ],
                        'rules' => [
                            'type' => Type::listOf(Types::table('\\'.Rules::class)), // 对应一个数组
                            'args' => ['filter'=>['type' => Types::filter(),'defaultValue' => ['op'=>'=']]]
                        ],
                    ],
                    'resolveField'=>[FatchData::class, 'rows']
                ]
            )]
        );
//        exit(\GraphQL\Utils\SchemaPrinter::doPrint($schema));
        $rs = GraphQL::executeQuery($schema, $this->query);
        $this->view->data  = $rs->data;
        $this->view->error = $rs->errors;
    }
}