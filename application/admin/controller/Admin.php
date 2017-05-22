<?php
namespace app\admin\controller;

use think\Session;
use think\Db;
use page\Page;
use app\admin\model\AdminModel;
use app\admin\service\AdminService;

/*
 * $this->admin 是已经在IndexController控制器实例化好的es_admin模型表
 * $this->role 是已经在IndexController控制器实例化好的es_role模型表
 * $this->auth 是已经在IndexController控制器实例化好的es_auth模型表
 * 也可以静态化模型表，例如AdminModel::where()->select();这个方法和Db('table')->where()->select()是一样的。
 * 一般现在在M层只需要做多关联表的时候才会用
 * 可以直接用Db->table('es_admin')获取到表的模型，我不这样做，是为了要经过M层来处理数据表，其实Db->table('table')也是经过
 * M层的，但是这个就不需要创建M层模块了，不像MVC了。所以我不才用这个方式，我把要访问的表放到model文件模型下，这样对于数据的逻辑
 * 处理就在M层了，where等方法已经被模型继承，在模型里我也继承了Model类，所以这样文件看起来更像MVC。实质不管哪个都是MVC，只不过
 * 文件看起来TP5的简单化了，不需要model模块也能像mvc模式一样操作。
 * 以上可能说的有误！以后就直接这样做，最好做在admin模块下创建service模块和logic模块，这两个模块logic都继承model，logic是写对
 * 数据处理的逻辑的，好比对数据的增删改成验证等，service是调用logic的功能，然后给c控制器去实例化调用。
 * */

/*
 * 用于调度es_role表操作和view下的Role模块的一个类
 * @author liyuzhao
 *
 * 以下7个函数功能
 * 1,
 * @access public
 * @abstract login 主要功能：显示登录页面和验证验证码和登录信息。两个逻辑：1：显示登录页面 2；验证码验证和账户和密码验证
 *
 * 2，
 * @access public
 * @abstract validated 主要功能：验证前端输入的账号和验证码，并显示在前端页面（ajax）。两个逻辑：1：验证ajax发送的账号 2：验证验证码
 *
 * 3，
 * @access public
 * @abstract adminlist 主要功能：显示管理员列表。一个逻辑：1：显示管理员列表和/分页
 *
 * 2，
 * @access public
 * @abstract addadmin 主要功能：添加管理员。两个逻辑：1：显示role角色名称 2：添加管理员/验证要添加的管理员数据
 *
 * 3，
 * @access public
 * $abstract editadmin 主要功能：修改管理员信息。两个逻辑：1：显示出管理员名称/显示出管理员对应的角色名称 2：验证更新的信息
 *
 * 4,access public
 * @abstract am_msg 主要功能：接收ajax的选择用户的id。一个逻辑：1：接收ajax发送的am_id,此功能是选择管理员名就自动显示出此管理员的信息
 * @return json 返回类型为json
 *
 * 5,
 * @access public
 * @abstract deladmin 主要功能：删除管理信息。两个逻辑：1：显示管理员列表 2；接收ajax发送过来的am_id，然后删除管理员
 * */

class Admin extends IndexController
{
    /*显示管理员列表*/
    public function adminlist()
    {
        $am = $this->admin->paginate(5);
        $total = $this->admin->count();
        $listrow = 5;
        //实例化分页类
        $page = new Page($total, $listrow);
        $show = $page->fpage();
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('adminlist', $am);

        /*查询角色表*/
        $role = $this->role->select();
        $this->assign('rolelist', $role);

        return $this->fetch();
    }

