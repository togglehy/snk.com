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


class b2c_openapi_stock
{
    public function __construct()
    {
        $this->req_params = vmc::singleton('base_component_request')->get_params(true);
    }

    public function confirm($args = array())
    {
        $args = array_merge((array)$args,$this->req_params);
        $sku = $args['sku'];
        if(!$sku){
            $this->_failure('缺少参数');
        }
        $_echo = array();
        $sku_bn = explode(',', $sku);
        if (count($sku_bn) > 0) {
            $result = app::get('b2c')->model('stock')->getList('sku_bn,warehouse,quantity-freez_quantity as num', array(
                'sku_bn' => $sku_bn,
            ));
            $_echo = utils::array_change_key($result, 'sku_bn');
        }
        $this->_success($_echo);
    }

    private function _success($data){
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode(array(
            'result'=>'success',
            'data'=>$data
        ));exit;
    }

    private function _failure($msg){
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode(array(
            'result'=>'failure',
            'data'=>[],
            'msg'=>$msg
        ));exit;
    }

}
