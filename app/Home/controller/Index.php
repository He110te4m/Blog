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

class Index extends Controller
{
    public function index()
    {
        // 获取通用模型
        $common = $this->getModel();

        // 获取参数
        $http = Despote::request();
        // 页码
        $page = $http->get('page', 1);
        // 每页显示的条目
        $limit = $http->get('limit', 10);
        // SQL 查询时起始位置
        $start = ($page - 1) * $limit;

        $keyword = $http->post('keyword');

        // 网站名
        $title = $common->getItem('site_name');
        // 网站描述信息
        $desc = $common->getItem('desc');
        // 社交链接
        $social = $common->getAllItem('`title`, `icon`, `url`', 'social');
        // 文章分类
        $category = $common->getAllItem('`title`, `key`', 'category');
        // 文章列表
        if ($common->verify($keyword)) {
            // 校验参数防止 SQL 注入
            $pattern = '/(select[\s])|(insert[\s])|(update[\s])|(delete[\s])|(from[\s])|(where[\s])|(drop[\s])/i';
            if (preg_match($pattern, $keyword)) {
                header('location: /Page/error.html');
                die;
            }

            $article = $common->getRecord('`a`.`aid` AS `id`, `a`.`title`, `a`.`cdate` AS `date`, `a`.`abstract`, `a`.`comment_num`, `c`.`key`, `c`.`title` AS `category`', '`article` AS `a`, `category` AS `c`', "WHERE `a`.`cid` = `c`.`cid` AND `a`.`title` LIKE '%{$keyword}%' ORDER BY `a`.`cdate` DESC LIMIT {$start}, {$limit}");
            // 取总页数
            $count = $common->getCount('`article`', "WHERE `title` LIKE '%{$keyword}%'");
        } else {
            $article = $common->getRecord('`a`.`aid` AS `id`, `a`.`title`, `a`.`cdate` AS `date`, `a`.`abstract`, `a`.`comment_num`, `c`.`key`, `c`.`title` AS `category`', '`article` AS `a`, `category` AS `c`', "WHERE `a`.`cid` = `c`.`cid` ORDER BY `a`.`cdate` DESC LIMIT {$start}, {$limit}");
            // 取总页数
            $count = $common->getCount('`article`');
        }
        // 处理文章和页码数据
        if ($article !== false) {
            $article = $article->fetchAll();
            foreach ($article as &$item) {
                $item['date'] = date('Y-m-d', $item['date']);
            }
        }
        $count = ceil($count / $limit);

        // 视图参数
        $pageParams = [
            'desc'    => $desc,
            'next'    => $page + 1,
            'title'   => $title,
            'count'   => $count,
            'social'  => $social,
            'article' => $article,
        ];
        // 布局参数
        $layoutParams = [
            'title'    => $title,
            'category' => $category,
        ];

        $this->render('index.html', $pageParams, 'home.html', $layoutParams);
    }
}
