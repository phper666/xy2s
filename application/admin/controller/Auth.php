<?php
namespace app\admin\controller;

use think\Db;
use page\Page;
use app\admin\model\AuthModel;
use app\admin\service\AuthService;

/*
 * 用于调度es_auth表操作和view下的Auth模块的一个类
 * @author liyuzhao
 * */

class Auth extends IndexController
{
    /*
     * 这是一个显示权限列表功能
     * */
    public function authlist()
    {
        if (empty($_POST)) {
            $auth = $this->auth->paginate(10);
            $total = $this->auth->count();
            $listrow = 10;
            //实例化分页类
            $page = new Page($total, $listrow);
            $show = $page->fpage();
            $this->assign('page', $show);// 赋值分页输出
            $this->assign('authlist', $auth);

            return $this->fetch();
        }
    }

    /*
     * 添加权限
     * */
    public function addauth()
    {
        if (empty($_POST)) {
            $auth = $this->auth->select();
            $this->assign('authlist', $auth);
            return $this->fetch();
        } else {
            //实例化权限模型
            $am = new AuthModel();
            $validate = $am->authValidate($_POST);
            if ($validate == 'true') {
                //数据验证通过后，实例化并调用AuthService的权限添加函数
                $as = new AuthService();
                $fl = $as->addAuth($_POST);
                if ($fl) {
                    $this->success("权限添加成功", "auth/authlist");
                } else {
                    $this->error("权限添加失败", "auth/addauth");
                }
            } else {
                $this->error($validate, "auth/addauth");
            }
        }
    }

    /*
     * 接收ajax的auth_id，然后查询auth表的信息
     * */
    public function auth_msg()
    {
        $a = $this->auth->where('auth_pid', $_POST['auth_id'])->select();
        echo json_encode($a);
    }

    /*
     * 接收ajax的auth_level，然后查询auth表的信息
     * */
    public function auth_level()
    {
        $a = $this->auth->where('auth_level', $_POST['auth_level'])->select();
        echo json_encode($a);
    }

    /*
     * 修改权限
     * */
    public function editauth()
    {
        if (empty($_POST)) {
            $auth = $this->auth->select();
            $this->assign('authlist', $auth);
            return $this->fetch();
        } else {
            //实例化权限模型
            $am = new authModel();
            $validate = $am->authValidate($_POST);
            if ($validate == 'true') {
                //数据验证通过后，实例化并调用AuthService的权限添加函数
                $as = new AuthService();
                $fl = $as->editAuth($_POST);
                if ($fl) {
                    $this->success("权限更新成功", "auth/authlist");
                } else {
                    $this->error("权限更新失败", "auth/addauth");
                }
            } else {
                $this->error($validate, "auth/addauth");
            }
        }
    }

    /*
     * 删除权限
     * */
    public function delauth()
    {
        if (empty($_POST)) {
            $auth = $this->auth->paginate(10);
            $total = $this->auth->count();
            $listrow = 10;
            //实例化分页类
            $page = new Page($total, $listrow);
            $show = $page->fpage();
            $this->assign('page', $show);// 赋值分页输出
            $this->assign('authlist', $auth);

            return $this->fetch();
        } else {
            /*接收删除ajax发送过来的id，然后删除数据库的信息*/
            $auth_id = $_POST['auth_id'];
            $fl = $this->auth->where('auth_id', $auth_id)->delete();
            if ($fl) {
                echo "删除成功";
            } else {
                echo "删除失败";
            }
        }
    }
}