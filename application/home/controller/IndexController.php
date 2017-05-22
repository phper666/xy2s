<?php
/**
 * 所有admin模块下的控制器都继承这个控制器
 * 在这个控制器里面可以写入一些，一开始就要验证的规则，好比url权限验证
 */

namespace app\home\controller;

use think\Controller;
use think\Request;
use think\Cookie;
use think\Db;

class IndexController extends Controller
{
    private $user;     //得到的es_user模型实例
    private $admin;     //得到的es_admin模型实例
    private $school;     //得到的es_school模型实例
    private $goods;     //得到的es_goods模型实例
    private $region;     //得到的es_region模型实例
    private $gonggao;     //得到的es_gonggao模型实例
    private $category;     //得到的es_category模型实例
    private $msg;     //得到的es_category模型实例
    private $comments;     //得到的es_comment模型实例
    private $goodsrepor;     //得到的es_goodsrepor模型实例
    private $repornum;     //得到的es_repornum模型实例
    private $forum_all_column;      //得到的es_forum_all_column模型实例
    private $forum_sub_column;      //得到的es_forum_sub_column模型实例
    private $forum_comment;      //得到的es_forum_comment模型实例
    private $forum_post;      //得到的es_forum_post模型实例
    private $forum_reply;      //得到的es_forum_reply模型实例
    private $link;      //得到的es_link模型实例
    private $tongzhi;      //得到的es_tongzhi模型实例

    public function __construct()
    {
        parent::__construct();
        $this->user = Db('user');     //得到的es_user模型
        $this->admin = Db('admin');     //得到的es_admin模型
        $this->school = Db('school');     //得到的es_school模型
        $this->goods = Db('goods');     //得到的es_goods模型
        $this->region = Db('region');     //得到的es_region模型
        $this->gonggao = Db('gonggao');     //得到的es_gonggao模型
        $this->category = Db('category');     //得到的es_category模型
        $this->msg = Db('msg');     //得到的es_category模型
        $this->comments = Db('comments');     //得到的es_comment模型
        $this->goodsrepor = Db('goodsrepor');     //得到的es_goodsrepor模型
        $this->repornum = Db('repornum');     //得到的es_repornum模型
        $this->forum_all_column = Db('forum_all_column');    //得到的es_forum_all_column模型
        $this->forum_sub_column = Db('forum_sub_column');    //得到的es_forum_sub_column模型
        $this->forum_comment = Db('forum_comment');    //得到的es_forum_comment模型
        $this->forum_post = Db('forum_post');    //得到的es_forum_post模型
        $this->forum_reply = Db('forum_reply');    //得到的es_forum_reply模型
        $this->link = Db('link');    //得到的es_link模型
        $this->tongzhi = Db('tongzhi');    //得到的es_tongzhi模型

        //实例化请求
        $request = Request::instance();
        $c = $request->controller();    //控制器
        $a = $request->action();        //方法
        if ($c == 'Personal') {
            if ($a == 'personal' || $a == 'xgpersonal' || $a == 'upload') {
                if (Cookie::get('username') == null) {
                    $this->error("请你先登录", "Index/index");
                }
            }
        } else if ($c == 'Goods') {
            if ($a == 'addgoods') {
                if (Cookie::get('username') == null) {
                    $this->error("请你先登录", "Index/index");
                }
            }
        }

        /*
         * 把每个方法中需要的查询放到这里重用
         * */
        //显示链接
        $link = $this->link->select();
        $this->assign('link', $link);
        //查询通知
        $tz = $this->tongzhi->where('user_id', Cookie::get('user_id'))->where('tz_ready', '否')->count();
        $this->assign('tz', $tz);
        //显示出所有的学校
        $school = $this->school->select();
        $this->assign('schoollist', $school);
        //显示出商品栏目
        $cate = $this->category->select();
        $this->assign('category',$cate);
    }

    /*魔术方法，获取想要的模型*/
    public function __get($name)
    {
        return $this->$name;
    }
}