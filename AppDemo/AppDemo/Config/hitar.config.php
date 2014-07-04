<?php
return array(
    'database'  =>  array(
        'master'    =>  array(
            'type'    =>  'mysql',
            'host'      =>  'localhost',    //数据库主机
            'user'      =>  'user',         //数据库用户名
            'password'  =>  '',             //数据库密码
            'name'      =>  'test',             //数据库名称
            'charset'   =>  'utf8'          //编码
        )
    ),
    //'default'   =>  'master',    //默认使用的数据库,读写不分离
    //'tb_prefix' =>  ''              //数据表前缀
    //'default'   =>  'master,master'    //默认使用的数据库,读写分离
);