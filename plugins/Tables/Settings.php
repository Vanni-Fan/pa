<?php
namespace plugins\Tables;
use PA;
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
        // todo 需要调整到 premissionadmin.com 上去进行安装
        // 1、 创建表
        $sql1 = 'CREATE TABLE IF NOT EXISTS "plugins_table_sources" (
          "id" INTEGER NOT NULL,
          "name" TEXT,
          "dbname" TEXT,
          "type" TEXT,
          "host" TEXT,
          "port" TEXT,
          "user" TEXT,
          "password" TEXT,
          "path" TEXT,
          "is_system" INTEGER,
          "status" INTEGER,
          PRIMARY KEY ("id")
        );';
        $sql2 = 'CREATE TABLE IF NOT EXISTS "plugins_table_menus" (
          "id" INTEGER NOT NULL,
          "menu_id" INTEGER,
          "source_id" integer,
          "model_file" TEXT,
          "table_name" TEXT,
          PRIMARY KEY ("id")
        );';
        $now = date('Y-m-d H:i:s');
        PA::$db->execute(
            'INSERT INTO "plugins"("name", "class_name", "is_enabled", "icon_url", "images", "description", "permission", "official_url", "author", "author_url", "version", "match_version", "license", "status_time", "publish_date", "inserted_time", "created_time", "updated_time", "created_user", "updated_user")' .
            'VALUES ("Tables", NULL, 1, NULL, NULL, "对MySQL表进行增删改查的简易操作", NULL, "http://pa.com", "Vanni Fan", "http://vanni.fan", "1.0", "^1.0", "BSD", ?1, ?1, ?1, NULL, NULL, NULL, NULL)'
        ,[$now]);
        PA::$db->execute($sql1);
        # 系统的数据源默认插入，但是不可编辑
        $sys_ds = [
            'name'     => PA::$config['pa_db']['dbname'],
            'type'     => PA::$config['pa_db']['adapter'],
            'host'     => PA::$config['pa_db']['host']??'',
            'port'     => PA::$config['pa_db']['port'],
            'user'     => PA::$config['pa_db']['username']??'',
            'password' => PA::$config['ps_db']['password']??'',
            'path'     => POWER_BASE_DIR . 'models/'
        ];
        PA::$db->execute(
            'INSERT INTO "plugins_table_sources"("name","dbname","type","host","port","user","password","path","is_system","status")
              VALUES("系统",:name,:type,:host,:port,:user,:password,:path,1,0)',
            $sys_ds
        );
        PA::$db->execute($sql2);
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
        $plugin->delete();
        PA::$db->execute('drop table if exists "plugins_table_sources"');
        PA::$db->execute('drop table if exists "plugins_table_menus"');
        $dir = POWER_DATA . 'TablesPlugins/Tables/System/';
        if(file_exists($dir . 'PluginsTableSources.php')) unlink($dir . 'PluginsTableSources.php');
        if(file_exists($dir . 'PluginsTableMenus.php'))   unlink($dir . 'PluginsTableMenus.php');
        return true;
    }
    public static function autoload(){ // 自动加载
        static $is_loaded = false;
        if($is_loaded) return;
        $dirs = PA::$loader->getDirs();
        $dirs[] = POWER_DATA.'TablesPlugins';
        PA::$loader->registerDirs($dirs);
        PA::$loader->register();
        $is_loaded = true;
    }
}