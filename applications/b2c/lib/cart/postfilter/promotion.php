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


class b2c_cart_postfilter_promotion implements b2c_interface_cart_postfilter
{
    private $all_rules;
    private $allow_rules;
    private $used_rules;
    private $ready_normal_rules;
    private $ready_coupon_rules;
    public function __construct(&$app)
    {
        $this->app = $app;
        $this->obj_soac = vmc::singleton('b2c_sales_order_aggregator_combine');
        $this->mdl_sales_rule_order = $this->app->model('sales_rule_order');
    }
    public function filter(&$filter, &$cart_result, $config = array())
    {
        // 取出符合当前购物车条件的规则(已经使用了conditions过滤)
        $this->_filter_rules($cart_result, $config);
        $this->used_rules = array(
            'normal' => array() ,
            'coupon' => array(),
        );
        $rules_groups = array(
            'normal' => $this->ready_normal_rules,
            'coupon' => $this->ready_coupon_rules,
        );

        foreach ($rules_groups as $key => $ready_type_items) {
            foreach ($ready_type_items as $rule) {
                if ($this->used_rules[$key][$rule['rule_id']]) {
                    continue;
                }
                /**
                 * $cart_result 是引用传递，会在内部进行修改
                 */
                if (!$this->_apply_order($cart_result, $rule, $save)) {
                    continue;
                }
                $solution = $rule['action_solution'];
                if (!$solution) {
                    continue;
                }
                reset($solution);
                $solution_class = key($solution);
                if(!$solution_class){
                    logger::warning('优惠方案异常!'.var_export($rule,1));
                    continue;
                }
                $this->_add_promotion_to_cart($solution_class, $rule, $cart_result , $key);
                $this->used_rules[$key][$rule['rule_id']] = true;
            }
        }

        foreach ($cart_result['objects']['coupon'] as &$coupon) {
            /**
             * @see line 200~210
             */
            if($coupon['params']['in_use']!='true')$coupon['params']['warning'] = '优惠券未被成功使用!';
        }

    }
    /**
     * 过滤订单促销规则.
     *
     * @param array $cart_result
     */
    private function _filter_rules($cart_result, $config = array())
    {
        if (!$this->all_rules) {
            $this->_init_rules();
        }

        if (!$this->allow_rules) {
            foreach ($this->all_rules as $key => $rule) {
                try{
                    $validated = $this->obj_soac->validate($cart_result, $rule['conditions']);
                }catch(Exception $e){

                }
                if ($validated) {
                    $this->allow_rules[$rule['rule_id']] = $rule;
                }
            }
        }
        $normal_rules = array();
        $coupon_rules = array();
        foreach ($this->allow_rules as $rule) {
            if ($rule['rule_type'] == 'N') {
                $normal_rules[$rule['rule_id']] = $rule;
            } elseif ($rule['rule_type'] == 'C') {
                $coupon_rules[$rule['rule_id']] = $rule;
            }
        }

        //普通促销
        foreach ($normal_rules as $rule) {
            $this->ready_normal_rules[$rule['rule_id']] = $rule;
            if ($rule['stop_rules_processing']) {
                break;
            } //排它
        }
        //优惠券促销
        foreach ($coupon_rules as $rule) {
            $this->ready_coupon_rules[$rule['rule_id']] = $rule;//同种促销规则优惠券会合并
            if ($rule['stop_rules_processing']) {
                break;
            } //排它
        }
    }
    // 初始化订单促销规则(根据当前时间,登录用户等级 从数据库中取出订单促销规则)
    private function _init_rules()
    {
        //$this->mdl_sales_rule_order
        $current_member = vmc::singleton('b2c_cart_stage')->get_member();
        $member_lv = $current_member['member_lv'] ? $current_member['member_lv'] : -1;
        $where[] = "status = 'true'";
        $where[] = sprintf(' (find_in_set(\'%s\', member_lv_ids))', $member_lv);
        $where[] = sprintf(' (%s >= from_time or from_time=0)', time());
        $where[] = sprintf(' (%s <= to_time or to_time=0)', time());
        $SQL = 'SELECT vmc_b2c_sales_rule_order.rule_id,vmc_b2c_sales_rule_order.name,cpns_name, description, conditions, action_conditions, action_solution, stop_rules_processing, rule_type FROM `vmc_b2c_sales_rule_order` LEFT JOIN `vmc_b2c_coupons` ON `vmc_b2c_sales_rule_order`.rule_id = `vmc_b2c_coupons`.rule_id
                         WHERE '.implode(' AND ', $where).'
                         ORDER BY  sort_order ASC,  vmc_b2c_sales_rule_order.rule_id DESC';
        $this->all_rules = vmc::database()->select($SQL);
        is_array($this->all_rules) or $this->all_rules = array();
        foreach ($this->all_rules as $_k => &$rule) {
            foreach ($rule as $_k1 => &$value) {
                if (in_array($_k1, array(
                    'rule_id',
                    'description',
                    'cpns_name',
                    'rule_type',
                    'name',
                ))) {
                    continue;
                }
                if (in_array(strtolower($value), array(
                    'true',
                    'false',
                ))) {
                    $value = (strtolower($value) == 'true') ? true : false;
                    continue;
                }
                $value = is_array($value) ? $value : unserialize($value);
            }
        }

        return true;
    }
    private function _apply_order(&$cart_result, &$rule, &$save)
    {
        //优惠方案不存在直接返回

        foreach ($rule['action_solution'] as $key => &$val) {
            if ($val['used']) {
                continue;
            }
            if (!is_string($key)) {
                continue;
            }
            $o = vmc::singleton($key);
            if (method_exists($o, 'get_status')) {
                if (!$o->get_status()) {
                    return false;
                }
            }
            $o->rule_id = $rule['rule_id'];
            $cart_object = array();
            $flag = $o->apply_order($cart_object, $val, $cart_result);
            if ($flag === false) {
                return false;
            }
            $val['used'] = true;
        }

        return $key;
    }
    //更新购物车价格、添加购物车促销描述
    private function _add_promotion_to_cart($solution_class, $rule, &$cart_result ,$rule_type)
    {
        $s_obj = vmc::singleton($solution_class);
        if ($s_obj) {
            $tag = $s_obj->get_desc_tag();
            //扩展购物车数据
            $pitem =  array(
                'tag' => $tag['name'], //促销规则标签
                'name' => $rule['name'], //促销规则名称
                'desc' => $rule['description'], //促销规则描述
                'rule_id' => $rule['rule_id'], //促销规则ID
                'rule_type' =>$rule_type,
                'solution' => $s_obj->getString() , //促销规则触发方案
                'save' => $s_obj->getSave(),//节省金额
            );
            //优惠券触发订单促销，特殊处理
            if($pitem['rule_type'] == 'coupon'){
                foreach ($cart_result['objects']['coupon'] as &$coupon) {
                    if($coupon['params']['rule_id'] == $pitem['rule_id']){
                        $pitem['coupon_obj_ident'] = $coupon['obj_ident'];//为了方便前端删除
                        $pitem['coupon_code'] = $coupon['params']['code'];
                        $pitem['name'] = $coupon['params']['name'];
                        $coupon['params']['save']=$pitem['save'];
                        $coupon['params']['in_use']="true";
                        break;//找到就立即停止
                    }
                }
            }
            $cart_result['promotions']['order'][] =$pitem;
        }
    }
}
