<?php
/**
 * Created by PhpStorm.
 * User: liyuzhao
 * Date: 2016/10/21
 * Time: 23:35
 */

namespace app\home\controller;
use think\Db;
use think\Cookie;
use page\Page;
use think\Image;

class Forum extends IndexController
{
    //论坛天地默认显示
    public function index()
    {
        //查询总栏目
        $fac = $this->forum_all_column->select();
        //查询总会员
        $uc = $this->user->count();
        $this->assign('uc',$uc);
        //查询总帖子
        $pc = $this->forum_post->count();
        $this->assign('pc',$pc);
        //查询最新注册的会员
        $max = $this->user->max('user_regtime');
        $newu = $this->user->where('user_regtime',$max)->find();
        $this->assign('newu',$newu);

        //查询今天的帖子次数
        $post = $this->forum_post->select();
        $newnum = 0;   //记录今天帖子次数
        foreach($post as $k=>$v){
            //如果和今天的日期相同，就表示为今天
            if(strstr($post[$k]['add_time'],' ',true) == date('Y-m-d',time())){
                $newnum+=1;
            }
        }
        $this->assign('newnum',$newnum);

        //获取昨天的帖子次数
        $time = time() - ( 1  *  24  *  60  *  60 );
        $lnum = 0;   //记录今天帖子次数
        foreach($post as $k=>$v){
            //如果和今天的日期相同，就表示为今天
            if(strstr($post[$k]['add_time'],' ',true) == date('Y-m-d',$time)){
                $lnum+=1;
            }
        }
        $this->assign('lnum',$lnum);

        //查询本版主最新发布的帖子
        $fac_id = input('param.fac_id');
        $max = $this->forum_post->where('fac_id',$fac_id)->max('add_time');
        $newpost = $this->forum_post->where('fac_id',$fac_id)->where('add_time',$max)->find();
        $this->assign('newpost',$newpost);
        //查询版主最新帖子的回复总次数
        $reply_count = $this->forum_reply->where('fp_id',$newpost['fp_id'])->count();
        $this->assign('reply_count',$reply_count);

        //查询子栏目
        $fac_id = input('param.fac_id');
        $fsclist = $this->forum_sub_column->where('fac_id',$fac_id)->find();
        //查询本版的所有子栏目
        $fsc_all = $this->forum_sub_column->where('fac_id',$fac_id)->select();
        //查询版主
        $f = $this->forum_all_column->where('fac_id',$fac_id)->find();
        $user = $this->admin->where('am_id',$f['am_id'])->find();

        $this->assign('faclist',$fac);
        $this->assign('fsc',$fsclist);
        $this->assign('fsc_all',$fsc_all);
        $this->assign('user',$user);
        return $this->fetch();
    }

