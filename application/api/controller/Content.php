<?php


namespace app\api\controller;

use app\common\controller\Api;


class Content extends Api
{
    protected $noNeedLogin = ['getcontent'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    public function getcontent ($id = '', $lang = 'en', $type = '')
    {
        $siteLang = array_keys(Config('site.multi_lang'));
        if (!$id){
            $this->error(__('Parameter %s can not be empty', ''));
        }
        if (!in_array($lang, $siteLang)){
            $this->error(__('This language is not available for now!'));
        }
        $content = model('app\admin\model\site\Content')
                 ->scope('languageContent', $id, $type, $lang)
                 ->find();
        return $this->success('', $content);
    }
}