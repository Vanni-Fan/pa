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
          "type" TEXT,
          "host" TEXT,
          "port" TEXT,
          "user" TEXT,
          "password" TEXT,
          PRIMARY KEY ("id")
        );';
        $sql2 = 'CREATE TABLE IF NOT EXISTS "plugins_table_menus" (
          "id" INTEGER NOT NULL,
          "menu_id" INTEGER,
          "source_id" integer,
          "table_name" TEXT,
          PRIMARY KEY ("id")
        );';
        $now = date('Y-m-d H:i:s');
        PA::$db->execute(
            'INSERT INTO "plugins"("name", "class_name", "is_enabled", "icon_url", "images", "description", "permission", "official_url", "author", "author_url", "version", "match_version", "license", "status_time", "publish_date", "inserted_time", "created_time", "updated_time", "created_user", "updated_user")' .
            'VALUES ("Tables", NULL, 1, NULL, NULL, "对MySQL表进行增删改查的简易操作", NULL, "http://pa.com", "Vanni Fan", "http://vanni.fan", "1.0", "^1.0", "BSD", ?1, ?1, ?1, NULL, NULL, NULL, NULL)'
        ,[$now]);
        PA::$db->execute($sql1);
        PA::$db->execute($sql2);
        // 2、 创建 model
        $template = file_get_contents(__DIR__ .'/ModelTemplate.php');
        file_put_contents(POWER_DATA . 'TablesPlugins/PluginsTableMenus.php',str_replace(
            ['__MODEL_NAME__','__DB_INFO__'],
            ['PluginsTableMenus', var_export(PA::$config['pa_db']->toArray(),1)],
            $template
        ));
        file_put_contents(POWER_DATA . 'TablesPlugins/PluginsTableSources.php',str_replace(
            ['__MODEL_NAME__','__DB_INFO__'],
            ['PluginsTableSources', var_export(PA::$config['pa_db']->toArray(),1)],
            $template
        ));

        return true;
    }
    public static function uninstall($controller, $plugin){
        $plugin->delete();
        PA::$db->execute('drop table if exists "plugins_table_sources"');
        PA::$db->execute('drop table if exists "plugins_table_menus"');
        if(file_exists(POWER_DATA . 'TablesPlugins/PluginsTableSources.php')) unlink(POWER_DATA . 'TablesPlugins/PluginsTableSources.php');
        if(file_exists(POWER_DATA . 'TablesPlugins/PluginsTableMenus.php')) unlink(POWER_DATA . 'TablesPlugins/PluginsTableMenus.php');
        return true;
    }
}