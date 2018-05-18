<?php
/*
 *    ____                        _
 *   |  _ \  ___  ___ _ __   ___ | |_ ___
 *   | | | |/ _ \/ __| '_ \ / _ \| __/ _ \
 *   | |_| |  __/\__ \ |_) | (_) | ||  __/
 *   |____/ \___||___/ .__/ \___/ \__\___|
 *                   |_|
 * 自定义需要加载的组件，核心组件如未注册会根据默认配置自动加载
 * @author      He110 (i@he110.info)
 */

return [
    // MySQL
    'sql'    => [
        'class' => '\despote\kernel\db\MySQL',

        // // 数据库地址，默认为 localhost
        // 'host'  => 'localhost',

        // // 数据库端口，默认为 3306
        // 'port'  => 3306,

        // // 数据库用户名，默认为 root
        // 'usr'   => 'root',

        // // 数据库密码，默认为 root
        // 'pwd'   => 'root',

        // // 数据库名，默认为 test
        // 'name'  => 'test',
    ],
    // 日志记录
    'logger' => [
        'class' => '\despote\kernel\Logger',
        // 日志等级
        'limit' => 5,
    ],
    'md'       => '\despote\extend\Parsedown',
];
