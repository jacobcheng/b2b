<?php

namespace app\admin\model\product;

use think\Model;
use traits\model\SoftDelete;

class Product extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'products';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'image',
        'description'
    ];








    public function getImageAttr ($value, $data)
    {
        $images = explode(',',$data['images']);
        return $images[0];
    }


    public function category()
    {
        return $this->belongsTo('app\common\model\Category', 'category_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function client()
    {
        return $this->belongsTo('app\admin\model\sales\Client', 'client_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function getDescriptionAttr()
    {
        return MultiDesc::scope('multiDesc', $this->id)->select();
    }

}
