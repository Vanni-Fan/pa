<?php
namespace plugins\DataSource;
use PA;
use Phalcon\Db\Column;
use plugins\DataSource\Models\DataSources;

class Settings {
    public static function setting(){
        PA::$dispatch->forward(
            [
                'controller'=>'Manager',
                'namespace'=>'plugins\DataSource\Controllers',
                'action'=>'settings',
            ]
        );
        return false;
    }
    public static function install($controller, $plugin){
        return PA::$db->createTable(
            'pa_datasources',
            '',
            ['columns' => [
                new Column('source_id',['type' => Column::TYPE_INTEGER,'size'=> 10,'notNull'=> true,'autoIncrement' => true,'primary' => true,]),
                new Column('name',['type' => Column::TYPE_VARCHAR,'size' => 255]),
                new Column('adapter',['type' => Column::TYPE_VARCHAR,'size' => 10]),
                new Column('dbname',['type' => Column::TYPE_VARCHAR,'size' => 50]),
                new Column('username',['type' => Column::TYPE_VARCHAR,'size' => 50]),
                new Column('password',['type' => Column::TYPE_VARCHAR,'size' => 50]),
                new Column('prefix',['type' => Column::TYPE_VARCHAR,'size' => 50]),
                new Column('host',['type' => Column::TYPE_VARCHAR,'size' => 50]),
                new Column('port',['type'=>Column::TYPE_MEDIUMINTEGER]),
                new Column('injection_name',['type' => Column::TYPE_VARCHAR,'size'=>10]),
                new Column('bind_events_manager',['type'=>Column::TYPE_SMALLINTEGER ,'size'=>1,'default'=>1]),
                new Column('created_time',['type'=>Column::TYPE_INTEGER,'unsigned'=>true]),
                new Column('updated_time',['type'=>Column::TYPE_INTEGER,'unsigned'=>true]),
                new Column('created_user',['type'=>Column::TYPE_INTEGER,'unsigned'=>true]),
                new Column('updated_user',['type'=>Column::TYPE_INTEGER,'unsigned'=>true]),
            ]]
        );
    }
    public static function uninstall($controller, $plugin){
        return PA::$db->dropTable('pa_datasources');
    }
    public static function autoload(){ // 自动加载
        static $is_loaded = false;
        if($is_loaded) return;
//        PA::$loader->registerDirs([POWER_DATA.'DataSource'],true);
//        PA::$loader->registerNamespaces([
//            'plugins\DataSource\Models'=>POWER_BASE_DIR.'plugins/DataSource/Models'
//        ],true);
        $is_loaded = true;

        // 初始化到DI，如果选中的话

        // 加入事件监听，如果选中的话
    }
}