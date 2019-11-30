<?php

namespace app\admin\behavior;


class PlaceOrder
{
    public function run(&$params)
    {
        $params['quotation_id'] = $params['id'];
        $params['lc_no'] = '';
        unset($params['id'],$params['ref_no'],$params['validay'],$params['transport_fee'],$params['insurance'],$params['createtime'],$params['updatetime'],$params['deletetime']);
        $params['leadtime'] = date("Y-m-d", strtotime("+ ".$params['leadtime']." days"));
        $params['service'] = json_encode($params['service']);
    }
}
