<?php
class BaseModel extends PowerModelBase{
    public function initialize(){
        $this->setDi(PA::$di);
        PA::$di->set('my_db', (new \Phalcon\Db\Adapter\PdoFactory())->newInstance('mysql',[
            'dbname'  => 'mydb',
            'username'=> 'root',
            'password'=> '123456',
            'host'    => 'mysql',
        ]));
        $this->setConnectionService('my_db');
    }
}