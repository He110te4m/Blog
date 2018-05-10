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
namespace app\Admin\model;

use \Despote;
use \despote\base\Model;
use \Exception;

class Common extends Model
{
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
     * @param  String  $where 记录需要满足的条件，需要编写 WHERE
     * @param  Array   $data  参数绑定的变量
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
     * @param  Array  $data  字段对应的值，索引数组
     * @return Mixed         执行代码，成功返回 true，否则返回错误信息
     */
    public function addRecord($table, $field, $data = [])
    {
        $result = true;

        try {
            $res = Despote::sql()->insert($table, $field, $data);

            if ($res->rowCount() === 0) {
                $data = implode(', ', $data);
                $result = "由于未知原因，INSERT INTO {$table} ({$field}) VALUES ({$data}) 执行失败";
            }
        } catch (Exception $e) {
            $result = $e->getMessage();
        }

        return $result;
    }

    /**
     * 删除记录
     * @param  String $table 需要删除的表
     * @param  String $cond  用于筛选需要删除的记录的条件
     * @param  Array  $data  参数绑定的数据
     * @return Mixed         执行代码，成功返回 true，否则返回错误信息
     */
    public function delRecord($table, $cond = '', $data = [])
    {
        $result = true;

        try {
            Despote::sql()->delete($table, $cond, $data);
        } catch (Exception $e) {
            $result = $e->getMessage();
        }

        return $result;
    }

    /**
     * 修改记录
     * @param  String $table  需要修改的表
     * @param  String $field  需要修改的字段
     * @param  String $cond   用于筛选需要修改的记录的条件
     * @param  Array  $data   参数绑定的数据
     * @return Mixed          执行代码，成功返回 true，否则返回错误信息
     */
    public function updateRecord($table, $field, $cond = '', $data = [])
    {
        $result = true;

        try {
            $res = Despote::sql()->update($table, $field, $cond, $data);

            if ($res->rowCount() === 0) {
                $result = "由于未知原因，UPDATE {$table} SET {$field} 执行失败";
            }
        } catch (Exception $e) {
            $result = $e->getMessage();
        }

        return $result;
    }

    /**
     * 获取数据库中的记录
     * @param  String $field 需要查询的字段名
     * @param  String $table 需要查询的表名
     * @param  String $cond  查询的条件
     * @param  Array  $data  参数绑定的变量
     * @return Object        成功返回查询结果集，失败返回 false
     */
    public function getRecord($field, $table, $cond = '', $data = [])
    {

        try {
            $res = Despote::sql()->select($field, $table, $cond, $data);
        } catch (Exception $e) {
            $res  = false;
        }

        return $res;
    }

    public function getItem($key)
    {
        $db = Despote::sql();

        $res = $db->select('`val`', '`setting`', "WHERE `key` = '{$key}' LIMIT 1");
        // 使用数据库自动缓存
        $result = $db->fetch($res)['val'];

        return $result;
    }

    public function getAllItem($field, $table)
    {
        $db = Despote::sql();

        $res = $db->select($field, $table);
        // 使用数据库自动缓存
        $result = $db->fetchAll($res);

        return $result;
    }

    public function check()
    {
        $sid1 = Despote::cookie()->get('sid');
        $sid2 = Despote::fileCache()->get('sid');

        return $sid2 !== false && $sid1 == $sid2;
    }
}
