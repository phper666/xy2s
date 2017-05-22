<?php
namespace app\home\service;

use think\Model;
use think\Validate;
use think\Db;

class UserService extends Model
{
    /*
        * 这是一个注册用户的操作方法，目的是把表单的信息接收格式化放入数据库
        * @access public
        * @param array $data 这个是用户注册的信息,是一个一维数组
        * @return bool 添加成功就返回true,反之为false
        * */
    public function regUser($data)
    {
        $fl = Db::table('es_user')->insert($data);
        if ($fl) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 这个是用户名是否存在的验证
     * $param $data 接收用户名，为一个一维数组
     * @return bool 验证成功返回true,反之false
     */
    public function checkName($data)
    {
        $fl = Db::table('es_user')->where('username', $data['username'])->find();
        if ($fl != null) {
            return true;
        } else {
            return false;
        }
    }
}