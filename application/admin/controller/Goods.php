<?php
namespace app\admin\controller;

use think\Db;
use page\Page;
use app\admin\model\GoodsModel;
use app\admin\service\GoodsService;

class Goods extends IndexController
{
    /*
    * 显示全部发布商品信息列表
    * */
    public function goodslist()
    {
        if (empty($_POST)) {
            $u = $this->user->select();
            $g = $this->goods->paginate(10);
            $total = $this->goods->count();
            $listrow = 10;
            //实例化分页类
            $page = new Page($total, $listrow);
            $show = $page->fpage();
            $this->assign('page', $show);// 赋值分页输出
            $this->assign('goodslist', $g);
            $this->assign('userlist', $u);

            return $this->fetch();
        }else{
            /*接收删除ajax发送过来的id，然后删除数据库的信息*/
            $goods_id = $_POST['goods_id'];
            //删除要删除的图片缓存
            $a = $this->goods->where('goods_id',$goods_id)->find();
            unlink(ABSOLUTE_STATIC.$a['goods_thumb1']);
            unlink(ABSOLUTE_STATIC.$a['goods_thumb2']);
            unlink(ABSOLUTE_STATIC.$a['goods_img1']);
            unlink(ABSOLUTE_STATIC.$a['goods_img2']);

            $fl = $this->goods->delete($goods_id);
            //删除举报内容
            $this->goodsrepor->where('goods_id',$goods_id)->delete();
            //删除举报次数
            $this->repornum->where('goods_id',$goods_id)->delete();
            if ($fl) {
                echo "删除成功";
            } else {
                echo "删除失败";
            }
        }
    }

    /*
     * 举报商品信息
     * */
    public function goodsrepor()
    {
        if (empty($_POST)) {
            //查询商品举报数目表
            $repornum = $this->repornum->order('rn_num desc')->paginate(10);

            $total = $this->repornum->count();
            $listrow = 10;
            //实例化分页类
            $page = new Page($total, $listrow);
            $show = $page->fpage();
            $this->assign('page', $show);// 赋值分页输出
            $this->assign('repornum',$repornum);
            //查询商品举报表
            $repor = $this->goodsrepor->select();
            $this->assign('repor',$repor);

            return $this->fetch();
        }else {
            /*接收删除ajax发送过来的id，然后删除数据库的信息*/
            $goods_id = $_POST['goods_id'];
            //删除要删除的图片缓存
            $a = $this->goods->where('goods_id',$goods_id)->find();
            unlink(ABSOLUTE_STATIC.$a['goods_thumb1']);
            unlink(ABSOLUTE_STATIC.$a['goods_thumb2']);
            unlink(ABSOLUTE_STATIC.$a['goods_img1']);
            unlink(ABSOLUTE_STATIC.$a['goods_img2']);

            $fl = $this->goods->delete($goods_id);
            //删除举报内容
            $this->goodsrepor->where('goods_id',$goods_id)->delete();
            //删除举报次数
            $this->repornum->where('goods_id',$goods_id)->delete();

            if ($fl) {
                echo "删除成功";
            } else {
                echo "删除失败";
            }
        }
    }
}