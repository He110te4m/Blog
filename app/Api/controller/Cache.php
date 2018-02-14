<?php
/*
 *    ____                        _
 *   |  _ \  ___  ___ _ __   ___ | |_ ___
 *   | | | |/ _ \/ __| '_ \ / _ \| __/ _ \
 *   | |_| |  __/\__ \ |_) | (_) | ||  __/
 *   |____/ \___||___/ .__/ \___/ \__\___|
 *                   |_|
 * @author      He110 (i@he110.top)
 * @namespace   app\Api\controller
 */
namespace app\Api\controller;

use \despote\base\Controller;

class Cache extends Controller
{
    private static $map = [
        0 => '刷新缓存成功',
        1 => '请求失败',
    ];

    public function flush()
    {
        \Despote::fileCache()->flush();

        $pageParams = [
            'data' => [
                'code' => 0,
                'msg'  => '刷新缓存成功',
            ],
        ];

        $this->render('api.php', $pageParams);
    }
}
