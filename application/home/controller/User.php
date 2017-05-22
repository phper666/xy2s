<?php
namespace app\home\controller;

use think\Db;
use think\Cookie;
use app\home\model\UserModel;
use app\home\service\UserService;

class User extends IndexController
{
    /*
     * 登录
     * */
    public function login()
    {
        if (empty($_POST)) {
            return $this->fetch();
        } else {
            //验证账号密码
            $u = $this->user->where('username', $_POST['username'])->find();
            if ($u['userpwd'] == $_POST['password']) {
                Cookie::set('username', $u['username'], 3600*12);    //缓存用户名
                Cookie::set('account_name', $u['account_name'], 3600*12);    //缓存用户真实姓名名
                Cookie::set('user_id', $u['user_id'], 3600*12);      //缓存用户id
                Cookie::set('user_img', $u['user_img'], 3600*12);      //缓存用户头像路径
                $this->success("登录成功,跳转到首页", "index/index");
            } else {
                $this->error("用户名或密码错误,请重新输入", "user/login");
            }
        }
    }

    /*
     * 用户注册
     * */
    public function register()
    {
        if (empty($_POST)) {
            return $this->fetch();
        } else {
            if (empty($_POST['email'])) {
                $_POST['email'] = '';
            }
            $username = $_POST['username'];
            $userpwd = $_POST['password'];
            $user_email = $_POST['email'];
            $question = $_POST['question'];
            $answer = $_POST['answer'];
            $user_regtime = date("Y-m-d H:i:s", time());
            $ip = $_SERVER["REMOTE_ADDR"];

            //验证接收的数据，实例化用户模型（验证）
            $um = new UserModel();
            $validate = $um->userValidate($_POST);

            if ($validate == 'true') {
                $data = [
                    'username' => $username,
                    'userpwd' => $userpwd,
                    'user_email' => $user_email,
                    'question' => $question,
                    'answer' => $answer,
                    'user_regtime' => $user_regtime,
                    'ip' => $ip
                ];
                //数据验证成功就实例化服务模型,并调用模型中的注册方法
                $us = new UserService();
                if ($us->regUser($data)) {
                    $this->success("注册成功", "index/index");
                } else {
                    $this->error("注册失败", "user/register");
                }
            } else {
                $this->error($validate, "user/register");    //返回错误的信息,然后跳转
            }

        }
    }

    /*
     * 接收ajax发送的用户名来查询是否存在
     * @echo bool 存在输出true，反之为false
     * */
    public function check_user()
    {
        //查询用户是否存在
        $u = $this->user->where('username', $_POST['username'])->find();
        if ($u) {
            echo json_encode($u);
        } else {
            echo 'false';
        }
    }

    /*
     * 重置密码
     * */
    public function resetpw()
    {
        // 临时关闭当前模板的布局功能
        $this->view->engine->layout(false);
        if (empty($_POST)) {
            return $this->fetch();
        } else {
            //判断用户的密保和答案是否正确，如果正确则直接更新密码
            $username = $_POST['username'];
            $question = $_POST['question'];
            $answer = $_POST['answer'];
            $password = $_POST['password'];
            $um = $this->user->where('username', $username)->find();
            if ($question == $um['question'] && $answer == $um['answer']) {
                $fl = $this->user->where('user_id', $um['user_id'])->update(['userpwd' => $password]);
                if ($fl) {
                    //重置成功自动跳转
                    $this->success("密码重置成功", "index/index");
                } else {
                    $this->error("密码重置失败", "user/resetpw");
                }
            }
        }
    }

    /*
     * 注册协议
     * */
    public function registeragreement()
    {
        // 临时关闭当前模板的布局功能
        $this->view->engine->layout(false);
        return $this->fetch();
    }

    /*
     * 退出登录
     * */
    public function logout()
    {
        //删除掉所有临时的上传图
        foreach(check_dir(ABSOLUTE_HOME . 'uploads/tmp_img') as $k=>$v){
            if(strpos($v,'.jpg') != false){
                unlink(ABSOLUTE_HOME . 'uploads/tmp_img/'.$v);
            }
        }
        //目录存在就删除、删除用户临时上传图片目录
        if(file_exists(ABSOLUTE_HOME . 'uploads/tmp_img/'.Cookie::get('username'))){
            delete_dir(ABSOLUTE_HOME . 'uploads/tmp_img/'.Cookie::get('username'));
        }
        Cookie::delete('username');
        Cookie::delete('user_id');
        Cookie::delete('account_name');
        Cookie::delete('user_img');
        Cookie::delete('school_name');
        Cookie::delete('goods_num');
        Cookie::delete('user_num');
        Cookie::delete('goods_jiaoyi');
        $this->success("退出成功", "index/index");
    }
}