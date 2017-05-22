<?php
namespace app\admin\validate;

use think\Validate;
use think\Db;

/*
 *这个是对auth数据信息的验证规则规定
 * */

class AuthValidate extends Validate
{
    protected $rule = [
        'auth_name' => 'require|max:24',
        'auth_dingji_id' => 'require',
        'auth_yiji_id' => 'require',
        'auth_c' => 'require',
        'auth_a' => 'require',
    ];

    protected $message = [
        'auth_name.require' => '权限名称必须',
        'auth_name.max' => '名称最多不能超过12个字符',
        'auth_dingji_id.require' => '必须要选择顶级权限',
        'auth_yiji_id.require' => '必须要选择一级权限',
        'auth_c.require' => '必须有控制器',
        'auth_a.require' => '必须有方法',
    ];

    //验证的场景
    protected $scene = [
        'dingji' => ['auth_name'],    //0级场景
        'yiji' => ['auth_name', 'auth_dingji_id', 'auth_c', 'auth_a'], //一级场景
        'erji' => ['auth_name', 'auth_dingji_id', 'auth_yiji_id', 'auth_c', 'auth_a'],    //二级场景
    ];


}