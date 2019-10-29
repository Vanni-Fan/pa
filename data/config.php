<?php
# 管理系统框架本身的配置
return [
    # 调试开关
    'debug'       => 1,
    'trace'       => 0,

    # 应用的基础目录
    'application' => POWER_BASE_DIR,

    # Power Admin 的基础URL配置
    'pa_url_path' => '/admin/',

    # Power Admin 的数据库配置
    'pa_db'       => [
        'adapter' => 'sqlite',
        'dbname'  => POWER_DATA . '/powerdb.sql3.db',
//        'prefix'  => 'pa_'
    ],

//    'pa_db'       => [
//        'adapter' => 'mysql',
//        'dbname'  => 'pa',
//        'username'=> 'root',
//        'password'=> '123456',
////        'prefix'  => 'pa_'
//    ],

    # Power Admin 的Cookie加密Key
    'cookie_key'    => file_get_contents(POWER_DATA .'/cookie.key'),
    'cookie_cipher' => 'aes-192-cbc',
    'cookie_name'   => 'admin_token',
    'cookie_fields' => ['user_id'=>4, 'login_time'=>4], // 必须保留 user_id, 'login_ip'=>8,只支持64为，暂时去除
    'cookie_maker'  => 'Power\\Models\\Users::makeToke',  // 需要返回对应的Token
    'cookie_parser' => 'Power\\Models\\Users::parseToken',// 解析Token
    'error' => [
        'handler'   => 'Power\\Controllers\\ErrorController::handlerError',
        'exception' => 'Power\\Controllers\\ErrorController::handlerException',
        'controller'=> 'Power\\Controllers\\ErrorController',
    ]
];