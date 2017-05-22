<?php
namespace app\admin\controller;

use think\Db;
use page\Page;
use app\admin\model\RoleModel;
use app\admin\service\RoleService;

/*
 *  用于调度es_admin表操作和view下的Admin模块的一个类
 * @author liyuzhao
 *
 * 以下5个函数功能
 * 1，
 * @access public
 * @abstract rolelist 主要功能：显示角色列表。这个有一个逻辑：1：显示角色列表
 *
 * 2，
 * @access public
 * @abstract addrole 主要功能：添加角色信息。这个有两个逻辑：1：显示auth权限名称 2：添加角色/验证要添加的角色数据
 *
 * 3，
 * @access public
 * $abstract editrole 主要功能：修改角色信息。这个有两个逻辑：1：显示出角色表/显示出角色对应的权限名称 2：验证更新的信息
 *
 * 4,access public
 * @abstract role_msg 主要功能：接收ajax信息。有一个逻辑：1：接收ajax发送的role_id,此功能是点击角色名就自动选择所有的ids出来
 * @return json 返回类型为json
 *
 * 5,access public
 * @abstract delrole 主要功能：删除角色信息。这个主要有两个逻辑：1：显示角色列表 2；接收ajax发送过来的role_id，然后删除角色
 * */

class Role extends IndexController
{
    /*
     * 这个是显示角色列表的一个功能
     * */
    public function rolelist()
    {
        /*第一个逻辑功能：显示出角色的列表*/
        $role = $this->role->paginate(5);
        $total = $this->role->count();
        $listrow = 5;
        //实例化分页类
        $page = new Page($total, $listrow);
        $show = $page->fpage();
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('rolelist', $role);

        return $this->fetch();
    }

    /*
    * 这个是添加角色的一个功能
    * @param $_POST 是一个一维数组
    * */
    public function addrole()
    {
        /*第一个逻辑功能：显示出添加角色的列表*/
        if (empty($_POST)) {
            $auth = $this->auth->select(); //要循环显示出auth权限
            $this->assign('authlist', $auth);
            return $this->fetch();
        } else {
            //验证接收的数据，实例化角色模型（验证）
            $rm = new RoleModel();
            $validate = $rm->roleValidate($_POST);
            //验证接收的数据
            if ($validate == 'true') {
                //数据验证成功就实例化服务模型
                $rs = new RoleService();
                if ($rs->addRole($_POST)) {
                    $this->success('添加角色成功', "role/rolelist");
                } else {
                    $this->error('添加角色失败', "role/addrole");
                }
            } else {
                $this->error($validate, "role/addrole");    //返回错误的信息,然后跳转
            }
        }
    }

    /*
     * 这是一个修改角色信息的功能
     * */
    public function editrole()
    {
        if (empty($_POST)) {
            $role = $this->role->select();
            $this->assign('rolelist', $role);

            $am = $this->auth->select();
            $this->assign('authlist', $am);

            return $this->fetch();
        } else {
            //验证接收的数据，实例化角色模型（验证）
            $rm = new RoleModel();
            $validate = $rm->roleValidate($_POST, 'edit');
            //验证接收的数据
            if ($validate == 'true') {
                //数据验证成功就实例化服务模型
                $rs = new RoleService();
                if ($rs->editRole($_POST)) {
                    $this->success('修改角色成功', "role/rolelist");
                } else {
                    $this->error('修改角色失败', "role/editrole");
                }
            } else {
                $this->error($validate, "role/editRole");    //返回错误的信息,然后跳转
            }
        }
    }

    /*
     * 这是接收ajax发送的role_id,此功能是点击角色名就自动选择所有的ids出来
     * @return json 返回一个json类型的对象集
     * */
    public function role_msg()
    {
        $rl = $this->role->where('role_id', $_POST['role_id'])->find();
        echo json_encode($rl);
    }

    /*
     * 这是一个删除角色信息的方法
     * */
    public function delrole()
    {
        if (empty($_POST)) {
            $role = $this->role->paginate(5);
            $total = $this->role->count();
            $listrow = 5;
            //实例化分页类
            $page = new Page($total, $listrow);
            $show = $page->fpage();
            $this->assign('rolelist', $role);
            $this->assign('page', $show);// 赋值分页输出

            return $this->fetch();
        } else {
            /*接收删除ajax发送过来的id，然后删除数据库的信息*/
            $role_id = $_POST['role_id'];
            $fl = $this->role->where('role_id', $role_id)->delete();
            if ($fl) {
                echo "删除成功";
            } else {
                echo "删除失败";
            }
        }
    }
}