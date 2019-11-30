<?php

namespace app\admin\model\product;

use think\Db;
use think\Model;
use traits\model\SoftDelete;

class Variety extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'varieties';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'unit_text',
        'description',
        'image',
        'category_id',
        'hscode'
    ];
    

    
    public function getUnitList()
    {
        return ['PC' => __('Pc'), 'SET' => __('Set'), 'BOX' => __('Box'), 'CARTON' => __('Carton'), 'G' => __('G'), 'KG' => __('Kg'), 'TON' => __('Ton'), 'CBM' => __('Cbm')];
    }


    public function getUnitTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['unit']) ? $data['unit'] : '');
        $list = $this->getUnitList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getImageAttr()
    {
        return $this->product->image;
    }

    public function getCategoryIdAttr ($value, $data)
    {
        //return Db::name('product_model')->where('id', $data['model_id'])->value('category_id');
        return $this->product->category_id;
    }

    public function getHscodeAttr ()
    {
        return $this->product->hscode;
    }

    public function product()
    {
        return $this->belongsTo('app\admin\model\product\Product', 'product_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function getDescriptionAttr()
    {
        return MultiDesc::scope('multiDesc', $this->id)->select();
    }
}
