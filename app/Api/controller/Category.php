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

class Category extends Controller
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

        $title = $http->post('title');

        if ($common->verify($title)) {
            list($code, $res) = $common->getRecord('1', '`category`', 'WHERE `title` = ?', [$title]);

            $result = $res->fetch();
            if ($result) {
                $code = 6;
            } else {
                $code = $common->addRecord('`category`', '`title`', [$title]);
            }
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
            list($code, $res) = $common->getRecord('`cid` AS `id`, `title`', '`category`', "ORDER BY `id` LIMIT {$start}, {$limit}");

            $list  = $res->fetchAll();
            $count = $common->getCount('`category`');
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

                $code = $common->delRecord('`category`', "WHERE `cid` IN {$ids}");
            } else {
                $code = $common->delRecord('`category`', 'WHERE `cid` = ?', [$id]);
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
            $code = $common->updateRecord('`category`', "`{$field}` = ?", 'WHERE `cid` = ?', [$value, $id]);
        } else {
            $code = 1;
        }

        $data = $common->getData($code);

        $this->render('api.php', ['data' => $data]);
    }
}
