<?php

namespace app\api\controller;

use app\common\controller\Api;

/**
 * 首页接口
 */
class Client extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['checkdata'];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 首页
     *
     */
    public function checkdata()
    {

    }
}
