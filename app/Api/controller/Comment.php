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

class Comment extends Controller
{
    public function init()
    {
        if ($this->getModel()->check() === false) {
            header('location: /Page/error.html');
            die;
        }
    }

    public function get()
    {
        // 初始化工具对象
        $common = $this->getModel();
        $http   = Despote::request();

        // 获取分页信息
        $page  = $http->get('page', 1);
        $limit = $http->get('limit', 10);

        // 搜索关键字
        $keyword = $http->get('keyword');

        // 设置初值
        $code  = 0;
        $count = 0;
        $list  = [];
        $start = ($page - 1) * $limit;

        if ($common->verify($keyword)) {
            $res = $common->getRecord('`c`.`id`, `c`.`cdate` AS `date`, `c`.`content`, `c`.`author`, `a`.`title`', '`comment` AS `c`, `article` AS `a`', "WHERE `a`.`aid` = `c`.`aid` AND `a`.`title` LIKE ? ORDER BY `c`.`id` LIMIT {$start}, {$limit}", ["%{$keyword}%"]);
            if ($res !== false) {
                $list  = $res->fetchAll();
                $count = $common->getCount('`comment`, `article` AS `a`', 'WHERE `a`.`aid` = `comment`.`aid` AND `a`.`title` LIKE ?', ["%{$keyword}%"]);
            } else {
                $code = 2;
            }
        } else {
            $res = $common->getRecord('`c`.`id`, `c`.`cdate` AS `date`, `c`.`content`, `c`.`author`, `a`.`title`', '`comment` AS `c`, `article` AS `a`', "WHERE `a`.`aid` = `c`.`aid` ORDER BY `c`.`id` LIMIT {$start}, {$limit}");
            if ($res !== false) {
                $list  = $res->fetchAll();
                $count = $common->getCount('`comment`');
            } else {
                $code = 2;
            }
        }

        foreach ($list as &$item) {
            $item['date'] = date('Y-m-d', $item['date']);
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
                // 删除评论
                $code = $common->delRecord('`comment`', 'WHERE `id` = ?', [$id]);
            } else {
                // 拼接 id
                $ids = json_decode($list, true);
                $ids = '(' . implode(',', $ids) . ')';

                // 批量删除评论
                $code = $common->delRecord('`comment`', "WHERE `id` IN {$ids}");
            }
        } else {
            $code = 1;
        }

        // 生成 data 数组
        $data = $common->getData($code);

        // 渲染 json
        $this->render('api.php', ['data' => $data]);
    }
}
