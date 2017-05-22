<?php
namespace app\admin\validate;

use think\Validate;

class SchoolValidate extends Validate
{
    protected $rule = [
        'school_area' => 'require',
        'school_city' => 'require',
        'school_name' => 'require',
        'keywords' => 'require'
    ];

    protected $message = [
        'school_area.require' => '省份必须',
        'school_city.require' => '城市必须',
        'school_name.require' => '学校必须',
        'keywords.require' => '关键字必须'
    ];
}