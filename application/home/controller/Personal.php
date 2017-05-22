<?php
namespace app\home\controller;

use think\Db;
use think\File;
use think\Image;
use think\Cookie;
use page\Page;
use app\home\model\PersonalModel;
use app\home\service\PersonalService;

class Personal extends IndexController
{
    private $arr  = array();     //这个数组用来装循环文件的名字的(缩略图的)，名字都为时间戳
    private $arr1  = array();     //这个数组用来装放进数据库的相对头像路径
    public function personal()
    {
        //调用公共函数，关联两个表查询
        $a = table_cognate('es_user', 'es_school', 'school_id', 'a.user_id=' . Cookie::get('user_id'));
        //显示用户信息
        $this->assign('userlist', $a);
        //显示当前有多少商品
        $goods = $this->goods->where('user_id',Cookie::get('user_id'))->count();
        $this->assign('goods_num',$goods);
        return $this->fetch();
    }

    public function xgpersonal()
    {
        if (empty($_POST)) {
            //调用公共函数，关联两个表查询
            $a = table_cognate('es_user', 'es_school', 'school_id', 'a.user_id=' . Cookie::get('user_id'));
            //显示用户信息
            $this->assign('userlist', $a);
            return $this->fetch();
        } else {
            //38到94行的代码都是为了把上传的图片按一定的像素转存，然后删除原图，不让它生成缓存
            $time = date("ymd");
            $dir = ABSOLUTE_HOME . 'uploads/tmp_img/'.Cookie::get('username').'/';
            $dir_touxiang = ABSOLUTE_HOME . 'img/'.Cookie::get('username').'/touxiang';    //要把商品图移动的目录位置
            //先判断是否存在目录，不存在就创建
            if (!file_exists(ABSOLUTE_HOME . 'img/'.Cookie::get('username'))) {
                mkdir(ABSOLUTE_HOME . 'img/'.Cookie::get('username'), '0777');
            }
            if (!file_exists($dir_touxiang)) {
                mkdir($dir_touxiang, '0777');
            }
            if (!file_exists($dir_touxiang.'/thumb')) {
                mkdir($dir_touxiang.'/thumb', '0777');
            }
            if (!file_exists($dir_touxiang.'/thumb/'.$time)) {
                mkdir($dir_touxiang.'/thumb/'.$time, '0777');
            }

            //调用查询目录的函数，并把格式都替换掉
            foreach(check_dir($dir) as $k=>$v){
                $this->arr[] = str_replace('touxiang.jpg','',$v);
            }

            //一个逻辑，把数组中最大的值找出来，保存，然后删掉原数组的此下标和元素，并且移动这张最新的（缩略图）
            $max = arr_max($this->arr); //缩略
            foreach($this->arr as $k=>$v){
                if($v == $max){
                    $new_dir = $dir_touxiang.'/thumb/'.$time.'/'.$v.'.jpg';
                    //得到最大值，就移动到指定的目录，并跳出循环
                    rename($dir.$v.'touxiang.jpg',$new_dir);

                    //删除上传的头像，只留最新的
                    foreach(check_dir($dir_touxiang.'/thumb/'.$time) as $k1=>$v1){
                        if($v1 != $v.'.jpg'){
                            unlink($dir_touxiang.'/thumb/'.$time.'/'.$v1);
                        }
                    }

                    //更换为统一的图片名
                    rename($new_dir, $dir_touxiang.'/thumb/'.$time.'/'.'touxiang.jpg');
                    //放进数据库缩略图的相对路径
                    $this->arr1[] = Cookie::get('username').'/touxiang/thumb/'.$time.'/'.'touxiang.jpg';
                    break;
                }
            }

            //删除掉所有的上传图tmp_img/用户 下面的
            foreach(check_dir($dir) as $k=>$v){
                if(strpos($v,'touxiang.jpg') != false){
                    unlink($dir.$v);
                }
            }

            //删除掉所有的上传图tmp_img下面的
            foreach(check_dir(ABSOLUTE_HOME . 'uploads/tmp_img') as $k=>$v){
                if(strpos($v,'.jpg') != false){
                    unlink(ABSOLUTE_HOME . 'uploads/tmp_img/'.$v);
                }
            }

            //接收修改的信息，验证格式化放入数据库,前端已经用jq来验证数据了，所以不需要再次验证，直接更新数据即可
            $arr = [
                'user_id' => Cookie::get('user_id'),
                'account_name' => $_POST['account_name'],
                'user_tel' => $_POST['user_tel'],
                'user_qq' => $_POST['user_qq'],
                'user_email' => $_POST['user_email'],
                'school_id' => $_POST['user_school'],
                'nianji' => $_POST['nianji'],
                'zhuanye' => $_POST['zhuanye'],
                'user_img' => $this->arr1[0]
            ];

            $fl = $this->user->update($arr);
            if ($fl) {
                $this->success("信息修改成功", "personal/personal");
            } else {
                $this->success("信息修改成功", "personal/personal");
            }
        }
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
                $image->thumb(150, 150, Image::THUMB_FIXED)->save($tx.'/'.$time. 'touxiang.jpg');
                echo $path . '/' .$time. 'touxiang.jpg';
            } else {
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }

    }

    /*
     * 用户修改密码
     * */
    public function modifypw()
    {
        if (empty($_POST)) {
            //显示出用户的学校
            $a = table_cognate('es_user', 'es_school', 'school_id', 'a.user_id=' . Cookie::get('user_id'));
            $this->assign('userlist', $a);
            return $this->fetch();
        } else {
            //前端已经验证，所以不用验证，直接判断密码是否正确，正确就更新密码
            $xpw = $_POST['xpassword'];
            $pw = $_POST['password'];
            $user_id = Cookie::get('user_id');
            $u = $this->user->where('user_id', $user_id)->find();
            if ($u['userpwd'] === $pw) {
                $fl = $this->user->where('user_id', $user_id)->update(['userpwd' => $xpw]);
                if ($fl) {
                    $this->success("密码更改成功", "index/index");
                } else {
                    $this->error("密码更改失败", "personal/modifypw");
                }
            } else {
                $this->error("密码更改失败", "personal/modifypw");
            }
        }
    }

    /*
     * 商品发布
     * */
    public function goodsfb()
    {
        //调用公共函数，关联两个表查询
        $a = table_cognate('es_school', 'es_goods', 'school_id', 'b.user_id=' . Cookie::get('user_id'));
        $total = count($a);//获取数据总数
        $listrow = 6;
        $page = new Page($total, $listrow);
        //这里暂时没有想到方法优化，先使用原生查询来关联两个表，并且规定每页显示多少数据,后期再优化，功能已实现
        $g = Db::query('select * from es_school s join es_goods g on s.school_id=g.school_id where user_id=' . Cookie::get('user_id') . ' ' . $page->limit);
        $show = $page->fpage();   //获得分页显示
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('goodslist', $g);
        //显示出用户的学校
        $a = table_cognate('es_user', 'es_school', 'school_id', 'a.user_id=' . Cookie::get('user_id'));
        $this->assign('userlist', $a);
        return $this->fetch();

    }

    /*
     * 商品修改
     * */
    public function xggoods()
    {
        if(empty($_POST)){
            //显示出学校和用户信息
            $school = $this->school->select();
            $this->assign('schoollist', $school);
            //显示出用户的学校
            $a = table_cognate('es_user', 'es_school', 'school_id', 'a.user_id=' . Cookie::get('user_id'));
            $this->assign('userlist', $a);
            $g = $this->goods->where('user_id',Cookie::get('user_id'))->select();
            $this->assign('goods',$g);
            return $this->fetch();
        }else{
            //前端已经验证数据了，直接更新要修改的商品数据
            $arr = [
               'goods_id'     =>      $_POST['goods_id'],
               'goods_name'   =>      $_POST['goods_name'],
               'goods_place'  =>      $_POST['goods_place'],
               'cost_price'   =>      $_POST['cost_price'],
               'ershou_price' =>      $_POST['ershou_price'],
               'goods_number' =>      $_POST['goods_number'],
               'user_qq'      =>      $_POST['user_qq'],
               'user_tel'     =>      $_POST['user_tel'],
               'is_delete'    =>      $_POST['is_delete'],
               'last_update'  =>      date('Y-m-d H:i:s',time())
            ];
            $fl = $this->goods->update($arr);
            $this->success("更新成功","personal/xggoods");
        }
    }

    /*显示商品修改信息*/
    public function xggoods_msg()
    {
        $goods_id = $_POST['goods_id'];
        $goods = $this->goods->where('goods_id',$goods_id)->find();
        echo json_encode($goods);
    }

    /*
     * 商品删除
     * */
    public function goodsdel()
    {
        if(empty($_POST)){
            //显示出学校和用户信息
            $school = $this->school->select();

            //显示出用户的学校
            $a = table_cognate('es_user', 'es_school', 'school_id', 'a.user_id=' . Cookie::get('user_id'));
            $this->assign('userlist', $a);

            $g = $this->goods->where('user_id',Cookie::get('user_id'))->paginate(10);
            $total = $this->goods->where('user_id',Cookie::get('user_id'))->count();
            $listrow = 10;
            //实例化分页类
            $page = new Page($total, $listrow);
            $show = $page->fpage();
            $this->assign('page', $show);// 赋值分页输出
            $this->assign('goodslist', $g);
            $this->assign('schoollist', $school);
            return $this->fetch();
        }else{
            //接收要删除的商品id，然后删除商品
            $goods_id = $_POST['goods_id'];
            $fl = $this->goods->delete($goods_id);
            if($fl){
                echo '删除成功';
            }else{
                echo '删除失败';
            }
        }
    }

    /*
     * 帖子管理
     * */
    public function post()
    {
        if (empty($_POST)) {
            //调用公共函数，关联两个表查询
            $a = table_cognate('es_user', 'es_school', 'school_id', 'a.user_id=' . Cookie::get('user_id'));
            //显示用户信息
            $this->assign('userlist', $a);
            //查询出当前用户所有的帖子
            $post = $this->forum_post->where('user_id', Cookie::get('user_id'))->order('fp_id desc')->paginate(10);
            $total = $this->forum_post->where('user_id', Cookie::get('user_id'))->count();
            $listrow = 10;
            //实例化分页类
            $page = new Page($total, $listrow);
            $show = $page->fpage();
            $this->assign('page', $show);// 赋值分页输出
            $this->assign('post', $post);
        } else {
            //验证editorValue不能为空
            if(!empty($_POST['editorValue'])){
                $arr = [
                    'fp_content' =>$_POST['editorValue'],
                    'last_update'=>date('Y-m-d H:i:s',time())
                ];
                $fl = $this->forum_post->where('fp_id',$_POST['fp_id'])->update($arr);
                if($fl){
                    $this->success("修改成功");
                }else{
                    $this->error("修改失败");
                }
            }else{
                $this->error("请修改内容，且内容不能为空");
            }
        }
        return $this->fetch();
    }

    /*
     * 按帖子id获取帖子信息
     * */
    public function post_msg()
    {
        $fp_id = $_POST['fp_id'];
        $fp = $this->forum_post->where('fp_id',$fp_id)->find();
        echo json_encode($fp);
    }

    /*
     * 按帖子的id删除帖子
     * */
    public function post_del()
    {
        $fp_id = $_POST['fp_id'];
        $fl = $this->forum_post->where('fp_id',$fp_id)->delete();
        if($fl){
            echo 'success';
        }else{
            echo 'fail';
        }
    }

    /*
     * 消息通知,没弄好
     * */
    public function msgtongzhi()
    {
        if(empty($_POST)){
            //显示出用户的学校
            $a = table_cognate('es_user', 'es_school', 'school_id', 'a.user_id=' . Cookie::get('user_id'));
            $this->assign('userlist', $a);
            //显示出通知
            $tz = $this->tongzhi->where('user_id',Cookie::get('user_id'))->order('tz_id desc')->paginate(10);
            $total = $this->tongzhi->where('user_id',Cookie::get('user_id'))->order('tz_id desc')->count();
            $listrow = 10;
            //实例化分页类
            $page = new Page($total, $listrow);
            $show = $page->fpage();
            $this->assign('page', $show);// 赋值分页输出
            $this->assign('tongzhi',$tz);

            //用户查询
            $u = $this->admin->select();
            $this->assign('u',$u);
            return $this->fetch();
        }else{
            $tz_id = $_POST['tz_id'];
            //更新通知的阅读状态
            $this->tongzhi->where('tz_id',$tz_id)->update(['tz_ready'=>'是']);
        }
    }

    /*
     * 通知阅读
     * */
    public function tongzhiready()
    {
        //接收通知的id
        $tz_id = input('param.tz_id');
        //显示出用户的学校
        $a = table_cognate('es_user', 'es_school', 'school_id', 'a.user_id=' . Cookie::get('user_id'));
        $this->assign('userlist', $a);
        //显示出通知
        $tz = $this->tongzhi->where('tz_id',$tz_id)->find();
        $this->assign('tz',$tz);
        //显示用户的名字
        $user = $this->user->where('user_id',Cookie::get('user_id'))->find();
        $this->assign('user',$user);
        return $this->fetch();
    }

    /*
     * 认证,没弄好
     * */
    public function renzheng()
    {
        //显示出用户的学校
        $a = table_cognate('es_user', 'es_school', 'school_id', 'a.user_id=' . Cookie::get('user_id'));
        $this->assign('userlist', $a);
        return $this->fetch();
    }
}