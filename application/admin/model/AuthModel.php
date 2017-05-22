<?php
namespace app\admin\model;

use think\Model;
use think\Validate;
use app\admin\service\AuthService;

/*目前还不用到模型，要多关联才会用到
要明白这个问题，必须了解 MVC 历史。早在 MVC 出现以前，程序员是将 html、css、js、php、SQL 写在一个 .php 文件内的，那时的网页非常简单。后来复杂了，需要多个人协同开发，一个开发后台，专写 php + SQL，一个开发前端，专写 html + css + js。形成了 VC 架构，但有个问题，他们之间不是异步开发的，而是同步开发，前端写完模板，phper 才能在上面加 php 代码。如果不小心字符串过长了，样式可能会错乱，又要找前端调整样式。这样工作效率很低。最后 M 出现了，phper 可以在 M 上写 php 代码，写完后，进行单元测试即可。前端在 V 上写 html + css + js 代码，这个过程是异步完成的，彼此之间互不影响，最后拼接的时候，用 C 调用一下 M 获得数据后，再渲染到 V 上即可。C 就是个桥接器而已。但现在的开发模式又变了，出现了很多后台和前台框架，这使得 M 和 V 的地位一下子下降了。很多 M 要完成的功能，后台框架包办了，如 ThinkPHP，很多 V 要完成的功能，前台框架包办了，如 Amaze UI。因为框架技术的发展，导致很多程序员的开发效率大增，开发成本大幅度下降。许多 phper 不需要依赖前端也可以开发出非常出色的网站。使得 MVC 本来为了协同开发而设计出来的模式显得不是那么重要了。所以完全可以用 C 替代 M。但受 ThinkPHP 框架限制，有些功能，如多对多关联模型，只能在 M 中实现。所以有时还是要用 M。有时一套 CMS 中要可以选择多套模板，这时就需要前端分担一些工作量，不然 phper 要累死了。
*/

/*
 * 用于es_auth表操作的验证的一个类
 * @author liyuzhao
 *
 *以下一个函数功能
 * @access public
 * @abstract authValide 主要功能：验证auth添加的信息。一个逻辑：主要对接收的auth信息进行验证
 * */

class AuthModel extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'es_auth';

    /*
    * 这是一个验证auth添加的信息
    * @param array $data 接收的auth信息，为一个一维数组
     * @param string $val 默认为add，这个参数是表名是添加验证还是编辑验证,默认会进行数据添加验证
    * @return bool 验证正确返回true
    * @return string 失败则返回错误信息
    * */
    public function authValidate($data, $val = 'add')
    {
        $validate = validate('AuthValidate');
        if (empty($data['auth_dingji_id'])) {
            $data['auth_dingji_id'] = ' ';
        }
        if (empty($data['auth_yiji_id'])) {
            $data['auth_yiji_id'] = ' ';
        }
        $check_data = [
            'auth_name' => $data['auth_name'],
            'auth_level' => $data['auth_level'],
            'auth_dingji_id' => $data['auth_dingji_id'],
            'auth_yiji_id' => $data['auth_yiji_id'],
            'auth_c' => $data['auth_c'],
            'auth_a' => $data['auth_a']
        ];

        //如果为0级
        if ($data['auth_level'] == '0') {
            if (!$validate->scene('dingji')->check($check_data)) {
                return $validate->getError();
            } else {
                if ($val == 'add') {
                    //这里调用service模块里面的验证权限名,如果是编辑就不用验证
                    $as = new AuthService();
                    $fl = $as->checkName($data);
                    if ($fl) {
                        return '权限名存在';
                    } else {
                        return true;
                    }
                } else {
                    return true;
                }
            }
        } else if ($data['auth_level'] == '1') {
            if (!$validate->scene('yiji')->check($check_data)) {
                return $validate->getError();
            } else {
                if ($val == 'add') {
                    //这里调用service模块里面的验证权限名,如果是编辑就不用验证
                    $as = new AuthService();
                    $fl = $as->checkName($data);
                    if ($fl) {
                        return '权限名存在';
                    } else {
                        return true;
                    }
                } else {
                    return true;
                }
            }
        } else if ($data['auth_level'] == '2') {
            if (!$validate->scene('erji')->check($check_data)) {
                return $validate->getError();
            } else {
                if ($val == 'add') {
                    //这里调用service模块里面的验证权限名,如果是编辑就不用验证
                    $as = new AuthService();
                    $fl = $as->checkName($data);
                    if ($fl) {
                        return '权限名存在';
                    } else {
                        return true;
                    }
                } else {
                    return true;
                }
            }
        }
    }
}