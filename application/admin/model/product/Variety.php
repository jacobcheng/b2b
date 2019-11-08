<?php

namespace app\admin\model\product;

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
        'description'
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


    public function getDescriptionAttr()
    {
        return MultiDesc::scope('multiDesc', $this->id)->select();
    }
}
