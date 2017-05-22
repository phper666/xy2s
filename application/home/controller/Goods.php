<?php
namespace app\home\controller;
use think\Db;
use think\Cookie;
use page\Page;
use think\Image;


class Goods extends IndexController{
    private $arr  = array();     //这个数组用来装循环文件的名字的(缩略图的)，名字都为时间戳
    private $arr1 = array();     //这个数组用来装循环文件的名字的(原图的)，名字都为时间戳
    private $arr2 = array();     //这个数组用来图的路径

    public function addgoods()
    {
        if(empty($_POST)){
            //显示出用户的学校
            $a = table_cognate('es_user', 'es_school', 'school_id', 'a.user_id=' . Cookie::get('user_id'));
            $this->assign('userlist', $a);
            return $this->fetch();
        }else{
            //26到94行的代码都是为了把上传的图片按一定的像素转存，然后删除原图，不让它生成缓存
            $time = date("ymd");
            $dir = ABSOLUTE_HOME . 'uploads/tmp_img/'.Cookie::get('username').'/';
            $dir_goods = ABSOLUTE_HOME . 'img/'.Cookie::get('username').'/shangpin';    //要把商品图移动的目录位置
            //先判断是否存在目录，不存在就创建
            if (!file_exists(ABSOLUTE_HOME . 'img/'.Cookie::get('username'))) {
                mkdir(ABSOLUTE_HOME . 'img/'.Cookie::get('username'), '0777');
            }
            if (!file_exists($dir_goods)) {
                mkdir($dir_goods, '0777');
            }
            if (!file_exists($dir_goods.'/thumb')) {
                mkdir($dir_goods.'/thumb', '0777');
            }
            if (!file_exists($dir_goods.'/thumb/'.$time)) {
                mkdir($dir_goods.'/thumb/'.$time, '0777');
            }

            //调用查询目录的函数，并把格式都替换掉
            foreach(check_dir($dir) as $k=>$v){
                if(strpos($v,'_yuantu.jpg') === false){
                    $this->arr[] = str_replace('.jpg','',$v);
                }else{
                    $this->arr1[] = str_replace('_yuantu.jpg','',$v);
                }
            }

            //一个逻辑，把数组中最大的值找出来，保存，然后删掉原数组的此下标和元素，并且移动二张最新的（缩略图）
            for($i=0;$i<2;$i++){
                $max = arr_max($this->arr); //缩略
                foreach($this->arr as $k=>$v){
                    if($v == $max){
                        //得到最大值，就移动到指定的目录，并跳出循环
                        rename($dir.$v.'.jpg', $dir_goods.'/thumb/'.$time.'/'.$v.'.jpg');
                        //放进数据库缩略图的相对路径
                        $this->arr2[] = 'home/img/'.Cookie::get('username').'/shangpin/thumb/'.$time.'/'.$v.'.jpg';
                        array_splice($this->arr, $k, 1); ;  //删除数组元素
                        break;
                    }
                }
            }

            //一个逻辑，把数组中最大的值找出来，保存，然后删掉原数组的此下标和元素，并且移动二张最新的（原图图）
            for($i=0;$i<2;$i++){
                $max = arr_max($this->arr1); //原图
                foreach($this->arr1 as $k=>$v){
                    if($v == $max){
                        //得到最大值，就移动到指定的目录，并跳出循环
                        rename($dir.$v.'_yuantu.jpg', $dir_goods.'/thumb/'.$time.'/'.$v.'_yuantu.jpg');
                        //放进数据库原图的相对路径
                        $this->arr2[] = 'home/img/'.Cookie::get('username').'/shangpin/thumb/'.$time.'/'.$v.'_yuantu.jpg';
                        array_splice($this->arr1, $k, 1); ;  //删除数组元素
                        break;
                    }
                }
            }

            //删除掉所有的临时缩略图
            foreach($this->arr as $k=>$v){
                unlink($dir.$v.'.jpg');
            }

            //删除掉所有的临时图
            foreach($this->arr1 as $k=>$v){
                unlink($dir.$v.'_yuantu.jpg');
            }

            //删除掉所有的上传图
            foreach(check_dir(ABSOLUTE_HOME . 'uploads/tmp_img') as $k=>$v){
                if(strpos($v,'.jpg') != false){
                    unlink(ABSOLUTE_HOME . 'uploads/tmp_img/'.$v);
                }
            }

            //查询出发布商品的用户信息
            $u = $this->user->where('user_id',Cookie::get('user_id'))->find();

            //前端已经对商品发布的数据进行了验证，所以不用在后端验证，直接插入更新数据库即可
            if(empty($_POST['user_tel'])){
                $_POST['user_tel'] = '';
            }
            if(empty($_POST['user_qq'])){
                $_POST['user_qq'] = '';
            }
            $data = [
              'goods_name'     =>   $_POST['goods_name'],
              'goods_desc'     =>   $_POST['goods_desc'],
              'goods_place'    =>   $_POST['goods_place'],
              'ershou_price'   =>   $_POST['goods_price'],
              'cost_price'     =>   $_POST['cost_price'],
              'cat_id'         =>   $_POST['goods_pp'],
              'goods_jiangjia' =>   $_POST['goods_jiangjia'],
              'user_tel'       =>   $_POST['user_tel'],
              'user_qq'        =>   $_POST['user_qq'],
              'goods_new'      =>   $_POST['goods_new'],
              'goods_number'   =>   $_POST['goods_number'],
              'goods_role'     =>   $_POST['goods_role'],
              'keywords'       =>   $_POST['goods_name'],
              'add_time'       =>   date("Y-m-d H:i:s",time()),
              'user_id'        =>   $u['user_id'],
              'school_id'      =>   $u['school_id'],
              'goods_thumb1'   =>   $this->arr2[0],
              'goods_img1'     =>   $this->arr2[2],
              'goods_thumb2'   =>   $this->arr2[1],
              'goods_img2'     =>   $this->arr2[3]
            ];
            //添加商品进商品表
            $fl = $this->goods->insertGetId($data);
            if($fl){
                //只要添加商品成功就把商品插入举报次数记录表
                $this->repornum->insert(['goods_id'=>$fl]);
                $this->success("商品发布成功","Index/index");
            }else{
                $this->error("商品发布失败","Index/index");
            }
        }
    }

