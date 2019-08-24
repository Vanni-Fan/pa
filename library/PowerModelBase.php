<?php
use Phalcon\Mvc\Model;
/**
 * 基础模型
 * @author vanni.fan
 */
class PowerModelBase extends Model{
    public function initialize(){
        PA::$di->set('db',PA::$db);
        $this->setDi(PA::$di);
        $this->setSource(PA::$config->path('pa_db.prefix').$this->getSource());
    }
}
