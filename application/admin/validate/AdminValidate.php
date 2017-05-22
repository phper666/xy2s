<?php
namespace app\admin\validate;

use think\Validate;

/*
 *这个是对admin数据信息的验证规则规定
 * */

class AdminValidate extends Validate
{
    protected $rule = [
        'am_name' => 'require|max:24',
        'am_email' => 'email',
        'am_password' => 'require'
    ];

    protected $message = [
        'am_name.require' => '名称必须',
        'am_name.max' => '名称最多不能超过12个字符',
        'am_email' => '邮箱格式错误',
        'am_password' => '必须输入密码'
    ];

    //验证的场景
    protected $scene = [
        'edit' => ['am_email'],
        'am_password' => ['require'],
    ];

}