    /*
     * 获取ajax传来的栏目id
     * */
    public function category_msg(){
        $cat_id = $_POST['cat_id'];
        $category = $this->category->where('parent_id',$cat_id)->select();
        echo json_encode($category);
    }

    /*
     * 获取uploadify上传的图片
     * */
    public function upload()
    {
        $time = time();
        // 获取uploadify传来的图片
        $files = request()->file();
        foreach ($files as $file) {       //file等于$files['Filedata']
            //把图片移动到框架应用根目录/public/static/home/uploads/tmp_img/用户名 的目录下
            $info = $file->move(ROOT_PATH . 'public' . DS . 'static/home/uploads/', '');
            if ($info) {
                $path = STATIC_URL . 'home/uploads/tmp_img/' . Cookie::get('username');
                $tx = $info->getPath() . '/' . Cookie::get('username');
                //创建一个临时保存头像缩略图的目录，如果存在就不创建
                if (!file_exists($tx)) {
                    mkdir($tx, '0777');
                }
                //把图片信息进行缩略,需要本地绝对路径
                $image = Image::open($info->getRealPath());
                $image->thumb(400, 400,Image::THUMB_FIXED)->save($tx.'/'.$time.'_yuantu'.'.jpg');
                $image->thumb(120, 150, Image::THUMB_FIXED)->save($tx.'/'.$time.'.jpg');
                echo $path .'/'.$time.'.jpg';
            } else {
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }

    }

    /*
     * 未登录的商品信息显示
     * */
    public function shangpin()
    {
        $goods_id = input('param.goods_id');
        //显示出点击的商品信息
        $goods = $this->goods->where('goods_id',$goods_id)->find();
        //只要一点击浏览商品就更新商品的浏览次数
        $click_count = $goods['click_count']+1;
        $this->goods->where('goods_id',$goods_id)->update(['click_count'=>$click_count]);

        $this->assign('g',$goods);
        //显示出卖家的姓名
        $user = $this->user->where('user_id',$goods['user_id'])->find();
        $this->assign('u',$user);
        //现在的时间减去发布的时间
        $d = date('ymd',time());  //格式161019
        $e = date('ymd',strtotime(strstr($goods['add_time'],' ',true)));    //把数据库的时间转换
        $s = $d - $e;
        if($s == 0){
            $this->assign('t','今');
        }else{
            $this->assign('t',$s);
        }

        return $this->fetch();

    }

    /*
     * 登录的商品信息显示
     * */
    public function shangpin_login()
    {
        //获取参数，post，get，put自动识别
        $goods_id = input('param.goods_id');
        //显示出点击的商品信息
        $goods = $this->goods->where('goods_id',$goods_id)->find();
        //只要一点击浏览商品就更新商品的浏览次数
        $click_count = $goods['click_count']+1;
        $this->goods->where('goods_id',$goods_id)->update(['click_count'=>$click_count]);
        
        $this->assign('g',$goods);
        //显示出卖家的姓名
        $user = $this->user->where('user_id',$goods['user_id'])->find();
        $this->assign('u',$user);
        //现在的时间减去发布的时间
        $d = date('ymd',time());  //格式161019
        $e = date('ymd',strtotime(strstr($goods['add_time'],' ',true)));    //把数据库的时间转换
        $s = $d - $e;
        if($s == 0){
            $this->assign('t','今');
        }else{
            $this->assign('t',$s);
        }

        //显示出顶级评论
        $comments = $this->comments->where('goods_id',$goods_id)->where('cm_parent',0)->paginate(4);
        $total = $this->comments->where('goods_id',$goods_id)->where('cm_parent',0)->count();

        $listrow = 4;
        //实例化分页类
        $page = new Page($total, $listrow);
        $show = $page->fpage();
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('comments',$comments);

        //显示出二级评论
        $comments_erji = $this->comments->where('goods_id',$goods_id)->where('cm_parent','<>',0)->select();
        $this->assign('comments_erji',$comments_erji);

        //计算出每个顶级评论下面有多少条评论
        $dj_count = array();
        foreach($this->comments->select() as $k=>$v){
            $num = $this->comments->where('cm_parent',$v['cm_id'])->count();
            $dj_count[$v['cm_id']] = $num;
        }
        $this->assign('dj_count',$dj_count);

        return $this->fetch();
    }

    /*
     * 登录后的商品举报
     * */
    public function jubao()
    {
        if(empty($_POST)){
            $this->view->engine->layout(false);
            return $this->fetch();
        }else{
            //接收举报的数据
            $goods_id = $_POST['goods_id'];
            $user_id  = $_POST['user_id'];
            $gr_content = $_POST['gr_content'];
            //查询举报人的用户名
            $u = $this->user->where('user_id',$user_id)->find();
            //查询被举报人的用户名
            $g = $this->goods->where('goods_id',$goods_id)->find();
            $gr_u = $this->user->where('user_id',$g['user_id'])->find();

            $arr = [
                'goods_id'   =>  $goods_id,
                'gr_time'    =>  date("Y-m-d H:i:s",time()),
                'gr_content' =>  $gr_content,
                'gr_username'=>  $gr_u['username'],
                'username'   =>  $u['username']
            ];
            
            //更新举报数据库
            $fl = $this->goodsrepor->insert($arr);
            //更新举报次数记录表数据
            $goods_num = $this->goodsrepor->where('goods_id',$goods_id)->count();
            $f = $this->repornum->where('goods_id',$goods_id)->update(['rn_num'=>$goods_num]);
            if($fl){
                $this->success("感谢您的举报，我们会及时处理","goods/shangpin_login?goods_id=".$goods_id);
            }else{
                $this->error("很遗憾，出现了未知的错误","goods/shangpin_login?goods_id=".$goods_id);
            }
        }
    }
}