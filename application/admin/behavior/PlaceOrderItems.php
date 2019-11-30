<?php

namespace app\admin\behavior;


class PlaceOrderItems
{
    public function run(&$params)
    {
        $items = $params['items'];
        foreach ($items as $item) {
            unset($item['id'],$item['createtime'],$item['updatetime'],$item['deletetime'],$item['unit_cost'],$item['quotation_id']);
            $item['variety'] = json_encode($item['variety']);
            $item['accessory'] = is_null($item['accessory']) ? '':json_encode($item['accessory']);
            $item['package'] = is_null($item['package']) ? '':json_encode($item['package']);
            $item['carton'] = is_null($item['carton']) ? '':json_encode($item['carton']);
            $item['process'] = json_encode($item['process']);
            $params->items()->save($item);
        }
        model('app\admin\model\sales\Quotation')->where('id',$params['quotation_id'])->update(['status' => 40]);
    }
}
