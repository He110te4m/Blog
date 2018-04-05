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

use \despote\base\Controller;

class Cache extends Controller
{
    public function flush()
    {
        $code = 0;
        \Despote::fileCache()->flush();

        $data = $this->getModel()->getData($code);

        $this->render('api.php', ['data' => $data]);
    }
}
