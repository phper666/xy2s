<?php
/**
 * Created by PhpStorm.
 * User: liyuzhao
 * Date: 2016/10/23
 * Time: 12:50
 */

namespace app\admin\controller;
use think\Db;
use page\Page;
use app\admin\model\ForumModel;

class Forum extends IndexController
{
    //栏目列表
    public function forumlist()
    {
        //显示总栏目信息
        $fac = $this->forum_all_column->select();
        $this->assign('faclist',$fac);
        //显示子栏目信息
        $fsc = $this->forum_sub_column->select();
        $this->assign('fsclist',$fsc);
        //管理员信息
        $am = $this->admin->select();
        $this->assign('amlist',$am);
        return $this->fetch();
    }

    //添加总栏目
    public function addallcolumn()
    {
        if(empty($_POST)){
            return $this->fetch();
        }else{
            $fac_name = $_POST['fac_name'];
            //验证数据
            $fm = new ForumModel();
            $validate = $fm->addallcolumnValidate($_POST);
            if($validate == 'true'){
                //验证成功，调用添加数据
                $arr = [
                    'fac_name'  =>  $_POST['fac_name'],
                    'add_time'  =>  date('Y-m-d H:i:s',time())
                ];
                $fl = $this->forum_all_column->insert($arr);
                if($fl){
                    $this->success("总栏目添加成功","forum/forumlist");
                }else{
                    $this->error("添加失败","forum/addallcolumn");
                }
            }else{
                $this->error($validate, "forum/addallcolumn");    //返回错误的信息,然后跳转
            }
        }
    }

    //添加子栏目
    public function addsubcolumn()
    {
        if(empty($_POST)){
            //显示出总栏目的信息
            $fac = $this->forum_all_column->select();
            $this->assign('faclist',$fac);
            return $this->fetch();
        }else{
            $fac_id = $_POST['fac_id'];
            $fsc_name = $_POST['fsc_name'];
            //验证数据
            $fm = new ForumModel();
            $validate = $fm->addsubcolumnValidate($_POST);
            if($validate == 'true'){
                //验证成功，调用添加数据
                $arr = [
                    'fsc_name'  =>  $_POST['fsc_name'],
                    'fac_id'    =>  $_POST['fac_id'],
                    'fsc_desc'  =>  $_POST['fsc_desc'],
                    'add_time'  =>  date('Y-m-d H:i:s',time())
                ];
                $fl = $this->forum_sub_column->insert($arr);
                if($fl){
                    $this->success("子栏目添加成功","forum/forumlist");
                }else{
                    $this->error("子添加失败","forum/addsubcolumn");
                }
            }else{
                $this->error($validate, "forum/addsubcolumn");    //返回错误的信息,然后跳转
            }
        }
    }

    //编辑栏目
    public function editcolumn()
    {
        if(empty($_POST)){
            //总栏目信息
            $faclist = $this->forum_all_column->select();
            //子栏目信息
            $fsclist = $this->forum_sub_column->select();
            //管理员信息
            $am = $this->admin->select();
            $this->assign('faclist',$faclist);
            $this->assign('fsclist',$fsclist);
            $this->assign('amlist',$am);
            return $this->fetch();
        }else{
            //前台已经验证数据，所以无需再验证，直接添加数据
            if($_POST['column_select'] == '1'){   //接收的是总栏目
                $arr = [
                    'fac_id'    =>  $_POST['fac_id'],
                    'am_id'     =>  $_POST['am_id'],
                    'fac_name'  =>  $_POST['fac_name'],
                ];
                $fl = $this->forum_all_column->update($arr);
                if($fl){
                    $this->success("总栏目更新成功","forum/forumlist");
                }else{
                    $this->error("总栏目失败","forum/addsubcolumn");
                }
            }else if($_POST['column_select'] == '2'){
                $arr = [
                    'fsc_id'    =>  $_POST['fsc_id'],
                    'fsc_name'  =>  $_POST['fsc_name'],
                    'fsc_desc'  =>  $_POST['fsc_desc'],
                ];
                $fl = $this->forum_sub_column->update($arr);
                if($fl){
                    $this->success("子栏目更新成功","forum/forumlist");
                }else{
                    $this->error("子栏目失败","forum/addsubcolumn");
                }
            }
        }
    }

    //删除栏目
    public function delcolumn()
    {
        if(empty($_POST)){
            //显示总栏目信息
            $fac = $this->forum_all_column->select();
            $this->assign('faclist',$fac);
            //显示子栏目信息
            $fsc = $this->forum_sub_column->select();
            $this->assign('fsclist',$fsc);
            return $this->fetch();
        }else{
            if($_POST['column_select'] == '1'){   //接收的是总栏目
                $fac_id = $_POST['fac_id'];
                //删除总栏目和所有归属的所有子栏目
                $this->forum_all_column->delete($fac_id);
                $this->forum_sub_column->where('fac_id',$fac_id)->delete();
                $this->success('删除成功',"forum/forumlist");
            }else if($_POST['column_select'] == '2'){
                $fsc_id = $_POST['fsc_id'];
                //删除子栏目
                $fl = $this->forum_sub_column->delete($fsc_id);
                if($fl){
                    $this->success("子栏目删除成功","forum/forumlist");
                }else{
                    $this->error("子栏目删除失败","forum/delcolumn");
                }
            }
        }

    }

    //获取前台发送过来总栏目id，然后返回要查找的版主id
    public function column_msg()
    {
        $fac_id = $_POST['fac_id'];
        $fac = $this->forum_all_column->where('fac_id',$fac_id)->find();
        $am = $this->admin->where('am_id',$fac['am_id'])->find();
        echo json_encode($am);
    }
}