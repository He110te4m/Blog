<?php
/*
 *    ____                        _
 *   |  _ \  ___  ___ _ __   ___ | |_ ___
 *   | | | |/ _ \/ __| '_ \ / _ \| __/ _ \
 *   | |_| |  __/\__ \ |_) | (_) | ||  __/
 *   |____/ \___||___/ .__/ \___/ \__\___|
 *                   |_|
 * @author      He110 (i@he110.top)
 * @namespace   app\Home\controller
 */
namespace app\Home\controller;

use \Despote;
use \despote\base\Controller;

class Index extends Controller
{
    public function index()
    {
        $db     = Despote::sql();
        $common = $this->getModel();
        $http   = Despote::request();
        $cache  = Despote::fileCache();

        $cate    = $http->get('cate');
        $page    = $http->get('page', 1);
        $keyword = $http->get('keyword');

        $limit  = 15;
        $params = '?';
        $start  = ($page - 1) * $limit;

        if ($common->verify($cate) || $common->verify($keyword)) {
            if ($common->verify($cate)) {
                // 获取分类 id
                $res = $common->getRecord('`cid`', '`category`', 'WHERE `title` = ?', [trim($cate)]);
                $cid = $res->fetch()['cid'];

                // 获取分类下的文章列表
                $res       = $common->getRecord('`aid`, `title`, `cdate` AS `date`', '`article`', "WHERE `cid` = ? ORDER BY `cdate` DESC LIMIT {$start}, {$limit}", [$cid]);
                $post_list = $res->fetchAll();

                // 获取分类下文章总数
                $count = $common->getCount('`article`', 'WHERE `cid` = ?', [$cid]);

                // 传递参数给分页
                $params .= '&cate=' . $cate;
            }

            if ($common->verify($keyword)) {
                // 获取符合搜索结果的文章列表
                $res       = $common->getRecord('`aid`, `title`, `cdate` AS `date`', '`article`', "WHERE `title` LIKE '%{$keyword}%' ORDER BY `cdate` DESC LIMIT {$start}, {$limit}");
                $post_list = $res->fetchAll();

                // 获取符合搜索结果的文章总数
                $count = $common->getCount('`article`', "WHERE `title` LIKE '%{$keyword}%'");

                // 传递参数给分页
                $params .= '&keyword=' . $keyword;
            }
        } else {
            $res       = $common->getRecord('`aid`, `title`, `cdate` AS `date`', '`article`', "ORDER BY `cdate` DESC LIMIT {$start}, {$limit}");
            $post_list = $res->fetchAll();
            $count     = $common->getCount('`article`');
        }

        foreach ($post_list as &$post) {
            $post['date'] = $common->formatDate($post['date']);
        }

        $pageCount = ceil($count / $limit);

        if ($page < 2) {
            $prev = '<li><a class="disable"><</a></li>';
        } else {
            $prev = '<li><a href="' . $params . '&page=' . ($page - 1) . '"><</a></li>';
        }

        if ($pageCount == $page || $pageCount < 2) {
            $next = '<li><a class="disable">></a></li>';
        } else {
            $next = '<li><a href="' . $params . '&page=' . ($page + 1) . '">></a></li>';
        }

        $pageParams = [
            'list'   => $post_list,
            'prev'   => $prev,
            'curr'   => $page,
            'next'   => $next,
            'count'  => $pageCount,
            'params' => $params,
        ];

        // 网站标题
        $title = $common->getSetting('title');
        // 博主
        $name = $common->getSetting('name');
        // 社交链接
        $socials = $common->getAllData('`icon`, `url`', '`social`');
        // 友情链接
        $links = $common->getAllData('`title`, `url`', '`link`');
        // 所有分类
        $categories = $common->getAllData('`title`', '`category`');

        $layoutParams = [
            'cate'       => $common->verify($cate) ? $cate : 'index',
            'title'      => $title,
            'author'     => $name,
            'socials'    => $socials,
            'links'      => $links,
            'categories' => $categories,
        ];

        $this->render('index.html', $pageParams, 'default.html', $layoutParams);
    }
}
