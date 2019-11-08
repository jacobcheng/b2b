<?php

namespace app\admin\model\sales;

use think\Model;
use traits\model\SoftDelete;

class Client extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'clients';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'type_text',
        'star_text',
        'status_text'
    ];
    

    
    public function getTypeList()
    {
        return ['零售商' => __('零售商'), '批发商' => __('批发商'), '品牌商' => __('品牌商'), '进口商' => __('进口商'), '连锁商场' => __('连锁商场'), '企业' => __('企业')];
    }

    public function getStarList()
    {
        return ['1' => __('Star 1'), '2' => __('Star 2'), '3' => __('Star 3'), '4' => __('Star 4'), '5' => __('Star 5')];
    }

    public function getStatusList()
    {
        return ['10' => __('Status 10'), '20' => __('Status 20'), '30' => __('Status 30'), '40' => __('Status 40'), '-1' => __('Status -1')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStarTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['star']) ? $data['star'] : '');
        $list = $this->getStarList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function category()
    {
        return $this->belongsTo('app\common\model\Category', 'category_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function admin()
    {
        return $this->belongsTo('app\admin\model\Admin', 'admin_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function country()
    {
        return $this->belongsTo('app\admin\model\Country', 'country_code', 'code', [], 'LEFT')->setEagerlyType(0);
    }


    public function city()
    {
        return $this->belongsTo('app\admin\model\City', 'city_code', 'code', [], 'LEFT')->setEagerlyType(0);
    }


    public function contact()
    {
        return $this->belongsTo('app\admin\model\sales\Contact', 'contact_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function contacts()
    {
        return $this->hasMany('app\admin\model\sales\Contact', 'client_id', 'id');
    }
}
