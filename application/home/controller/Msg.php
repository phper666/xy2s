<?php
/**
 * Created by PhpStorm.
 * User: liyuzhao
 * Date: 2016/10/19
 * Time: 22:41
 */

namespace app\home\controller;
use think\Db;
use think\Cookie;

class Msg extends IndexController
{
    /*
  * 意见反馈页面
  * */
    public function suggest(){
        $this->view->engine->layout(false);
        return $this->fetch();
    }
    
    //反馈信息添加
    public function addmsg()
    {
        //添加反馈信息表
        $arr = [
            'msg_content'     =>    $_POST['suggest'],
            'username'        =>    Cookie::get('username'),
            'add_time'        =>    date("Y-m-d H:i:s", time())
        ];

        $fl = $this->msg->insert($arr);
        if($fl){
            $this->success("感谢您的反馈","index/index");
        }else{
            $this->error("反馈失败,请尝试刷新","msg/suggest");
        }
    }
}