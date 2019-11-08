<?php

namespace app\admin\model\site;

//use app\admin\model\site\Content;
use app\admin\model\site\Image;
//use app\admin\model\site\Tag;
use think\Model;
use traits\model\SoftDelete;

class Blog extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'blogs';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'post_status_text',
        'content',
        'languages',
        'url',
        'images',
        //'tags'
    ];




    public function getUrlAttr($value, $data)
    {
        return getLanguageUrl().'/blog/'.$data['slug'].".html";
    }


    public function getPostStatusList()
    {
        return ['published' => __('Published'), 'draft' => __('Draft')];
    }


    public function getPostStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['post_status']) ? $data['post_status'] : '');
        $list = $this->getPostStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function category()
    {
        return $this->belongsTo('app\common\model\Category', 'category_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function admin()
    {
        return $this->belongsTo('app\admin\model\Admin', 'id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function getContentAttr()
    {
        return Content::scope('languageContent', $this->id, 'blog', request()->param('lang'))->find();
    }


    public function getLanguagesAttr()
    {
        return Content::scope('allContent', $this->id, 'blog')->column('language');
    }

    public function getImagesAttr()
    {
        return Image::scope('languageImages', $this->id, 'blog', request()->param('lang'))->select();
    }

    public function getTagsAttr()
    {
        return Tag::where('id', 'in', $this->tag_ids)->select();
    }
}
