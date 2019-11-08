<?php

namespace app\admin\model\product;

use think\Model;

class MultiDesc extends Model
{


    // 表名
    protected $name = 'multi_desc';


    // 追加属性
    protected $append = [

    ];
    

    



    public function scopeMultiDesc ($query, $id)
    {
        $query->where(['desc_type' => getController(), 'desc_id' => $id]);
    }
}
