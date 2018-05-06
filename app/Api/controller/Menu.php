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

use \despote\base\Controller;

class Menu extends Controller
{
    public function get()
    {
        $data = [
            [
                'id' => '1',
                'name' => '仪表盘',
                'parentId' => '0',
                'url' => '',
                'icon' => '',
                'order' => '1',
                'isHeader' => '1',
            ], [
                'id' => '2',
                'name' => '文章模块',
                'parentId' => '0',
                'url' => '',
                'icon' => '',
                'order' => '1',
                'isHeader' => '0',
                'childMenus' => [
                    [
                        'id' => '21',
                        'name' => '文章发布',
                        'parentId' => '2',
                        'url' => '/Admin/Article/add.html',
                        'icon' => '&#xe604;',
                        'order' => '1',
                        'isHeader' => '0',
                        'childMenus' =>'',
                    ], [
                        'id' => '22',
                        'name' => '文章管理',
                        'parentId' => '2',
                        'url' => '/Admin/Article/manage.html',
                        'icon' => '&#xe602;',
                        'order' => '1',
                        'isHeader' => '0',
                        'childMenus' =>'',
                    ]
                ]
            ], [
                'id' => '3',
                'name' => '分类模块',
                'parentId' => '0',
                'url' => '',
                'icon' => '',
                'order' => '1',
                'isHeader' => '0',
                'childMenus' => [
                    [
                        'id' => '31',
                        'name' => '新增分类',
                        'parentId' => '3',
                        'url' => '/Admin/Category/add.html',
                        'icon' => '&#xe604;',
                        'order' => '1',
                        'isHeader' => '0',
                        'childMenus' =>'',
                    ], [
                        'id' => '32',
                        'name' => '分类管理',
                        'parentId' => '3',
                        'url' => '/Admin/Category/manage.html',
                        'icon' => '&#xe602;',
                        'order' => '1',
                        'isHeader' => '0',
                        'childMenus' =>'',
                    ]
                ]
            ]
        ];

        $this->render('api.php', ['data' => $data]);
    }
}
