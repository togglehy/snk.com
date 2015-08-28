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


class b2c_cart_prefilter_promotion_goods implements b2c_interface_cart_prefilter
{
    /**
     * 初始化商品促销过滤.
     *
     * @param array $aGoodsId // array(xxx,xxx,xxx);
     */
    private function _init_config($goods_id_arr, $filter = array())
    {
        if (empty($goods_id_arr)) {
            return false;
        }
        $filter['goods_id'] = $goods_id_arr;
        $current_member = vmc::singleton('b2c_cart_stage')->get_member();
        if (!$filter['member_lv']) {
            $filter['member_lv'] = $current_member['member_lv'] ? $current_member['member_lv'] : -1;
        }
        //开启的促销
        $where[] = "pref.status = 'true'";
        //一定商品范围的促销
        $where[] = 'pref.goods_id IN ('.implode(',', $filter['goods_id']).')';
        //一定会员级别的促销
        $where[] = ' (find_in_set(\''.$filter['member_lv'].'\', pref.member_lv_ids))';
        //促销时间限制
        if (!empty($filter['current_time'])) {
            $where[] = sprintf('(%s >= pref.from_time or pref.from_time=0)', $filter['current_time']);
            $where[] = sprintf('(%s <= pref.to_time or pref.to_time=0)', $filter['current_time']);
        }
        $SQL = 'SELECT pref.*,srg.name,srg.s_template
                    FROM vmc_b2c_sales_rule_goods AS srg
                    JOIN vmc_b2c_goods_promotion_ref AS pref  ON pref.rule_id = srg.rule_id
                    WHERE '.implode(' AND ', $where).'
                    ORDER BY pref.sort_order ASC,pref.ref_id DESC';
        $res = vmc::database()->select($SQL);
        if (empty($res)) {
            return false;
        }

        return utils::array_change_key($res, 'goods_id', true);
    }
    public function filter(&$cart_result, $config)
    {
        $cart_goods_items = &$cart_result['objects']['goods'];
        // 没有商品数据
        if (empty($cart_goods_items)) {
            return false;
        }
        if (empty($config['promotion']['goods'])) {
            foreach ($cart_goods_items as $cart_object) {
                $goods_id_arr[] = $cart_object['item']['product']['goods_id'];
            }
            $config = $this->_init_config(array_unique($goods_id_arr), array(
                'current_time' => time(),
            ));
        } else {
            //临时定义的促销过滤
            $config = $config['promotion']['goods'];
        }
        foreach ($cart_goods_items as &$cart_object) {
            if ($cart_object['disabled'] == 'true') {
                continue;
            } //该项被禁用
            $goods_id = $cart_object['item']['product']['goods_id'];
            $rule_config = $config[$goods_id];
            if ($rule_config) {
                $this->_dofilter($cart_result, $cart_object, $rule_config);
            }
        }
    }
    private function _dofilter(&$cart_result, &$cart_object, &$rule_config)
    {
        if (isset($rule_config['goods_id'])) {
            $rule_config[] = $rule_config;
        }
        $before_filter_price = $cart_object['item']['product']['buy_price'];
        $rule_object = array();
        foreach ($rule_config as $key => $rule) {
            $action_solution = is_array($rule['action_solution']) ? $rule['action_solution'] : unserialize($rule['action_solution']);
            if ($rule_config[$rule['s_template']] == true) {
                continue;
            }
            $tpl = $rule['s_template'].'_'.$rule['goods_id'];
            if(!isset($rule['s_template'])||empty($rule['s_template']))continue;
            $tpl_ins = vmc::singleton($rule['s_template']);
            if ($rule_object[$tpl]) {
                $action_solution = $rule_object[$tpl]['action_solution'];
                $rule = $rule_object[$tpl]['rule'];
            }
            if ($rule['stop_rules_processing'] == 'false' && $tpl_ins->stop_rule_with_same_solution) {
                $rule_object[$tpl]['action_solution'] = $action_solution;
                $rule_object[$tpl]['action_solution'] = $rule;
            }
            $this->_action($cart_object, $action_solution, $cart_object, $rule, $cart_result, $rule_config);
            //排他
            if ($rule['stop_rules_processing'] == 'true') {
                break;
            }
        }
    }
    // 执行优惠
    private function _action(&$cart_object, $action_solution, &$cart_object, $rule, &$cart_result, &$rule_config)
    {
        if (!$action_solution) {
            return false;
        }
        foreach ($action_solution as $key => $solution) {
            try {
                // 执行指定优惠方案
                $s_obj = vmc::singleton($key);
                if (method_exists($o, 'get_status')) {
                    if (!$o->get_status()) {
                        return false;
                    }
                }
                $s_obj->rule_id = $rule['rule_id'];
                if ($solution['quantity'] && $cart_object['quantity'] < $solution['quantity']) {
                    return false;//商品购买数量不满足
                }
                $s_obj->apply($cart_object, $solution, $cart_result); //引用传递
            } catch (Exception $e) { //没有相关的优惠方法
                return false; // 出现错误返回false
            }
            $this->_add_promotion_to_cart($s_obj, $key, $rule_config, $cart_object, $rule, $cart_result);

            return $key;
        }
    }
    //更新购物车价格、添加购物车促销描述
    private function _add_promotion_to_cart($s_obj, $solution_key, &$rule_config, &$cart_object, $rule, &$cart_result)
    {
        $omath = vmc::singleton('ectools_math');
        //优惠执行成功时返回解决方案适用的lib
        if ($s_obj) {
            $rule_config[$solution_key]['used'] = true; // 这个优惠执行过
            $tag = $s_obj->get_desc_tag();
            //扩展购物车数据
            $cart_result['promotions']['goods'][$cart_object['obj_ident']][] = array(
                'tag' => $tag['name'], //促销规则标签
                'name' => $rule['name'], //促销规则名称
                'desc' => $rule['description'], //促销规则描述
                'rule_id' => $rule['rule_id'], //促销规则ID
                'solution' => $s_obj->getString() , //促销规则触发方案
                'save' => $s_obj->getSave(), //节省小计

            );
        }
    }
}
