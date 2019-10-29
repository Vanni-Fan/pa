<?php
/** The file is generated automatically by TablePlugin */
namespace Tables\System;

class PluginsTableSources extends \PowerModelBase{
    public function initialize():void{
        $this->setDi(\PA::$di);
        $db = \Phalcon\Db\Adapter\Pdo\Factory::load(array (
  'adapter' => 'sqlite',
  'dbname' => 'D:\\wwwroot\\pa\\pa\\public/../data//powerdb.sql3.db',
));
        \PA::$di->set('System_db', $db);
        $this->setConnectionService('System_db');
        $this->setSource('plugins_table_sources');
        $db->setEventsManager(\PA::$em);
    }
}