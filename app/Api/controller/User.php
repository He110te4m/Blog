<?php
/*
 *    ____                        _
 *   |  _ \  ___  ___ _ __   ___ | |_ ___
 *   | | | |/ _ \/ __| '_ \ / _ \| __/ _ \
 *   | |_| |  __/\__ \ |_) | (_) | ||  __/
 *   |____/ \___||___/ .__/ \___/ \__\___|
 *                   |_|
 * @author      He110 (i@he110.info)
 * @namespace   app\Api\controller
 */
namespace app\Api\controller;

use \Despote;
use \despote\base\Controller;

class User extends Controller
{
    public function login()
    {
        // 初始化工具对象
        $common = $this->getModel();

        // 设置初值
        $time = time();
        $key  = Despote::request()->post('key');
        if ($common->verify($key)) {
            // 计算动态密码
            $pass = $common->getItem('key');
            $pass = md5(floor(($time - $pass) / 30));
            // 密码匹配
            if ($pass == $key) {
                $code = 0;
                // 设置登陆状态
                $sid  = md5('He110' . $time);
                Despote::cookie()->set('sid', $sid, 86400);
                Despote::fileCache()->set('sid', $sid, 86400);
            } else {
                $code = 3;
            }
        } else {
            $code = 1;
        }

        // 生成 data 数组
        $data = $common->getData($code);

        // 渲染 json
        $this->render('api.php', ['data' => $data]);
    }

    public function layout()
    {
        // 设置初值
        $code = 0;

        Despote::cookie()->del('sid');
        Despote::fileCache()->del('sid');

        // 生成 data 数组
        $data = $this->getModel()->getData($code);

        // 渲染 json
        $this->render('api.php', ['data' => $data]);
    }

    public function flush()
    {
        // 设置初值
        $code = 0;

        Despote::fileCache()->flush();

        // 生成 data 数组
        $data = $this->getModel()->getData($code);

        // 渲染 json
        $this->render('api.php', ['data' => $data]);
    }

    public function edit()
    {
        $common = $this->getModel();

        // 修改密码
        $new  = Despote::request()->post('key');
        if ($common->verify($new)) {
            $code = $common->updateRecord('`setting`', '`val` = ?', 'WHERE `key` = ?', [$new, 'key']);
        } else {
            $code = 1;
        }

        // 生成 data 数组
        $data = $common->getData($code);

        // 渲染 json
        $this->render('api.php', ['data' => $data]);
    }
}
