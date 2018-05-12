<?php
/*
 *    ____                        _
 *   |  _ \  ___  ___ _ __   ___ | |_ ___
 *   | | | |/ _ \/ __| '_ \ / _ \| __/ _ \
 *   | |_| |  __/\__ \ |_) | (_) | ||  __/
 *   |____/ \___||___/ .__/ \___/ \__\___|
 *                   |_|
 * @author      He110 (i@he110.info)
 * @namespace   app\Api\controller
 */
namespace app\Api\controller;

use \Despote;
use \despote\base\Controller;
use \Exception;

class Social extends Controller
{
    public function init()
    {
        if ($this->getModel()->check() === false) {
            header('location: /Page/error.html');
            die;
        }
    }

    public function add()
    {
        // 初始化工具对象
        $common = $this->getModel();
        $http   = Despote::request();

        // 获取数据
        $url  = $http->post('url');
        $title = $http->post('title');

        if ($common->verify($url) && $common->verify($title)) {
            $code = $common->addRecord('`social`', '`url`, `title`', [$url, $title]);
        } else {
            $code = 1;
        }

        // 生成 data 数组
        $data = $common->getData($code);

        // 渲染 json
        $this->render('api.php', ['data' => $data]);
    }

    public function get()
    {
        // 初始化工具对象
        $common = $this->getModel();
        $http   = Despote::request();

        // 获取分页信息
        $page  = $http->get('page', 1);
        $limit = $http->get('limit', 10);

        // 设置初值
        $code  = 0;
        $count = 0;
        $list  = [];
        $start = ($page - 1) * $limit;

        $res = $common->getRecord('`id`, `title`, `icon`, `url`', '`social`', "ORDER BY `id` LIMIT {$start}, {$limit}");
        if ($res !== false) {
            $list  = $res->fetchAll();
            $count = $common->getCount('`social`');

            foreach ($list as &$item) {
                $item['icon'] = htmlspecialchars($item['icon']);
            }
        } else {
            $code = 2;
        }


        // 生成 data 数组
        $data = $common->getData($code, ['count' => $count, 'data' => $list]);

        // 渲染 json
        $this->render('api.php', ['data' => $data]);
    }

    public function del()
    {
        // 初始化工具对象
        $common = $this->getModel();
        $http   = Despote::request();

        // 获取数据
        $id   = $http->post('id');
        $list = $http->post('list');

        if ($common->verify($id) || $common->verify($list)) {
            if ($common->verify($id)) {
                // 删除分类
                $code = $common->delRecord('`social`', 'WHERE `id` = ?', [$id]);
            } else {
                // 拼接 id
                $ids = json_decode($list, true);
                $ids = '(' . implode(',', $ids) . ')';

                // 批量删除分类
                $code = $common->delRecord('`social`', "WHERE `id` IN {$ids}");
            }
        }

        // 生成 data 数组
        $data = $common->getData($code);

        // 渲染 json
        $this->render('api.php', ['data' => $data]);
    }

    public function edit()
    {
        // 初始化工具对象
        $common = $this->getModel();
        $http   = Despote::request();

        // 获取数据
        $id    = $http->post('id');
        $field = $http->post('field');
        $value = $http->post('value');

        if ($common->verify($id) && $common->verify($field) && $common->verify($value)) {
            $code = $common->updateRecord('`social`', "`{$field}` = ?", 'WHERE `id` = ?', [$value, $id]);
        } else {
            $code = 1;
        }

        // 生成 data 数组
        $data = $common->getData($code);

        // 渲染 json
        $this->render('api.php', ['data' => $data]);
    }
}
