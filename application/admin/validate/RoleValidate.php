<?php
namespace app\admin\validate;

use think\Validate;

/*
 *这个是对role数据信息的验证规则规定
 * */

class RoleValidate extends Validate
{
    protected $rule = [
        'role_name' => 'require|max:24'
    ];

    protected $message = [
        'role_name.require' => '角色名称必须',
        'role_name.max' => '名称最多不能超过12个字符'
    ];

    //验证的场景
    protected $scene = [
        'edit' => [''],
    ];

}