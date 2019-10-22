<?php
/** The file is generated automatically by TablePlugin */
namespace Tables\__DB_NAME__;

class __MODEL_NAME__ extends \PowerModelBase{
    public function initialize():void{
        $this->setDi(\PA::$di);
        $db = \Phalcon\Db\Adapter\Pdo\Factory::load(__DB_INFO__);
        \PA::$di->set('__DB_NAME___db', $db);
        $this->setConnectionService('__DB_NAME___db');
        $this->setSource('__TABLE_NAME__');
        $db->setEventsManager(\PA::$em);
    }
}