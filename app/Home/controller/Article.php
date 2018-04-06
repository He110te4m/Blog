<?php
/*
 *    ____                        _
 *   |  _ \  ___  ___ _ __   ___ | |_ ___
 *   | | | |/ _ \/ __| '_ \ / _ \| __/ _ \
 *   | |_| |  __/\__ \ |_) | (_) | ||  __/
 *   |____/ \___||___/ .__/ \___/ \__\___|
 *                   |_|
 * @author      He110 (i@he110.top)
 * @namespace   app\Home\controller
 */
namespace app\Home\controller;

use \Despote;
use \despote\base\Controller;

class Article extends Controller
{
    public function detail()
    {
        $common = $this->getModel();
        $cache  = Despote::fileCache();

        // 校验链接是否合法
        $aid = Despote::request()->get('id');
        if (!$common->verify($aid)) {
            header('location: /404.html');
            die;
        }

        // 尝试从缓存中获取数据
        $post = $cache->get('post' . $aid);
        if ($post === false) {
            // 从数据库中获取并缓存
            $res  = Despote::sql()->select('`category`, `title`, `content`, `cdate` AS `date`', '`article_view`', 'WHERE `aid` = ? LIMIT 1', [$aid]);
            $post = $res->fetch();

            // 解压缩并编译 markdown
            $post['content'] = Despote::md()->parse(gzuncompress($post['content']));

            // 缓存一周
            $cache->set('post' . $aid, $post, 604800);
        }

        // 格式化时间
        $post['date'] = $common->formatDate($post['date']);

        // 规范化的传递数据给视图
        $pageParams = $post;

        // 网站标题
        $title = $common->getSetting('title');
        // 博主
        $name = $common->getSetting('name');
        // 社交链接
        $socials = $common->getAllData('`icon`, `url`', '`social`');
        // 友情链接
        $links = $common->getAllData('`title`, `url`', '`link`');
        // 所有分类
        $categories = $common->getAllData('`title`', '`category`');

        $layoutParams = [
            'title'      => $title,
            'author'     => $name,
            'socials'    => $socials,
            'links'      => $links,
            'categories' => $categories,
            'cate'       => $post['category'],
        ];

        $this->render('detail.html', $pageParams, 'default.html', $layoutParams);
    }
}
