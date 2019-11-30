<?php

namespace app\admin\behavior;


class SaveItem
{
    public function run(&$params)
    {
        $update = request()->param('update');
        $item = isset($params['id']) ? model('app\admin\model\sales\QuotationItem')->get($params['id']): false;
        $variety = (!empty($item) && !$update && $params['variety'] == $item['variety']['id']) ?  $item['variety']: model('app\admin\model\product\variety')->get($params['variety']);
        list($productCBM, $packageCBM) = [$variety['length'] * $variety['width'] * $variety['height'], $variety['plength'] * $variety['pwidth'] * $variety['pheight']];
        $cbm = $packageCBM > $productCBM ? $packageCBM : $productCBM;
        list($params['cbm'], $params['grossw'], $params['netw'], $params['ctn'], $params['unit_cost']) = [
            $cbm/1000000 * $params['quantity'],
            $variety['pweight'] * $params['quantity'],
            $variety['weight'] * $params['quantity'],
            $params['quantity'],
            $variety['cost']
        ];
        $params['variety'] = json_encode($variety);

        if ($params['process']){
            foreach (json_decode($params['process'], true) as $value) {
                $params['unit_cost'] += $value['cost'];
            }
        }

        if ($params['accessory']){
            $accessory = model('app\admin\model\product\Accessory')->all($params['accessory']);
            if (empty($item) || $update) {
                foreach ($accessory as $value) {
                    $params['unit_cost'] += $value['cost'];
                    $params['grossw'] += $value['weight'] * $params['quantity'];
                    $params['netw'] += $value['weight'] * $params['quantity'];
                }
                $params['accessory'] = json_encode($accessory);
            } else {
                $params['accessory'] = [];
                foreach ($accessory as $value) {
                    foreach ($item['accessory'] as $val){
                        if ($value['id'] == $val['id']){
                            $params['unit_cost'] += $val['cost'];
                            $params['grossw'] += $val['weight'] * $params['quantity'];
                            $params['netw'] += $val['weight'] * $params['quantity'];
                            $params['accessory'][] = $val;
                        } else {
                            $params['unit_cost'] += $value['cost'];
                            $params['grossw'] += $value['weight'] * $params['quantity'];
                            $params['netw'] += $value['weight'] * $params['quantity'];
                            $params['accessory'][] = $value;
                        }
                    }
                }
                $params['accessory'] = json_encode($params['accessory']);
            }
        }

        //$pnc = 0;
        $params['unit_package_cost'] = 0;
        if ($params['package']){
            $package = (!empty($item) && !$update && $params['package'] == $item['package']['id']) ? $item['package'] : model('app\admin\model\product\Package')->get($params['package']);
            $params['cbm'] = $package['length'] * $package['width'] * $package['height'] / 1000000 * $params['quantity'];
            $params['grossw'] = $params['netw'] +  $package['weight'] * $params['quantity'];
            //$pnc += $package['cost'];
            $params['unit_package_cost'] = round($package['cost']/$package['rate'],2);
            $params['package'] = json_encode($package);
        }

        $params['unit_carton_cost'] = 0;
        if ($params['carton']){
            $carton = (!empty($item) && !$update && $params['carton'] == $item['carton']['id']) ? $item['carton'] : model('app\admin\model\product\Carton')->get($params['carton']);
            $params['ctn'] = ceil($params['quantity']/$carton['rate']);
            $params['cbm'] = $carton['length'] * $carton['width'] * $carton['height'] / 1000000 * $params['ctn'];
            $params['netw'] = $params['grossw'];
            $params['grossw'] += $carton['weight'] * $params['ctn'];
            //$pnc += round($carton['cost']/$carton['rate'], 2);
            $params['unit_carton_cost'] = round($carton['cost']/$carton['rate'],2);
            $params['carton'] = json_encode($carton);
        }
    }
}
