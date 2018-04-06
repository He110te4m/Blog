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

class Article extends Controller
{
    public function init()
    {
        if ($this->getModel()->check() === false) {
            header('location: /404.html');
            die;
        }
    }

    public function add()
    {
        $categories = $this->getModel()->getAllData('`title`', '`category`');

        $pageParams = [
            'categories' => $categories,
        ];

        $this->render('article-add.html', $pageParams);
    }

    public function manage()
    {
        $this->render('article-manage.html');
    }

    public function edit()
    {
        $common = $this->getModel();

        // 校验 URL 参数
        $id = Despote::request()->get('id');
        if (!$common->verify($id)) {
            header('location: /404.html');
            die;
        }

        // 获取文章数据
        $res    = $common->getRecord('`aid` AS `id`, `title`, `category`, `content`, `cdate` AS `date`', '`article_view`', 'WHERE `aid` = ? LIMIT 1', [$id]);
        $result = $res->fetch();
        // 解压缩文章内容
        $result['content'] = gzuncompress($result['content']);

        // 获取分类列表
        $categories = $common->getAllData('`title`', '`category`');

        // 规范化的传递数据给视图
        $pageParams = $result;
        // 补全需要传递的数据
        $pageParams['categories'] = $categories;

        $this->render('article-edit.html', $pageParams);
    }
}
