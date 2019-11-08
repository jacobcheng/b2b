<?php

namespace app\api\controller;

use app\common\controller\Api;


class Product extends Api
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = ['getcategorytree'];

    public function _initialize()
    {
        parent::_initialize();
    }


    public function getcategorytree()
    {
        if  ($this->request->isAjax()) {
            //if (!$id) return $this->error(__('Parameter %s can not be empty', ''));
            //$where['type'] = 'catalog';
            $datalist = model('app\common\model\Category')
                ->where(['type' => 'product'])
                ->order('weigh asc')
                ->field('id,pid,name,nickname')
                ->select();

            $list [] = ['id' => '0', 'parent' => '#', 'text' => __('All'), 'state' => ['opened' => true, 'selected' => true]];
            foreach ($datalist as $key =>$value) {
                $list[] = [
                    'id' => $value['id'],
                    'parent' => $value['pid'] ? : '0',
                    'text'   => $value['nickname'], //TODO:多语言设置
                    'state'  => $key === 0 ? ['opened' => true]:''
                ];
            }
            return json($list);
        }
    }
}
