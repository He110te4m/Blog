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
    public function index()
    {
        // 获取通用模型
        $common = $this->getModel();

        // 获取网站标题
        $title = $common->getItem('site_name');

        // 视图参数
        $pageParams = [
            'title'     => $title,
            'sub_title' => '后台首页',
        ];

        $this->render('index.html', $pageParams);
    }
}
