<?php
return [
    'template' => [
        // 模板引擎类型 支持 php think 支持扩展
        'type' => 'Think',
        // 模板路径
        'view_path' => '',
        // 模板后缀
        'view_suffix' => 'html',
        // 模板文件名分隔符
        'view_depr' => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin' => '[{',
        // 模板引擎普通标签结束标记
        'tpl_end' => '}]',
        // 标签库标签开始标记
        'taglib_begin' => '[{',
        // 标签库标签结束标记
        'taglib_end' => '}]',
        //开启模板布局
        'layout_on' => true,
        'layout_name' => 'layout',
    ],

    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie' => [
        // cookie 名称前缀
        'prefix' => '',
        // cookie 保存时间
        'expire' => 3600,    //没有效果
        // cookie 保存路径
        'path' => '/',
        // cookie 有效域名
        'domain' => '',
        //  cookie 启用安全传输
        'secure' => false,
        // httponly设置
        'httponly' => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],
];
