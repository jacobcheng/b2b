<?php

namespace app\admin\model\site;

//use app\admin\model\content\Image;
use think\Model;


class Banner extends Model
{
    // 表名
    protected $name = 'banners';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'width_text',
        'images',
        'languages'
    ];
    

    
    public function getWidthList()
    {
        return ['fullwidth' => __('Fullwidth'), 'fullscreen' => __('Fullscreen')];
    }


    public function getWidthTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['width']) ? $data['width'] : '');
        $list = $this->getWidthList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getImagesAttr()
    {
        return Image::scope('languageImages', $this->id, 'banner', request()->param('lang'))->order('weigh desc')->select();
    }

    public function getLanguagesAttr()
    {
        return Image::scope('languageImages', $this->id)->group('language')->column('language');
    }
}
