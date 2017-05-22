<?php
namespace app\admin\service;

use think\Db;
use think\Model;

class SchoolService extends Model
{
    /*
    * 这是一个查询此地区此地方学校名是否存在的功能
    * @param $data 这是一个一维数组
    * @return bool 存在返回true，反之返回false
    * */
    public function checkName($data)
    {
        $arr['school_name'] = $data['school_name'];
        $arr['school_city'] = $data['school_city'];
        $arr['school_area'] = $data['school_area'];
        $fl = Db::table('es_school')->where($arr)->find();
        if ($fl != null) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 这是一个添加学校的操作方法，目的是把表单的信息接收格式化放入数据库
     * @access public
     * @param array $data 这个是学校的信息,是一个一维数组
     * @return bool 添加成功就返回true,反之为false
     * */
    public function addSchool($data)
    {
        $arr = [
            'school_name' => $data['school_name'],
            'school_city' => $data['school_city'],
            'school_area' => $data['school_area'],
            'keywords' => $data['keywords']
        ];

        $fl = Db::table('es_school')->insert($arr);
        if ($fl) {
            return true;
        } else {
            return false;
        }
    }
}