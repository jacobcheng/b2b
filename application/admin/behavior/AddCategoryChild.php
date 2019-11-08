<?php
namespace  app\admin\behavior;
use app\common\model\Category;

class AddCategoryChild
{
    public function run (&$params)
    {
        if ($params['menu_type'] !== 'page'){
            $category = Category::where('id', $params['menu_id'])->find();
            if ($category['pid'] === 0) {
                $child = Category::getChildList($category['id']);
                if ($child) {
                    $this->addList($child, $params);
                }
            }
            return;
        }
        return;
    }

    public function addList($childlist, $father) {
        $childlist = collection($childlist)->toArray();
        foreach ($childlist as $value) {
            $menu = new \app\admin\model\content\Menu;
            $menu->save([
                'type'      => $father['type'],
                'menu_type' => $father['menu_type'],
                'menu_id'   => $value['id'],
                'name'      => $value['content']['title'],
                'weigh'     => $value['weigh'],
                'pid'       => $father['id']
            ]);
            $ret = Category::getChildList($value['id']);
            if ($ret) {
                self::addList($ret, $menu);
            }
        }
    }
}