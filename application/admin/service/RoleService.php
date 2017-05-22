<?php
namespace app\admin\service;

use think\Db;
use think\Model;

/*
 * 用于封装es_role表操作的逻辑的一个类
 * @author liyuzhao
 * @module admin/controller/Role
 *
 * 以下四个类功能
 * 1，
 * @access public
 * @abstract addRole 主要功能：添加角色 一个逻辑：1，调用拼接表单信息的方法，插入数据库
 *
 * 2，
 * @access public
 * @abstract editRole 主要功能：更新角色信息 一个逻辑：1，调用拼接表单信息的方法，更新数据库
 *
 * 3，
 * @access public
 * @abstract pinjie 主要功能：拼接表单的信息，然后格式化。
 *
 * 4，@access public
 * @abstract checkName 主要功能：检测数据库中是否含有该用户。
 * */

class RoleService extends Model
{
    /*
     * 这是一个添加角色的操作方法，目的是把表单的信息接收格式化放入数据库
     * @access public
     * @param array $data 这个是角色的信息,是一个一维数组
     * @return bool 添加成功就返回true,反之为false
     * */
    public function addRole($data)
    {
        //直接调用拼接的方法
        $arr = RoleService::pinjie($data);
        $fl = Db::table('es_role')->insert($arr);
        if ($fl) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 这个一个封装好的更新角色信息的方法
     * @access public
     * @param $data 这是一个一维数组
     * return bool 添加成功就返回true,反之为false
     * */
    public function editRole($data)
    {
        $role_id = $data['role_id'];
        unset($data['role_id']);    //如果不unset掉，会在拼接ids那里出现role_id这个字符
        $arr = RoleService::pinjie($data);
        //更新role表的信息
        $fl = Db::table('es_role')->where('role_id', $role_id)->update($arr);
        if ($fl) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 这个是拼接role表字段的信息的方法，因为更新和添加数据库信息的代码重用，所以做成了一个方法调用
     * @param $data 这是一个一维数组
     * @return array 返回拼接好的数组
     * */
    public function pinjie($data)
    {
        $ids = ',';
        foreach ($data as $k => $v) {
            if ($k !== 'role_name') {
                $ids .= $k . ',';
            }
        }
        //删除字符串左右的,号
        $ids = ltrim($ids, ',');
        $ids = rtrim($ids, ',');

        //把角色所属的控制器和方法出来,还可以优化不需要Db，可以实例化auth模型，但是查询到的是一个对象，所以不采用，太麻烦，要把数据格式化
        $auth = Db::table('es_auth')->where('auth_id', 'in', $ids)->select();
        $auth_ac = ',';
        //把控制器和方法拼接起来
        foreach ($auth as $k => $v) {
            if ($v['auth_level'] != 0) {
                $auth_ac .= $v['auth_c'] . '-' . $v['auth_a'] . ',';
            }
        }
        //删除字符串左右的,号
        $auth_ac = ltrim($auth_ac, ',');
        $auth_ac = rtrim($auth_ac, ',');

        //把数据插入数据库
        $arr = [
            'role_name' => $data['role_name'],
            'role_auth_ids' => $ids,
            'role_auth_ac' => $auth_ac
        ];
        return $arr;
    }

    /*
     * 这是一个查询角色名是否存在的功能
     * @param $data 这是一个一维数组
     * @return bool 存在返回true，反之返回false
     * */
    public function checkName($data)
    {
        $fl = Db::table('es_role')->where('role_name', $data['role_name'])->find();
        if ($fl != null) {
            return true;
        } else {
            return false;
        }
    }
}
