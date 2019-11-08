<?php


namespace app\admin\controller;
use app\common\controller\Backend;


class Location extends Backend
{
    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = ['index'];

    protected  $model = null;
    public function _initialize()
    {
        parent::_initialize();
    }
    public function index()
    {
        if ($this->request->request('keyField'))
        {
            return $this->selectpage();
        }
    }

    public function selectpage()
    {
        if($this->request->request("showField") == 'country_name'){
            $this->model = model('country');
        } else {
            $this->model = model('city');
        }
        return parent::selectpage();
    }
}