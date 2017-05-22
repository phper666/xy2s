<?php
/**
 * Created by PhpStorm.
 * User: liyuzhao
 * Date: 2016/10/23
 * Time: 14:29
 */
//对数据库的逻辑操作 拼接信息等
namespace app\admin\service;
use think\Model;
use think\Db;

class ForumService extends Model
{
    //检查是否存在总栏目
    public function checkAllName($data)
    {
        $fl = Db::table('es_forum_all_column')->where('fac_name', $data['fac_name'])->find();
        if ($fl != null) {
            return true;
        } else {
            return false;
        }
    }

    //检查是否存在总栏目
    public function checkSubName($data)
    {
        $fl = Db::table('es_forum_sub_column')->where('fsc_name', $data['fsc_name'])->find();
        if ($fl != null) {
            return true;
        } else {
            return false;
        }
    }
}