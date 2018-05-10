<?php
/*
 *    ____                        _
 *   |  _ \  ___  ___ _ __   ___ | |_ ___
 *   | | | |/ _ \/ __| '_ \ / _ \| __/ _ \
 *   | |_| |  __/\__ \ |_) | (_) | ||  __/
 *   |____/ \___||___/ .__/ \___/ \__\___|
 *                   |_|
 * @author      He110 (i@he110.info)
 * @namespace   app\Admin\controller
 */
namespace app\Admin\controller;

use \despote\base\Controller;
use \Despote;

class Index extends Controller
{
    public function init()
    {
        if ($this->getModel()->check() === false) {
            header('location: /Admin/User/login.html');
            die;
        }
    }

    public function index()
    {
        // 视图参数
        $pageParams = [
            'title'     => $this->getModel()->getItem('site_name'),
            'sub_title' => '后台首页',
        ];

        $this->render('index.html', $pageParams);
    }
}
