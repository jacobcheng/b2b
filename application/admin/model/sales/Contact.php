<?php

namespace app\admin\model\sales;

use think\Model;
use traits\model\SoftDelete;

class Contact extends Model
{

    use SoftDelete;

    //数据库
    protected $connection = 'database';
    // 表名
    protected $name = 'contacts';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];

    protected $insert = [
        'admin_id'
    ];


    public function setAdminIdAttr()
    {
        return session('admin.id');
    }



    public function admin()
    {
        return $this->belongsTo('app\admin\model\Admin', 'admin_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }



    public function client()
    {
        return $this->belongsTo('app\admin\model\sales\Client', 'client_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
