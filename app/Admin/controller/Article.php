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

use \Despote;
use \despote\base\Controller;

class Article extends Controller
{
    public function add()
    {
        $category = $this->getModel()->getAllItem('`title`, `key`', 'category');

        // 视图参数
        $pageParams = [
            'category' => $category
        ];

        $this->render('post.add.html', $pageParams, 'child.html');
    }

    public function manage()
    {
        $pageParams = [];

        $this->render('post.manage.html', $pageParams, 'child.html');
    }

    public function edit()
    {
        $common = $this->getModel();

        $id = Despote::request()->get('id');
        if (!$common->verify($id)) {
            header('location: /Page/error.html');
            die;
        }

        $res = $common->getRecord('`aid` AS `id`, `title`, `category`, `content`, `cdate` AS `date`, `abstract`', '`article_list`', 'WHERE `aid` = ? LIMIT 1', [$id]);
        $result = $res->fetch();
        $result['date'] = date('Y-m-d H:i:s', $result['date']);

        $category = $this->getModel()->getAllItem('`title`, `key`', 'category');

        $pageParams = array_merge($result, ['categories' => $category]);

        $this->render('post.edit.html', $pageParams, 'child.html');
    }
}
