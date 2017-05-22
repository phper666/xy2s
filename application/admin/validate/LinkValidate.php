<?php
namespace app\admin\validate;

use think\Validate;

/*
 *这个是对role数据信息的验证规则规定
 * */

class LinkValidate extends Validate
{
    protected $rule = [
        'link_name' => 'require|max:24',
        'link_link' => 'require|max:40'
    ];

    protected $message = [
        'link_name.require' => '链接名称必须',
        'link_name.max' => '链接名最多不能超过12个字符',
        'link_link.require' => '链接必须',
        'link_link.max' => '链接最多不能超过40个字符'
    ];
}