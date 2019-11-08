<?php

namespace app\admin\model\site;

use think\Model;
use traits\model\SoftDelete;

class Content extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'multi_contents';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];

    public function setContentAttr($value)
    {
        if (is_array($value)){
            return json_encode($value);
        } else {
            return $value;
        }
    }

    public function getContentAttr($value)
    {
        if ($this->getData('content_type') === 'widget') {
            return json_decode($value, true);
        } else {
            return $value;
        }
    }

    protected function scopeAllContent($query, $id, $content_type = '')
    {
        $query->where(['content_type' => $content_type, 'content_id' => $id]);
    }

    protected function scopeLanguageContent($query, $id, $content_type = '', $language = '')
    {
        $language = $language ? : getDefaultLanguage();
        $query->where(['content_type' => $content_type, 'content_id' => $id, 'language' => $language]);
    }
}
