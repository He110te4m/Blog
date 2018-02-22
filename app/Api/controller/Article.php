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

class Article extends Controller
{
    private static $map = [
        0 => '成功',
        1 => '请求失败',
        2 => '数据库操作出错',
    ];

    public function init()
    {
        $sid1 = Despote::cookie()->get('sid');
        $sid2 = Despote::fileCache()->get('sid');
        if ($sid2 === false || $sid1 != $sid2) {
            header('location: /404.html');
            die;
        }
    }

    public function add()
    {
        $db   = Despote::sql();
        $http = Despote::request();

        $code = 0;

        $title    = $http->post('title');
        $date     = $http->post('date');
        $category = $http->post('category');
        $content  = $http->post('content');

        if (is_null($title) || is_null($date) || is_null($category) || is_null($content)) {
            $code = 1;
        } else {
            $date    = strtotime($date);
            $content = gzcompress($content);

            try {
                $db->begin();
                $res    = $db->select('`cid`', '`category`', 'WHERE `title` = ? LIMIT 1', [$category]);
                $result = $res->fetch();

                if ($result) {
                    $cid = $result['cid'];
                } else {
                    try {
                        $db->insert('`category`', '`title`', [$category]);
                    } catch (Exception $e) {
                        $code = 2;
                    }
                    $cid = $db->getIns()->lastInsertId();
                }
                $db->insert('`article`', '`cid`, `title`, `cdate`', [$cid, $title, $date]);
                $db->insert('`article_content`', '`content`', [$content]);
                $db->commit();
            } catch (Exception $e) {
                $db->back();
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

        $code    = 0;
        $data    = [];
        $count   = 0;
        $page    = $http->get('page');
        $limit   = $http->get('limit');
        $keyword = $http->get('keyword');
        $start   = ($page - 1) * $limit;

        if (is_null($page) || is_null($limit)) {
            $code = 1;
        } else {
            if (is_null($keyword)) {
                try {
                    $res  = $db->select('`aid` AS `id`, `title`, `category`', '`article_view`', "ORDER BY `cdate` DESC LIMIT {$start}, {$limit}");
                    $data = $res->fetchAll();

                    $res   = $db->select('COUNT(1)', '`article_view`');
                    $count = $res->fetch()['COUNT(1)'];
                } catch (Exception $e) {
                    $code = 2;
                }
            } else {
                try {
                    $res  = $db->select('`aid` AS `id`, `title`, `category`', '`article_content`', "WHERE `title` LIKE '%{$keyword}%' ORDER BY `cdate` DESC LIMIT {$start}, {$limit}");
                    $data = $res->fetchAll();

                    $res   = $db->select('COUNT(1)', '`article`', "WHERE `title` LIKE '%{$keyword}%'");
                    $count = $res->fetch()['COUNT(1)'];
                } catch (Exception $e) {
                    $code = 2;
                }
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
                    $db->begin();
                    $db->delete('`article`', 'WHERE `aid` = ?', [$id]);
                    $db->delete('`article_content`', 'WHERE `aid` = ?', [$id]);
                    $db->commit();
                } catch (Exception $e) {
                    $db->back();
                    $code = 2;
                }
            } else {
                $ids = json_decode($list, true);
                $ids = '(' . implode(',', $ids) . ')';
                try {
                    $db->begin();
                    $db->delete('`article`', "WHERE `aid` IN {$ids}");
                    $db->delete('`article_content`', "WHERE `aid` IN {$ids}");
                    $db->commit();
                } catch (Exception $e) {
                    $db->back();
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

        $id       = $http->post('id');
        $title    = $http->post('title');
        $date     = $http->post('date');
        $category = $http->post('category');
        $content  = $http->post('content');

        if (is_null($id) || is_null($title) || is_null($date) || is_null($category) || is_null($content)) {
            $code = 1;
        } else {
            $date    = strtotime($date);
            $content = gzcompress($content);

            try {
                $db->begin();
                $res = $db->select('`cid`', '`category`', 'WHERE `title` = ? LIMIT 1', [$category]);
                $cid = $res->fetch()['cid'];

                $db->update('`article`', '`cid` = ?, `title` = ?, `cdate` = ?', 'WHERE `aid` = ?', [$cid, $title, $date, $id]);
                $db->update('`article_content`', '`content` = ?', 'WHERE `aid` = ?', [$content, $id]);
                $db->commit();
            } catch (Exception $e) {
                $db->back();
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
