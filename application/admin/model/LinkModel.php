<?php
namespace app\admin\model;

use think\Model;
use think\Db;
use think\Validate;
use app\admin\service\LinkService;

class LinkModel extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'es_link';

    /*
     * 这是一个验证link添加的信息
     * @param array $data 接收的link信息，为一个一维数组
     * @param string $val 默认为add，这个参数是表名是添加验证还是编辑验证,默认会对链接进行存在验证
     * @return bool 验证正确返回true
     * @return string 失败则返回错误信息
     * */
    public function linkValidate($data, $val = 'add')
    {
        $validate = validate('LinkValidate');   //验证的规则，这个可以直接实例化到我admin模块下的validate目录下的link类，里面我定义了规则
        $check_data = [
            'link_name' => $data['link_name'],
            'link_link' => $data['link_link']
        ];

        if (!$validate->check($check_data)) {
            return $validate->getError();
        } else {
            if ($val == 'add') {
                //对角色名进行验证
                $ls = new LinkService();
                $fl = $ls->checkName($data);
                if ($fl) {
                    return '链接存在';
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }
    }
}