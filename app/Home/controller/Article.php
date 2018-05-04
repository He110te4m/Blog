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
            $res  = $common->getRecord('`aid`,  `title`, `category`, `cdate` AS `date`, `content`, `comment_num`', '`article_list`', 'WHERE `aid` = ? LIMIT 1', [$aid]);
            $post = $res->fetch();

            // 处理日期格式
            $post['date'] = date('Y-m-d', $post['date']);
            // 编译 markdown
            $post['content'] = Despote::md()->parse($post['content']);

            // 缓存一周
            $cache->set('post-' . $aid, $post, 604800);
        }

        // 网站名
        $title = $common->getItem('site_name');
        $author = $common->getItem('author');
        $intro = $common->getItem('intro');
        // 文章分类
        $category = $common->getAllItem('`title`, `key`', 'category');

        // 视图参数
        $pageParams = [
            'post'   => $post,
            'intro'  => $intro,
            'author' => $author,
        ];
        // 布局参数
        $layoutParams = [
            'title'     => $title,
            'category'  => $category,
            'sub_title' => $post['title'],
        ];

        $this->render('detail.html', $pageParams, 'home.html', $layoutParams);
    }
}
