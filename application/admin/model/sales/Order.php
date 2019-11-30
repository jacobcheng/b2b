<?php

namespace app\admin\model\sales;

use think\Model;
use traits\model\SoftDelete;

class Order extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'orders';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'currency_text',
        'incoterms_text',
        'transport_text',
        'status_text',
        'total_cbm',
        'total_gross_weight',
        'total_net_weight',
        'total_ctn',
        'total_amount',
        'total_usd_amount',
        'total_tax_amount',
    ];


    protected $insert = [
        'ref_no',
        'status' => 1
    ];


    public function setRefNoAttr ()
    {
        $num = self::whereTime('createtime', 'd')->count();
        return "SC".date("Ymd").sprintf("%03d",$num+1);
    }
    
    public function getCurrencyList()
    {
        return ['USD' => __('USD'), 'CNY' => __('CNY')];
    }

    public function getIncotermsList()
    {
        return ['EXW' => __('EXW'), 'FCA' => __('FCA'), 'FAS' => __('FAS'), 'FOB' => __('FOB'), 'CFR' => __('CFR'), 'CIF' => __('CIF'), 'CPT' => __('CPT'), 'CIP' => __('CIP'), 'DAT' => __('DAT'), 'DAP' => __('DAP'), 'DDP' => __('DDP')];
    }

    public function getTransportList()
    {
        return ['Express Service' => __('Express service'), 'By Sea' => __('By sea'), 'By Air' => __('By air'), 'By Train' => __('By train'), 'By Road' => __('By road')];
    }

    public function getStatusList()
    {
        return ['10' => __('Pending'), '20' => __('Processing'), '30' => __('Collected'), '40' => __('Completed'), '-1' => __('Cancel')];
    }


    public function getCurrencyTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['currency']) ? $data['currency'] : '');
        $list = $this->getCurrencyList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIncotermsTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['incoterms']) ? $data['incoterms'] : '');
        $list = $this->getIncotermsList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getTransportTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['transport']) ? $data['transport'] : '');
        $list = $this->getTransportList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getServiceAttr ($value)
    {
        return json_decode($value, true);
    }



    public function quotation()
    {
        return $this->belongsTo('app\admin\model\sales\Quotation', 'quotation_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function client()
    {
        return $this->belongsTo('app\admin\model\sales\Client', 'client_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function country()
    {
        return $this->belongsTo('app\admin\model\Country', 'country_code', 'code', [], 'LEFT')->setEagerlyType(0);
    }

    public function contact()
    {
        return $this->belongsTo('app\admin\model\sales\Contact', 'contact_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function admin()
    {
        return $this->belongsTo('app\admin\model\Admin', 'admin_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }



    public function items()
    {
        return $this->hasMany('app\admin\model\sales\OrderItem', 'order_id', 'id', [], 'LEFT');
    }


    public function getTotalAmountAttr ()
    {
        return OrderItem::where('order_id',$this->getData('id'))->sum('amount');
    }

    public function getTotalUsdAmountAttr ()
    {
        return OrderItem::where('order_id',$this->getData('id'))->sum('usd_amount');
    }

    public function getTotalTaxAmountAttr ()
    {
        return OrderItem::where('order_id',$this->getData('id'))->sum('tax_amount');
    }

    public function getServiceAmountAttr ()
    {
        $amount = 0;
        if ($this->service) {
            foreach ($this->service as $value) {
                $amount += $value['cost'];
            }
        }
        return $amount;
    }

    public function getTotalCbmAttr ()
    {
        return OrderItem::where('order_id',$this->getData('id'))->sum('cbm');
    }

    public function getTotalGrossWeightAttr ()
    {
        return OrderItem::where('order_id',$this->getData('id'))->sum('grossw');
    }

    public function getTotalNetWeightAttr ()
    {
        return OrderItem::where('order_id',$this->getData('id'))->sum('netw');
    }

    public function getTotalCtnAttr ()
    {
        return OrderItem::where('order_id',$this->getData('id'))->sum('ctn');
    }
}
