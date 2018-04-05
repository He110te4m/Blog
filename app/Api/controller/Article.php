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
        if ($this->getModel()->check() === false) {
            header('location: /404.html');
            die;
        }
    }

    public function add()
    {
        $db     = Despote::sql();
        $common = $this->getModel();
        $http   = Despote::request();

        $code = 0;

        $title    = $http->post('title');
        $date     = $http->post('date');
        $category = $http->post('category');
        $content  = $http->post('content');

        if ($common->verify($title) && $common->verify($date) && $common->verify($category) && $common->verify($content)) {
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

        $page    = $http->get('page');
        $limit   = $http->get('limit');
        $keyword = $http->get('keyword');
        $start   = ($page - 1) * $limit;

        if ($common->verify($page) || $common->verify($limit)) {
            if ($common->verify($keyword)) {
                list($code, $res) = $common->getRecord('`aid` AS `id`, `title`, `category`', '`article_view`', "WHERE `title` LIKE '%{$keyword}%' ORDER BY `cdate` DESC LIMIT {$start}, {$limit}");

                $list  = $res->fetchAll();
                $count = $common->getCount('`article`', "WHERE `title` LIKE '%{$keyword}%'");
            } else {
                list($code, $res) = $common->getRecord('`aid` AS `id`, `title`, `category`', '`article_view`', "ORDER BY `cdate` DESC LIMIT {$start}, {$limit}");

                $list  = $res->fetchAll();
                $count = $common->getCount('`article`');
            }
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

        $code = 0;

        $id   = $http->post('id');
        $list = $http->post('list');

        if ($common->verify($id) || $common->verify($list)) {
            if ($common->verify($list)) {
                $ids = json_decode($list, true);
                $ids = '(' . implode(',', $ids) . ')';

                $code = $common->delRecord('`article`', "WHERE `aid` IN {$ids}");
                $code = $common->delRecord('`article_content`', "WHERE `aid` IN {$ids}");
            } else {
                $code = $common->delRecord('`article`', 'WHERE `aid` = ?', [$id]);
                $code = $common->delRecord('`article_content`', 'WHERE `aid` = ?', [$id]);
            }
        } else {
            $code = 1;
        }

        $data = $common->getData($code);

        $this->render('api.php', ['data' => $data]);
    }

    public function edit()
    {
        $db     = Despote::sql();
        $common = $this->getModel();
        $http   = Despote::request();

        $code = 0;

        $id       = $http->post('id');
        $title    = $http->post('title');
        $date     = $http->post('date');
        $category = $http->post('category');
        $content  = $http->post('content');

        if ($common->verify($id) && $common->verify($title) && $common->verify($date) && $common->verify($category) && $common->verify($content)) {
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
        } else {
            $code = 1;
        }

        $data = $common->getData($code);

        $this->render('api.php', ['data' => $data]);
    }
}
