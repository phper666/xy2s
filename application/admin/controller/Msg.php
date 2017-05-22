<?php
/**
 * Created by PhpStorm.
 * User: liyuzhao
 * Date: 2016/10/19
 * Time: 22:04
 */
namespace app\admin\controller;
use think\Db;
use page\Page;

class Msg extends IndexController
{
    /*
     * 显示和删除反馈信息
     * */
    public function msglistdel()
    {
        if (empty($_POST)) {
            $msg = $this->msg->paginate(10);
            $total = $this->msg->count();
            $listrow = 10;
            //实例化分页类
            $page = new Page($total, $listrow);
            $show = $page->fpage();
            $this->assign('page', $show);// 赋值分页输出
            $this->assign('msglist', $msg);
            return $this->fetch();
        }else {
            /*接收删除ajax发送过来的id，然后删除数据库的信息*/
            $msg_id = $_POST['msg_id'];
            $fl = $this->msg->delete($msg_id);
            if ($fl) {
                echo "删除成功";
            } else {
                echo "删除失败";
            }
        }
    }

    /*
     * 显示通知和删除通知
     * */
    public function tongzhilistdel()
    {
        if(empty($_POST)){
            $tz = $this->tongzhi->paginate(10);
            $total = $this->tongzhi->count();
            $listrow = 10;
            //实例化分页类
            $page = new Page($total, $listrow);
            $show = $page->fpage();
            $this->assign('tz',$tz);
            $this->assign('page', $show);// 赋值分页输出

            //显示管理员账号
            $a = $this->admin->select();
            $this->assign('a',$a);
            //显示用户账号
            $u = $this->user->select();
            $this->assign('u',$u);

            return $this->fetch();
        }else {
            /*接收删除ajax发送过来的id，然后删除数据库的信息*/
            $tz_id = $_POST['tz_id'];
            $fl = $this->tongzhi->delete($tz_id);
            if ($fl) {
                echo "删除成功";
            } else {
                echo "删除失败";
            }
        }
    }

    /*
     * 发送通知
     * */
    public function tongzhi()
    {
        if(empty($_POST)){
            //显示所有用户的信息
            $u = $this->user->select();
            $this->assign('userlist',$u);
            return $this->fetch();
        }else{
            //前端已经验证过数据，所以不用验证，也无需拼接和检验用户
            $arr = [
                'am_id'     =>  $_POST['am_id'],
                'user_id'   =>  $_POST['user_id'],
                'tz_content'=>  $_POST['tz_content'],
                'add_time'  =>  date('Y-m-d H:i:s',time())
            ];
            $fl = $this->tongzhi->insert($arr);
            if($fl){
                $this->success("通知已发送","msg/tongzhi");
            }else{
                $this->error("通知发送失败","msg/tongzhi");
            }
        }
    }
}