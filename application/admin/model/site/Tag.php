<?php

namespace app\admin\model\site;

//use app\admin\model\site\Content;
use think\Db;
use think\Model;


class Tag extends Model
{

    

    

    // 表名
    protected $name = 'multi_tags';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_text',
        'content',
        'languages',
        'relate_ids',
        'url'
    ];
    

    
    public function getTypeList()
    {
        return ['productPage' => __('Productpage'), 'article' => __('Article')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function getUrlAttr($value, $data)
    {
        return getLanguageUrl().'/tag/'.$data['slug'].".html";
    }


    public function getContentAttr()
    {
        return Content::scope('languageContent', $this->id, 'tag', request()->param('lang'))->find();
    }


    public function getLanguagesAttr()
    {
        return Content::scope('allContent', $this->id, 'tag')->column('language');
    }

    public function getRelateIdsAttr()
    {
        return Db::name('tag_relate')->where('tag_id', $this->id)->column('relate_id');
    }

}
