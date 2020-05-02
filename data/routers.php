<?php
# 管理系统框架本身的路由配置
$pa_url_path = PA::$config->pa_url_path ?? '/admin/';
$pa_url_path = '/'.trim($pa_url_path,'/').'/';
$__ROUTERS = [
    'POST' => [
        $pa_url_path.'login' => [
            'namespace'  => 'Power\Controllers',
            'controller' => 'authorization',
            'action'     => 'login',
            'priority'  => 10,
        ], # 登录
        $pa_url_path.'menu/{menu_id:[0-9]+}/item/:params' =>[
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'append',
            'params'     => 2,
            'priority'  => 10,
        ], # 添加的动作
        $pa_url_path.'menu/{menu_id:[0-9]+}/item/new/:params' =>[
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'append',
            'params'     => 2,
            'priority'  => 10,
        ], # 添加的动作
        $pa_url_path.'menu/{menu_id:[0-9]+}/item/{item_id:[0-9]+}/:params' =>[
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'update',
            'priority'  => 10,
            'params'     => 3
        ], # 修改动作
        $pa_url_path.'menu/{menu_id:[0-9]+}/item/{item_id:[0-9,]+}/delete/:params' =>[
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'delete',
            'priority'   => 20,
            'params'     => 3
        ], # 删除的POST形式
        $pa_url_path.'menu/{menu_id:[0-9]+}/items/:params' => [
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'list',
            'priority'  => 10,
            'params'     => 2
        ],         # 首页列表页面
    ],
    'GET' => [
        $pa_url_path => [
            'namespace'  => 'Power\Controllers',
            'controller' => 'index',
            'action'     => 'index',
            'priority'  => 10,
        ],
        $pa_url_path.'login'      => [
            'namespace'  => 'Power\Controllers',
            'controller' => 'authorization',
            'action'     => 'loginPage',
            'priority'  => 10,
        ], # 显示登录页面
        $pa_url_path.'logout'     => [
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
        $pa_url_path.'menu/{menu_id:[0-9]+}/index/:params' => [
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'index',
            'priority'  => 10,
            'params'     => 2
        ],         # 首页列表页面
        $pa_url_path.'menu/{menu_id:[0-9]+}/items/:params' => [
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'list',
            'priority'  => 10,
            'params'     => 2
        ],         # 首页列表页面
        $pa_url_path.'menu/{menu_id:[0-9]+}/item/new/:params' =>[
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'new',
            'priority'  => 10,
            'params'     => 2
        ],              # 添加Item的页面
        $pa_url_path.'menu/{menu_id:[0-9]+}/item/{item_id:[0-9]+}/:params' =>[
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'display',
            'priority'  => 10,
            'params'     => 3
        ], # 显示Item页面
        $pa_url_path.'menu/{menu_id:[0-9]+}/item/{item_id:[0-9,]+}/delete/:params' =>[
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'delete',
            'priority'  => 10,
            'params'     => 3
        ], # 删除的Get形式
    ],
    'DELETE' => [
        $pa_url_path.'menu/{menu_id:[0-9]+}/item/{item_id:[0-9,]+}/:params' =>[
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'delete',
            'priority'  => 10,
            'params'     => 3
        ], # 删除的 DELETE 形式
    ],
    'PUT' => [  
        $pa_url_path.'menu/{menu_id:[0-9]+}/item/:params' =>[
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'append',
            'priority'  => 10,
            'params'     => 2
        ], # 添加的动作
        $pa_url_path.'menu/{menu_id:[0-9]+}/item/{item_id:[0-9]+}/:params' =>[
            'namespace'  => 'Power\Controllers',
            'controller' => 'Router',
            'action'     => 'update',
            'priority'  => 10,
            'params'     => 3
        ], # 修改动作
    ]
];

if(substr($pa_url_path,-1) === '/') {
    $__ROUTERS['GET'][substr($pa_url_path,0,-1)] = [
        'namespace'  => 'Power\Controllers',
        'controller' => 'index',
        'action'     => 'index',
        'priority'  => 10,
    ];
}

return $__ROUTERS;