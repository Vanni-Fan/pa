<?php
namespace plugins\Proxy;
use PA;
use Phalcon\Db\Column;

class Settings {
    public static function setting(){
        PA::$dispatch->forward(
            [
                'controller'=>'Manager',
                'namespace'=>'plugins\Proxy\Controllers',
                'action'=>'settings',
            ]
        );
        return false;
    }
    public static function install($controller, $plugin){
//        PA::$db->createTable(
//            PA::$config->pa_db->prefix.'tables_menus',
//            '',
//            ['columns'=>[
//                new Column('id'            , ['type'=>Column::TYPE_INTEGER    , 'size'=>10     , 'unsigned'=>1 , 'autoIncrement'=>1, 'primary'=>1]),
//                new Column('canFilter'     , ['type'=>Column::TYPE_TINYINTEGER, 'size'=>1      , 'default'=>1  , 'unsigned'=>1]),
//            ]]
//        );
//
//        PA::$db->createTable(
//            PA::$config->pa_db->prefix.'tables_fields',
//            '',
//            ['columns'=>[
//                new Column('id'      , ['type'=>Column::TYPE_INTEGER    , 'size'=>10     , 'unsigned'=>1  , 'autoIncrement'=>1, 'primary'=>1]),
//                new Column('class'   , ['type'=>Column::TYPE_VARCHAR    , 'size'=>255    , 'notNull'=>0]),
//            ]]
//        );
        return true;
    }
    public static function uninstall($controller, $plugin){
//        PA::$db->dropTable(PA::$config->pa_db->prefix.'tables_fields');
//        PA::$db->dropTable(PA::$config->pa_db->prefix.'tables_menus');
        return true;
    }
    public static function autoload(){ // 自动加载
        PA::$router->add('/_proxy/(.+)', [
            'namespace'  => 'plugins\Proxy\Controllers',
            'controller' => 'Proxy',
            'action'     => 'index',
        ]);
    }
}