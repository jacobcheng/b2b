<?php

namespace app\admin\model\site;

use think\Model;

class Image extends Model
{

    // 表名
    protected $name = 'multi_images';


    // 追加属性
    protected $append = [

    ];

    protected function scopeAllContent($query, $id)
    {
        $query->where(['image_type' => getController(), 'image_id' => $id]);
    }

    protected function scopeLanguageImages($query, $id, $image_type = '', $language = '')
    {
        $language = $language ? : getDefaultLanguage();
        $image_type = $image_type ? :getController();
        $query->where(['image_type' => $image_type, 'image_id' => $id, 'language' => $language])->field('url, title, alt, tagline, weigh');
    }
}
