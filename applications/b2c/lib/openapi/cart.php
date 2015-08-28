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


class b2c_openapi_cart
{
    private $req_params = array();
    public function __construct($http = true)
    {
        if ($http) {
            header('Content-Type:application/json; charset=utf-8');
            $this->req_params = vmc::singleton('base_component_request')->get_params(true);
        }
    }
    /**
     * 购物车数量
     */
    public function count(){
        $mdl_cartobjects = app::get('b2c')->model('cart_objects');
        $filter = array('obj_type'=>'goods');
        $session = vmc::singleton('base_session');
        $session->start();
        $filter['member_ident'] = md5($session->sess_id());//非会员购物车
        if($member_id = vmc::singleton('b2c_user_object')->get_member_id()){
            //会员购物车
            $filter['member_ident'] = md5($member_id);
            $filter['member_id'] = $member_id;
        }
        $count = $mdl_cartobjects->count($filter);
        $this->_success(array('count'=>$count));
    }
    /**
     * 购物车结果
     */
    public function preview(){
        $cart_stage = vmc::singleton('b2c_cart_stage');
        $cart_result = $cart_stage->currency_result();
        $is_empty = $cart_stage->is_empty($cart_result);
        if($is_empty){
            $this->_failure('购物车为空');
        }
        $result_arr = array();
        foreach ($cart_result['objects']['goods'] as $key => $item) {
             $product = $item['item']['product'];
             $product['item_url'] = app::get('site')->router()->gen_url(array('app' => 'b2c', 'ctl' => 'site_product', 'args' => array($product['product_id'])));
             $product['remove_url'] = app::get('site')->router()->gen_url(array('app' => 'b2c', 'ctl' => 'site_cart', 'act'=>'remove','args' => array($item['obj_ident'])));
             $product['image_url'] = base_storager::image_path($product['image_id'], 'm');
             $product['price'] = vmc::singleton('ectools_math')->formatNumber($product['price'], app::get('ectools')->getConf('site_decimal_digit_count'), app::get('ectools')->getConf('site_decimal_digit_count'));
             $result_arr[] = $product;
        }
        $this->_success($result_arr);
    }



    private function _success($data)
    {
        echo json_encode(array(
            'result' => 'success',
            'data' => $data,
        ));
        exit;
    }

    private function _failure($msg)
    {
        echo json_encode(array(
            'result' => 'failure',
            'data' => [],
            'msg' => $msg,
        ));
        exit;
    }
}
