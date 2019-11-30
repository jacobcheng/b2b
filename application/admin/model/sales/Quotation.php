<?php

namespace app\admin\model\sales;

use think\Db;
use think\Model;
use traits\model\SoftDelete;

class Quotation extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'quotations';
    
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
        'leadtime_text',
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


    protected function setRefNoAttr ()
    {
        $num = self::whereTime('createtime', 'd')->count();
        return 'LW'.date('Ymd').sprintf("%03d",$num+1);
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
        return ['Express Service' => __('Express service'), 'By Sea' => __('By sea'), 'By Air' => __('By air'), 'By Train' => __('By train'), 'By Road' => __('By road'), '' => __('')];
    }

    public function getStatusList()
    {
        return ['10' => __('New'), '20' => __('Quoted'), '30' => __('Print PI'), '40' => __('Ordered'), '-1' => __('Expired')];
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


    public function getLeadtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['leadtime']) ? $data['leadtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
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

    protected function setLeadtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function getServiceAttr ($value)
    {
        return json_decode($value, true);
    }


    public function client()
    {
        return $this->belongsTo('app\admin\model\sales\Client', 'client_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function contact()
    {
        return $this->belongsTo('app\admin\model\sales\Contact', 'contact_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function country()
    {
        return $this->belongsTo('app\admin\model\Country', 'country_code', 'code', [], 'LEFT')->setEagerlyType(0);
    }


    public function admin()
    {
        return $this->belongsTo('app\admin\model\Admin', 'admin_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function account()
    {
        return $this->belongsTo('app\admin\model\accounting\Account', 'account_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public  function items()
    {
        return $this->hasMany('app\admin\model\sales\QuotationItem', 'quotation_id', 'id', [], 'LEFT');
    }

    //获取单位运费
    public function getUnitFee ($newcbm = 0, $newweight = 0, $id = 0)
    {
        $total_cbm = QuotationItem::where(['quotation_id' => $this->id, 'id' => ['<>', $id]])->sum('cbm') + $newcbm;
        $total_weight = QuotationItem::where(['quotation_id' => $this->id, 'id' => ['<>', $id]])->sum('grossw') + $newweight;
        $unit_cbm_cost = round($this->transport_fee / $total_cbm,2);
        $unit_weight_cost = round($this->transport_fee / $total_weight,2);
        if ($this->incoterms !== "EXW" && $total_cbm != 0){
            switch ($this->incoterms) {
                case 'Express Service':
                    return $total_cbm * 200 > $total_weight ? ['cbm' => $unit_cbm_cost]:['weight' => $unit_weight_cost];
                    break;

                case 'By Air':
                    return $total_cbm * 1.67 > $total_weight ? ['cbm' => $unit_cbm_cost]:['weight' => $unit_weight_cost];
                    break;

                default:
                    return ['cbm' => $unit_cbm_cost];
                    break;
            }
        } else {
            return ['grossw' => 0];
        }
    }


    public function getTotalAmountAttr ()
    {
        return QuotationItem::where('quotation_id',$this->getData('id'))->sum('amount');
    }

    public function getTotalUsdAmountAttr ()
    {
        return QuotationItem::where('quotation_id',$this->getData('id'))->sum('usd_amount');
    }

    public function getTotalTaxAmountAttr ()
    {
        return QuotationItem::where('quotation_id',$this->getData('id'))->sum('tax_amount');
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
        return QuotationItem::where('quotation_id',$this->getData('id'))->sum('cbm');
    }

    public function getTotalGrossWeightAttr ()
    {
        return QuotationItem::where('quotation_id',$this->getData('id'))->sum('grossw');
    }

    public function getTotalNetWeightAttr ()
    {
        return QuotationItem::where('quotation_id',$this->getData('id'))->sum('netw');
    }

    public function getTotalCtnAttr ()
    {
        return QuotationItem::where('quotation_id',$this->getData('id'))->sum('ctn');
    }
}
