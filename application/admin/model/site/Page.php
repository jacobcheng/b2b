<?php

namespace app\admin\model\site;

use app\admin\model\site\Content;
use think\Model;
use traits\model\SoftDelete;

class Page extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'pages';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'content',
        'languages',
        'url'
    ];



    protected $auto = [
        'name', //TODO:可能不需要name字段
        'view'
    ];

    public function setNameAttr($value, $data)
    {
        if (!isset($data['content']['language']) || $data['content']['language'] === getDefaultLanguage()){
            return $data['content']['title'];
        }
        return $value;
    }

    public function setViewAttr($value)
    {
        return isset($value) ? $value:'';
    }


    public function getUrlAttr($value, $data)
    {
        if ($data['slug'] === '/') {
            $lang = request()->param('lang');
            return in_array($lang, array_keys(config('site.multi_lang'))) ? '/'.$lang.'.html':'/';
        } else {
            return getLanguageUrl().'/'.$data['slug'].".html";
        }
    }



    public function getContentAttr ()
    {
        return Content::scope('languageContent', $this->id, 'page', request()->param('lang'))->find();
    }

    public function getLanguagesAttr ()
    {
        return Content::scope('allContent', $this->id, 'page')->column('language');
    }

}
