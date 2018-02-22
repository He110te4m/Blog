<?php
/*
 *    ____                        _
 *   |  _ \  ___  ___ _ __   ___ | |_ ___
 *   | | | |/ _ \/ __| '_ \ / _ \| __/ _ \
 *   | |_| |  __/\__ \ |_) | (_) | ||  __/
 *   |____/ \___||___/ .__/ \___/ \__\___|
 *                   |_|
 * @author      He110 (i@he110.top)
 * @namespace   app\Admin\controller
 */
namespace app\Admin\controller;

use \Despote;
use \despote\base\Controller;

class Index extends Controller
{
    public function index()
    {
        $db    = Despote::sql();
        $cache = Despote::fileCache();

        $sid1 = $cache->get('sid');
        $sid2 = Despote::cookie()->get('sid');

        if ($sid1 === false || $sid1 != $sid2) {
            header('location: /Admin/User/login');
            die;
        }

        // 网站标题
        $res   = $db->select('`val`', '`setting`', 'WHERE `key` = ? LIMIT 1', ['title']);
        $title = $db->fetch($res)['val'];

        $pageParams = [
            'title' => $title,
        ];

        $this->render('index.html', $pageParams);
    }

    public function main()
    {
        echo "默认后台首页";
    }
}
