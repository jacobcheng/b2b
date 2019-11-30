<?php

namespace app\admin\behavior;


class CalculateQuotationPrice
{
    public function run(&$params)
    {
        $id = empty($item) ? '0' : $item['id'];
        $quotation =  model('app\admin\model\sales\Quotation')->get($params['quotation_id']);
        $unit_fee = $quotation->getUnitFee($params['cbm'], $params['grossw'], $id);

        $params['unit_price'] = isset($params['unit_price']) && $params['unit_price'] ? $params['unit_price'] :round((($params[key($unit_fee)] * current($unit_fee)/$params['quantity']) + ($params['unit_cost'] * (1 + $params['profit']/100)) + $params['unit_package_cost'] + $params['unit_carton_cost']) * (1 + $quotation['insurance']/1000), 2);
        $params['usd_unit_price'] = isset($params['usd_unit_price']) && $params['usd_unit_price'] ? $params['usd_unit_price']:round($params['unit_price']/$quotation['rate'],2);
        $params['usd_amount'] = $params['usd_unit_price'] * $params['quantity'];
        $params['amount'] = $params['unit_price'] * $params['quantity'];
        $params['tax_amount'] = $params['amount']/(1 - $quotation['vat']/100);
    }
}
