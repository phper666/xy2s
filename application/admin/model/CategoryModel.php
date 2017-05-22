<?php
namespace app\admin\model;

use think\Model;
use think\Db;
use think\Validate;
use app\admin\service\CategoryService;

class CategoryModel extends Model
{
    protected $table = 'es_category';

    /*
     * 这是一个验证category添加的信息
     * @param array $data 接收的category信息，为一个一维数组
     * @param string $val 默认为add，这个参数是表名是添加验证还是编辑验证,默认会对栏目名进行存在验证
     * @return bool 验证正确返回true
     * @return string 失败则返回错误信息
     * */
    public function CategoryValidate($data, $val = 'add')
    {
        $validate = validate('CategoryValidate');   //验证的规则，这个可以直接实例化到我admin模块下的validate目录下的Role类，里面我定义了规则
        $check_data = [
            'cat_name' => $data['cat_name']
        ];

        if (!$validate->check($check_data)) {
            return $validate->getError();
        } else {
            if ($val == 'add') {
                //对栏目名进行验证
                $cs = new CategoryService();
                $fl = $cs->checkName($data);
                if ($fl) {
                    return '栏目存在';
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }
    }
}