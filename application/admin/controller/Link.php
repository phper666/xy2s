<?php
namespace app\admin\controller;

use think\Db;
use page\Page;
use app\admin\model\LinkModel;
use app\admin\service\LinkService;

class Link extends IndexController
{
    /*
     * 显示所有链接的信息
     * */
    public function linklist()
    {
        if (empty($_POST)) {
            /*第一个逻辑功能：显示出链接的列表*/
            $link = $this->link->paginate(10);
            $total = $this->link->count();
            $listrow = 10;
            //实例化分页类
            $page = new Page($total, $listrow);
            $show = $page->fpage();
            $this->assign('page', $show);// 赋值分页输出
            $this->assign('linklist', $link);

            return $this->fetch();
        }
    }

    /*
     * 添加链接
     * */
    public function addlink()
    {
        if (empty($_POST)) {
            return $this->fetch();
        } else {
            //实例化LinkModel来验证数据
            $lm = new LinkModel();
            $validate = $lm->linkValidate($_POST);
            if ($validate == 'true') {
                //实例化model的逻辑层
                $fl = new LinkService();
                if ($fl->addLink($_POST)) {
                    $this->success("添加链接成功", "link/linklist");
                } else {
                    $this->error("添加失败", "link/addlink");
                }
            } else {
                $this->error($validate, "link/addlink");
            }
        }
    }

    /*
     * 修改链接
     * */
    public function editlink()
    {
        if (empty($_POST)) {
            /*第一个逻辑功能：显示出链接的列表*/
            $link = $this->link->paginate(10);
            $total = $this->link->count();
            $listrow = 10;
            //实例化分页类
            $page = new Page($total, $listrow);
            $show = $page->fpage();
            $this->assign('page', $show);// 赋值分页输出
            $this->assign('linklist', $link);

            return $this->fetch();
        } else {
            //实例化LinkModel来验证数据
            $lm = new LinkModel();
            $validate = $lm->linkValidate($_POST, 'edit');
            if ($validate == 'true') {
                //实例化model的逻辑层
                $fl = new LinkService();
                if ($fl->editLink($_POST)) {
                    $this->success("修改链接成功", "link/linklist");
                } else {
                    $this->error("修改失败", "link/editlink");
                }
            } else {
                $this->error($validate, "link/editlink");
            }
        }
    }

    /*
     * 接收ajax传来的link_id,查询表信息
     * */
    public function link_msg()
    {
        if (!empty($_POST)) {
            $link = Db::table('es_link')->where('link_id', $_POST['link_id'])->find();
            echo json_encode($link);
        }
    }

    /*
     * 接收ajax传来的link_id，然后删除表信息
     * */
    public function dellink()
    {
        if (empty($_POST)) {
            $link = $this->link->paginate(10);
            $total = $this->link->count();
            $listrow = 10;
            //实例化分页类
            $page = new Page($total, $listrow);
            $show = $page->fpage();
            $this->assign('page', $show);// 赋值分页输出
            $this->assign('linklist', $link);
            return $this->fetch();
        } else {
            /*接收删除ajax发送过来的id，然后删除数据库的信息*/
            $link_id = $_POST['link_id'];
            $fl = $this->link->where('link_id', $link_id)->delete();
            if ($fl) {
                echo "删除成功";
            } else {
                echo "删除失败";
            }
        }
    }
}