    //论坛发帖模块
    public function forum_post()
    {
        $fac_id = input('param.fac_id');
        $fsc_id = input('param.fsc_id');

        //查询总栏目
        $fac = $this->forum_all_column->select();
        $this->assign('faclist',$fac);

        //查询子栏目
        $fsc = $this->forum_sub_column->where('fsc_id',$fsc_id)->find();
        $this->assign('fsc',$fsc);

        //查询今天的帖子次数
        $post = $this->forum_post->where('fac_id',$fac_id)->where('fsc_id',$fsc_id)->select();
        $newnum = 0;   //记录今天帖子次数
        foreach($post as $k=>$v){
            //如果和今天的日期相同，就表示为今天
            if(strstr($post[$k]['add_time'],' ',true) == date('Y-m-d',time())){
                $newnum+=1;
            }
        }
        $this->assign('newnum',$newnum);
        //发送要接收的fac_id和fsc_id
        $this->assign('fac_id',$fac_id);
        $this->assign('fsc_id',$fsc_id);

        if(empty($_POST)){
            //查询发帖表,分20条一页
            $a = $this->forum_post->where('fac_id',$fac_id)->where('fsc_id',$fsc_id)->order('fp_id desc')->select();
            $post = $this->forum_post->where('fac_id',$fac_id)->where('fsc_id',$fsc_id)->order('fp_id desc')->paginate(20);
            $total = count($a);//获取数据总数
            $listrow = 20;
            $page = new Page($total, $listrow);
            $show = $page->fpage();   //获得分页显示
            $this->assign('page', $show);// 赋值分页输出
            $this->assign('p',$post);
            //查询出发帖人的名字
            $user = $this->user->select();
            $this->assign('user',$user);


            //每个帖子的最后回复时间，把它放进数组中
            $reply = $this->forum_reply->select();
            $data = array();    //装时间
            $data1 = array();   //装用户id
            $data2 = array();   //装每个帖子的回复总次数
            foreach($reply as $k=>$v){
                $fsc_time = $this->forum_reply->where('fac_id',$fac_id)->where('fsc_id',$fsc_id)->where('fp_id',$v['fp_id'])->max('add_time');
                $user_id = $this->forum_reply->where('fac_id',$fac_id)->where('fsc_id',$fsc_id)->where('fr_id',$v['fr_id'])->find();
                $reply_count = $this->forum_reply->where('fac_id',$fac_id)->where('fsc_id',$fsc_id)->where('fp_id',$v['fp_id'])->count();
                $data[$v['fp_id']] = strstr($fsc_time,' '); //把时间最大的存起来
                //查询用户
                $u = $this->user->where('user_id',$user_id['user_id'])->find();
                $data1[$v['fp_id']] = $u['username'];  //把时间最新的用户存起来
                $data2[$v['fp_id']] = $reply_count;  //把时间最新的用户存起来
            }
            $this->assign('fsc_time',$data);
            $this->assign('data1',$data1);
            $this->assign('data2',$data2);
        }else if(!empty($_POST['fastpostsubmit'])){
            //前台已经验证过了，所以不用验证，直接入库
            $arr = [
                'user_id'     =>    Cookie::get('user_id'),
                'fp_content'  =>    $_POST['editorValue'],
                'fp_biaoti'   =>    $_POST['subject'],
                'fsc_id'      =>    $_POST['fsc_id'],
                'fac_id'      =>    $_POST['fac_id'],
                'add_time'    =>    date("Y-m-d H:i:s",time()),
                'click_count' =>    1

            ];
            $fl = $this->forum_post->insert($arr);
            if($fl){
                $this->success("发帖成功","forum/forum_post?fac_id=".$fac_id."&fsc_id=".$fsc_id);
            }else{
                $this->error("发帖失败");
            }
        }else if(!empty($_POST['srch_submit'])){
            //查询出发帖人的名字
            $user = $this->user->select();
            $this->assign('user',$user);
            //每个帖子的最后回复时间，把它放进数组中
            $reply = $this->forum_reply->select();
            $data = array();    //装时间
            $data1 = array();   //装用户id
            $data2 = array();   //装每个帖子的回复总次数
            foreach($reply as $k=>$v){
                //查询所有的主题帖子/分20条一页
                $srch = $_POST['srchtxt'];
                $a = $this->forum_post->where('fp_biaoti','like','%'.$srch.'%')->order('fp_id desc')->select();
                $post = $this->forum_post->where('fp_biaoti','like','%'.$srch.'%')->order('fp_id desc')->paginate(20);
                $total = count($a);//获取数据总数
                $listrow = 20;
                $page = new Page($total, $listrow);
                $show = $page->fpage();   //获得分页显示
                $this->assign('page', $show);// 赋值分页输出
                $this->assign('p',$post);
                $fsc_time = $this->forum_reply->where('fac_id',$fac_id)->where('fsc_id',$fsc_id)->where('fp_id',$v['fp_id'])->max('add_time');
                $user_id = $this->forum_reply->where('fac_id',$fac_id)->where('fsc_id',$fsc_id)->where('fr_id',$v['fr_id'])->find();
                $reply_count = $this->forum_reply->where('fac_id',$fac_id)->where('fsc_id',$fsc_id)->where('fp_id',$v['fp_id'])->count();
                $data[$v['fp_id']] = strstr($fsc_time,' '); //把时间最大的存起来
                //查询用户
                $u = $this->user->where('user_id',$user_id['user_id'])->find();
                $data1[$v['fp_id']] = $u['username'];  //把时间最新的用户存起来
                $data2[$v['fp_id']] = $reply_count;  //把时间最新的用户存起来
            }
            $this->assign('fsc_time',$data);
            $this->assign('data1',$data1);
            $this->assign('data2',$data2);

        }else if(!empty($_POST['zhuti'])) {     //查询所有的顶置帖子
            $zhuti = $_POST['zhuti'];   //获取要查询的主题、全部主题、热门、顶置、今天发布
            if ($zhuti == '显示置顶') {
                //查询出发帖人的名字
                $user = $this->user->select();
                $this->assign('user', $user);
                //每个帖子的最后回复时间，把它放进数组中
                $reply = $this->forum_reply->select();
                $data = array();    //装时间
                $data1 = array();   //装用户id
                $data2 = array();   //装每个帖子的回复总次数
                foreach ($reply as $k => $v) {
                    //查询所有的主题帖子/分20条一页
                    $a = $this->forum_post->where('fp_dingzhi', '是')->order('fp_id desc')->select();
                    $post = $this->forum_post->where('fp_dingzhi', '是')->order('fp_id desc')->paginate(20);
                    $total = count($a);//获取数据总数
                    $listrow = 20;
                    $page = new Page($total, $listrow);
                    $show = $page->fpage();   //获得分页显示
                    $this->assign('page', $show);// 赋值分页输出
                    $this->assign('p', $post);
                    $fsc_time = $this->forum_reply->where('fac_id', $fac_id)->where('fsc_id', $fsc_id)->where('fp_id', $v['fp_id'])->max('add_time');
                    $user_id = $this->forum_reply->where('fac_id', $fac_id)->where('fsc_id', $fsc_id)->where('fr_id', $v['fr_id'])->find();
                    $reply_count = $this->forum_reply->where('fac_id', $fac_id)->where('fsc_id', $fsc_id)->where('fp_id', $v['fp_id'])->count();
                    $data[$v['fp_id']] = strstr($fsc_time, ' '); //把时间最大的存起来
                    //查询用户
                    $u = $this->user->where('user_id', $user_id['user_id'])->find();
                    $data1[$v['fp_id']] = $u['username'];  //把时间最新的用户存起来
                    $data2[$v['fp_id']] = $reply_count;  //把时间最新的用户存起来
                }
                $this->assign('fsc_time', $data);
                $this->assign('data1', $data1);
                $this->assign('data2', $data2);
            } else if ($zhuti == '全部主题') {
                //查询全部主题帖,分20条一页
                $a = $this->forum_post->where('fac_id',$fac_id)->where('fsc_id',$fsc_id)->order('fp_id desc')->select();
                $post = $this->forum_post->where('fac_id',$fac_id)->where('fsc_id',$fsc_id)->order('fp_id desc')->paginate(20);
                $total = count($a);//获取数据总数
                $listrow = 20;
                $page = new Page($total, $listrow);
                $show = $page->fpage();   //获得分页显示
                $this->assign('page', $show);// 赋值分页输出
                $this->assign('p',$post);
                //查询出发帖人的名字
                $user = $this->user->select();
                $this->assign('user',$user);


                //每个帖子的最后回复时间，把它放进数组中
                $reply = $this->forum_reply->select();
                $data = array();    //装时间
                $data1 = array();   //装用户id
                $data2 = array();   //装每个帖子的回复总次数
                foreach($reply as $k=>$v){
                    $fsc_time = $this->forum_reply->where('fac_id',$fac_id)->where('fsc_id',$fsc_id)->where('fp_id',$v['fp_id'])->max('add_time');
                    $user_id = $this->forum_reply->where('fac_id',$fac_id)->where('fsc_id',$fsc_id)->where('fr_id',$v['fr_id'])->find();
                    $reply_count = $this->forum_reply->where('fac_id',$fac_id)->where('fsc_id',$fsc_id)->where('fp_id',$v['fp_id'])->count();
                    $data[$v['fp_id']] = strstr($fsc_time,' '); //把时间最大的存起来
                    //查询用户
                    $u = $this->user->where('user_id',$user_id['user_id'])->find();
                    $data1[$v['fp_id']] = $u['username'];  //把时间最新的用户存起来
                    $data2[$v['fp_id']] = $reply_count;  //把时间最新的用户存起来
                }
                $this->assign('fsc_time',$data);
                $this->assign('data1',$data1);
                $this->assign('data2',$data2);
            } else if ($zhuti == '今天发布') {
                //查询最热门帖子/分20条一页
                //查询出发帖人的名字
                $time = date('Y-m-d',time());

                $user = $this->user->select();
                $this->assign('user', $user);
                //每个帖子的最后回复时间，把它放进数组中
                $reply = $this->forum_reply->select();
                $data = array();    //装时间
                $data1 = array();   //装用户id
                $data2 = array();   //装每个帖子的回复总次数
                foreach ($reply as $k => $v) {
                    //查询所有的主题帖子/分20条一页
                    $a = $this->forum_post->where('add_time','like','%'.$time.'%')->where('fac_id',$fac_id)->where('fsc_id',$fsc_id)->order('click_count desc')->select();
                    $post = $this->forum_post->where('add_time','like','%'.$time.'%')->where('fac_id',$fac_id)->where('fsc_id',$fsc_id)->order('click_count desc')->paginate(20);
                    $total = count($a);//获取数据总数
                    $listrow = 20;
                    $page = new Page($total, $listrow);
                    $show = $page->fpage();   //获得分页显示
                    $this->assign('page', $show);// 赋值分页输出
                    $this->assign('p', $post);
                    $fsc_time = $this->forum_reply->where('fac_id', $fac_id)->where('fsc_id', $fsc_id)->where('fp_id', $v['fp_id'])->max('add_time');
                    $user_id = $this->forum_reply->where('fac_id', $fac_id)->where('fsc_id', $fsc_id)->where('fr_id', $v['fr_id'])->find();
                    $reply_count = $this->forum_reply->where('fac_id', $fac_id)->where('fsc_id', $fsc_id)->where('fp_id', $v['fp_id'])->count();
                    $data[$v['fp_id']] = strstr($fsc_time, ' '); //把时间最大的存起来
                    //查询用户
                    $u = $this->user->where('user_id', $user_id['user_id'])->find();
                    $data1[$v['fp_id']] = $u['username'];  //把时间最新的用户存起来
                    $data2[$v['fp_id']] = $reply_count;  //把时间最新的用户存起来
                }
                $this->assign('fsc_time', $data);
                $this->assign('data1', $data1);
                $this->assign('data2', $data2);
            } else if ($zhuti == '最热门') {
                //查询最热门帖子/分20条一页
                //查询出发帖人的名字
                $user = $this->user->select();
                $this->assign('user', $user);
                //每个帖子的最后回复时间，把它放进数组中
                $reply = $this->forum_reply->select();
                $data = array();    //装时间
                $data1 = array();   //装用户id
                $data2 = array();   //装每个帖子的回复总次数
                foreach ($reply as $k => $v) {
                    //查询所有的主题帖子/分20条一页
                    $a = $this->forum_post->order('click_count desc')->where('fac_id',$fac_id)->where('fsc_id',$fsc_id)->select();
                    $post = $this->forum_post->order('click_count desc')->where('fac_id',$fac_id)->where('fsc_id',$fsc_id)->paginate(20);
                    $total = count($a);//获取数据总数
                    $listrow = 20;
                    $page = new Page($total, $listrow);
                    $show = $page->fpage();   //获得分页显示
                    $this->assign('page', $show);// 赋值分页输出
                    $this->assign('p', $post);
                    $fsc_time = $this->forum_reply->where('fac_id', $fac_id)->where('fsc_id', $fsc_id)->where('fp_id', $v['fp_id'])->max('add_time');
                    $user_id = $this->forum_reply->where('fac_id', $fac_id)->where('fsc_id', $fsc_id)->where('fr_id', $v['fr_id'])->find();
                    $reply_count = $this->forum_reply->where('fac_id', $fac_id)->where('fsc_id', $fsc_id)->where('fp_id', $v['fp_id'])->count();
                    $data[$v['fp_id']] = strstr($fsc_time, ' '); //把时间最大的存起来
                    //查询用户
                    $u = $this->user->where('user_id', $user_id['user_id'])->find();
                    $data1[$v['fp_id']] = $u['username'];  //把时间最新的用户存起来
                    $data2[$v['fp_id']] = $reply_count;  //把时间最新的用户存起来
                }
                $this->assign('fsc_time', $data);
                $this->assign('data1', $data1);
                $this->assign('data2', $data2);
            }
        }

        return $this->fetch();
    }

