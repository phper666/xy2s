<?php
namespace app\admin\service;

use think\Db;
use think\Model;

class LinkService extends Model
{
    /*
     * 这是一个添加链接的操作方法，目的是把表单的信息接收格式化放入数据库
     * @access public
     * @param array $data 这个是链接的信息,是一个一维数组
     * @return bool 添加成功就返回true,反之为false
     * */
    public function addLink($data)
    {
        $arr = [
            'link_name' => $data['link_name'],
            'link_link' => $data['link_link']
        ];
        $fl = Db::table('es_link')->insert($arr);
        if ($fl) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 这个一个封装好的更新链接信息的方法
     * @access public
     * @param $data 这是一个一维数组
     * return bool 添加成功就返回true,反之为false
     * */
    public function editLink($data)
    {
        $arr = [
            'link_id' => $data['link_id'],    //主键
            'link_name' => $data['link_name'],
            'link_link' => $data['link_link']
        ];
        //更新role表的信息
        $fl = Db::table('es_link')->update($arr);
        if ($fl) {
            return true;
        } else {
            return false;
        }
    }

    /*
    * 这是一个查询链接名是否存在的功能
    * @param $data 这是一个一维数组
    * @return bool 存在返回true，反之返回false
    * */
    public function checkName($data)
    {
        $arr['link_link'] = $data['link_link'];
        $fl = Db::table('es_link')->where($arr)->select();
        if ($fl != null) {
            return true;
        } else {
            return false;
        }
    }
}