<?php
/**
 * Created by PhpStorm.
 * User: liyuzhao
 * Date: 2016/10/20
 * Time: 0:04
 */

namespace app\home\controller;

use think\Db;
use page\Page;
use think\Cookie;

class Search extends IndexController
{
    public function searchgoods()
    {
        //公告显示
        $gg = $this->gonggao->select();
        $this->assign('gglist',$gg);

        //显示出商品栏目
        $cate = $this->category->select();
        $this->assign('catelist',$cate);

        $keywords = $_POST['keyword'];  //获取要查询的关键字

        //如果学校的缓存是所有学校，就显示所有学校按关键字搜索的物品
        if (Cookie::get('school_name') == '所有学校'){

            $g = $this->goods->where('keywords','like','%'.$keywords.'%')->order('goods_id desc')->paginate(12);
            $total = count($g);//获取数据总数
            $listrow = 12;
            $page = new Page($total, $listrow);
            $show = $page->fpage();   //获得分页显示
            $this->assign('page', $show);// 赋值分页输出
            $this->assign('goodslist', $g);// 赋值分页输出
            //缓存学校的名字
            Cookie::set('school_name','所有学校',3600);
        }else{
            $school_name = Cookie::get('school_name');

            $s = $this->school->where('school_name',$school_name)->find();
            //查询此学校下的所有商品
            $g = $this->goods->where('school_id',$s['school_id'])->where('keywords','like','%'.$keywords.'%')->order('goods_id desc')->paginate(6);

            $total = count($g);//获取数据总数
            $listrow = 6;
            $page = new Page($total, $listrow);

            $show = $page->fpage();   //获得分页显示
            $this->assign('page', $show);// 赋值分页输出
            $this->assign('goodslist', $g);
        }
        return $this->fetch();
    }
}