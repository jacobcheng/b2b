<?php

namespace app\admin\controller\site;

use app\admin\controller\general\Config;
use app\common\library\Email;
use app\common\model\Config as ConfigModel;
use think\Exception;

/**
 * 系统配置
 *
 * @icon fa fa-cogs
 * @remark 可以在此增改系统的变量和分组,也可以自定义分组和变量,如果需要删除请从数据库中删除
 */
class SiteConfig extends Config
{

    /**
     * @var \app\common\model\Config
     */
    protected $model = null;
    protected $noNeedRight = ['check'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Config');
    }

    /**
     * 查看
     */
    public function index()
    {
        $siteList = [];
        $groupList = ConfigModel::getGroupList();
        foreach ($groupList as $k => $v) {
            if(in_array($k, ['website', 'blog_category', 'product_category', 'blog_page', 'product_page'])) {
                $siteList[$k]['name'] = $k;
                $siteList[$k]['title'] = $v;
                $siteList[$k]['list'] = [];
            }
        }

        foreach ($this->model->all() as $k => $v) {
            if (!isset($siteList[$v['group']])) {
                continue;
            }
            $value = $v->toArray();
            $value['title'] = __($value['title']);
            if (in_array($value['type'], ['select', 'selects', 'checkbox', 'radio'])) {
                $value['value'] = explode(',', $value['value']);
            }
            $value['content'] = json_decode($value['content'], TRUE);
			$value['tip'] = htmlspecialchars($value['tip']);
            $siteList[$v['group']]['list'][] = $value;
        }
        $index = 0;
        foreach ($siteList as $k => &$v) {
            $v['active'] = !$index ? true : false;
            $index++;
        }
        $this->view->assign('siteList', $siteList);
        $this->view->assign('typeList', ConfigModel::getTypeList());
        $this->view->assign('groupList', ConfigModel::getGroupList());
        return $this->view->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
        parent::add();
    }

    /**
     * 编辑
     * @param null $ids
     */
    public function edit($ids = NULL)
    {
        parent::edit();
    }

    public function del($ids = "")
    {
        parent::del();
    }

    /**
     * 刷新配置文件
     */
    protected function refreshFile()
    {
        parent::refreshFile();
    }

    /**
     * 检测配置项是否存在
     * @internal
     */
    public function check()
    {
        parent::check();
    }

}