    /*添加管理员*/
    public function addadmin()
    {
        /*第一个功能逻辑：显示出role表的名称*/
        if (empty($_POST)) {
            $role = $this->role->select();
            $this->assign('rolelist', $role);
            return $this->fetch();
        }

        /*第二个逻辑功能：接收添加管理员的信息*/
        $am_name = $_POST['am_name'];
        $am_password = $_POST['am_password'];
        $real_name = $_POST['real_name'];
        $am_email = $_POST['am_email'];
        $role_id = $_POST['role_id'];
        $am_regtime = date("Y-m-d H:i:s", time());
        $reg_ip = $_SERVER["REMOTE_ADDR"];

        //验证接收的数据，实例化管理员模型（验证）
        $am = new AdminModel();
        $validate = $am->adminValidate($_POST);

        //验证接收的数据
        if ($validate == 'true') {
            $data = [
                'am_name' => $am_name,
                'am_password' => $am_password,
                'real_name' => $real_name,
                'am_email' => $am_email,
                'role_id' => $role_id,
                'am_regtime' => $am_regtime,
                'reg_ip' => $reg_ip
            ];
            //数据验证成功就实例化服务模型,并调用模型中的添加方法
            $as = new AdminService();
            if ($as->addAdmin($data)) {
                $this->success("管理员添加成功", "Admin/adminlist");
            } else {
                $this->error("管理员添加失败", "Admin/addadmin");
            }
        } else {
            $this->error($validate, "Admin/addadmin");    //返回错误的信息,然后跳转
        }

    }

    /*修改管理员信息*/
    public function editadmin()
    {
        /*第一个逻辑功能：显示出角色名和管理员名*/
        if (empty($_POST)) {
            $role = $this->role->select();
            $this->assign('rolelist', $role);

            $am = $this->admin->select();
            $this->assign('adminlist', $am);

            return $this->fetch();
        }

        /*第二个逻辑功能：接收要修改的信息，然后更新数据*/
        if (!empty($_POST)) {
            $am_name = $_POST['am_name'];
            $am_password = $_POST['am_password'];
            $real_name = $_POST['real_name'];
            $am_email = $_POST['am_email'];
            $role_id = $_POST['role_id'];
            $am_id = $_POST['am_id'];

            //验证接收的数据，实例化管理员模型（验证）
            $am = new AdminModel();
            //验证接收的数据
            $validate = $am->adminValidate($_POST, 'edit');
            if ($validate == 'true') {
                $data = [
                    'am_name' => $am_name,
                    'am_password' => $am_password,
                    'real_name' => $real_name,
                    'am_email' => $am_email,
                    'am_id' => $am_id
                ];

                //数据验证成功就实例化服务模型,并调用模型中的添加方法
                $as = new AdminService();
                if ($as->editAdmin($data)) {
                    $this->success("管理员信息更新成功", "Admin/adminlist");
                } else {
                    $this->error("管理员信息更新失败", "Admin/editadmin");
                }
            } else {
                $this->error($validate, "Admin/editadmin");    //返回错误的信息,然后跳转
            }
        }
    }

    /*用于接收ajax的选择用户的id，然后返回用户的信息*/
    public function am_msg()
    {
        if (!empty($_POST['am_id'])) {
            $am = $this->admin->where('am_id', $_POST['am_id'])->find();
            echo json_encode($am);
        }

    }

    /*删除管理员*/
    public function deladmin()
    {
        if (empty($_POST)) {
            $role = $this->role->select();
            $this->assign('rolelist', $role);

            $am = $this->admin->paginate(5);
            $total = $this->admin->count();
            $listrow = 5;
            //实例化分页类
            $page = new Page($total, $listrow);
            $show = $page->fpage();
            $this->assign('adminlist', $am);
            $this->assign('page', $show);// 赋值分页输出

            return $this->fetch();
        } else {
            /*接收删除ajax发送过来的id，然后删除数据库的信息*/
            $am_id = $_POST['am_id'];
            if ($am_id == '1') {
                echo "无权删除";
            } else {
                $fl = $this->admin->where('am_id', $am_id)->delete();
                if ($fl) {
                    echo "删除成功";
                } else {
                    echo "删除失败";
                }
            }
        }
    }
}