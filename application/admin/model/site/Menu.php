<?php

namespace app\admin\model\site;

use fast\Tree;
use think\Model;


class Menu extends Model
{
    // 表名
    protected $name = 'menus';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text',
        'url'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }


    function getUrlAttr($value, $data)
    {
        $model = $data['menu_type'] === 'page' ? model('app\admin\model\site\Page'): model('Category');
        $menu = $model->where('id', $data['menu_id'])->find();
        return $menu['url'];
    }

    
    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public static function buildMenu($menu_item,$dropdown, $level)
    {
        if ($dropdown) {
            $dropdown_active = !is_null(request()->param('category')) && ($menu_item['menu_id'] === model('app\admin\model\content\Page')->where('slug', request()->param('category'))->cache()->value('id') && $menu_item['menu_type'] === 'page') ? ' active':'';
            $reverse = $level > 2 ? ' dropdown-reverse':'';
            $submenu = $level > 1 ? 'dropdown-submenu':'dropdown';
            $toggle = $level > 1 ? '':' dropdown-toggle';
            if (empty($menu_item['childlist'])) {
                return "<li><a class=\"nav-link".$dropdown_active."\" href=\"".$menu_item['url']."\">".$menu_item['content']['title']."</a></li>\n";
            } else {
                return "<li class=\"".$submenu.$reverse."\">\n<a class=\"dropdown-item".$toggle.$dropdown_active."\" href=\"".$menu_item['url']."\">".$menu_item['content']['title']."</a><ul class=\"dropdown-menu\">\n";
            }
        } else {
            $nav_active = !is_null(request()->param('slug')) && ($menu_item['menu_id'] === model('Category')->where('slug',request()->param('slug'))->cache()->value('id') && $menu_item['menu_type'] === 'siteCategory') ? ' active':'';
            if (empty($menu_item['childlist'])) {
                return "<li class=\"nav-item\"><a class=\"nav-link".$nav_active."\" href=\"".$menu_item['url']."\">".$menu_item['content']['title']."</a></li>";
            } else {
                return "<li class=\"nav-item\">\n<a class=\"nav-link".$nav_active."\" href=\"".$menu_item['url']."\">".$menu_item['content']['title']."</a>\n<ul>\n";
            }
        }
    }

    public function getMenuTree($menuList,$dropdown = true, $pid = 0){
        $tree = Tree::instance();
        $tree->init($menuList, 'pid');
        return self::prepareMenu($tree->getTreeArray($pid), $dropdown,0);
    }

    public function prepareMenu($menuList, $dropdown, $level){
        $html = '';
        $level ++;
        foreach ($menuList as $item){
            $html .= $this->buildMenu($item, $dropdown, $level);
            if (!empty($item['childlist'])){
                $html .= $this->prepareMenu($item['childlist'], $dropdown, $level);
                $html .= "</ul>\n";
            }
        }
        return $html;
    }
}
