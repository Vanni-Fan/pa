<?php
class BaseModel extends PowerModelBase{
    public function initialize(){
        $this->setDi(PA::$di);
        PA::$di->set('my_db', (new \Phalcon\Db\Adapter\PdoFactory())->newInstance('mysql',[
            'dbname'  => 'test',
            'username'=> 'root',
            'password'=> '123456',
            'host'    => '192.168.2.202',
        ]));
        $this->setConnectionService('my_db');
    }
}
