<?php
namespace plugins\GraphQL\Controllers;
use plugins\GraphQL\FetchData;
use Power\Controllers\ApiController;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use plugins\GraphQL\Types;
use Power\Models\UserConfigs;
use Power\Models\Configs;
use Power\Models\Logs;
use Power\Models\Plugins;
use Power\Models\Roles;
use Power\Models\menus;
use Power\Models\Users;
use PowerModelBase;


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
                {key:"user_id",op:ELT,val:"1"},
                {key:"user_id",val:"1"}
              ],
            },
            {key:"name",op:AND,val:"admin"}
          ]
        }';
        $this->query = '
        query{
            users(filter:{where:'.$filter.'}){
                user_id
                nickname
                password
                name
                #Logs(filter:{where:{key:"log_id",val:"716",op:GT}}){
                Logs(filter:{where:{sub:[{key:"log_id",val:"7168"},{key:"log_id",val:"716"}],op:OR}}){
                    log_id
                    name
                }
                roles{
                    role_id
                    name
                }
            }
            roles(filter:{where:{sub:[{key:"name",val:"%管理员%",op:LIKE},{key:"role_id",val:"2"}],op:OR}}){
                role_id
                name
               # router
            }
            logs(filter:{key:"log_id",val:"234"}){
                log_id
                name
            }
            configs{
                config_id
                user_id
                menu_id
                name
                value
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

//        $params = ['conditions'=>'(a=?0)','bind'=>[100]];
//        $params = [];
//        $where = [
//            'key'=>'user_id','op' => 'XOR','val'=>11, // 简短条件
//            'where'=>[
//                'op'=>'andx',
//                'sub'=>[
//                    ['key'=>'name','val'=>22],
//                    ['key'=>'user_id','val'=>33,'op'=>'>'],
//                    [
//                        'op'=>'AND',
//                        'sub'=>[
//                            ['key'=>'user_id','val'=>44],
//                            ['key'=>'user_id','val'=>55,'op'=>'>=']
//                        ]
//                    ],
//                    ['key'=>'user_id','val'=>66]
//                ]
//            ]
//        ];
//        FatchData::parseWhere(Users::getInstance(), $where, $params);
//        print_r($params);
//        exit;
        
        
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
                            'type' => Type::listOf(Types::table('\\'.UserConfigs::class)), // 对应一个数组
                            'args' => ['filter'=>['type' => Types::filter(),'defaultValue' => ['op'=>'=']]]
                        ],
//                        'extensions' => [
//                            'type' => Type::listOf(Types::table('\\'.Configs::class)), // 对应一个数组
//                            'args' => ['filter'=>['type' => Types::filter(),'defaultValue' => ['op'=>'=']]]
//                        ],
                        'plugins' => [
                            'type' => Type::listOf(Types::table('\\'.Plugins::class)), // 对应一个数组
                            'args' => ['filter'=>['type' => Types::filter(),'defaultValue' => ['op'=>'=']]]
                        ],
                        'roles' => [
                            'type' => Type::listOf(Types::table('\\'.Roles::class)), // 对应一个数组
                            'args' => ['filter'=>['type' => Types::filter(),'defaultValue' => ['op'=>'=']]]
                        ],
                        'menus' => [
                            'type' => Type::listOf(Types::table('\\'.Menus::class)), // 对应一个数组
                            'args' => ['filter'=>['type' => Types::filter(),'defaultValue' => ['op'=>'=']]]
                        ],
                    ],
                    'resolveField'=>[FetchData::class, 'rows']
                ]
            )]
        );
        exit(\GraphQL\Utils\SchemaPrinter::doPrint($schema));
//        $rs = GraphQL::executeQuery($schema, $this->query);
        $this->view->data  = $rs->data;
        $this->view->error = $rs->errors;
    }
}