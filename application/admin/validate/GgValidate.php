<?php
namespace app\admin\validate;

use think\Validate;
use think\Db;

/*
 *这个是对Gg数据信息的验证规则规定
 * */

class GgValidate extends Validate
{
    protected $rule = [
        'gg_name' => 'require|max:24',
        'gg_content' => 'max:200'
    ];

    protected $message = [
        'gg_name.require' => '标题必须',
        'gg_name.max' => '标题最多不能超过12个字符',
        'gg_content.max' => '内容最多不能超过200个字符'
    ];
}