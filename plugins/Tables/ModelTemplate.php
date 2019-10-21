<?php
/** The file is generated automatically by TablePlugin */
namespace Tables;

class __MODEL_NAME__ extends \PowerModelBase{
    public function initialize(){
        $this->setDi(\PA::$di);
        \PA::$di->set("plugin_table_db", \Phalcon\Db\Adapter\Pdo\Factory::load(__DB_INFO__));
        $this->setConnectionService("plugin_table_db");
    }
}