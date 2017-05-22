<?php
namespace app\admin\validate;

use think\Validate;
use think\Db;

/*
 *这个是对category数据信息的验证规则规定
 * */

class CategoryValidate extends Validate
{
    protected $rule = [
        'cat_name' => 'require|max:24'
    ];

    protected $message = [
        'cat_name.require' => '栏目名必须',
        'cat_name.max' => '名称最多不能超过12个字符'
    ];
}