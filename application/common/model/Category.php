<?php

namespace app\common\model;

use app\admin\model\site\Content;
use think\Model;

/**
 * 分类模型
 */
class Category extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
        'type_text',
        'flag_text',
        'content',
        'languages',
        'url'
    ];

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $row->save(['weigh' => $row['id']]);
        });
    }

    public function setFlagAttr($value, $data)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }


    public function setSlugAttr($value, $data)
    {
        return empty($value) ? str_replace(' ', '-', strtolower($data['name'])) : $value;
    }

    public function getUrlAttr($value, $data)
    {
        $prefix = $data['type'] === 'product' ? '/product-category/' : '/blog-category/';//TODO:待优化
        return getLanguageUrl().$prefix.$data['slug'].".html";
    }

    /**
     * 读取分类类型
     * @return array
     */
    public static function getTypeList()
    {
        $typeList = config('site.categorytype');
        foreach ($typeList as $k => &$v) {
            $v = __($v);
        }
        return $typeList;
    }

    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['type'];
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getFlagList()
    {
        return ['hot' => __('Hot'), 'index' => __('Index'), 'recommend' => __('Recommend')];
    }

    public function getFlagTextAttr($value, $data)
    {
        $value = $value ? $value : $data['flag'];
        $valueArr = explode(',', $value);
        $list = $this->getFlagList();
        return implode(',', array_intersect_key($list, array_flip($valueArr)));
    }

    /**
     * 读取分类列表
     * @param string $type   指定类型
     * @param string $status 指定状态
     * @return array
     */
    public static function getCategoryArray($type = null, $status = null)
    {
        $list = collection(self::where(function ($query) use ($type, $status) {
            if (!is_null($type)) {
                $query->where('type', '=', $type);
            }
            if (!is_null($status)) {
                $query->where('status', '=', $status);
            }
        })->order('weigh', 'desc')->select())->toArray();
        return $list;
    }

    public function getContentAttr()
    {
        return Content::scope('languageContent', $this->id, 'siteCategory', request()->param('lang'))->find();
    }


    public function getLanguagesAttr ()
    {
        return Content::scope('allContent', $this->id, 'siteCategory')->column('language');
    }

    public static function getChildList($pid)
    {
        $list = self::where('pid', $pid)->select();
        if ($list) {
            return $list;
        }
    }

    public function getAllChildIds($pid)
    {
        $ids = [];
        $child = self::getChildList($pid);
        if ($child) {
            $childids = array_column($child, 'id');
            foreach ($childids as $childid) {
                $arr = $this->getAllChildIds($childid);
                $ids = array_merge($ids, $arr);
            }
        }
        $ids[] = $pid;
        return $ids;
    }
}
