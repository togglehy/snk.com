<?php
// +----------------------------------------------------------------------
// | VMCSHOP [V M-Commerce Shop]
// +----------------------------------------------------------------------
// | Copyright (c) vmcshop.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.vmcshop.com/licensed)
// +----------------------------------------------------------------------
// | Author: Shanghai ChenShang Software Technology Co., Ltd.
// +----------------------------------------------------------------------


class b2c_order_log {
    private $operator = array(
        'ident' => '',
        'name' => '',
        'model' => ''
    );
    private $order_id = 0;
    public function __construct($app) {
        $this->mdl_log = app::get('b2c')->model('order_log');
    }
    public function set_operator($operator) {
        $this->operator = $operator;
        $this->operator['ident'] = intval($this->operator['ident']);
        if($this->operator['ident']<0){
            $this->operator['ident'] = 0;
        }
        return $this;
    }
    public function set_order_id($order_id) {
        $this->order_id = $order_id;
        return $this;
    }
    public function success($behavior = 'update', $log = '成功', $addons = array()) {
        if ($behavior!='create' && $this->order_id < 1) return false;
        $sdf = array(
            'log' => $log,
            'behavior' => $behavior,
            'addons' => $addons,
            'operator' => $this->operator,
            'log_time' => time() ,
            'result' => 'success',
            'order_id' => $this->order_id,
        );
        return $this->mdl_log->save($sdf, null, true);
    }
    public function fail($behavior = 'update', $log = '失败', $addons = array()) {
        if ($behavior!='create' && $this->order_id < 1) return false;
        $sdf = array(
            'log' => $log,
            'behavior' => $behavior,
            'addons' => $addons,
            'operator' => $this->operator,
            'log_time' => time() ,
            'result' => 'fail',
            'order_id' => $this->order_id,
        );
        return $this->mdl_log->save($sdf, null, true);
    }
}
