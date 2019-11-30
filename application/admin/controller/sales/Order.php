<?php

namespace app\admin\controller\sales;

use app\common\controller\Backend;
use NumberToWords\NumberToWords;
use think\Db;
use think\Exception;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Hook;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Order extends Backend
{
    
    /**
     * Order模型对象
     * @var \app\admin\model\sales\Order
     */
    protected $model = null;

    /**
     * 是否开启数据限制
     * 支持auth/personal
     * 表示按权限判断/仅限个人
     * 默认为禁用,若启用请务必保证表中存在admin_id字段
     */
    protected $dataLimit = 'personal';

    /**
     * 数据限制字段
     */
    protected $dataLimitField = 'admin_id';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\sales\Order;
        $this->view->assign("currencyList", $this->model->getCurrencyList());
        $this->view->assign("incotermsList", $this->model->getIncotermsList());
        $this->view->assign("transportList", $this->model->getTransportList());
        $this->view->assign("statusList", $this->model->getStatusList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                    ->with(['client','contact', 'country', 'admin', 'quotation'])
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['client','contact', 'country', 'admin', 'quotation'])
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            foreach ($list as $row) {
                $row->visible(['id','ref_no','country_code','currency','incoterms','leadtime','status','createtime']);
				$row->visible(['quotation']);
				$row->getRelation('quotation')->visible(['ref_no']);
				$row->visible(['client']);
				$row->getRelation('client')->visible(['short_name']);
				$row->visible(['contact']);
				$row->getRelation('contact')->visible(['appellation']);
				$row->visible(['admin']);
                $row->visible(['country']);
                $row->getRelation('country')->visible(['country_name']);
				$row->getRelation('admin')->visible(['nickname']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }


    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    Hook::listen('before_write_order', $params);
                    $result = $this->model->allowField(true)->save($params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success('','',$this->model->id);
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    Hook::listen('after_write_order', $row);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }


    public function detail ($ids = NULL)
    {
        $row = $this->model->find($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        $this->assignconfig('order', $row);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }


    /*public function placeorder ($ids = null)
    {
        $row = model("app\admin\model\sales\Quotation")->get($ids);
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    //$params['quotation_id'] = $ids;
                    Hook::listen('before_placeorder', $row);
                    $result = $this->model->allowField(true)->save($params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    foreach ($row->items as $item) {
                        unset($item['id'], $item['quotation_id'], $item['unit_cost']);
                        $item['product'] = json_encode($item['product']);
                        $item['accessory'] = json_encode($item['accessory']);
                        $item['package'] = json_encode($item['package']);
                        $item['accessory'] = json_encode($item['accessory']);
                        $item['carton'] = json_encode($item['carton']);
                        $this->model->items()->save($item);
                    }
                    $this->success('', '', ['ids' => $this->model->id, 'ref_no' => $this->model->ref_no]);
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $row['leadtime'] = date('Y-m-d', time() + ($row['leadtime'] * 24 * 60 * 60));
        $this->view->assign('row', $row);
        return $this->fetch('edit');
    }*/

    public function placeorder ($ids = null)
    {
        if ($this->request->isAjax()) {
            $row = model("app\admin\model\sales\Quotation")->with('items')->find($ids);
            $result = false;
            Db::startTrans();
            try {
                //$params['quotation_id'] = $ids;
                $params = $row->toArray();
                Hook::listen('before_placeorder', $params);
                $result = $this->model->allowField(true)->save($params);
                Hook::listen('after_placeorder', $this->model);
                Db::commit();
            } catch (ValidateException $e) {
                Db::rollback();
                $this->error($e->getMessage());
            } catch (PDOException $e) {
                Db::rollback();
                $this->error($e->getMessage());
            } catch (Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            if ($result !== false) {
                $this->success('', '', ['ids' => $this->model->id, 'ref_no' => $this->model->ref_no]);
            } else {
                $this->error(__('No rows were inserted'));
            }
        }
    }


    public function print ($ids = null, $type)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        $client = model('app\admin\model\sales\Client')->get($row['client_id']);
        $totalamount = $row['currency']==="CNY" ? ($row['tax_rate'] > 0 ? $row['total_tax_amount'] : $row['total_amount']) : $row['total_usd_amount'];
        $numberToWords = new NumberToWords();
        $currency = $numberToWords->getCurrencyTransformer('en');
        $saytotal = $currency->towords(($totalamount+$row['service_amount'])*100, "USD");
        $this->view->assign(["row" =>  $row, "client" => $client, "saytotal" => $saytotal]);
        if ($type === "ci") {
            return $this->view->fetch("print_ci");
        } elseif ($type === "pl") {
            return $this->view->fetch("print_pl");
        }
    }
}
