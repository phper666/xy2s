<?php
namespace app\admin\controller;

use think\Db;
use page\Page;
use app\admin\model\CategoryModel;
use app\admin\service\CategoryService;

class Category extends IndexController
{
    /*
    * 显示全部栏目的列表
    * */
    public function categorylist()
    {
        $cg = $this->category->paginate(10);
        $total = $this->category->count();
        $listrow = 10;
        //实例化分页类
        $page = new Page($total, $listrow);
        $show = $page->fpage();
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('categorylist', $cg);
        return $this->fetch();
    }

    /*
     * 添加栏目
     * */
    public function addcategory()
    {
        if (empty($_POST)) {
            $cg = $this->category->select();
            $this->assign('categorylist', $cg);
            return $this->fetch();
        } else {
            //实例化category模型来验证数据
            $cm = new CategoryModel();
            $validate = $cm->CategoryValidate($_POST);
            if ($validate == 'true') {
                //验证成功就把数据格式化插入数据库
                $cs = new CategoryService();
                if ($cs->addCategory($_POST)) {
                    $this->success('添加栏目成功', "category/categorylist");
                } else {
                    $this->error('添加栏目失败', "category/addcategory");
                }
            } else {
                $this->error($validate, "category/addcategory");    //返回错误的信息,然后跳转
            }
        }
    }

    /*
     * 修改栏目
     * */
    public function editcategory()
    {
        if (empty($_POST)) {
            $cg = $this->category->select();
            $this->assign('categorylist', $cg);
            return $this->fetch();
        } else {
            //验证接收的数据，实例化角色模型（验证）
            $cm = new CategoryModel();
            $validate = $cm->categoryValidate($_POST, 'edit');
            //验证接收的数据
            if ($validate == 'true') {
                //数据验证成功就实例化服务模型
                $cs = new CategoryService();
                if ($cs->editCategory($_POST)) {
                    $this->success('修改栏目成功', "category/categorylist");
                } else {
                    $this->error('修改栏目失败', "category/editcategory");
                }
            } else {
                $this->error($validate, "category/editcategory");    //返回错误的信息,然后跳转
            }
        }
    }

    /*
     * 接收ajax传来的cat_id来查询category表的信息
     * */
    public function category_msg()
    {
        $cg = $this->category->where('cat_id', $_POST['cat_id'])->find();
        echo json_encode($cg);
    }

    /*
     * 删除栏目
     * */
    public function delcategory()
    {
        if (empty($_POST)) {
            $cg = $this->category->paginate(10);
            $total = $this->category->count();
            $listrow = 10;
            //实例化分页类
            $page = new Page($total, $listrow);
            $show = $page->fpage();
            $this->assign('categorylist', $cg);
            $this->assign('page', $show);// 赋值分页输出

            return $this->fetch();
        } else {
            /*接收删除ajax发送过来的id，然后删除数据库的信息*/
            $cat_id = $_POST['cat_id'];
            $fl = $this->category->where('cat_id', $cat_id)->delete();
            if ($fl) {
                echo "删除成功";
            } else {
                echo "删除失败";
            }
        }
    }
}