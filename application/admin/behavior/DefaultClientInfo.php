<?php

namespace app\admin\behavior;


class DefaultClientInfo
{
    public function run(&$params)
    {
        $params['quotation_id'] = isset($params['quotation_id']) ? $params['quotation_id']:'';
        if ($params['default_info']) {
            $client = model('app\admin\model\sales\Client')->where('id', $params['client_id'])->find();
            $params['contact_id'] = $client['contact']['id'];
            $params['country_code'] = $client['country_code'];
            $params['destination'] = $client['address'];
        }
    }
}
