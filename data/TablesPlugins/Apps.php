<?php
/** The file is generated automatically by TablePlugin */
namespace Tables;
use Phalcon\Db\Adapter\Pdo\Factory;
class Apps extends \Phalcon\Mvc\Model{
    public function initialize(){
        $this->setDi(\PA::$di);
        \PA::$di->set("plugin_table_db", Factory::load(array (
  'host' => '192.168.3.205',
  'dbname' => 'mp',
  'port' => 33080,
  'username' => 'root',
  'password' => 'aJ6^B@sDw771',
  'adapter' => 'mysql',
  'options' => 
  array (
    1002 => 'SET NAMES utf8mb4',
  ),
)));
        $this->setConnectionService("plugin_table_db");
    }
}