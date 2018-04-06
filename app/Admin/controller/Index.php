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

use \despote\base\Controller;

class Index extends Controller
{
    public function init()
    {
        if ($this->getModel()->check() === false) {
            header('location: /404.html');
            die;
        }
    }

    public function index()
    {
        // 网站标题
        $title = $this->getModel()->getSetting('title');

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
