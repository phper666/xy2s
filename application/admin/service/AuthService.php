<?php
namespace app\admin\service;

use think\Db;
use think\Model;
use think\Session;

/*
 * 用于封装es_auth表操作的逻辑的一个类
 * @author liyuzhao
 * @module admin/controller/Auth
 *
 * 以下类功能
 * 1，
 * @access public
 * @abstract addAuth 主要功能：添加权限 一个逻辑：1，格式化表单的信息，插入数据库
 * */

class AuthService extends Model
{
    /*
     * 这是一个添加权限的操作方法，目的是把表单的信息接收格式化放入数据库
     * @access public
     * @param array $data 这个是权限的信息,是一个一维数组
     * @return bool 添加成功就返回true,反之为false
     * */
    public function addAuth($data)
    {
        if (empty($data['auth_a'])) {
            $data['auth_a'] = '';
        }
        if (empty($data['auth_c'])) {
            $data['auth_c'] = '';
        }
        if (empty($data['auth_dingji_id'])) {
            $data['auth_dingji_id'] = '';
        }
        if (empty($data['auth_yiji_id'])) {
            $data['auth_yiji_id'] = '';
        }

        if ($data['auth_level'] == '0') {
            $a['auth_pid'] = 0;
        } else if ($data['auth_level'] == '1') {
            $a['auth_pid'] = $data['auth_dingji_id'];
        } else if ($data['auth_level'] == '2') {
            $a['auth_pid'] = $data['auth_yiji_id'];
        }

        $arr = [
            'auth_name' => $data['auth_name'],
            'auth_a' => $data['auth_a'],
            'auth_c' => $data['auth_c'],
            'auth_level' => $data['auth_level'],
            'auth_pid' => $a['auth_pid'],
        ];

        $auth_id = Db::table('es_auth')->insertGetId($arr);

        //调用更新全路径方法
        $fl = AuthService::autoPath($data, $auth_id);
        if ($fl) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 这个一个封装好的更新权限信息的方法
     * @access public
     * @param $data 这是一个一维数组
     * return bool 添加成功就返回true,反之为false
     * */
    public function editAuth($data)
    {
        $auth_id = $data['auth_name']; //获取要更新的id
        $arr = [
            'auth_id' => $auth_id,
            'auth_c' => $data['auth_c'],
            'auth_a' => $data['auth_a'],
            'auth_name' => $data['auth_new_name'],
            'auth_level' => $data['auth_level'],
        ];
        $fl = Db::table('es_auth')->update($arr);
        if ($fl) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 这个是对权限名是否存在的验证
     * @param $data 接收用权限名，为一个一维数组
     * @return bool 验证存在返回true,反之false
     */
    public function checkName($data)
    {
        $fl = Db::table('es_auth')->where('auth_name', $data['auth_name'])->find();
        if ($fl != null) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 这个是更新全路径的一个方法，一添加数据就自动更新全路径
     * @param $data 接收的是权限信息，一维数组
     * @param $id 这个是添加数据自增长的值，也就是添加数据的id
     * @return bool 全路径更新成功返回true，反之false
     * */
    public function autoPath($data, $id)
    {
        if ($data['auth_level'] == '1') {
            $arr['auth_path'] = $data['auth_dingji_id'] . '-' . $id;
        } else if ($data['auth_level'] == '0') {
            $arr['auth_path'] = '';
        } else if ($data['auth_level'] == '2') {
            $arr['auth_path'] = $data['auth_dingji_id'] . '-' . $data['auth_yiji_id'] . '-' . $id;
        }
        $fl = Db::table('es_auth')->where('auth_id', $id)->update($arr);
        if ($fl) {
            return true;
        } else {
            return false;
        }
    }
}