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
namespace app\Home\model;

use \Despote;
use \despote\base\Model;

class Viewer extends Model
{
    /**
     * 校验是否访问太过频繁
     *
     * @return Mixed 获取不到 IP 返回 false，不会太过频繁返回 true，出错返回错误信息
     */
    public function verify () {
        // 获取请求对象
        $http  = Despote::request();
        $cache = Despote::fileCache();

        // 尝试获取用户真实 IP
        $ip = is_null($http->getUserRealIP()) ? $http->getUserIP() : $http->getUserRealIP();
        // 获取不到 IP 则返回
        if (is_null($ip)) {
            return false;
        }

        // 判断是否为禁止 IP
        if ($cache->has('ban: ' . $ip) === false) {
            // 验证是否有记录
            $key = 'verify: ' . $ip;
            $now = microtime(true);

            // 校验是否有不良记录
            $result = $cache->get($key);
            if ($result === false) {
                // 初始数据
                $val = [
                    'num' => 0,
                    'time' => $now,
                ];

                // 没有记录则创建记录
                $cache->set($key, $val, 43200);

                return true;
            } else {
                // 计算两次访问间隔
                if (($now - $result['time']) < 20) {
                    // 更新访问日志
                    $result['num']++;
                    $result['time'] = $now;
                    $cache->set($key, $result, 43200);

                    // 检测频繁访问次数，太过频繁拉黑
                    if ($result['num'] > 200 ) {
                        $cache->set('ban: ' . $ip, $ip, 86400);
                    }

                    return '访问太过频繁';
                } else {
                    // 评论间隔小于 20 秒就不需要记录次数，只需要记录访问时间
                    $result['time'] = $now;
                    $cache->set($key, $result, 43200);

                    return true;
                }
            }
        } else {
            return 'IP 禁止访问';
        }
    }
}
