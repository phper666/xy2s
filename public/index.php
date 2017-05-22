<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
//定义一个绝对路径来移动文件或图片,定义到d盘
define("ABSOLUTE_HOME" ,"G:/wamp64/www/xy2s/public/static/home/");
define("ABSOLUTE_STATIC" ,"G:/wamp64/www/xy2s/public/static/");

//定义js,img,css的路径常量，前台
define("GEN_URL","http://localhost/xy2s/");

//定义前台常量
define("CSS_URL",GEN_URL . "public/static/home/css/");
define("JS_URL",GEN_URL . "public/static/home/js/");
define("IMG_URL",GEN_URL . "public/static/home/img/");
define("UPLOADS_URL",GEN_URL . "public/static/home/uploads/");

//后台常量
define("ADMIN_CSS_URL",GEN_URL . "public/static/admin/css/");
define("ADMIN_JS_URL",GEN_URL . "public/static/admin/js/");
define("ADMIN_IMG_URL",GEN_URL . "public/static/admin/img/");

//定义一个到application下的URL路径
define("APP_URL",GEN_URL . "public/index.php/");

//定义一个到app/admin模块下的路径
define("APP_ADMIN_URL",APP_URL . "admin/");

//定义一个到app/home模块下的路径
define("APP_HOME_URL",APP_URL . "home/");

//为上传文件定义一个路径，到public就ok了
define("STATIC_URL",GEN_URL . "public/static/");

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');

// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';



