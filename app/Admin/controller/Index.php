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

        // 网站标题
        $title = $cache->get('title');
        if ($title === false) {
            $res   = $db->select('`val`', '`setting`', 'WHERE `key` = ? LIMIT 1', ['title']);
            $title = $res->fetch()['val'];
            $cache->set('title', $title, 28800);
        }

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
