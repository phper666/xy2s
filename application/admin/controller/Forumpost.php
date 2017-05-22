<?php
/**
 * Created by PhpStorm.
 * User: liyuzhao
 * Date: 2016/10/29
 * Time: 20:56
 */
namespace app\admin\controller;
use think\Db;
use page\Page;
use think\Session;
use app\admin\model\ForumModel;

class Forumpost extends IndexController
{
    //版主的帖子管理
    public function forumpost()
    {
        //查询当前用户/版主管理的板块
        $fac = $this->forum_all_column->where('am_id',Session::get('am_id'))->select();
        $fsc = $this->forum_sub_column->select();
        $fp = $this->forum_post->select();
        $this->assign('fac',$fac);
        $this->assign('fsc',$fsc);
        $this->assign('fp',$fp);
        //查询出管理板块的帖子
        return $this->fetch();
    }

    //添加版块子栏目
    public function addforumpost()
    {

        if(empty($_POST)){
            //显示出总栏目的信息
            $fac = $this->forum_all_column->where('am_id',Session::get('am_id'))->select();
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
                    $this->success("子栏目添加成功","forumpost/forumpost");
                }else{
                    $this->error("子添加失败","forumpost/forumpost");
                }
            }else{
                $this->error($validate, "forumpost/forumpost");    //返回错误的信息,然后跳转
            }
        }
    }
    
    /*
     * 帖子管理
     * */
    public function postguanli()
    {
        //管理的栏目和子栏目都查询出来
        $f = table_cognate('es_forum_all_column','es_forum_sub_column','fac_id','am_id='.Session::get('am_id'));
        $this->assign('f',$f);
        if(empty($_POST)){
            //显示指定的帖子
            $fsc = input('param.fsc_id');
            $p = $this->forum_post->where('fsc_id',$fsc)->paginate(10);
            $total = $this->forum_post->where('fsc_id',$fsc)->count();
            $listrow = 10;
            //实例化分页类
            $page = new Page($total, $listrow);
            $show = $page->fpage();
            $this->assign('p', $p);
            $this->assign('page', $show);// 赋值分页输出

        }else{
            //显示指定的栏目帖子
            $fsc = $_POST['fsc_id'];
            $p = $this->forum_post->where('fsc_id',$fsc)->paginate(10);
            $total = $this->forum_post->where('fsc_id',$fsc)->count();
            $listrow = 10;
            //实例化分页类
            $page = new Page($total, $listrow);
            $show = $page->fpage();
            $this->assign('p', $p);
            $this->assign('page', $show);// 赋值分页输出
        }
        return $this->fetch();
    }

    /*
     * 删帖
     * */
    public function post_del()
    {
        //删除帖子
        $fp_id = $_POST['fp_id'];
        $fl = $this->forum_post->where('fp_id', $fp_id)->delete();
        if ($fl) {
            echo "删除成功";
        } else {
            echo "删除失败";
        }
    }

    /*
     * 修改顶置贴
     * */
    public function post_dingzhi()
    {
        //删除帖子
        $fp_id = $_POST['fp_id'];
        $fp_dingzhi = $_POST['fp_dingzhi'];
        $fl = $this->forum_post->where('fp_id', $fp_id)->update(['fp_dingzhi'=>$fp_dingzhi]);
        if ($fl) {
            echo "更新成功";
        } else {
            echo "更新失败";
        }
    }
}