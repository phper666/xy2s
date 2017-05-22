<?php
namespace app\home\model;

use think\Model;
use think\Validate;
use think\Db;
use app\home\service\UserService;

class UserModel extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'es_user';
    protected $pk = 'user_id';

    /*
     * 这是一个验证role添加的信息
     * @param array $data 接收的am_user信息，为一个一维数组
     * @param string $val 默认为add，这个参数是表名是添加验证还是编辑验证,默认会对用户进行存在验证
     * @return bool 验证正确返回true
     * @return string 失败则返回错误信息
     * */
    public function userValidate($data, $val = 'add')
    {
        $validate = validate('UserValidate');   //验证的规则，这个可以直接实例化到我home模块下的validate目录下的user类，里面我定义了规则
        $check_data = [
            'user_email' => $data['email']
        ];

        if (!$validate->check($check_data)) {
            return $validate->getError();
        } else {
            if ($val == 'add') {
                //对管理员名进行验证
                $us = new UserService();
                $fl = $us->checkName($data);
                if ($fl) {
                    return '用户存在';
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }
    }
}