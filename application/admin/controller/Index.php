<?php
namespace app\admin\controller;

use think\Db;
use think\Session;
use app\admin\model\AdminModel;

/*
 * 用于调度es_role表操作和view下的Index模块的一个类
 * @author liyuzhao
 *
 *以下4分函数功能
 * 1，
 * @access public
 * @abstract index 主要功能：显示index网页。一个逻辑：1，渲染显示出view模块下的index网页
 *
 * 2，
 * @access public
 * @abstract head 主要功能：显示head网页。一个逻辑：1：渲染显示出view模块下的head网页
 *
 * 3，
 * @access public
 * @abstract left 主要功能：显示left网页。两个逻辑：1：如果是超级管理员则显示出所有功能，如果不是则显示特定管理员功能 2：显示left网页
 *
 * 4，
 * @access public
 * @abstract right 主要功能：显示right网页。一个逻辑：1：喧嚷显示出view模块下的right网页
 * */

class Index extends IndexController
{
    public function index()
    {
        /*var_dump(Db::query('select * from es_admin'));
        $mydb = db();
        var_dump($mydb->query('select * from es_admin'));*/
        return $this->fetch();
    }

    public function head()
    {
        return $this->fetch();
    }

    public function left()
    {
        //第一个逻辑功能，取出账号管理表的数据
        /*进行判断，如果是超级管理员就显示出所有的功能，如果是管理员，就显示该有的功能*/
        $am_name = Session::get('am_name');
        $am = $this->admin->where('am_name', $am_name)->find();
        if ($am['role_id'] == 0) {
            $showlist = $this->auth->select();
            $this->assign('showlist', $showlist);
        } else {
            $role = $this->role->where('role_id', $am['role_id'])->find();
            $showlist = $this->auth->where('auth_id', 'in', $role['role_auth_ids'])->select();
            $this->assign('showlist', $showlist);
        }
        return $this->fetch();
    }

    public function right()
    {
        return $this->fetch();
    }
}