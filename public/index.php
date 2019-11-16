<?php
use Phalcon\Loader;

# 设置全局常量
define('POWER_BASE_DIR', realpath(__DIR__ . '/../').'/');
define('POWER_VIEW_DIR', POWER_BASE_DIR.'views/templates/');
define('POWER_DIST_DIR', POWER_BASE_DIR.'public/dist/');
define('SINGLE_POWER',   realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__);
define('POWER_DATA',     POWER_BASE_DIR.'data/');

# 初始化全局对象
$loader = new Loader();
$loader->registerDirs([POWER_BASE_DIR.'library']);
$loader->register();

$app = new Power\App();

# 单系统则直接Run，否则返回对象
if(!SINGLE_POWER) return $app;
$app->init()->run();