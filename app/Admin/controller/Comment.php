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

use \Despote;
use \despote\base\Controller;

class Comment extends Controller
{
    public function init()
    {
        if ($this->getModel()->check() === false) {
            header('location: /Admin/User/login.html');
            die;
        }
    }

    public function manage()
    {
        $pageParams = [];

        $this->render('comment.html', $pageParams, 'child.html');
    }
}
