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
namespace app\Api\model;

use \Despote;
use \despote\base\Model;
use \Exception;

class Common extends Model
{
    private static $map = [
        0 => '响应成功',
        1 => '连接过程中数据丢失',
        2 => '数据库响应出错',
        3 => '提交信息不完整',
        4 => '用户名已存在',
        5 => '用户名或密码错误',
        6 => '分类已存在',
    ];

    /**
     * 校验变量，用于校验 HTTP 提交的参数
     * @param  Mixed   $data  需要校验的变量，如果是数组并且需要检测是否含有某个键，请使用第二个参数
     * @param  Array   $field 需要校验的键名列表，使用索引数组方式传入
     * @return Boolean        校验结果，只要有一个不满足就返回 false，否则返回 true
     */
    public function verify($data, $field = [])
    {
        $flag = true;

        if (is_array($data) && !empty($field)) {
            foreach ($field as $item) {
                if (!isset($data[$item])) {
                    $flag = false;
                    break;
                }
            }
        } else {
            is_null($data) && $flag = false;
        }

        return $flag;
    }

    /**
     * 获取某个条件下数据库中有多少条记录
     * @param  String  $table 数据库表名
     * @param  string  $where 记录需要满足的条件，需要编写 WHERE
     * @param  array   $data  参数绑定的变量
     * @return Integer        查询出来的记录总数
     */
    public function getCount($table, $where = '', $data = [])
    {
        $count = 0;

        $res   = Despote::sql()->select('COUNT(1)', $table, $where, $data);
        $count = $res->fetch()['COUNT(1)'];

        return $count;
    }

    /**
     * 插入记录
     * @param  String $table 需要插入的表
     * @param  String $field 需要设置值的字段
     * @param  array  $data  字段对应的值，索引数组
     * @return Integer       执行代码，0 为插入完成，2 为插入失败
     */
    public function addRecord($table, $field, $data = [])
    {
        $code = 0;

        try {
            $res = Despote::sql()->insert($table, $field, $data);

            $res->rowCount() || $code = 2;
        } catch (Exception $e) {
            $code = 2;
        }

        return $code;
    }

    public function delRecord($table, $cond = '', $data = [])
    {
        $code = 0;

        try {
            Despote::sql()->delete($table, $cond, $data);
        } catch (Exception $e) {
            $code = 2;
        }

        return $code;
    }

    public function updateRecord($table, $field, $cond = '', $data = [])
    {
        $code = 0;

        try {
            $res = Despote::sql()->update($table, $field, $cond, $data);

            $res->rowCount() || $code = 2;
        } catch (Exception $e) {
            $code = 2;
        }

        return $code;
    }

    /**
     * 获取数据库中的记录
     * @param  String $field 需要查询的字段名
     * @param  String $table 需要查询的表名
     * @param  String $cond  查询的条件
     * @param  Array  $data  参数绑定的变量
     * @return Array         0 为状态码，1 为查询结果集
     */
    public function getRecord($field, $table, $cond = '', $data = [])
    {
        $code = 0;
        $res  = false;

        try {
            $res = Despote::sql()->select($field, $table, $cond, $data);
        } catch (Exception $e) {
            $code = 2;
        }

        return [$code, $res];
    }

    /**
     * 生成 data 数组
     * @param  Integer $code 错误代码
     * @param  array   $arr  附加信息，关联数组
     * @return Array         data 数组
     */
    public function getData($code, $arr = [], $msg = '')
    {
        $data = [
            'code' => $code,
            'msg'  => empty($msg) ? self::$map[$code] : $msg,
        ];

        $data = array_merge($data, $arr);

        return $data;
    }

    public function check()
    {
        $sid1 = Despote::cookie()->get('sid');
        $sid2 = Despote::fileCache()->get('sid');

        return $sid2 !== false && $sid1 == $sid2;
    }
}