    //论坛回帖
    public function forum_reply()
    {
        //总栏目查询
        $fac = $this->forum_all_column->select();
        $this->assign('faclist',$fac);

        //显示指定帖子的内容
        $fac_id = input('param.fac_id');
        $fsc_id = input('param.fsc_id');
        $fp_id  = input('param.fp_id');
        $arr = [
            'fac_id'    =>  $fac_id,
            'fsc_id'    =>  $fsc_id,
            'fp_id'     =>  $fp_id
        ];
        $p = $this->forum_post->where($arr)->find();
        $this->assign('p',$p);

        //显示出此栏目的超级版主几个字，不管是版主发帖还是回复
        $bz = $this->forum_all_column->where('fac_id',$fac_id)->find();
        $banzhu = $this->admin->where('am_id',$bz['am_id'])->find();
        $this->assign('banzhu',$banzhu);

        //显示该帖主的所有帖总数
        $p_count = $this->forum_post->where('user_id',$p['user_id'])->count();
        $this->assign('post_count',$p_count);

        //每次进入指定的帖子就更新数据库
        $click_count = $p['click_count'] + 1;
        $this->forum_post->where('fp_id',$fp_id)->update(['click_count'=>$click_count]);

        //显示回复次数
        $fc = $this->forum_reply->where('fp_id',$fp_id)->count();
        $this->assign('fc',$fc);

        //显示发帖人的账号
        $fr = $this->forum_post->where($arr)->find();
        $un = $this->user->where('user_id',$fr['user_id'])->find();
        $this->assign('un',$un);
        $this->assign('add_time',$fr);

        //定义一个变量，显示出楼层数
        $louceng = 2;
        $this->assign('louceng',$louceng);

        //显示回复帖子内容
        if(empty($_POST)){
            //查询在主题底下的评论次数，不是回帖
            $b = $this->forum_comment->where('fr_id',0)->where('fp_id',$fp_id)->count();
            $this->assign('b',$b);

            //查询回帖的评论次数
            $bb = $this->forum_reply->field('fr_id')->where('fp_id',$fp_id)->select();  //查询出此主题有几个回帖的id
            foreach($bb as $k=>$v){
                $c[$v['fr_id']] = $this->forum_comment->where('fr_id',$v['fr_id'])->count();
            }
            if(!empty($c)){
                $this->assign('c',$c);
            }

            //显示所有回帖内容/每页显示8条
            $a = $this->forum_reply->where('fp_id',$fp_id)->order('fr_id asc')->select();
            $fp = $this->forum_reply->where('fp_id',$fp_id)->order('fr_id asc')->paginate(8);
            $total = count($a);//获取数据总数
            $listrow = 8;
            $page = new Page($total, $listrow);
            $show = $page->fpage();   //获得分页显示
            $this->assign('page', $show);// 赋值分页输出
            $this->assign('fplist',$fp);

            //显示回帖人的用户名
            $user = $this->user->select();
            $this->assign('userlist',$user);
        }else{
            //前台已经验证过了，所以不用验证，直接入库
            $arr = [
                'user_id'     =>    Cookie::get('user_id'),
                'fr_content'  =>    $_POST['editorValue'],
                'fp_id'       =>    $_POST['fp_id'],
                'fsc_id'      =>    $_POST['fsc_id'],
                'fac_id'      =>    $_POST['fac_id'],
                'add_time'    =>    date("Y-m-d H:i:s",time()),
            ];
            $fl = $this->forum_reply->insert($arr);
            if($fl){
                $this->success("回帖成功");
            }else{
                $this->error("回帖失败");
            }
        }
        return $this->fetch();
    }

