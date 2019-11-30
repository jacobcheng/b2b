<?php

namespace app\admin\behavior;


class UpdateItems
{
    public function run(&$params)
    {
        $unit_fee = $params->getUnitFee();
        if (count($params['items']) > 0) {
            foreach ($params['items'] as $value) {
                $unit_package_cost = empty($value['package']) ? 0 : $value['carton']['cost']/$value['carton']['rate'];
                $unit_carton_cost = empty($value['carton']) ? 0 : $value['package']['cost']/$value['package']['rate'];
                $insurance = $params['insurance'] ? : 0 ;
                $value['unit_price'] = round(((floatval($value[key($unit_fee)]) * current($unit_fee) / $value['quantity']) + (floatval($value['unit_cost']) * (1 + $value['profit'] / 100)) + $unit_package_cost + $unit_carton_cost) * (1 + $insurance / 10000), 2);
                $value['amount'] = $value['unit_price'] * $value['quantity'];
                $value['usd_unit_price'] = round($value['unit_price'] / $params['rate'], 2);
                $value['usd_amount'] = $value['usd_unit_price'] * $value['quantity'];
                $value['tax_amount'] = $params['vat'] > 0 ? $value['amount']/(1 - $params['vat']/100):'';
                $value->save();
            }
        }
    }
}
