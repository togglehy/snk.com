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


class b2c_cart_object_goods implements b2c_interface_cart_object
{
    private $app;
    private $member_ident; // 用户标识
    private $mdl_cartobjects;
    private $no_database;
    private $no_database_cart_object;
    /**
     * 构造函数.
     *
     * @param $object $app  // service 调用必须的
     */
    public function __construct()
    {
        $this->app = app::get('b2c');
        $this->session = vmc::singleton('base_session');
        $this->session->start();
        $this->member_info = vmc::singleton('b2c_cart_stage')->get_member();
        $this->mdl_cartobjects = $this->app->model('cart_objects');
        $this->mdl_goods = $this->app->model('goods');
        $this->mdl_products = $this->app->model('products');
        if (!empty($this->member_info)) {
            $this->member_id = $this->member_info['member_id'];
            $this->member_discout = $this->member_info['lv_discount'];
        }
        $this->member_discout = ($this->member_discout ? $this->member_discout : 1);
        $this->db = vmc::database();
        $this->obj_math = vmc::singleton('ectools_math');
    }
    public function get_type()
    {
        return 'goods';
    }
    public function get_part_type()
    {
        return array();
    }
    /**
     * 购物车项参数组织.
     */
    private function _params($object)
    {
        return array(
            'item' => $object,
            'warning' => false,
        );
    }
    /**
     * 添加购物车项($object = array('goods'=>array('product_id'=>xxx,'num'=>22))).
     *
     * @return bool
     */
    public function add_object($object, &$msg = '', $append = true, $is_fastbuy = false)
    {
        $object = $object['goods'];
        $arr_save = array(
            'obj_ident' => 'goods_'.$object['product_id'],
            'obj_type' => 'goods',
            'params' => $this->_params(array(
                'product_id' => $object['product_id'],
            )) ,
            'quantity' => ($object['num'] ? $object['num'] : 1),
        );
        if ($is_fastbuy) {
            $arr_save['is_fastbuy'] = 'true';
        }
        $arr_save['member_ident'] = md5($this->session->sess_id());
        if ($this->member_id) {
            $arr_save['member_ident'] = md5($this->member_id);
            $arr_save['member_id'] = $this->member_id;
        }
        // 追加|更新
        if ($append && !$is_fastbuy) {
            // 如果存在相同商品 则追加
            $filter = array(
                'obj_ident' => $arr_save['obj_ident'],
                'member_ident' => $arr_save['member_ident'],
            );
            if ($arr_save['member_id']) {
                $filter['member_id'] = $arr_save['member_id'];
            }
            if ($cart_object = $this->mdl_cartobjects->getRow('*', $filter)) {
                $arr_save['quantity'] += $cart_object['quantity'];
            }
        }
        if (!$this->_check($arr_save, $msg)) { //验证加入项
            return false;
        }
        $is_save = $this->mdl_cartobjects->save($arr_save);
        if (!$is_save) {
            $msg = ('购物车状态保存异常');

            return false;
        }

        return $arr_save['obj_ident'];
    }
    //更新购物车项数量
    public function update($ident, $quantity, &$msg)
    {
        $arr_save = array(
            'obj_ident' => $ident,
            'obj_type' => 'goods',
        );
        $arr_save['member_ident'] = md5($this->session->sess_id());
        if ($this->member_id) {
            $arr_save['member_ident'] = md5($this->member_id);
            $arr_save['member_id'] = $this->member_id;
        }
        $cart_object = $this->mdl_cartobjects->getRow('*', $arr_save);

        if (floatval($quantity) == floatval($cart_object['quantity'])) {
            return $arr_save['obj_ident'];
        }
        $cart_object['quantity'] = floatval($quantity);

        if (!$this->_check($cart_object, $msg)) { //验证加入项
            return false;
        }
        $is_save = $this->mdl_cartobjects->save($cart_object);
        if (!$is_save) {
            $msg = ('购物车状态保存异常');

            return false;
        }

        return $cart_object['obj_ident'];
    }
    /**
     * 指定的购物车商品项.
     *
     * @param string $sIdent
     * @param bool   $rich   // 是否只取cart_objects中的数据 还是完整的sdf数据
     *
     * @return array
     */
    public function get($ident = null, $rich = false, $is_fastbuy = false)
    {
        if (empty($ident)) {
            return $this->getAll($rich, $is_fastbuy);
        }
        $filter = array(
            'obj_ident' => $ident,
            'member_ident' => $this->member_ident,
        );
        if ($is_fastbuy) {
            $filter['is_fastbuy'] = 'true';
        }
        $filter['member_ident'] = md5($this->session->sess_id());
        if ($this->member_id) {
            $filter['member_ident'] = md5($this->member_id);
            $filter['member_id'] = $this->member_id;
        }
        $cart_objects = $this->mdl_cartobjects->getList('*', $filter);
        if (empty($cart_objects)) {
            return array();
        }
        if ($rich) {
            $cart_objects = $this->_get_rich($cart_objects);
        }

        return $cart_objects;
    }
    // 购物车里的所有商品项
    public function getAll($rich = false, $is_fastbuy = false)
    {
        $filter = array(
            'obj_type' => 'goods',
        );
        if ($is_fastbuy) {
            $filter['is_fastbuy'] = 'true';
        }
        $filter['member_ident'] = md5($this->session->sess_id());
        if ($this->member_id) {
            $filter['member_ident'] = md5($this->member_id);
            $filter['member_id'] = $this->member_id;
        }
        $cart_objects = $this->mdl_cartobjects->getList('*', $filter);
        if (!$rich) {
            return $cart_objects;
        }

        return $this->_get_rich($cart_objects);
    }
    // 删除购物车中指定商品项
    public function delete($sIdent = null, $is_fastbuy = false)
    {
        if (!$sIdent || empty($sIdent)) {
            return $this->deleteAll();
        }
        $filter = array(
            'obj_ident' => $sIdent,
            'obj_type' => 'goods',
        );
        if ($is_fastbuy) {
            $filter['is_fastbuy'] = 'true';
        }
        $filter['member_ident'] = md5($this->session->sess_id());
        if ($this->member_id) {
            $filter['member_ident'] = md5($this->member_id);
            $filter['member_id'] = $this->member_id;
        }

        return $this->mdl_cartobjects->delete($filter);
    }
    // 清空购物车中商品项数据
    public function deleteAll($is_fastbuy = false)
    {
        $filter = array(
            'obj_type' => 'goods',
        );
        if ($is_fastbuy) {
            $filter['is_fastbuy'] = 'true';
        }
        $filter['member_ident'] = md5($this->session->sess_id());
        if ($this->member_id) {
            $filter['member_ident'] = md5($this->member_id);
            $filter['member_id'] = $this->member_id;
        }

        return $this->mdl_cartobjects->delete($filter);
    }
    // 小计购物车
    public function count(&$cart_result)
    {
        if (empty($cart_result['objects']['goods'])) {
            return false;
        }
        $cart_result['object_count'] = count($cart_result['objects']['goods']);
        //[objects]['goods']['item']['product']
        foreach ($cart_result['objects']['goods'] as &$cart_object) {
            if ($cart_object['disabled'] == 'true') {
                continue;
            } //该项被禁用

            $item_product = $cart_object['item']['product'];
            //购物车重量
            $count_weight = $this->obj_math->number_multiple(array(
                $item_product['weight'],
                $cart_object['quantity'],
            ));
            $cart_result['weight'] = $this->obj_math->number_plus(array($cart_result['weight'], $count_weight));
            //购物车合计
            $count_cart_amount = $this->obj_math->number_multiple(array(
                $item_product['price'],
                $cart_object['quantity'],
            ));
            $cart_result['cart_amount'] = $this->obj_math->number_plus(array(
                $cart_result['cart_amount'],
                $count_cart_amount,
            ));

            /**
             * 计算购物应得积分
             * score_convert  setting
             * cart_amount 购物车商品总金额
             */
            $cart_result['gain_score'] = $this->obj_math->number_multiple(array(
                app::get('b2c')->getConf('score_convert',1),
                $cart_result['cart_amount'],
            ));
            //会员身份优惠合计
            $minus_member_discount = $this->obj_math->number_minus(array(
                $item_product['price'],
                $item_product['member_lv_price'],
            ));
            $count_member_discount_amount = $this->obj_math->number_multiple(array(
                $minus_member_discount,
                $cart_object['quantity'],
            ));
            $cart_result['member_discount_amount'] = $this->obj_math->number_plus(array(
                $cart_result['member_discount_amount'],
                $count_member_discount_amount,
            ));

            $cart_result['goods_count'] += $cart_object['quantity'];
        }
    }
    /**
     * 获得购物车丰富的详细的数据.
     *
     * @param array $cart_objects 购物车标准数据
     *
     * @return array 包含货品详细数据,购物车项状态数据
     */
    private function _get_rich($cart_objects)
    {

        //$cart_objects = utils::array_change_key($cart_objects, 'obj_ident');

        foreach ($cart_objects as $key => $object) {
            $product_id_arr[] = $object['params']['item']['product_id'];
        }

        $mdl_product = app::get('b2c')->model('products');
        $mdl_goods = app::get('b2c')->model('goods');
        $products = $mdl_product->getList('*', array(
            'product_id' => $product_id_arr,
        ));
        $products = utils::array_change_key($products, 'product_id');
        $goods_id_arr = array_keys(utils::array_change_key($products, 'goods_id'));
        $goods_info = $mdl_goods->getList('goods_id,brand_id,cat_id,type_id,marketable,image_default_id,nostore_sell,min_buy', array(
            'goods_id' => $goods_id_arr,
        ));
        $goods_info = utils::array_change_key($goods_info, 'goods_id');
        foreach ($products as $key => $product) {
            $goods = $goods_info[$product['goods_id']];
            $products[$key]['image_id'] = ($product['image_id'] ? $product['image_id'] : $goods['image_default_id']);
            $products[$key]['marketable'] = ($product['marketable'] == 'true' && $goods['marketable'] == 'true') ? 'true' : 'false';
            $products[$key]['min_buy'] = $goods['min_buy'];
            $products[$key]['nostore_sell'] = $goods['nostore_sell'];
            $products[$key]['cat_id'] = $goods['cat_id'];
            $products[$key]['brand_id'] = $goods['brand_id'];
            $products[$key]['type_id'] = $goods['type_id'];
            //价格扩展
            $products[$key]['buy_price'] = $products[$key]['member_lv_price'] = $this->obj_math->number_multiple(array(
                $product['price'],
                $this->member_discout,
            ));
        }
        foreach ($cart_objects as $key => $object) {
            $product_id = $object['params']['item']['product_id'];
            //购物车数据扩展
            $cart_objects[$key]['item']['product'] = $products[$product_id];
        }
        $this->_warning($cart_objects);

        return $cart_objects;
    }
    //加入\更新购物车时验证
    private function _check($object, &$msg)
    {
        //TODO  购物车项加入时候验证告警

        // if (empty($object['item'])) {
        //     $object = $this->_get_rich(array($object));
        //     $object = $object[0];
        // }
        // if (!vmc::singleton('b2c_goods_stock')->is_available_stock(
        // $object['item']['product']['bn'],
        // $object['quantity'])) {
        //     $msg = '库存不足';
        //
        //     return false;
        // }

        return true;
    }
    /**
     * 购物车商品可售卖验证
     * @param &$cart_objects rich 购物车商品项
     */
    private function _warning(&$cart_objects)
    {
        foreach ($cart_objects as &$object) {
            if ($object['item']['product']['marketable'] != 'true') {
                $object['warning'] = '已下架';
                $object['disabled'] = 'true'; //不能参与结算
                continue;
            }
            if ($object['item']['product']['nostore_sell'] != '1' && !vmc::singleton('b2c_goods_stock')->is_available_stock(
             $object['item']['product']['bn'],
             $object['quantity'],$abs_stock)) {
                $object['warning'] = '库存不足,当前最多可售数量:'.$abs_stock;
                $object['disabled'] = 'true'; //不能参与结算
                continue;
            }
        }
    }
}
