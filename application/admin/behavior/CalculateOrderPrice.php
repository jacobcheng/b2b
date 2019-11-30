<?php

namespace app\admin\behavior;


class CalculateOrderPrice
{
    public function run(&$params)
    {
        //$id = isset($params['id']) ? '0' : $params['id'];
        $order =  model('app\admin\model\sales\Order')->get($params['order_id']);
        $params['profit'] = 0;
        $params['unit_price'] = isset($params['unit_price']) && $params['unit_price'] ? $params['unit_price'] :$params['usd_unit_price'] * $order['rate'];
        $params['usd_unit_price'] = isset($params['usd_unit_price']) && $params['usd_unit_price'] ? $params['usd_unit_price']:round($params['unit_price']/$order['rate'],2);
        $params['usd_amount'] = $params['usd_unit_price'] * $params['quantity'];
        $params['amount'] = $params['unit_price'] * $params['quantity'];
        $params['tax_amount'] = $params['amount']/(1 - $order['vat_rate']/100);
    }
}
