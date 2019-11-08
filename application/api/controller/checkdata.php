<?php

namespace app\api\controller;

use app\common\controller\Api;

/**
 * 首页接口
 */
class Checkdata extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['client_name', 'client_short_name', 'contact_email'];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 首页
     *
     */
    public function client()
    {
        $params = $this->request->post('row/a');
        $id = $this->request->post('id');
        $client = $this->model->withTrashed()->where(key($params), current($params))->find();
        if ($client && $client['admin_id'] === $this->auth->id) {
            if ($id){
                return $this->success();
            } else {
                return $this->error("该客户已存在".($client['deletetime'] ? "并被删除": ""));
            }
        } elseif ($client && $client['admin_id'] !== $this->auth->id) {
            return $this->error("该客户属于 ".$client['admin']['nickname'].($client['deletetime'] ? " 并被删除": ""));
        } else {
            return $this->success();
        }
    }
}
