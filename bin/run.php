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
    'help'   => 'h|help;bool'
];

# 参数描述
$desc = [
    'uri'     => 'Alias to $_SERVER["REQUEST_URI"]',
    'module'  => 'Module',
    'server'  => 'Alias to $_SERVER["SERVER_NAME"]',
    'config'  => 'Config file',
    'help'    => 'Show the help'
];

# 获得参数
Shell::args(array_slice($argv,1));
Shell::rules($rule);
Shell::description($desc);

if(!(realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__)) return $app;
PA::$args = Shell::parse();
$app->run(PA::$args['config']);