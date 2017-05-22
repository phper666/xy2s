<?php
namespace app\admin\service;

use think\Db;
use think\Model;

/*
 * 这是一个对gonggao表的操作逻辑类，增删改查的逻辑
 * */

class GgService extends Model
{
    /*
     * 添加公告
     * */
    public function addGg($data)
    {
        if ($data['gg_status'] == 'yes') {
            $data['gg_status'] = 1;
        } else {
            $data['gg_status'] = 0;
        }
        $arr = [
            'am_id' => $data['am_id'],
            'am_name' => $data['am_name'],
            'gg_name' => $data['gg_name'],
            'gg_content' => $data['gg_content'],
            'gg_status' => $data['gg_status'],
            'add_time' => date("Y-m-d H:i:s", time())
        ];
        $fl = Db::table('es_gonggao')->insert($arr);
        if ($fl) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 修改公告
     * */
    public function editGg($data)
    {
        $arr = [
            'gg_id' => $data['gg_id'],  //获取要更新的id
            'am_name' => $data['am_name'],
            'gg_content' => $data['gg_content'],
            'gg_name' => $data['gg_name']
        ];
        $fl = Db::table('es_gonggao')->update($arr);
        if ($fl) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 删除公告
     * */
    public function delGg($data)
    {
        $fl = Db::table('es_gonggao')->where('gg_id', $data['gg_id'])->delete();
        if ($fl) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 更改status状态
     * */
    public function updateStatus($data)
    {
        $gg = Db::table('es_gonggao')->where('gg_id', $data['gg_id'])->find();
        if ($gg['gg_status'] == 1) {
            $fl = Db::table('es_gonggao')->where('gg_id', $data['gg_id'])->update(['gg_status' => 0]);
            if ($fl) {
                return true;
            } else {
                return false;
            }
        } else {
            $fl = Db::table('es_gonggao')->where('gg_id', $data['gg_id'])->update(['gg_status' => 1]);
            if ($fl) {
                return true;
            } else {
                return false;
            }
        }
    }

    /*
     * 查询标题是否存在
     * @param $data 一个一维数组，是表单发送的数据
     * return bool 标题存在就返回true，反之false
     * */
    public function checkName($data)
    {
        $fl = Db::table('es_gonggao')->where('gg_name', $data['gg_name'])->find();
        if ($fl != null) {
            return true;
        } else {
            return false;
        }
    }
}