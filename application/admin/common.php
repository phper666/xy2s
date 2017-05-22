<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/23
 * Time: 20:50
 */


// 应用公共文件

use think\Db;
/*
 * author liyuzhao
 * time 2016/10/13 19:07
 * 可以关联两个数据库表的函数
 * @param string $table1 第一个表，要带完全表名称
 * @param string $table2 要关联的表，要带完全表名称
 * @param string $field 两个表要关联的字段
 * @param string $field_id 默认为空时，会查询出所有两个表$field字段数据相同的所有数据，例如有两个表，一个是学校表，另一个是商品表，$field = school_id=1,就会查询出school_id=1关联后的所有数据，如果条件为a表有的字段，而b表没有，例如学校表有一个用户id，而商品表没有，你要查询用户id下的关联后的数据就要加上（如果学校表为表1，即为a表）a.user_id
 * 前三个参数必须要
 * return array 关连成功返回两个表字段相同的所有数组
 * return null 失败返回一个null
 * */
function table_cognate($table1='',$table2='',$field='',$field_id='')
{
    $join = [
        [$table2.' b','a.'.$field. '= b.'.$field]
    ];
    if($field_id == ''){
        return Db::table($table1)->alias('a')->join($join)->where('a.'.$field.'='.'b.'.$field)->select();
    }else if($field_id != ''){
        return Db::table($table1)->alias('a')->join($join)->where($field_id)->select();
    }
}