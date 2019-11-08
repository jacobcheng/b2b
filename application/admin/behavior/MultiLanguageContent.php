<?php
namespace  app\admin\behavior;

class MultiLanguageContent
{
    public function run (&$params)
    {
        $content = $params->getData('content');
        $data = [
            'content_type' => getController(),
            'content_id'   => $params->id,
            'language'     => isset($content['language']) ? $content['language']:getDefaultLanguage(),
            'title'        => $content['title'],
            'seo_title'    => isset($content['seo_title']) ? $content['seo_title']:'',
            'keyword'      => isset($content['keyword']) ? $content['keyword']:'',
            'description'  => isset($content['description']) ? $content['description']:'',
            'content'      => isset($content['content']) ? $content['content']:'',
        ];
        if (in_array($data['language'], $params->languages)){
            model('app\admin\model\site\Content')->isUpdate(true)->save($data);
        } else {
            model('app\admin\model\site\Content')->save($data);
        }
    }
}