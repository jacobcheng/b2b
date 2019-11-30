<?php

namespace app\admin\model\sales;

use think\Model;
use traits\model\SoftDelete;

class QuotationItem extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'quotation_items';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    /*protected $type = [
        'grossw' => 'float',
        'cbm' => 'float',
    ];*/

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




    public function quotation()
    {
        return $this->belongsTo('app\admin\model\sales\Quotation', 'quotation_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    /*public function product()
    {
        return $this->belongsTo('app\admin\model\product\Product', 'product_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }*/


    public function accessory()
    {
        return $this->belongsTo('app\admin\model\product\Accessory', 'accessory_ids', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function package()
    {
        return $this->belongsTo('app\admin\model\product\Package', 'package_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function carton()
    {
        return $this->belongsTo('app\admin\model\product\Carton', 'carton_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


}
