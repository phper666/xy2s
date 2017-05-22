<?php
/**
 * Created by PhpStorm.
 * User: liyuzhao
 * Date: 2016/10/23
 * Time: 14:29
 */
//验证规则
namespace app\admin\validate;
use think\Validate;

class ForumValidate extends Validate
{
    protected $rule = [
        'fac_name' => 'require|max:24',
        'fsc_name' => 'require|max:24',
    ];

    protected $message = [
        'fac_name.require' => '名称必须',
        'fac_name.max' => '名称最多不能超过24个字符',
        'fsc_name.require' => '名称必须',
        'fsc_name.max' => '名称最多不能超过24个字符',
    ];

    //验证场景
    protected $scene = [
        'addallcolumn'  =>  ['fac_name'],
        'addsubcolumn'  =>  ['fsc_name'],
    ];
}
