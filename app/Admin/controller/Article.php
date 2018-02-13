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
    public function add()
    {
        $db    = Despote::sql();
        $cache = Despote::fileCache();

        $categories = $cache->get('categories');
        if ($categories === false) {
            $res        = $db->select('`title`', '`category`');
            $categories = $res->fetchAll();
            $cache->set('categories', $categories, 28800);
        }

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
        $db    = Despote::sql();
        $http  = Despote::request();
        $cache = Despote::fileCache();

        $id = $http->get('id');

        if (is_null($id)) {
            header('location: /404.html');
            die;
        }

        $res               = $db->select('`aid` AS `id`, `article`.`title`, `category`.`title` AS `category`, `content`, `cdate` AS `date`', '`article`, `category`', 'WHERE `aid` = ? AND `article`.`cid` = `category`.`cid` LIMIT 1', [$id]);
        $result            = $res->fetch();
        $result['content'] = gzuncompress($result['content']);

        $categories = $cache->get('categories');
        if ($categories === false) {
            $res        = $db->select('`title`', '`category`');
            $categories = $res->fetchAll();
            $cache->set('categories', $categories, 28800);
        }

        $pageParams               = $result;
        $pageParams['categories'] = $categories;

        $this->render('article-edit.html', $pageParams);
    }
}
