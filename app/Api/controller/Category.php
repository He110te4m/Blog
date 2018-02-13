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
use \Exception;

class Category extends Controller
{
    private static $map = [
        0 => '成功',
        1 => '请求失败',
        2 => '数据库操作出错',
        3 => '分类已存在',
    ];

    public function add()
    {
        $db   = Despote::sql();
        $http = Despote::request();

        $code = 0;

        $title = $http->post('title');

        if (is_null($title)) {
            $code = 1;
        } else {
            try {
                $res    = $db->select('1', '`category`', 'WHERE `title` = ?', [$title]);
                $result = $res->fetch();
                if ($result) {
                    $code = 3;
                } else {
                    $db->insert('`category`', '`title`', [$title]);
                }
            } catch (Exception $e) {
                $code = 2;
            }
        }

        $pageParams = [
            'data' => [
                'code' => $code,
                'msg'  => self::$map[$code],
            ],
        ];

        $this->render('api.php', $pageParams);
    }

    public function all()
    {

        $db   = Despote::sql();
        $http = Despote::request();

        $code  = 0;
        $data  = [];
        $count = 0;
        $page  = $http->get('page');
        $limit = $http->get('limit');
        $start = ($page - 1) * $limit;

        if (is_null($page) || is_null($limit)) {
            $code = 1;
        } else {
            try {
                $res  = $db->select('`cid` AS `id`, `title`', '`category`', "ORDER BY `id` LIMIT {$start}, {$limit}");
                $data = $res->fetchAll();

                $res   = $db->select('COUNT(1)', '`category`');
                $count = $res->fetch()['COUNT(1)'];
            } catch (Exception $e) {
                $code = 2;
            }
        }

        $pageParams = [
            'data' => [
                'code'  => $code,
                'msg'   => self::$map[$code],
                'count' => $count,
                'data'  => $data,
            ],
        ];

        $this->render('api.php', $pageParams);
    }

    public function del()
    {
        $db   = Despote::sql();
        $http = Despote::request();

        $code = 0;

        $id   = $http->post('id');
        $list = $http->post('list');

        if (is_null($id) && is_null($list)) {
            $code = 1;
        } else {
            if (is_null($list)) {
                try {
                    $db->delete('`category`', 'WHERE `cid` = ?', [$id]);
                } catch (Exception $e) {
                    $code = 2;
                }
            } else {
                $ids = json_decode($list, true);
                $ids = '(' . implode(',', $ids) . ')';
                try {
                    $db->delete('`category`', "WHERE `cid` IN {$ids}");
                } catch (Exception $e) {
                    $code = 2;
                }
            }
        }

        $pageParams = [
            'data' => [
                'code' => $code,
                'msg'  => self::$map[$code],
            ],
        ];

        $this->render('api.php', $pageParams);
    }

    public function edit()
    {
        $db   = Despote::sql();
        $http = Despote::request();

        $code = 0;

        // 获取数据
        $id    = $http->post('id');
        $field = $http->post('field');
        $value = $http->post('value');

        if (is_null($id) || is_null($field) || is_null($value)) {
            $code = 1;
        } else {
            try {
                $db->update('`category`', "`{$field}` = ?", 'WHERE `cid` = ?', [$value, $id]);
            } catch (Exception $e) {
                $code = 2;
            }
        }

        $pageParams = [
            'data' => [
                'code' => $code,
                'msg'  => self::$map[$code],
            ],
        ];

        $this->render('api.php', $pageParams);
    }
}
