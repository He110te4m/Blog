<?php
/*
 *    ____                        _
 *   |  _ \  ___  ___ _ __   ___ | |_ ___
 *   | | | |/ _ \/ __| '_ \ / _ \| __/ _ \
 *   | |_| |  __/\__ \ |_) | (_) | ||  __/
 *   |____/ \___||___/ .__/ \___/ \__\___|
 *                   |_|
 * @author      He110 (i@he110.info)
 * @namespace   app\Home\controller
 */
namespace app\Home\controller;

use \despote\base\Controller;
use \Despote;

class Article extends Controller
{
    public function detail()
    {
        // 获取通用模型
        $common = $this->getModel();
        // 获取缓存对象
        $cache  = Despote::fileCache();

        // 校验 aid
        $aid = Despote::request()->get('id');
        if (!$common->verify($aid)) {
            header('location: /Page/error.html');
            die;
        }

        // 尝试从缓存中获取数据
        $post = $cache->get('post-' . $aid);
        if ($post === false) {
            // 从数据库中获取并缓存
            $post = $common->getRecord('`aid`,  `title`, `category`, `cdate` AS `date`, `content`, `comment_num`', '`article_list`', 'WHERE `aid` = ? LIMIT 1', [$aid])->fetch();

            // 处理日期格式
            $post['date'] = date('Y-m-d', $post['date']);
            // 编译 markdown
            $post['content'] = Despote::md()->parse($post['content']);

            // 缓存一周
            $cache->set('post-' . $aid, $post, 604800);
        }

        // 获取评论
        $comment = $common->getRecord('`cdate` as `date`, `author`, `email`, `website`, `content`', '`comment`', 'WHERE `aid` = ? ORDER BY `cdate` DESC', [$aid])->fetchAll();
        foreach ($comment as &$item) {
            $item['date'] = date('Y-m-d', $item['date']);
        }

        // 网站名
        $title = $common->getItem('site_name');
        $author = $common->getItem('author');
        $intro = $common->getItem('intro');
        // 文章分类
        $category = $common->getAllItem('`title`, `key`', 'category');

        // 视图参数
        $pageParams = [
            'post'    => $post,
            'intro'   => $intro,
            'author'  => $author,
            'comment' => $comment,
        ];
        // 布局参数
        $layoutParams = [
            'title'     => $title,
            'category'  => $category,
            'sub_title' => $post['title'],
        ];

        $this->render('detail.html', $pageParams, 'home.html', $layoutParams);
    }

    public function comment()
    {
        // 状态码
        $code = 1;

        // 框架内置对象
        $http  = Despote::request();

        // 校验用户是否有权限访问
        $result = $this->getModel('Viewer')->verify();
        if ($result === false) {
            header('location: /Page/error.html');
            die;
        } else if ($result === true) {
            // 获取通用模型
            $common = $this->getModel();

            // 获取参数
            $aid    = $http->post('id');
            $url    = $http->post('url');
            $text   = $http->post('text');
            $email  = $http->post('email');
            $author = $http->post('author');

            // 校验是否有数据上传
            if ($common->verify($aid) && $common->verify($url) && $common->verify($text) && $common->verify($email) && $common->verify($author)) {
                // 校验是否上传了有效数据
                if (empty($aid) || empty($text) || empty($email) || empty($author)) {
                    $code   = 2;
                    $result = '文明上网，请不要 XSS~';
                } else {
                    $date   = time();
                    $aid    = htmlspecialchars($aid);
                    $url    = htmlspecialchars($url);
                    $text   = htmlspecialchars($text);
                    $email  = htmlspecialchars($email);
                    $author = htmlspecialchars($author);

                    $result = $common->addRecord('`comment`', '`aid`, `cdate`, `website`, `content`, `email`, `author`', [$aid, $date, $url, $text, $email, $author]);
                    if ($result === true) {
                        $code = 0;
                        $result  = '';
                    } else {
                        $result = '数据库响应出错，请联系管理员解决~';
                    }
                }
            } else {
                $result = '文明上网，请不要 XSS~';
            }
        }

        $msg  = $result;

        echo json_encode(['code' => $code, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
    }
}
