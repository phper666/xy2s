<?php
namespace app\admin\service;

use think\Model;
use think\Validate;
use think\Db;

/*
 * 用于封装es_admin表操作的逻辑的一个类
 * @author liyuzhao
 * @module admin/controller/Admin
 *
 * 以下3个函数功能
 * 1，
 * @access public
 * @abstract addAdmin 主要功能：添加管理员。一个逻辑：添加管理员
 *
 * 2，
 * @access public
 * @abstract editAdmin 主要功能：更新管理员信息。一个逻辑：更新管理员信息
 *
 * 3，
 * @access public
 * @abstract checkName 主要功能：检测是否有角色名
 * */

class AdminService extends Model
{
    /*
         * 这是一个添加管理员的操作方法，目的是把表单的信息接收格式化放入数据库
         * @access public
         * @param array $data 这个是管理员的信息,是一个一维数组
         * @return bool 添加成功就返回true,反之为false
         * */
    public function addAdmin($data)
    {
        $fl = Db::table('es_admin')->insert($data);
        if ($fl) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 这个一个封装好的更新管理员信息的方法
     * @access public
     * @param $data 这是一个一维数组
     * return bool 添加成功就返回true,反之为false
     * */
    public function editAdmin($data)
    {
        //更新admin表的信息
        $fl = Db::table('es_admin')->update($data);
        if ($fl) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 这个是对管理者用户名是否存在的验证
     * $param $data 接收用户名，为一个一维数组
     * @return bool 验证成功返回true,反之false
     */
    public function checkName($data)
    {
        $fl = Db::table('es_admin')->where('am_name', $data['am_name'])->find();
        if ($fl != null) {
            return true;
        } else {
            return false;
        }
    }
}