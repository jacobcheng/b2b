<?php

namespace app\admin\model\sales;

use think\Model;
use traits\model\SoftDelete;

class OrderItem extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'order_items';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];





    public function setProcessAttr ($value)
    {
        return $value ? $value:"[]";
    }

    public function getVarietyAttr ($value)
    {
        return json_decode($value, true);
    }

    public function getPackageAttr ($value)
    {
        return json_decode($value, true);
    }

    public function getCartonAttr ($value)
    {
        return json_decode($value, true);
    }

    public function getAccessoryAttr ($value)
    {
        return $value ? json_decode($value, true):[];
    }

    public function getProcessAttr ($value)
    {
        return json_decode($value, true);
    }





    public function order()
    {
        return $this->belongsTo('app\admin\model\sales\Order', 'order_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function variety()
    {
        return $this->belongsTo('app\admin\model\product\Variety', 'variety', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function accessory()
    {
        return $this->belongsTo('app\admin\model\product\Accessory', 'accessory', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function package()
    {
        return $this->belongsTo('app\admin\model\product\Package', 'package', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function carton()
    {
        return $this->belongsTo('app\admin\model\product\Carton', 'carton', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
