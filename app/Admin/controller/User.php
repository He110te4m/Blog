<?php
/*
 *    ____                        _
 *   |  _ \  ___  ___ _ __   ___ | |_ ___
 *   | | | |/ _ \/ __| '_ \ / _ \| __/ _ \
 *   | |_| |  __/\__ \ |_) | (_) | ||  __/
 *   |____/ \___||___/ .__/ \___/ \__\___|
 *                   |_|
 * @author      He110 (i@he110.top)
 * @namespace   app\Admin\controller
 */
namespace app\Admin\controller;

use \Despote;
use \despote\base\Controller;

class User extends Controller
{
    public function login()
    {
        if ($this->getModel()->check()) {
            header('location: /Admin/Index/index');
            die;
        }

        $this->render('login.html');
    }

    public function layout()
    {
        Despote::cookie()->del('sid');
        Despote::fileCache()->del('sid');
        header('location: /Admin/User/login');
    }
}
