<?php
# 管理系统框架本身的配置
return [
    # 调试开关
    'debug'       => 1,
    'trace'       => 0,

    # 应用的基础目录
    'application' => POWER_BASE_DIR,

    # 多模块设置
    'modules' => null, // 指向一个目录，目录下的子目录会被认为是模块，每一个模块下面应该有完整mvc（controllers：必须，models：可选，views:可选）

    # 模块的域名绑定
    'root_domain'=>null, // 根域名，用于子域名的拼接，如果没有，子域名则使用去掉第一段的 $_SERVER['SERVER_NAME']
    'domain_bind'=>[],   // [ 域名|子域名|* => 模块名 ], 比如 ['www'=>'web','www.aa.com'=>'web','*'=>'web'] 等

    # 用户的同步方法
    'user_handler'=>null, // 如果有用户的配置，在PA登录时，会回调此方法，必须是 \Power\HandlerPAUserAbs 的子类

    # 额外的路由配置
    'routers'=>[
        '*'=>[
            '/dist/:params'  => [
                'namespace'  => 'Power\Controllers',
                'controller' => 'resource',
                'action'     => 'render',
                'priority'   => 10,
            ], # 显示资源文件
        ],
    ],
//    'routers'=>[
//        '*'=>[ // 可以指定 POST, GET, DELETE, PATCH 请求方法，也可使用 * 表示通配所有方法
//            '正则表达式'=>[ // 可以是一个具体的URL，也可以是一个正则表达式
//                'module'     => 'web', // 默认为空
//                'namespace'  => 'web\Controllers', // 默认 \
//                'controller' => 'activities', // 默认 index
//                'action'     => 'index', // 默认 index
//                'params'     => 1, // 默认无
//                'priority'   => 10, // 默认0，优先级，Phalcon的路由为后加入的优先，所以为了避免替换，可以设置一个比较到的优先级
//            ]
//        ]
//    ];

    # Power Admin 的基础URL配置
    'pa_url_path' => '/admin/',

    # Power Admin 的数据库配置
    'pa_db'       => [
        'adapter' => 'sqlite',
        'dbname'  => POWER_DATA . '/powerdb.sql3.db',
//        'prefix'  => 'pa_'
    ],

    'pa_db'       => [
        'adapter' => 'mysql',
        'dbname'  => 'pa',
        'username'=> 'root',
        'password'=> '123456',
        'host'=>'mysql',
        'prefix'  => 'pa_'
    ],

    # Power Admin 的Cookie加密Key
    'cookie_key'    => file_get_contents(POWER_DATA .'/cookie.key'),
    'cookie_cipher' => 'aes-192-cbc',
    'cookie_name'   => 'admin_token',
    'cookie_fields' => ['user_id'=>4, 'login_time'=>4], // 必须保留 user_id, 'login_ip'=>8,只支持64为，暂时去除
    'cookie_maker'  => 'Power\\Models\\Users::makeToke',  // 需要返回对应的Token
    'cookie_parser' => 'Power\\Models\\Users::parseToken',// 解析Token

    # 错误处理函数
    'error' => [
        'handler'   => 'Power\\Controllers\\ErrorController::handlerError',
        'exception' => 'Power\\Controllers\\ErrorController::handlerException',
        'controller'=> 'Power\\Controllers\\ErrorController',
    ]
];