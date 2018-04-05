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
    public function login()
    {
        $time   = time();
        $common = $this->getModel();
        $key    = Despote::request()->post('key');

        if ($common->verify($key)) {
            list($code, $res) = $common->getRecord('`val`', '`setting`', 'WHERE `key` = ? LIMIT 1', ['key']);

            $pass = $res->fetch()['val'];
            $pass = md5(floor(($time - $pass) / 30));
            if ($pass == $key) {
                $code = 0;
                Despote::cookie()->set('sid', md5('He110' . $time), 86400);
                Despote::fileCache()->set('sid', md5('He110' . $time), 86400);
            } else {
                $code = 5;
            }
        } else {
            $code = 1;
        }

        $data = $common->getData($code);

        $this->render('api.php', ['data' => $data]);
    }

    public function update()
    {
        $common = $this->getModel();
        $key    = Despote::request()->post('key');

        if ($common->verify($key)) {
            if ($common->check() === false) {
                $code = 3;
            } else {
                $code = $common->updateRecord('`setting`', '`val` = ?', 'WHERE `key` = ? LIMIT 1', [$key, 'key']);
            }
        } else {
            $code = 1;
        }

        $data = $common->getData($code);

        $this->render('api.php', ['data' => $data]);
    }
}
