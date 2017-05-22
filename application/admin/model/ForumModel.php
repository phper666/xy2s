<?php
/**
 * Created by PhpStorm.
 * User: liyuzhao
 * Date: 2016/10/23
 * Time: 14:29
 */
//验证的操作
namespace app\admin\model;
use think\Model;
use think\Db;
use app\admin\service\ForumService;

class ForumModel extends Model
{
    public function addallcolumnValidate($data, $val = 'add')
    {
        $validate = validate('ForumValidate');   //验证的规则，这个可以直接实例化到我admin模块下的validate目录下的Forum类，里面我定义了规则
        $check_data = [
            'fac_name' => $data['fac_name']
        ];

        if (!$validate->scene('addallcolumn')->check($check_data)) {
            return $validate->getError();
        } else {
            if ($val == 'add') {
                //对管理员名进行验证
                $fs = new ForumService();
                $fl = $fs->checkAllName($data);
                if ($fl) {
                    return '总栏目存在';
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }
    }

    public function addsubcolumnValidate($data, $val = 'add')
    {
        $validate = validate('ForumValidate');   //验证的规则，这个可以直接实例化到我admin模块下的validate目录下的Forum类，里面我定义了规则
        $check_data = [
            'fsc_name' => $data['fsc_name']
        ];

        if (!$validate->scene('addsubcolumn')->check($check_data)) {
            return $validate->getError();
        } else {
            if ($val == 'add') {
                //对管理员名进行验证
                $fs = new ForumService();
                $fl = $fs->checkSubName($data);
                if ($fl) {
                    return '子栏目存在';
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }
    }
}