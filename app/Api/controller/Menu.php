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

class Menu extends Controller
{
    public function nav()
    {
        $data = [[
            "title"  => "后台首页",
            "icon"   => "icon-computer",
            "href"   => "/Admin/Index/main",
            "spread" => false,
        ], [
            "title"    => "文章模块",
            "icon"     => "icon-wenben",
            "href"     => "",
            "spread"   => false,
            "children" => [
                [
                    'title'  => '发表文章',
                    'icon'   => 'icon-edit',
                    'href'   => '/Admin/Article/add',
                    'spread' => false,
                ], [
                    'title'  => '文章管理',
                    'icon'   => 'icon-text',
                    'href'   => '/Admin/Article/manage',
                    'spread' => false,
                ],
            ],
        ], [
            "title"  => "分类管理",
            "icon"   => "&#xe705;",
            "href"   => "/Admin/Category/manage",
            "spread" => false,
        ], [
            "title"  => "友情链接",
            "icon"   => "&#xe64c;",
            "href"   => "/Admin/Link/manage",
            "spread" => false,
        ]];

        $this->render('api.php', ['data' => $data]);
    }
}
