<?php

namespace app\admin\behavior;


class AddDescription
{
    public function run(&$params)
    {
        $description = $params['adddescription'];
        if (empty($params['description'])) {
            foreach ($description as $lang => $value) {
                $data = [
                    'desc_type' => getController(),
                    'lang' => $lang,
                    'desc_id' => $params['id'],
                    'description' => $value['description'],
                    'specification' => isset($value['specification']) ? $value['specification']:''
                ];
                model('app\admin\model\product\MultiDesc')->save($data);
            }
        } else {
            foreach ($params['description'] as $value) {
                foreach ($description as $lang => $val) {
                    if ($value['lang'] === $lang) {
                        $value['description'] = $val['description'];
                        $value['specification'] = isset($val['specification']) ? $val['specification']:'';
                        $value->save();
                    }
                }
            }
        }
    }
}
