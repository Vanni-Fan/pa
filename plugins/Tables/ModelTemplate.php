<?php
/** The file is generated automatically by TablePlugin */
namespace Tables;
use Phalcon\Db\Adapter\Pdo\Factory;
class __MODEL_NAME__ extends \Phalcon\Mvc\Model{
    public function initialize(){
        $this->setDi(\PA::$di);
        \PA::$di->set("plugin_table_db", Factory::load(__DB_INFO__);
        $this->setConnectionService("plugin_table_db");
    }
}