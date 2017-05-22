<?php
/**
 * 所有admin模块下的控制器都继承这个控制器
 * 在这个控制器里面可以写入一些，一开始就要验证的规则，好比url权限验证
 */

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;

/*
 *以下2个函数功能
 * 1，
 * @access public
 * @abstract __construct 主要功能：实例化得到各表的实例，然后验证权限。两个逻辑：1，判断是否为超级管理员，如果是则不验证权限，如果不是则只能访问自己所属的模块和方法 2，未登录的时候只开放一个验证码页面和登录页面
 *
 * 2.
 * @access public
 * @abstract __get 主要功能：这个是魔术方法，主要获取父类中的私有变量（此变量可以在子类中直接使用） 一个逻辑：1，返回私有变量
 * */

class IndexController extends Controller
{
    private $admin;     //得到的es_admin模型实例
    private $role;      //得到的es_Role模型实例
    private $auth;      //得到的es_Auth模型实例
    private $gg;      //得到的es_gonggao模型实例
    private $school;      //得到的es_gonggao模型实例
    private $region;      //得到的es_region模型实例
    private $category;      //得到的es_category模型实例
    private $link;      //得到的es_link模型实例
    private $user;      //得到的es_user模型实例
    private $msg;      //得到的es_msg模型实例
    private $repornum;      //得到的es_repornum模型实例
    private $goodsrepor;      //得到的es_goodsrepor模型实例
    private $forum_all_column;      //得到的es_forum_all_column模型实例
    private $forum_sub_column;      //得到的es_forum_sub_column模型实例
    private $forum_comment;      //得到的es_forum_comment模型实例
    private $forum_post;      //得到的es_forum_post模型实例
    private $forum_reply;      //得到的es_forum_reply模型实例
    private $tongzhi;      //得到的es_tongzhi模型实例

    public function __construct()
    {
        $this->admin = Db('admin');     //得到的es_admin模型
        $this->auth = Db('auth');     //得到的es_Auth模型
        $this->role = Db('role');    //得到的es_Role模型
        $this->gg = Db('gonggao');    //得到的es_gonggao模型
        $this->school = Db('school');    //得到的es_school模型
        $this->region = Db('region');    //得到的es_region模型
        $this->category = Db('category');    //得到的es_category模型
        $this->link = Db('link');    //得到的es_link模型
        $this->user = Db('user');    //得到的es_user模型
        $this->goods = Db('goods');    //得到的es_goods模型
        $this->msg = Db('msg');    //得到的es_msg模型
        $this->repornum = Db('repornum');    //得到的es_repornum模型
        $this->goodsrepor = Db('goodsrepor');    //得到的es_goodsrepor模型
        $this->forum_all_column = Db('forum_all_column');    //得到的es_forum_all_column模型
        $this->forum_sub_column = Db('forum_sub_column');    //得到的es_forum_sub_column模型
        $this->forum_comment = Db('forum_comment');    //得到的es_forum_comment模型
        $this->forum_post = Db('forum_post');    //得到的es_forum_post模型
        $this->forum_reply = Db('forum_reply');    //得到的es_forum_reply模型
        $this->tongzhi = Db('tongzhi');    //得到的es_tongzhi模型

        parent::__construct();
        $am_name = Session::get('am_name');
        //实例化请求
        $request = Request::instance();
        $c = $request->controller();
        $a = $request->action();
        $canew = $c . '-' . $a;
        if (!empty($am_name)) {
            $am = Db::table('es_admin')->field('role_id')->where('am_name', $am_name)->find();
            if ($am['role_id'] != 0) {
                //先判断是否为超级管理员，如果不为超级管理员，url访问只能访问自己的操作
                $am = Db::table('es_admin')->where('am_name', $am_name)->find();
                $auth_ac = Db::table('es_role')->field('role_auth_ac')->where('role_id', $am['role_id'])->find();
                $auth_ac = implode(',', $auth_ac);
                $auth_ac = $auth_ac . ',' . 'Index-index,Index-head,Index-left,Index-right,System-login,System-logout,System-modifypwd';
                $auth_ac = explode(',', $auth_ac);   //把值放到一维数组中
                $fl = in_array($canew, $auth_ac);
                if ($fl === false) {
                    $this->error("你无权访问，自动跳回首页", "index/index");
                }
            }
        } else {
            if ($canew !== 'System-login' && $canew !== 'System-validated') {    //开放登录页面和验证页面
                $this->success('请您先登录', "system/login");//查不到就不让访问
            }
        }
    }

    /*魔术方法，获取想要的模型*/
    public function __get($name)
    {
        return $this->$name;
    }
}