    //接收前台传来的楼层id，然后删除指定的帖子楼层
    public function forum_del_louceng()
    {
        $fr_id = $_POST['fr_id'];
        //删除楼层
        $fl = $this->forum_reply->where('fr_id',$fr_id)->delete();
        if($fl){
            echo 'success';
        }else{
            echo 'fail';
        }
    }

    //论坛回帖评论
   public function forum_comment()
   {
       //print_r($_POST);Array ( [fc_content] => 使得房贷首付 [fac_id] => 5 [fsc_id] => 5 [fp_id] => 16 [fr_id] => 0 [user_id] => 3 )
       //前台已经验证，直接入库即可，需要去除掉接收到content里面的用户名
       if(strstr($_POST['fc_content'],"回复") != ''){
           $_POST['fc_content'] = strstr($_POST['fc_content'],"回复");
       }else{
           $_POST['fc_content'] = strstr($_POST['fc_content'],":");
       }
       $arr = [
           'fac_id'     =>      $_POST['fac_id'],
           'fsc_id'     =>      $_POST['fsc_id'],
           'fp_id'      =>      $_POST['fp_id'],
           'fr_id'      =>      $_POST['fr_id'],
           'user_id'    =>      $_POST['user_id'],
           'fc_content' =>      $_POST['fc_content']
       ];
       $fl = $this->forum_comment->insert($arr);
       if($fl){
           echo 'success';
       }else{
           echo 'fail';
       }
   }

