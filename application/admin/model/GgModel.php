<?php
namespace app\admin\model;

use think\Model;
use think\Db;
use think\Validate;
use app\admin\service\GgService;

/*
 * 这是一个对接收到的公告内容进行验证
 * */

class GgModel extends Model
{
    protected $table = 'es_gonggao';

    /*
     * 这是一个验证gonggao添加的信息
     * @param array $data 接收的gonggao信息，为一个一维数组
     * @param array $val 默认为add，默认会验证标题是否存在
     * @return bool 验证正确返回true
     * @return string 失败则返回错误信息
     * */
    public function ggValidate($data, $val = 'add')
    {
        $validate = validate('GgValidate');     //实例化Gg表的验证规则
        $check_data = [
            'gg_name' => $data['gg_name'],
            'gg_content' => $data['gg_content']
        ];
        if (!$validate->check($check_data)) {
            return $validate->getError();
        } else {
            if ($val == 'add') {
                //对标题进行验证
                $rs = new GgService();
                $fl = $rs->checkName($data);
                if ($fl) {
                    return '标题已存在，请更换标题';
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }
    }
}