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

class Cate extends Controller
{
    public function detail()
    {
        // 获取通用模型
        $common = $this->getModel();
        // 获取请求对象
        $http = Despote::request();

        // 判断请求合法性
        $key = $http->get('key');
        if (!$common->verify($key)) {
            header('location: /Page/error.html');
            die;
        }

        // 分页获取数据
        $page  = $http->get('page', 1);
        $limit = $http->get('limit', 10);
        $start = ceil(($page - 1) / $limit);

        // 网站名
        $title = $common->getItem('site_name');
        // 文章分类
        $category = $common->getAllItem('`title`, `key`', 'category');
        // 需要显示的分类
        $cate = $common->getRecord('`cid`, `key`, `desc`, `title`', 'category', 'WHERE `key` = ? LIMIT 1', [$key])->fetch();
        $cid = $cate['cid'];
        unset($cate['cid']);
        // 获取文章列表
        $article = $common->getRecord('`aid` AS `id`, `title`, `cdate` AS `date`, `abstract`, `comment_num`', '`article`', "WHERE `cid` = ? ORDER BY `cdate` DESC LIMIT {$start}, {$limit}", [$cid])->fetchAll();
        foreach ($article as &$item) {
            $item['date'] = date('Y-m-d', $item['date']);
        }
        // 页码
        $count = $common->getCount('`article`', 'WHERE `cid` = ?', [$cid]);
        $count = ceil($count / $limit);

        // 视图参数
        $pageParams = [
            'cate'    => $cate,
            'next'    => $page + 1,
            'count'   => $count,
            'article' => $article,
        ];
        // 布局参数
        $layoutParams = [
            'title'     => $title,
            'category'  => $category,
            'sub_title' => $cate['title'],
        ];

        $this->render('cate.html', $pageParams, 'home.html', $layoutParams);
    }
}
