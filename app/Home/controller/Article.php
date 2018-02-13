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

class Article extends Controller
{
    public function detail()
    {
        $db    = Despote::sql();
        $cache = Despote::fileCache();

        $aid = Despote::request()->get('id');
        if (is_null($aid)) {
            header('location: /404.html');
            die;
        }

        $post = $cache->get('post' . $aid);
        if ($post === false) {
            $res  = $db->select('`category`.`title` AS `category`, `article`.`title`, `content`, `cdate` AS `date`', '`article`, `category`', 'WHERE `article`.`aid` = ? AND `article`.`cid` = `category`.`cid` LIMIT 1', [$aid]);
            $post = $res->fetch();

            $post['content'] = Despote::md()->parse(gzuncompress($post['content']));

            // 缓存一周
            $cache->set('post' . $aid, $post, 604800);
        }

        $units = ['年', '月', '天', '小时', '分钟', '秒'];
        $vals  = [31104000, 2592000, 86400, 3600, 60, 1];

        foreach ($vals as $index => $item) {
            $num = floor((time() - $post['date']) / $item);
            if ($num > 0) {
                $post['date'] = $num . $units[$index] . '前';
                break;
            }
        }

        $pageParams = $post;

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

        $this->render('detail.html', $pageParams, 'default.html', $layoutParams);
    }
}
