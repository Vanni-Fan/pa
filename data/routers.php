<?php
# 管理系统框架本身的路由配置
$routers = [
    'POST' => [
        PA_URL_PATH.'login' => [
            'namespace'  => 'Power\Controllers',
            'controller' => 'authorization',
            'action'     => 'login',
            'priority'  => 10,
        ], # 登录
        PA_URL_PATH.'menu/{menu_id:[0-9]+}/item/:params' =>[
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'append',
            'params'     => 2,
            'priority'  => 10,
        ], # 添加的动作
        PA_URL_PATH.'menu/{menu_id:[0-9]+}/item/new/:params' =>[
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'append',
            'params'     => 2,
            'priority'  => 10,
        ], # 添加的动作
        PA_URL_PATH.'menu/{menu_id:[0-9]+}/item/{item_id:[0-9]+}/:params' =>[
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'update',
            'priority'  => 10,
            'params'     => 3
        ], # 修改动作
        PA_URL_PATH.'menu/{menu_id:[0-9]+}/item/{item_id:[0-9,]+}/delete/:params' =>[
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'delete',
            'priority'   => 20,
            'params'     => 3
        ], # 删除的POST形式
        PA_URL_PATH.'menu/{menu_id:[0-9]+}/items/:params' => [
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'list',
            'priority'  => 10,
            'params'     => 2
        ],         # 首页列表页面
    ],
    'GET' => [
        PA_URL_PATH => [
            'namespace'  => 'Power\Controllers',
            'controller' => 'index',
            'action'     => 'index',
            'priority'  => 10,
        ],
        PA_URL_PATH.'login'      => [
            'namespace'  => 'Power\Controllers',
            'controller' => 'authorization',
            'action'     => 'loginPage',
            'priority'  => 10,
        ], # 显示登录页面
        PA_URL_PATH.'logout'     => [
            'namespace'  => 'Power\Controllers',
            'controller' => 'authorization',
            'action'     => 'logout',
            'priority'  => 10,
        ], # 登出
        '/dist/:params' => [
            'namespace'  => 'Power\Controllers',
            'controller' => 'resource',
            'action'     => 'render',
            'priority'  => 10,
        ], # 显示资源文件
        PA_URL_PATH.'menu/{menu_id:[0-9]+}/index/:params' => [
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'index',
            'priority'  => 10,
            'params'     => 2
        ],         # 首页列表页面
        PA_URL_PATH.'menu/{menu_id:[0-9]+}/items/:params' => [
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'list',
            'priority'  => 10,
            'params'     => 2
        ],         # 首页列表页面
        PA_URL_PATH.'menu/{menu_id:[0-9]+}/item/new/:params' =>[
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'new',
            'priority'  => 10,
            'params'     => 2
        ],              # 添加Item的页面
        PA_URL_PATH.'menu/{menu_id:[0-9]+}/item/{item_id:[0-9]+}/:params' =>[
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'display',
            'priority'  => 10,
            'params'     => 3
        ], # 显示Item页面
        PA_URL_PATH.'menu/{menu_id:[0-9]+}/item/{item_id:[0-9,]+}/delete/:params' =>[
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'delete',
            'priority'  => 10,
            'params'     => 3
        ], # 删除的Get形式
    ],
    'DELETE' => [
        PA_URL_PATH.'menu/{menu_id:[0-9]+}/item/{item_id:[0-9,]+}/:params' =>[
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'delete',
            'priority'  => 10,
            'params'     => 3
        ], # 删除的 DELETE 形式
    ],
    'PUT' => [  
        PA_URL_PATH.'menu/{menu_id:[0-9]+}/item/:params' =>[
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'append',
            'priority'  => 10,
            'params'     => 2
        ], # 添加的动作
        PA_URL_PATH.'menu/{menu_id:[0-9]+}/item/{item_id:[0-9]+}/:params' =>[
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'update',
            'priority'  => 10,
            'params'     => 3
        ], # 修改动作
    ]
];

if(substr(PA_URL_PATH,-1) === '/') {
    $routers['GET'][substr(PA_URL_PATH,0,-1)] = [
        'namespace'  => 'Power\Controllers',
        'controller' => 'index',
        'action'     => 'index',
        'priority'  => 10,
    ];
}

return $routers;