<?php
return array(
    'view'  =>  array(
        'engine'    =>  'Smarty',   //模板引擎
        'default_version'   =>  ''   //默认视图版本
    ),
    //URL配置
    'url'   =>  array(
        //'mode'  =>  0,
        'suffix'    =>  '.html',
        'default_module'    =>  'home',
        'default_controller'   =>  'index',
        'default_action'    =>  'index',
        'route' => array(
            'default_all'   =>  array(
                'url'   =>  '',
                'type'  => \Hiano\Route\Route::TYPE_STATIC,
                'parameter' =>  array(
                    'module'    =>  'home',
                    'controller'    =>  'index',
                    'action'    =>  'index'
                )
            ),
            'default_module' => array(
                'url'   =>  ':module',
                'type'  => \Hiano\Route\Route::TYPE_STATIC,
                'parameter' =>  array(
                    'controller'    =>  'index',
                    'action'        =>  'index'
                )
            ),
            'default_module_controller' => array(
                'url'   =>  ':module/:controller',
                'type'  => \Hiano\Route\Route::TYPE_STATIC,
                'parameter' =>  array(
                    'action'        =>  'index'
                )
            ),
            'default'   =>  array(
                'url'   =>  ':module/:controller/:action',
                'type'  => \Hiano\Route\Route::TYPE_DYMATIC,
                //'parameter' => array(),
                //'requirement'   =>array()
            )
        )
    ),
    //'session_path'  =>  'database:session/id|data|dateline', //{null,database:table/field_id|field_data|field_dateline}
    //调试配置
    'debug' =>  array(
        'enable'    =>  true,
        'error_reporting'   =>  E_ALL & ~E_NOTICE
    ),
    'security'  =>  array(
        'csrf_defender' =>  true
    ),
    'cache' =>  array(
        'enable'    =>  FALSE,
        'default_type'  =>  'apc'
    ),
    'memcached' =>  array(
        'host'  =>  'localhost',
        'port'  =>  '11211'
    ),
    'timezone'  =>  'Asia/Shanghai' //默认时区
);
