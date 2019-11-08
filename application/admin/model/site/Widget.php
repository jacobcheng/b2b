<?php

namespace app\admin\model\site;

use think\Model;


class Widget extends Model
{
    // 表名
    protected $name = 'widgets';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'widget_type_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    
    public function getWidgetTypeList()
    {
        return ['current_blog' => __('Current_blog'), 'current_product' => __('Current_product'), 'gallery' => __('Gallery'), 'text' => __('Text'), 'button' => __('Button'), 'contact_info' => __('Contact_info'), 'menu' => __('Menu'), 'contact_form' => __('Contact_form'), 'social_links' => __('Social_links'), 'search_form' => __('Search_form'), 'image' => __('Image'), 'navigation' => __('Navigation'), 'tags' => __('Tags')];
    }


    public function getWidgetTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['widget_type']) ? $data['widget_type'] : '');
        $list = $this->getWidgetTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getContentAttr()
    {
        return Content::scope('languageContent', $this->id, 'tag', request()->param('lang'))->find();
    }
}
