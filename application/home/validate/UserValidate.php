<?php
namespace app\home\validate;

use think\Validate;

/*
 *这个是对admin数据信息的验证规则规定
 * */

class UserValidate extends Validate
{
    protected $rule = [
        'user_email' => 'email'
    ];

    protected $message = [
        'user_email' => '邮箱格式错误'
    ];
}