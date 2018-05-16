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

class Article extends Controller
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
        $db     = Despote::sql();
        $common = $this->getModel();
        $http   = Despote::request();

        // 设置初值
        $code = 0;

        // 获取数据
        $date     = $http->post('date');
        $title    = $http->post('title');
        $content  = $http->post('content');
        $category = $http->post('category');
        $abstract = $http->post('abstract');

        // 校验数据
        if ($common->verify($date) && $common->verify($title) && $common->verify($content) && $common->verify($category)) {
            $res = $common->getRecord('`cid`', '`category`', 'WHERE `key` = ?', [$category]);
            if ($res !== false) {
                // 处理数据
                $cid  = $res->fetch()['cid'];
                $date = strtotime($date);

                // 使用事务处理文章添加
                try {
                    $db->begin();

                    $code = $common->addRecord('`article`', '`cid`, `title`, `cdate`, `abstract`', [$cid, $title, $date, $abstract]);
                    $aid = $db->getLastID();
                    $code = $common->addRecord('`article_content`', '`aid`, `content`', [$aid, $content]);

                    $db->commit();
                } catch(Exception $e) {
                    $db->back();
                }
            } else {
                $code = 2;
            }
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

        // 搜索关键字
        $keyword = $http->get('keyword');

        // 设置初值
        $code  = 0;
        $count = 0;
        $list  = [];
        $start = ($page - 1) * $limit;

        if ($common->verify($keyword)) {
            $res = $common->getRecord('`aid` AS `id`, `cdate` AS `date`, `title`, `category`, `abstract`', '`article_list`', "WHERE `title` LIKE ? ORDER BY `cdate` DESC LIMIT {$start}, {$limit}", ["%{$keyword}%"]);

            if ($res !== false) {
                $list = $res->fetchAll();
                $count = $common->getCount('`article`', 'WHERE `title` LIKE ?', ["%{$keyword}%"]);
            } else {
                $code = 2;
            }
        } else {
            $res = $common->getRecord('`aid` AS `id`, `cdate` AS `date`, `title`, `category`, `abstract`', '`article_list`', "ORDER BY `cdate` DESC LIMIT {$start}, {$limit}");

            if ($res !== false) {
                $list = $res->fetchAll();
                $count = $common->getCount('`article`');
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
        $db     = Despote::sql();
        $common = $this->getModel();
        $http   = Despote::request();

        // 设置初值
        $code = 0;

        // 获取数据
        $id   = $http->post('id');
        $list = $http->post('list');

        if ($common->verify($id) || $common->verify($list)) {
            if ($common->verify($id)) {
                // 删除文章
                try {
                    $db->begin();

                    $common->delRecord('`article`', 'WHERE `aid` = ?', [$id]);
                    $common->delRecord('`article_content`', 'WHERE `aid` = ?', [$id]);

                    $db->commit();
                } catch (Exception $e) {
                    $code = 2;
                    $db->back();
                }
            } else {
                // 拼接 id
                $ids = json_decode($list, true);
                $ids = '(' . implode(', ', $ids) . ')';

                // 批量删除文章
                try {
                    $db->begin();

                    $common->delRecord('`article`', "WHERE `aid` IN {$ids}");
                    $common->delRecord('`article_content`', "WHERE `aid` IN {$ids}");

                    $db->commit();
                } catch (Exception $e) {
                    $code = 2;
                    $db->back();
                }
            }
        } else {
            $code = 1;
        }

        // 生成 data 数组
        $data = $common->getData($code);

        // 渲染 json
        $this->render('api.php', ['data' => $data]);
    }

    public function edit()
    {
        // 初始化工具对象
        $db     = Despote::sql();
        $common = $this->getModel();
        $http   = Despote::request();

        // 设置初值
        $code = 0;

        $id       = $http->post('id');
        $title    = $http->post('title');
        $date     = $http->post('date');
        $category = $http->post('category');
        $abstract = $http->post('abstract');
        $content  = $http->post('content');

        if ($common->verify($id) && $common->verify($title) && $common->verify($date) && $common->verify($category) && $common->verify($abstract) && $common->verify($content)) {
            $date = strtotime($date);

            $res = $common->getRecord('`cid`', '`category`', 'WHERE `key` = ?', [$category]);
            if ($res !== false) {
                $cid = $res->fetch()['cid'];
                try {
                    $db->begin();

                    $common->updateRecord('`article`', '`cid` = ?, `title` = ?, `cdate` = ?, `abstract` = ?', 'WHERE `aid` = ? LIMIT 1', [$cid, $title, $date, $abstract, $id]);
                    $common->updateRecord('`article_content`', '`content` = ?', 'WHERE `aid` = ?', [$content, $id]);

                    $db->commit();
                } catch(Exception $e) {
                    $code = 2;
                    $db->back();
                }
            } else {
                $code = 2;
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
