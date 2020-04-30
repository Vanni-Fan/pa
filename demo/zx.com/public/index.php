<?php
class Txt{
   static function log(...$data){
       $event = array_shift($data);
       switch($type = $event->getType()){
           case 'beforeCheckRoute':
           case 'notMatchedRoute':
           case 'matchedRoute':
               error_log("\n事件类型：".$type."; Route：[".$data[1]->getPattern().']',3,'/tmp/log.txt');
               break;
           case 'beforeCheckRoutes':
           break;
           case 'beforeQuery':
            // print_r($data);
                error_log($data[0]->getSQLStatement()."\n",3,'/tmp/log.txt');
                break;
           default:
               $sources = [];
               foreach($data as $source) $sources[] = is_object($source) ? get_class($source) : print_r($source,1);
               error_log("\n\n事件类型：".$type."; 源于：[".implode(', ',$sources).']',3,'/tmp/log.txt');
       }
   }
}

$app = include '/var/www/pa/public/index.php'; // 引入 PA 的 index.php 文件

# 如果您需要 events 中配置监听对象，那么需要实现将对象注入到 $di 中
PA::$di->set('my_db',(new \Phalcon\Db\Adapter\PdoFactory())->newInstance('mysql',[
    'dbname'  => 'wy_db',
    'username'=> 'root',
    'password'=> '123456',
    'host'    => '192.168.9.223',
]));

$app->run(
    [
        'trace' => 1, // 打开调试
        'application' => __DIR__.'/..',
        'module_path' => __DIR__ .'/../modules',
        'domain_bind' => [
            'h5'=>'api,web',
            'www'=>'web',
            'api'=>'api',
            'in'=>'admin,api,web',
            'oa'=>'admin',
        ],
        'pa_url_path' => 'dashboard',
        'events'=>[
            // 'router:matchedRoute@router,db' => 'Txt::log',
            'db:beforeQuery@my_db' => function($a, $b){echo $b->getSQLStatement();}, # 监控自己的 db，需要事先注入到 $di 中
            //'db:beforeQuery@my_db,db' => 'Txt::log', # ok
            'db:beforeQuery' => 'Txt::log', # 监控 pa 的 db
            // 'dispatch:beforeException' => 'Txt::log',
            // 'application:boot' => 'Txt::log',
            // 'loader:beforeCheckClass' => 'Txt::log',
            // 'router:matchedRoute' => 'Txt::log',
        ],
    ]
);