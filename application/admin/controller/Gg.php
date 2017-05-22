<?php
namespace app\admin\controller;

use think\Db;
use page\Page;
use think\Session;
use app\admin\validate;
use app\admin\model\GgModel;
use app\admin\service\GgService;

class Gg extends IndexController
{
    /*
     * 显示所有发布的公告列表
     * */
    public function gglist()
    {
        if (empty($_POST)) {
            //如果是超级管理员就显示所有的公告，否则只显示该管理者的公告
            if (Session::get('am_name') == 'admin') {
                $gg = $this->gg->paginate(10);
                $total = $this->gg->count();
                $listrow = 10;
                //实例化分页类
                $page = new Page($total, $listrow);
                $show = $page->fpage();
                $this->assign('page', $show);// 赋值分页输出
                $this->assign('gglist', $gg);
            } else {
                $gg = $this->gg->where('am_name', Session::get('am_name'))->paginate(10);
                $total = $this->gg->where('am_name', Session::get('am_name'))->count();
                $listrow = 10;
                //实例化分页类
                $page = new Page($total, $listrow);
                $show = $page->fpage();
                $this->assign('page', $show);// 赋值分页输出
                $this->assign('gglist', $gg);
            }
            return $this->fetch();
        }
    }

    /*
     * 添加公告
     * */
    public function addgg()
    {
        if (empty($_POST)) {
            return $this->fetch();
        } else {
            //实例化GgModel模型，对接收的数据进行验证
            $gm = new GgModel();
            $validate = $gm->ggValidate($_POST);
            if ($validate == 'true') {
                //如果验证通过就调用GgService的添加函数
                $gs = new GgService();
                $fl = $gs->addGg($_POST);
                if ($fl) {
                    $this->success("公告添加成功", "gg/gglist");
                } else {
                    $this->error("公告添加成功失败", "gg/addgg");
                }
            }
        }
    }

    /*
     * 修改公告信息
     * */
    public function editgg()
    {
        if (empty($_POST)) {
            //如果是超级管理员就显示所有的公告，否则只显示该管理者的公告
            if (Session::get('am_name') == 'admin') {
                $gg = $this->gg->select();
                $this->assign('gglist', $gg);
            } else {
                $gg = $this->gg->where('am_name', Session::get('am_name'))->select();
                $this->assign('gglist', $gg);
            }
            return $this->fetch();
        } else {
            //实例化GgModel模型，对接收的数据进行验证
            $gm = new GgModel();
            $validate = $gm->ggValidate($_POST, 'edit');

            if ($validate == 'true') {
                //如果验证通过就调用GgService的添加函数
                $gs = new GgService();
                $fl = $gs->editGg($_POST);
                if ($fl) {
                    $this->success("公告更新成功", "gg/gglist");
                } else {
                    $this->error("公告更新失败", "gg/addgg");
                }
            }else {
                $this->error($validate, "gg/addgg");
            }
        }
    }

    /*
     * 删除公告
     * */
    public function delgg()
    {
        if (empty($_POST)) {
            //如果是超级管理员就显示所有的公告，否则只显示该管理者的公告
            if (Session::get('am_name') == 'admin') {
                $gg = $this->gg->paginate(10);
                $total = $this->gg->count();
                $listrow = 10;
                //实例化分页类
                $page = new Page($total, $listrow);
                $show = $page->fpage();
                $this->assign('page', $show);// 赋值分页输出
                $this->assign('gglist', $gg);
            } else {
                $gg = $this->gg->where('am_name', Session::get('am_name'))->paginate(10);
                $total = $this->gg->where('am_name', Session::get('am_name'))->count();
                $listrow = 10;
                //实例化分页类
                $page = new Page($total, $listrow);
                $show = $page->fpage();
                $this->assign('page', $show);// 赋值分页输出
                $this->assign('gglist', $gg);
            }

            return $this->fetch();
        } else {
            //接收ajax传来的gg_id来删除公告
            $gs = new GgService();
            $fl = $gs->delGg($_POST);
            if ($fl) {
                echo '删除成功';
            } else {
                echo '删除失败';
            }
        }
    }

    /*
     * 接收ajax传来的gg_id来改变状态
     * */
    public function gg_status()
    {
        //更改状态
        $gs = new GgService();
        $fl = $gs->updateStatus($_POST);
        if ($fl) {
            echo '更新成功';
        } else {
            echo '更新失败';
        }
    }
}

