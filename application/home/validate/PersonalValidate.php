<?php
namespace app\home\validate;

use think\Validate;

/*
 *这个是对admin数据信息的验证规则规定
 * */

class UserValidate extends Validate
{
    protected $rule = [
        'account_name'  =>  'require|max:15|alphaNum',
        'user_email'         =>  'email',
    ];

    protected $message  =   [
        'account_name.require' => '姓名必须',
        'account_name.max'     => '姓名最多不能超过15个字符',
        'user_email'           => '邮箱格式错误',
    ];
}