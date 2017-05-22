<?php
namespace app\admin\controller;

use think\Db;
use think\Session;

class System extends IndexController
{
    public function login()
    {
        /*第一个逻辑功能：显示登录页面*/
        if (empty($_POST)) {
            return $this->fetch();
        } else {
            /*第二个逻辑功能：判断登录的用户和验证码是否正确*/
            //接收登录的表单数据
            $am_name = $_POST['am_name'];
            $am_pwd = $_POST['am_password'];
            $captcha = $_POST['captcha'];
            if (!captcha_check($captcha)) {
                echo 'captcha error';
            } else {
                $am = $this->admin->where('am_name', $am_name)->find();

                if ($am_pwd == $am['am_password']) {
                    Session::set('am_name', $am_name);   //把用户名存到session中
                    Session::set('am_id', $am['am_id']);   //把用户名存到session中
                    Session::set('log_ip', $_SERVER["REMOTE_ADDR"]);   //把用户登录ip存到session中
                    Session::set('real_name', $am['real_name']);   //把姓名存到session中
                    Session::set('am_regtime', $am['am_regtime']);   //把注册时间存到session中
                    Session::set('am_logtime', date("Y-m-d H:i:s", time()));      //缓存登陆时间
                    $this->admin->where('am_name', $am_name)->update(['am_logtime' => date("Y-m-d H:i:s", time())]);//更新登录时间
                    echo 'Login success';
                } else {
                    echo 'password error';
                }
            }
        }
    }

    /* 用来验证前端输入的账号和验证码，并显示在前端页面
     public function validated(){
         //接收ajax发送的数据，然后进行验证
         if(!empty($_POST['am_name'])){
             $am_name = $_POST['am_name'];
             $am = $this->admin->where('am_name',$am_name)->find();
             if($am != null){
                 echo '存在';
             }else{
                 echo '账号错误';
             }
         }
 
         //接收ajax发送的数据，然后进行验证
         if(!empty($_POST['captcha'])){
             $captcha = $_POST['captcha'];
             if(!captcha_check($captcha)){
                 echo '验证码错误,请点击图片刷新';
             }else{
                 echo '验证码正确';
                 Session::set('captcha',$_POST['captcha']);
             }
         }
     }*/

    /*退出登录*/
    public function logout()
    {
        /*逻辑功能：只负责清空session和跳转到登录页面*/
        Session::delete('am_name');
        Session::delete('am_id');
        Session::delete('captcha');
        $this->success("退出成功", "system/login");
    }

    /*
     * 修改当前管理员密码
     * */
    public function modifypwd()
    {
        if (empty($_POST)) {
            return $this->fetch();
        } else {
            //修改密码
            $fl = $this->admin->where('am_id', Session::get('am_id'))->update(['am_password' => $_POST['am_password']]);
            if ($fl) {
                $this->success("密码更新成功", "system/modifypwd");
            } else {
                $this->error("密码更新失败", "system/modifypwd");
            }
        }
    }
}