<?php
namespace plugins\Tables;
use PA;
use Phalcon\Db\Column;

class Settings {
    public static function setting(){
        PA::$dispatch->forward(
            [
                'controller'=>'Manager',
                'namespace'=>'plugins\Tables\Controllers',
                'action'=>'settings',
            ]
        );
        return false;
    }
    public static function install($controller, $plugin){
        PA::$db->createTable(
            PA::$config->pa_db->prefix.'tables_menus',
            '',
            ['columns'=>[
                new Column('id'            , ['type'=>Column::TYPE_INTEGER    , 'size'=>10     , 'unsigned'=>1 , 'autoIncrement'=>1, 'primary'=>1]),
                new Column('source_id'     , ['type'=>Column::TYPE_INTEGER    , 'unsigned'=>1]),
                new Column('table'         , ['type'=>Column::TYPE_VARCHAR    , 'size'=>50]),
                new Column('menu_id'       , ['type'=>Column::TYPE_INTEGER    , 'unsigned'=>1]),
                new Column('parent_menu_id', ['type'=>Column::TYPE_INTEGER    , 'unsigned'=>1]),
                new Column('menu_name'     , ['type'=>Column::TYPE_VARCHAR    , 'size'=>10]),
                new Column('menu_icon'     , ['type'=>Column::TYPE_VARCHAR    , 'size'=>50     , 'default'=>'']),
                new Column('title'         , ['type'=>Column::TYPE_VARCHAR    , 'size'=>50]),
                new Column('subtitle'      , ['type'=>Column::TYPE_VARCHAR    , 'size'=>50     , 'notNull'=>0]),
                new Column('filters'       , ['type'=>Column::TYPE_TEXT       , 'notNull'=>0]),
                new Column('canMin'        , ['type'=>Column::TYPE_TINYINTEGER, 'size'=>1      , 'default'=>1  , 'unsigned'=>1]),
                new Column('canClose'      , ['type'=>Column::TYPE_TINYINTEGER, 'size'=>1      , 'default'=>1  , 'unsigned'=>1]),
                new Column('canSelect'     , ['type'=>Column::TYPE_TINYINTEGER, 'size'=>1      , 'default'=>1  , 'unsigned'=>1]),
                new Column('canEdit'       , ['type'=>Column::TYPE_TINYINTEGER, 'size'=>1      , 'default'=>1  , 'unsigned'=>1]),
                new Column('canAppend'     , ['type'=>Column::TYPE_TINYINTEGER, 'size'=>1      , 'default'=>1  , 'unsigned'=>1]),
                new Column('canDelete'     , ['type'=>Column::TYPE_TINYINTEGER, 'size'=>1      , 'default'=>1  , 'unsigned'=>1]),
                new Column('canFilter'     , ['type'=>Column::TYPE_TINYINTEGER, 'size'=>1      , 'default'=>1  , 'unsigned'=>1]),
            ]]
        );

        PA::$db->createTable(
            PA::$config->pa_db->prefix.'tables_fields',
            '',
            ['columns'=>[
                new Column('id'      , ['type'=>Column::TYPE_INTEGER    , 'size'=>10     , 'unsigned'=>1  , 'autoIncrement'=>1, 'primary'=>1]),
                new Column('table_id', ['type'=>Column::TYPE_INTEGER    , 'unsigned'=>1]),
                new Column('field'   , ['type'=>Column::TYPE_VARCHAR    , 'size'=>50]),
                new Column('text'    , ['type'=>Column::TYPE_VARCHAR    , 'size'=>255]),
                new Column('tooltip' , ['type'=>Column::TYPE_VARCHAR    , 'size'=>255    , 'notNull'=>0]),
                new Column('width'   , ['type'=>Column::TYPE_SMALLINTEGER, 'notNull'=>0   , 'unsigned'=>1]),
                new Column('sort'    , ['type'=>Column::TYPE_TINYINTEGER, 'size'=>1      , 'default'=>1   , 'unsigned'=>1]),
                new Column('filter'  , ['type'=>Column::TYPE_TINYINTEGER, 'size'=>1      , 'default'=>1   , 'unsigned'=>1]),
                new Column('show'    , ['type'=>Column::TYPE_TINYINTEGER, 'size'=>1      , 'default'=>1   , 'unsigned'=>1]),
                new Column('canShow' , ['type'=>Column::TYPE_TINYINTEGER, 'size'=>1      , 'default'=>1   , 'unsigned'=>1]),
                new Column('primary' , ['type'=>Column::TYPE_TINYINTEGER, 'size'=>1      , 'default'=>0   , 'unsigned'=>1]),
                new Column('render'  , ['type'=>Column::TYPE_TEXT       , 'size'=>50     , 'notNull'=>0]),
                new Column('type'    , ['type'=>Column::TYPE_VARCHAR    , 'size'=>50     , 'notNull'=>50]),
                new Column('params'  , ['type'=>Column::TYPE_TEXT       , 'notNull'=>0]),
                new Column('icon'    , ['type'=>Column::TYPE_VARCHAR    , 'size'=>255    , 'notNull'=>0]),
                new Column('class'   , ['type'=>Column::TYPE_VARCHAR    , 'size'=>255    , 'notNull'=>0]),
            ]]
        );
        return true;
    }
    public static function uninstall($controller, $plugin){
        PA::$db->dropTable(PA::$config->pa_db->prefix.'tables_fields');
        PA::$db->dropTable(PA::$config->pa_db->prefix.'tables_menus');
        return true;
    }
    public static function autoload(){ // 自动加载
//        static $is_loaded = false;
//        if($is_loaded) return;
//        $dirs = PA::$loader->getDirs();
//        $dirs[] = POWER_DATA.'TablesPlugins';
//        PA::$loader->registerDirs($dirs);
//        PA::$loader->register();
//        $is_loaded = true;
    }
}
