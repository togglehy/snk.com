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


class b2c_goods_stage
{
    private $member_id = false;
    public function __construct($app)
    {
        $this->app = $app;
        $this->mdl_products = app::get('b2c')->model('products');
        $this->mdl_goods = app::get('b2c')->model('goods');
    }
    public function set_member($member_id)
    {
        $user_object = vmc::singleton('b2c_user_object');
        $this->member_info = $user_object->get_member_info($member_id);
        if ($this->member_info['member_id']) {
            $this->member_id = $this->member_info['member_id'];
        }
    }

    /*丰富商品基础信息*/
    public function gallery(&$goods_list)
    {
        $goods_list = utils::array_change_key($goods_list, 'goods_id');
        $gids = array_keys($goods_list);
        $mdl_products = app::get('b2c')->model('products');
        $products = $this->mdl_products->getList('*', array(
            'goods_id' => $gids,
            'marketable' => 'true',
        ), 0, -1, 'goods_id,is_default');
        $products = utils::array_change_key($products, 'goods_id', 1);
        //满意度平均值
        $goods_avg_mark = app::get('b2c')->model('goods_mark')->avg_mark($gids);

        if ($this->member_id) {
            $lv_discount = $this->member_info['lv_discount'];
            if ($lv_discount > 1) {
                $lv_discount = 1;
            }
            if ($lv_discount < 0) {
                $lv_discount = false;
            }
        }
        foreach ($products as $k => $v) {
            //$goods_list[$k] = array_filter($goods_list[$k]);
            if (is_array($v)) {
                $goods_list[$k]['product'] = $v[0];
            } else {
                $goods_list[$k]['product'] = $v;
            }
            //$goods_list[$k]['product'] = array_filter($goods_list[$k]['product']);
            $goods_list[$k]['mark_star'] = isset($goods_avg_mark[$k])?$goods_avg_mark[$k]['num']:5;
            if ($goods_list[$k]['product'] && !empty($goods_list[$k]['product']['image_id'])) {
                $goods_list[$k]['image_default_id'] = $goods_list[$k]['product']['image_id'];
            }

            if ($lv_discount) {
                //会员价
                $goods_list[$k]['product']['buy_price'] = $goods_list[$k]['product']['member_lv_price'] = $lv_discount * $goods_list[$k]['product']['price'];
            } else {
                $goods_list[$k]['product']['buy_price'] = $goods_list[$k]['product']['price'];
            }
            //列表页单品多样展示
            if(isset($goods_list[$k]['goods_setting']['list_extension']) && is_array($v)){
                array_shift($v);
                $tmp_extension = $v;
                foreach ($tmp_extension as $extension) {
                    if(in_array($extension['product_id'],$goods_list[$k]['goods_setting']['list_extension']) && $goods_list[$k]['product']['product_id']!=$extension['product_id']){
                            if ($lv_discount) {
                                //会员价
                                $extension['buy_price'] = $extension['member_lv_price'] = $lv_discount * $extension['price'];
                            } else {
                                $extension['buy_price'] = $extension['price'];
                            }
                            $goods_list[$k]['product']['list_extension'][] = $extension;
                    }
                }
            }
        }
    }

