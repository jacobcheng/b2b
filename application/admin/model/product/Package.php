<?php

namespace app\admin\model\product;

use think\Model;
use traits\model\SoftDelete;

class Package extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'packages';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'description'
    ];










    public function getDescriptionAttr()
    {
        return MultiDesc::scope('multiDesc', $this->id)->select();
    }

}
