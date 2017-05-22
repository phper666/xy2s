<?php
namespace app\admin\service;

use think\Db;
use think\Model;

class CategoryService extends Model
{
    /*
     * 这是一个添加栏目的操作方法，目的是把表单的信息接收格式化放入数据库
     * @access public
     * @param array $data 这个是栏目的信息,是一个一维数组
     * @return bool 添加成功就返回true,反之为false
     * */
    public function addCategory($data)
    {
        $arr = [
            'cat_name' => $data['cat_name'],
            'cat_desc' => $data['cat_desc'],
            'parent_id' => $data['parent_id'],
            'cat_level' => $data['cat_level']
        ];

        $fl = Db::table('es_category')->insert($arr);
        if ($fl) {
            return true;
        } else {
            return false;
        }
    }

    /*
    * 这个一个封装好的修改栏目信息的方法
    * @access public
    * @param $data 这是一个一维数组
    * return bool 添加成功就返回true,反之为false
    * */
    public function editCategory($data)
    {
        $arr = [
            'cat_id' => $data['cat_id'],    //主键id
            'cat_name' => $data['cat_name'],
            'cat_desc' => $data['cat_desc']
        ];
        //更新category表的信息
        $fl = Db::table('es_category')->update($arr);
        if ($fl) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 这是一个查询栏目名是否存在的功能
     * @param $data 这是一个一维数组
     * @return bool 存在返回true，反之返回false
     * */
    public function checkName($data)
    {
        $fl = Db::table('es_category')->where('cat_name', $data['cat_name'])->find();
        if ($fl != null) {
            return true;
        } else {
            return false;
        }
    }
}