    /**
     * 获得商品及默认货品详情
     * @param $pkey string|int g+商品id 或  货品id
     * @param &$msg string 错误反馈
     */
    public function detail($pkey, &$msg)
    {
        if(!$pkey){
            $msg = '缺少参数';
            return false;
        }

        if(substr($pkey,0,1)=='g'){
            //传入了商品ID
            $data_detail = $this->mdl_goods->dump(substr($pkey,1), '*', 'default');
            foreach ($data_detail['product'] as $key => $product) {
                if($product['is_default'] == 'true'){
                    $current_product = $product;
                    break;
                }
            }
            if(!isset($current_product)){
                    $current_product = current($data_detail['product']);
            }
        }else{

            //任务传入了货品ID
            $product = $this->mdl_products->dump($pkey);

            $data_detail = $this->mdl_goods->dump($product['goods_id'], '*', 'default');
            $current_product = $data_detail['product'][$pkey];
        }

        if(!$data_detail || !$current_product){
            $msg = 'NOT FOUND';
            return false;
        }

        $current_product_sprc_desc = explode(':::', $current_product['spec_desc']);
        $spec_options = false;
        if ($data_detail['spec_desc'] && count($data_detail['spec_desc']) > 0) {
            foreach ($data_detail['spec_desc']['v'] as $key => $value) {
                unset($data_detail['spec_desc']['v'][$key]);
                foreach (explode(',', $value) as $value) {
                    $data_detail['spec_desc']['v'][$key][$value] = array(
                        'label' => $value,
                    );
                }
            }
        }
        foreach ($data_detail['product'] as $key => $product) {
            /*规格选项计算 BEGIN*/
            $spec_desc_arr = explode(':::', $product['spec_desc']);
            $diff_spec = array_diff_assoc($spec_desc_arr, $current_product_sprc_desc);
            if (count($diff_spec) == 1) {
                $data_detail['spec_desc']['v'][key($diff_spec) ][current($diff_spec) ]['product_id'] = $product['product_id'];
                $data_detail['spec_desc']['v'][key($diff_spec) ][current($diff_spec) ]['sku_bn'] = $product['bn'];
                $data_detail['spec_desc']['v'][key($diff_spec) ][current($diff_spec) ]['marketable'] = $product['marketable'];
                if ($data_detail['goods_setting'] && $data_detail['goods_setting']['spec_info_vimage'] && $data_detail['goods_setting']['spec_info_vimage'] == $data_detail['spec_desc']['t'][key($diff_spec)]){
                    $data_detail['spec_desc']['v'][key($diff_spec) ][current($diff_spec) ]['p_image_id'] = $product['image_id'];
                }
            }
            if (count($diff_spec) == 0) {
                foreach ($current_product_sprc_desc as $key => $value) {
                    $data_detail['spec_desc']['v'][$key][$value]['product_id'] = $product['product_id'];
                    $data_detail['spec_desc']['v'][$key][$value]['sku_bn'] = $product['bn'];
                    $data_detail['spec_desc']['v'][$key][$value]['marketable'] = $product['marketable'];
                    $data_detail['spec_desc']['v'][$key][$value]['current'] = 'true';
                    if ($data_detail['goods_setting'] && $data_detail['goods_setting']['spec_info_vimage'] && $data_detail['goods_setting']['spec_info_vimage'] == $data_detail['spec_desc']['t'][$key]){
                        $data_detail['spec_desc']['v'][$key][$value]['p_image_id'] = $product['image_id'];
                    }
                }
            }
            /*规格选项计算 END*/
        }
        //只给当前货品数据
        $data_detail['product'] = $current_product;

        //默认图
        $product_image_id = $data_detail['product']['image_id'];
        if ($data_detail['product'] && $product_image_id) {
            foreach ($data_detail['images'] as $k => $i) {
                if ($i['image_id'] == $product_image_id) {
                    unset($data_detail['images'][$k]);
                }
            }
            array_unshift($data_detail['images'], array('image_id' => $product_image_id));
            $data_detail['image_default_id'] = $product_image_id;
        } else {
            $data_detail['images'] = array_values($data_detail['images']);
        }

        //会员价计算
        if ($this->member_id) {
            $lv_discount = $this->member_info['lv_discount'];

            if ($lv_discount > 1) {
                $lv_discount = 1;
            }
            if ($lv_discount < 0) {
                $lv_discount = false;
            }
            $data_detail['product']['buy_price'] = $data_detail['product']['member_lv_price'] = $lv_discount * $data_detail['product']['price'];
        } else {
            $data_detail['product']['buy_price'] = $data_detail['product']['price'];
        }

        //商品满意度星级
        $goods_avg_mark = app::get('b2c')->model('goods_mark')->avg_mark($data_detail['goods_id']);
        $data_detail['mark_star'] = isset($goods_avg_mark[$data_detail['goods_id']])?$goods_avg_mark[$data_detail['goods_id']]['num']:5;
        return $data_detail;
    }

    /**
     * 获得某商品促销当前可用促销信息
     */
    public function promotion($goods_id)
    {
        $tag_map = array(
            'b2c_promotion_solutions_addscore' => '额外送积分',
            'b2c_promotion_solutions_byfixed' => '减价',
            'b2c_promotion_solutions_bypercent' => '打折',
            'b2c_promotion_solutions_tofixed' => '特价',
            'b2c_promotion_solutions_topercent' => '打折',
            'b2c_promotion_solutions_toscore' => '积分翻倍',
        );
        $mdl_goods_promotion_ref = app::get('b2c')->model('goods_promotion_ref');
        $now = time();
        $plist = $mdl_goods_promotion_ref->getList('*', array(
            'goods_id' => $goods_id,
            'status' => 'true',
            'from_time|lthan' => $now,
            'to_time|bthan' => $now,
        ), 0, -1, 'sort_order ASC');
        $plist = utils::array_change_key($plist, 'rule_id');
        foreach ($plist as $key => $value) {
            if (!is_array($value['action_solution'])) {
                $value['action_solution'] = unserialize($value['action_solution']);
            }
            $plist[$key]['tag'] = $tag_map[key($value['action_solution']) ];
            $plist[$key]['now'] = $now;
            unset($plist[$key]['action_solution']);
            unset($plist[$key]['free_shipping']);
            unset($plist[$key]['sort_order']);
        }

        return $plist;
    }
}
