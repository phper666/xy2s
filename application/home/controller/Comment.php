<?php
/**
 * Created by PhpStorm.
 * User: liyuzhao
 * Date: 2016/10/20
 * Time: 13:21
 */

namespace app\home\controller;
use think\Db;
use page\Page;

class Comment extends IndexController
{
    public function addcomment()
    {
        //获取被回复人的id
        $cm_id = $_POST['cm_id'];
        //接收前台传来的评论，添加评论信息
        $arr = [
            'username'      =>      $_POST['username'],
            'user_id'       =>      $_POST['user_id'],
            'goods_id'      =>      $_POST['goods_id'],
            'cm_content'    =>      $_POST['comment'],
            'cm_parent'     =>      $cm_id,
            'cm_time'       =>      date("Y-m-d H:i:s",time())
        ];

        $fl = $this->comments->insert($arr);
        if($fl){
            echo 'success';
        }else{
            echo 'fail';
        }
    }
}