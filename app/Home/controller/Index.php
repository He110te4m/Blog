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
        $db    = Despote::sql();
        $http  = Despote::request();
        $cache = Despote::fileCache();

        $cate    = $http->get('cate');
        $page    = $http->get('page', 1);
        $keyword = $http->get('keyword');

        $start  = ($page - 1) * 20;
        $params = '?';

        if (is_null($cate) && is_null($keyword)) {
            $res       = $db->select('`aid`, `title`, `cdate` AS `date`', '`article`', "ORDER BY `cdate` DESC LIMIT {$start}, 20");
            $post_list = $res->fetchAll();

            $res   = $db->select('COUNT(1)', '`article`');
            $count = $res->fetch()['COUNT(1)'];
        } else if (is_null($keyword)) {
            $res = $db->select('`cid`', '`category`', 'WHERE `title` = ?', [trim($cate)]);
            $cid = $res->fetch()['cid'];

            $res       = $db->select('`aid`, `title`, `cdate` AS `date`', '`article`', "WHERE `cid` = ? ORDER BY `cdate` DESC LIMIT {$start}, 20", [$cid]);
            $post_list = $res->fetchAll();

            $res   = $db->select('COUNT(1)', '`article`', 'WHERE `cid` = ?', [$cid]);
            $count = $res->fetch()['COUNT(1)'];

            $params .= '&cate=' . $cate;
        } else if (is_null($cate)) {
            $res       = $db->select('`aid`, `title`, `cdate` AS `date`', '`article`', "WHERE `title` LIKE '%{$keyword}%' ORDER BY `cdate` DESC LIMIT {$start}, 20");
            $post_list = $res->fetchAll();

            $res   = $db->select('COUNT(1)', '`article`', "WHERE `title` LIKE '%{$keyword}%'");
            $count = $res->fetch()['COUNT(1)'];

            $params .= '&keyword=' . $keyword;
        }

        $units = ['年', '月', '天', '小时', '分钟', '秒'];
        $vals  = [31104000, 2592000, 86400, 3600, 60, 1];

        foreach ($post_list as $index => $post) {
            foreach ($vals as $i => $item) {
                $num = floor((time() - $post['date']) / $item);
                if ($num > 0) {
                    $post_list[$index]['date'] = $num . $units[$i] . '前';
                    break;
                }
            }
        }

        $pageCount = ceil($count / 20);

        if ($page == 1) {
            $prev = '<li><a class="disable"><</a></li>';
        } else {
            $prev = '<li><a href="' . $params . '&page=' . ($page - 1) . '"><</a></li>';
        }

        if ($pageCount == $page || $pageCount < 3) {
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
        $title = $cache->get('title');
        if ($title === false) {
            $res   = $db->select('`val`', '`setting`', 'WHERE `key` = ? LIMIT 1', ['title']);
            $title = $res->fetch()['val'];
            $cache->set('title', $title, 28800);
        }

        // 博主
        $name = $cache->get('name');
        if ($name === false) {
            $res  = $db->select('`val`', '`setting`', 'WHERE `key` = ? LIMIT 1', ['name']);
            $name = $res->fetch()['val'];
            $cache->set('name', $name, 28800);
        }

        // 社交链接
        $socials = $cache->get('socials');
        if ($socials === false) {
            $res     = $db->select('`icon`, `url`', '`social`');
            $socials = $res->fetchAll();
            $cache->set('socials', $socials, 28800);
        }

        // 友情链接
        $links = $cache->get('links');
        if ($links === false) {
            $res   = $db->select('`title`, `url`', '`link`');
            $links = $res->fetchAll();
            $cache->set('links', $links, 28800);
        }

        // 所有分类
        $categories = $cache->get('categories');
        if ($categories === false) {
            $res        = $db->select('`title`', '`category`');
            $categories = $res->fetchAll();
            $cache->set('categories', $categories, 28800);
        }

        $layoutParams = [
            'title'      => $title,
            'author'     => $name,
            'socials'    => $socials,
            'links'      => $links,
            'categories' => $categories,
        ];

        $this->render('index.html', $pageParams, 'default.html', $layoutParams);
    }
}
