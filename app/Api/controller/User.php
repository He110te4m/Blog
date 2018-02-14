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

use \Despote;
use \despote\base\Controller;

class User extends Controller
{
    private static $map = [
        0 => '登陆成功',
        1 => '请求登陆失败',
        2 => '数据库连接出错',
        3 => '身份验证错误',
    ];

    public function login()
    {
        $time = time();
        $key  = Despote::request()->post('key');

        if (is_null($key)) {
            $code = 1;
        } else {
            try {
                $res  = Despote::sql()->select('`val`', '`setting`', 'WHERE `key` = ? LIMIT 1', ['key']);
                $pass = $res->fetch()['val'];
                $pass = floor(($time - $pass) / 30);
                if ($pass == $key) {
                    $code = 0;
                    Despote::cookie()->set('sid', md5('He110' . $time), 86400);
                    Despote::fileCache()->set('sid', md5('He110' . $time), 86400);
                } else {
                    $code = 3;
                }
            } catch (\Exception $e) {
                $code = 2;
            }
        }

        $pageParams = [
            'data' => [
                'code' => $code,
                'msg'  => self::$map[$code],
            ],
        ];

        $this->render('api.php', $pageParams);
    }

    public function update()
    {
        $key  = Despote::request()->post('key');
        $sid1 = Despote::cookie()->get('sid');
        $sid2 = Despote::fileCache()->get('sid');
        if (is_null($key)) {
            $code = 1;
        } else if ($sid2 === false || $sid1 != $sid2) {
            $code = 3;
        } else {
            $code = 0;
            try {
                Despote::sql()->update('`setting`', '`val` = ?', 'WHERE `key` = ? LIMIT 1', [$key, 'key']);
            } catch (\Exception $e) {
                $code = 2;
            }
        }

        $pageParams = [
            'data' => [
                'code' => $code,
                'msg'  => self::$map[$code],
            ],
        ];

        $this->render('api.php', $pageParams);
    }
}
