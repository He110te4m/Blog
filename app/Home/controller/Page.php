<?php
/*
 *    ____                        _
 *   |  _ \  ___  ___ _ __   ___ | |_ ___
 *   | | | |/ _ \/ __| '_ \ / _ \| __/ _ \
 *   | |_| |  __/\__ \ |_) | (_) | ||  __/
 *   |____/ \___||___/ .__/ \___/ \__\___|
 *                   |_|
 * @author      He110 (i@he110.info)
 * @namespace   app\Home\controller
 */
namespace app\Home\controller;

use \despote\base\Controller;
use \Despote;

class Page extends Controller
{
    public function error()
    {
        // 获取通用模型
        $common = $this->getModel();

        // 网站名
        $title = $common->getItem('site_name');
        // 文章分类
        $category = $common->getAllItem('`title`, `key`', 'category');

        // 视图参数
        $pageParams = [];
        // 布局参数
        $layoutParams = [
            'title'     => $title,
            'category'  => $category,
            'sub_title' => '页面走丢了',
        ];

        $this->render('404.html', $pageParams, 'home.html', $layoutParams);
    }

    public function link()
    {
        // 获取通用模型
        $common = $this->getModel();

        // 友情链接
        $link = $common->getAllItem('`url`, `avatar`, `name`, `desc`', '`link`');
        // 网站名
        $title = $common->getItem('site_name');
        // 文章分类
        $category = $common->getAllItem('`title`, `key`', '`category`');

        // 视图参数
        $pageParams = [
            'link' => $link,
        ];
        // 布局参数
        $layoutParams = [
            'title'     => $title,
            'category'  => $category,
            'sub_title' => '友情链接',
        ];

        $this->render('link.html', $pageParams, 'home.html', $layoutParams);
    }

    public function about()
    {
        // 获取通用模型
        $common = $this->getModel();

        // 网站名
        $title = $common->getItem('site_name');
        // 文章分类
        $category = $common->getAllItem('`title`, `key`', '`category`');

        // 视图参数
        $pageParams = [];
        // 布局参数
        $layoutParams = [
            'title'     => $title,
            'category'  => $category,
            'sub_title' => '关于我',
        ];

        $this->render('about.html', $pageParams, 'home.html', $layoutParams);
    }
}
