<?php
namespace  app\admin\behavior;

class MultiLanguageImages
{
    public function run (&$params)
    {
        $model = model('app\admin\model\site\Image');
        $images = $params->getData('images');
        if (isset($images['language'])){
            $language = $images['language'];unset($images['language']);
        } elseif ($params->getData('content')['language']){
            $language = $params->getData('content')['language'];
        } else {
            $language = getDefaultLanguage();
        }
        $model->where(['image_type' => getController(), 'image_id' => $params->id, 'language' => $language])->delete();
        $datas = [];
        foreach ($images as $image) {
            if (!empty($image['url'])){
                $data = [
                    'image_type'   => getController(),
                    'image_id'     => $params->id,
                    'language'     => $language,
                    'url'          => $image['url'],
                    'title'        => $image['title'],
                    'tagline'      => isset($image['tagline']) ? $image['tagline']:'',
                    'alt'          => $image['alt'],
                    'weigh'        => isset($image['weigh']) ? $image['weigh']:'',
                ];
                $datas[] = $data;
            }
        }
        $model->saveAll($datas);
    }
}