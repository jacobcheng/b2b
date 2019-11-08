<?php


namespace app\api\controller;

use app\common\controller\Api;


class Image extends Api
{
    protected $noNeedLogin = ['getimage'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    public function getimage ($id = '', $lang = 'en', $type = '')
    {
        $siteLang = array_keys(Config('site.multi_lang'));
        if (!$id){
            $this->error(__('Parameter %s can not be empty', ''));
        }
        if (!in_array($lang, $siteLang)){
            $this->error(__('This language is not available for now!'));
        }
        $images = model('app\admin\model\site\Image')
                 ->scope('languageImages', $id, $type, $lang)
                 ->select();
        return $this->success('', $images);
    }
}