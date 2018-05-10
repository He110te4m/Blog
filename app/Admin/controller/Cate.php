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

class Cate extends Controller
{
    public function manage()
    {
        $pageParams = [];

        $this->render('cate.html', $pageParams, 'child.html');
    }
}
