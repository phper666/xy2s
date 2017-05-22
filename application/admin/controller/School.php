<?php
namespace app\admin\controller;

use think\Db;
use page\Page;
use app\admin\model\SchoolModel;
use app\admin\service\SchoolService;

class School extends IndexController
{
    /*
     * 显示全部学校的列表
     * */
    public function schoollist()
    {
        if (empty($_POST)) {
            $s = $this->school->paginate(10);
            $total = $this->school->count();
            $listrow = 10;
            //实例化分页类
            $page = new Page($total, $listrow);
            $show = $page->fpage();
            $this->assign('page', $show);// 赋值分页输出
            $this->assign('schoollist', $s);
            return $this->fetch();
        }
    }

    /*
     * 添加学校
     * */
    public function addschool()
    {
        if (empty($_POST)) {
            $r = $this->region->select();
            $this->assign('regionlist', $r);
            return $this->fetch();
        } else {
            //实例化school模型来验证数据
            $sm = new SchoolModel();
            $validate = $sm->SchoolValidate($_POST);
            if ($validate == 'true') {
                //验证成功就把数据格式化插入数据库
                $ss = new SchoolService();
                if ($ss->addSchool($_POST)) {
                    $this->success('添加学校成功', "school/schoollist");
                } else {
                    $this->error('添加学校失败', "school/addschool");
                }
            } else {
                $this->error($validate, "school/addschool");    //返回错误的信息,然后跳转
            }
        }
    }

    /*
     * 删除学校
     * */
    public function delschool()
    {
        if (empty($_POST)) {
            $s = $this->school->paginate(10);
            $total = $this->school->count();
            $listrow = 10;
            //实例化分页类
            $page = new Page($total, $listrow);
            $show = $page->fpage();
            $this->assign('page', $show);// 赋值分页输出
            $this->assign('schoollist', $s);
            return $this->fetch();
        } else {
            /*接收删除ajax发送过来的id，然后删除数据库的信息*/
            $school_id = $_POST['school_id'];
            $fl = $this->school->where('school_id', $school_id)->delete();
            if ($fl) {
                echo "删除成功";
            } else {
                echo "删除失败";
            }
        }
    }

    /*
     * 接收ajax传来的region_id用来查询出此id下的子id
     * */
    public function city_msg()
    {
        $r = $this->region->where('PARENT_ID', $_POST['region_id'])->select();
        echo json_encode($r);
    }
}