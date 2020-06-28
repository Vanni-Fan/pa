<?php
namespace plugins\Tables;
use PA;
use Phalcon\Db\Column;
use Power\Models\Plugins;

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
                new Column('name'    , ['type'=>Column::TYPE_VARCHAR    , 'size'=>255]),
                new Column('tooltip' , ['type'=>Column::TYPE_VARCHAR    , 'size'=>255    , 'notNull'=>0]),
                new Column('width'   , ['type'=>Column::TYPE_TINYINTEGER, 'notNull'=>0   , 'unsigned'=>1]),
                new Column('sort'    , ['type'=>Column::TYPE_TINYINTEGER, 'size'=>1      , 'default'=>1   , 'unsigned'=>1]),
                new Column('filter'  , ['type'=>Column::TYPE_TINYINTEGER, 'size'=>1      , 'default'=>1   , 'unsigned'=>1]),
                new Column('show'    , ['type'=>Column::TYPE_TINYINTEGER, 'size'=>1      , 'default'=>1   , 'unsigned'=>1]),
                new Column('primary' , ['type'=>Column::TYPE_TINYINTEGER, 'size'=>1      , 'default'=>0   , 'unsigned'=>1]),
                new Column('render'  , ['type'=>Column::TYPE_TEXT       , 'size'=>50     , 'notNull'=>0]),
                new Column('type'    , ['type'=>Column::TYPE_VARCHAR    , 'size'=>50     , 'notNull'=>50]),
                new Column('params'  , ['type'=>Column::TYPE_TEXT       , 'notNull'=>0]),
                new Column('icon'    , ['type'=>Column::TYPE_VARCHAR    , 'size'=>255    , 'notNull'=>0]),
                new Column('class'   , ['type'=>Column::TYPE_VARCHAR    , 'size'=>255    , 'notNull'=>0]),
            ]]
        );
        return true;

        // 2、 创建 model
        $db_name = 'System';
        $template = file_get_contents(__DIR__ .'/ModelTemplate.php');
        $dir = POWER_DATA . 'TablesPlugins/Tables/'.$db_name.'/';
        is_dir($dir) || mkdir($dir,0777, true);
        file_put_contents($dir . 'PluginsTableMenus.php',str_replace(
            ['__DB_NAME__','__TABLE_NAME__','__MODEL_NAME__','__DB_INFO__'],
            [$db_name, (PA::$config['pa']['prefix']??'').'plugins_table_menus','PluginsTableMenus', var_export(PA::$config['pa_db']->toArray(),1)],
            $template
        ));
        file_put_contents($dir . 'PluginsTableSources.php',str_replace(
            ['__DB_NAME__','__TABLE_NAME__','__MODEL_NAME__','__DB_INFO__'],
            [$db_name, (PA::$config['pa']['prefix']??'').'plugins_table_sources', 'PluginsTableSources', var_export(PA::$config['pa_db']->toArray(),1)],
            $template
        ));

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