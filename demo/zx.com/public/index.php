<?php
//class Txt{
//    static function log(...$data){
//        $event = array_shift($data);
//        switch($type = $event->getType()){
//            case 'beforeCheckRoute':
//            case 'notMatchedRoute':
//            case 'matchedRoute':
//                error_log("\n事件类型：".$type."; Route：[".$data[1]->getPattern().']',3,'/tmp/log.txt');
//                break;
//            case 'beforeCheckRoutes':
//            default:
//                $sources = [];
//                foreach($data as $source) $sources[] = is_object($source) ? get_class($source) : print_r($source,1);
//                error_log("\n\n事件类型：".$type."; 源于：[".implode(', ',$sources).']',3,'/tmp/log.txt');
//        }
//    }
//}

$app = include '/var/www/html/public/index.php'; // 引入 PA 的 index.php 文件

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
//        'event'=>[
//            'handler' => 'Txt::log',
//            'events'=>[
//                'router:matchedRoute',
//                'router:beforeCheckRoutes',
//                'router:beforeCheckRoute',
//                'router:notMatchedRoute',
//                'dispatch:beforeException',
//                'application:boot',
//                'loader:beforeCheckClass',
//                'router:matchedRoute',
//            ],
//        ]
    ]
);