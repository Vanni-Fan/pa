<?php
/** The file is generated automatically by TablePlugin */
namespace Tables;
use Phalcon\Db\Adapter;

class PluginsTableMenus extends \Phalcon\Mvc\Model{
    public function initialize(){
        $this->setDi(\PA::$di);
        \PA::$di->set("plugin_table_db", \Phalcon\Db\Adapter\Pdo\Factory::load(array (
  'adapter' => 'sqlite',
  'dbname' => 'D:\\wwwroot\\pa\\public/../data//powerdb.sql3.db',
)));
        $this->setConnectionService("plugin_table_db");
    }
}