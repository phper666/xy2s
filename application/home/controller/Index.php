<?php
namespace app\home\controller;

use think\Db;
use page\Page;
use ip\Ip;
use think\Cookie;

class Index extends IndexController
{
    /*
     * index页面
     * */
    public function index()
    {
        //打开首页就缓存有多少用户和有多少商品,和交易了多少比
        Cookie::set('goods_num',$this->goods->count());
        Cookie::set('user_num',$this->user->count());
        Cookie::set('goods_jiaoyi',$this->goods->where('is_delete',1)->count());

        //公告显示
        $gg = $this->gonggao->select();
        $this->assign('gglist',$gg);

        //栏目显示
        $cate = $this->category->select();
        $this->assign('catelist',$cate);

        //显示出所有学校的商品
        if (empty($_POST) && empty($_GET['cat_id'])) {
            //调用公共函数，关联两个表查询
            $a = table_cognate('es_school', 'es_goods', 'school_id');
            $total = count($a);//获取数据总数
            $listrow = 8;
            $page = new Page($total, $listrow);
            //这里暂时没有想到方法优化，先使用原生查询来关联两个表，并且规定每页显示多少数据,后期再优化，功能已实现
            $g = Db::query('select * from es_school s join es_goods g on s.school_id=g.school_id ' .'order by goods_id DESC '. $page->limit);
            $show = $page->fpage();   //获得分页显示
            $this->assign('page', $show);// 赋值分页输出
            $this->assign('goodslist', $g);
            //缓存学校的名字
            Cookie::set('school_name','所有学校',3600);

            return $this->fetch();
        }

        //如果有选择学校就显示出此学校的所有商品
        if(!empty($_POST['school_name'])) {
            //更新学校缓存
            Cookie::set('school_name',$_POST['school_name'],3600);
            $s = $this->school->where('school_name',$_POST['school_name'])->find();
            //调用公共函数，关联两个表查询
            $a = table_cognate('es_school', 'es_goods', 'school_id','a.school_id='.$s['school_id']);

            $total = count($a);//获取数据总数
            $listrow = 8;
            $page = new Page($total, $listrow);
            //这里暂时没有想到方法优化，先使用原生查询来关联两个表，并且规定每页显示多少数据,后期再优化，功能已实现
            $g = Db::query('select * from es_school s join es_goods g on s.school_id=g.school_id where s.school_id='.$s['school_id'].' '.'order by goods_id DESC '. $page->limit);
            $show = $page->fpage();   //获得分页显示
            $this->assign('page', $show);// 赋值分页输出
            $this->assign('goodslist', $g);
            $this->assign('school_name', $_POST['school_name']);

            return $this->fetch();
        }

        //接收前台的栏目id，如果没有选择学校就显示出所有的学校的栏目下的商品
        if(!empty($_GET['cat_id'])){
            //如果缓存是所有学校，就显示出所有学校的栏目的商品
            if(Cookie::get('school_name') == '所有学校'){
                $cat_id = $_GET['cat_id'];
                $c = $this->category->where('cat_id',$cat_id)->find();
                //调用公共函数，关联两个表查询
                $a = table_cognate('es_category', 'es_goods', 'cat_id','a.cat_id='.$c['cat_id']);

                $total = count($a);//获取数据总数
                $listrow = 8;
                $page = new Page($total, $listrow);
                //这里暂时没有想到方法优化，先使用原生查询来关联两个表，并且规定每页显示多少数据,后期再优化，功能已实现
                $g = Db::query('select * from es_category c join es_goods g on c.cat_id=g.cat_id where c.cat_id='.$c['cat_id'].' '.'order by goods_id DESC '. $page->limit);
                $show = $page->fpage();   //获得分页显示
                $this->assign('page', $show);// 赋值分页输出
                $this->assign('goodslist', $g);

                return $this->fetch();
            }else{
                /*
                 * 显示出固定学校的栏目下的商品
                 * */
                $school_name = Cookie::get('school_name');
                $s = $this->school->where('school_name',$school_name)->find();
                $a = $this->goods->where('school_id',$s['school_id'])->where('cat_id',$_GET['cat_id'])->paginate(8);
                $total = count($a);//获取数据总数
                $listrow = 8;
                $page = new Page($total, $listrow);
                //这里暂时没有想到方法优化，先使用原生查询来关联两个表，并且规定每页显示多少数据,后期再优化，功能已实现
                $g = Db::query('select * from es_goods '.'where school_id='.$s['school_id'].' and cat_id='.$_GET['cat_id'].' '.'order by goods_id DESC '. $page->limit);

                $show = $page->fpage();   //获得分页显示
                $this->assign('page', $show);// 赋值分页输出
                $this->assign('goodslist', $g);
                return $this->fetch();
            }
        }
    }

    /*
     * 选择学校页面
     * */
    public function check_school(){
        $school = $this->school->select();
        $this->assign('schoollist', $school);
        return $this->fetch();
    }

}