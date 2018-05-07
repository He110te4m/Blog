<?php
/*
 *    ____                        _
 *   |  _ \  ___  ___ _ __   ___ | |_ ___
 *   | | | |/ _ \/ __| '_ \ / _ \| __/ _ \
 *   | |_| |  __/\__ \ |_) | (_) | ||  __/
 *   |____/ \___||___/ .__/ \___/ \__\___|
 *                   |_|
 * @author      He110 (i@he110.info)
 * @namespace   app\Admin\controller
 */
namespace app\Admin\controller;

use \despote\base\Controller;
use \Despote;

class Article extends Controller
{
    public function add()
    {
        // 获取通用模型
        $common = $this->getModel();

        // 视图参数
        $pageParams = [
        ];
        // 布局参数
        $layoutParams = [
            'mod' => 'post.add',
        ];

        $this->render('post.add.html', $pageParams, 'child.html', $layoutParams);
    }
}
