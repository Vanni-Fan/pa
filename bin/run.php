<?php
$loader = new Phalcon\Loader();
$loader->registerDirs([__DIR__.'/../library']);

$app  = require __DIR__ . '/../public/index.php';

# 参数
$rule = [
    'uri'    => 'u|uri;default:/index/index/index',
    'module' => 'm|module',
    'config' => 'c|config;default:/var/www/pa/data/config.php',
    'server' => 's|server',
];

# 参数描述
$desc = [
    'uri'     => 'Alias to $_SERVER["REQUEST_URI"]',
    'module'  => 'Module',
    'server'  => 'Alias to $_SERVER["SERVER_NAME"]',
    'config'  => 'Config file'
];

# 获得参数
PA::$args = Shell::arguments($argv,$rule);

try{
    $app->run(PA::$args['config']);
}catch (Throwable $e){
    $msg = "\n发生错误:\n".print_r($e->getTraceAsString(),1)."\n";
    echo Shell::getColorText($msg,31,43);
    echo Shell::getColorText("\n",37,40);
    echo Shell::showhelp($rule,$desc);
}