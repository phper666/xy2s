<?php
namespace app\admin\model;

use think\Model;
use think\Db;
use think\Validate;
use app\admin\service\SchoolService;

class SchoolModel extends Model
{
    protected $table = 'es_School';

    /*
     * 这是一个验证school添加的信息
     * @param array $data 接收的school信息，为一个一维数组
     * @return bool 验证正确返回true
     * @return string 失败则返回错误信息
     * */
    public function SchoolValidate($data)
    {
        $validate = validate('SchoolValidate');   //验证的规则，这个可以直接实例化到我admin模块下的validate目录下的Role类，里面我定义了规则
        $check_data = [
            'school_name' => $data['school_name'],
            'school_area' => $data['school_area'],
            'school_city' => $data['school_city'],
            'keywords' => $data['keywords']
        ];

        if (!$validate->check($check_data)) {
            return $validate->getError();
        } else {
            //对学校名进行验证
            $ss = new SchoolService();
            $fl = $ss->checkName($data);
            if ($fl) {
                return '学校存在';
            } else {
                return true;
            }
        }
    }
}