    //主题评论显示
    public function pinglun()
    {
        $this->view->engine->layout(false);
        //显示评论内容、分页
        $fp_id = input('param.fp_id');
        $b  = $this->forum_comment->where('fr_id',0)->where('fp_id',$fp_id)->select();
        $fc = $this->forum_comment->where('fr_id',0)->where('fp_id',$fp_id)->paginate(4);
        $total = count($b);//获取数据总数
        $listrow = 4;
        $page = new Page($total, $listrow);
        $show = $page->fpage1();   //获得分页显示
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('fcomment',$fc);
        //显示出用户id对应的用户名
        $user = $this->user->select();
        $this->assign('user',$user);
        return $this->fetch();
    }


    //回帖评论显示
    public function reply_pinglun()
    {
        $this->view->engine->layout(false);
        //接收到评论的评论id
        $fr_id = input('param.fr_id');
        //显示评论内容、分页
        $fp_id = input('param.fp_id');
        $b  = $this->forum_comment->where('fr_id',$fr_id)->where('fp_id',$fp_id)->select();
        $fc = $this->forum_comment->where('fr_id',$fr_id)->where('fp_id',$fp_id)->paginate(4);
        $total = count($b);//获取数据总数
        $listrow = 4;
        $page = new Page($total, $listrow);
        $show = $page->fpage1();   //获得分页显示
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('fcomment',$fc);
        //显示出用户id对应的用户名
        $user = $this->user->select();
        $this->assign('user',$user);
        return $this->fetch();
    }
}