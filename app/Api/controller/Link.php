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

use \Despote;
use \despote\base\Controller;

class Link extends Controller
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
        $common = $this->getModel();
        $http   = Despote::request();

        $url   = $http->post('url');
        $title = $http->post('title');

        if ($common->verify($url) && $common->verify($title)) {
            $code = $common->addRecord('`link`', '`url`, `title`', [$url, $title]);
        } else {
            $code = 1;
        }

        $data = $common->getData($code);

        $this->render('api.php', ['data' => $data]);
    }

    public function all()
    {
        $common = $this->getModel();
        $http   = Despote::request();

        $page  = $http->get('page');
        $limit = $http->get('limit');

        $count = 0;
        $list  = [];
        $start = ($page - 1) * $limit;

        if ($common->verify($page) && $common->verify($limit)) {
            list($code, $res) = $common->getRecord('`id`, `title`, `url`', '`link`', "ORDER BY `id` LIMIT {$start}, {$limit}");

            $list  = $res->fetchAll();
            $count = $common->getCount('`link`');
        } else {
            $code = 1;
        }

        $data = $common->getData($code, ['count' => $count, 'data' => $list]);

        $this->render('api.php', ['data' => $data]);
    }

    public function del()
    {
        $common = $this->getModel();
        $http   = Despote::request();

        $id   = $http->post('id');
        $list = $http->post('list');

        if ($common->verify($id) || $common->verify($list)) {
            if ($common->verify($list)) {
                $ids = json_decode($list, true);
                $ids = '(' . implode(',', $ids) . ')';

                $code = $common->delRecord('`link`', "WHERE `id` IN {$ids}");
            } else {
                $code = $common->delRecord('`link`', 'WHERE `id` = ?', [$id]);
            }
        } else {
            $code = 1;
        }

        $data = $common->getData($code);

        $this->render('api.php', ['data' => $data]);
    }

    public function edit()
    {
        $common = $this->getModel();
        $http   = Despote::request();

        // 获取数据
        $id    = $http->post('id');
        $field = $http->post('field');
        $value = $http->post('value');

        if ($common->verify($id) && $common->verify($field) && $common->verify($value)) {
            $code = $common->updateRecord('`link`', "`{$field}` = ?", 'WHERE `id` = ?', [$value, $id]);
        } else {
            $code = 1;
        }

        $data = $common->getData($code);

        $this->render('api.php', ['data' => $data]);
    }
}
