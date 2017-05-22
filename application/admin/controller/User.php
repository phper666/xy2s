<?php
namespace app\admin\controller;

use think\Db;
use page\Page;

class User extends IndexController
{
    /*
     * 显示所有的用户
     * */
    public function userlist()
    {
        if (empty($_POST)) {
            $user = $this->user->paginate(10);
            $total = $this->user->count();
            $listrow = 10;
            //实例化分页类
            $page = new Page($total, $listrow);
            $show = $page->fpage();
            $this->assign('page', $show);// 赋值分页输出
            $this->assign('userlist', $user);
            return $this->fetch();
        }
    }

    /*
     * 删除用户
     * */
    public function deluser()
    {
        if (empty($_POST)) {
            $user = $this->user->paginate(10);
            $total = $this->user->count();
            $listrow = 10;
            //实例化分页类
            $page = new Page($total, $listrow);
            $show = $page->fpage();
            $this->assign('page', $show);// 赋值分页输出
            $this->assign('userlist', $user);
            return $this->fetch();
        } else {
            //接收ajax传来的user_id，然后删除用户
            $user_id = $_POST['user_id'];
            $fl = $this->user->delete($user_id);
            if ($fl) {
                echo 'del success';
            } else {
                echo 'del error';
            }
        }
    }
}