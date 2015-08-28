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


class b2c_sales_goods_process extends b2c_sales_basic_prefilter
{
    protected $default_aggregator = 'b2c_sales_goods_aggregator_combine';
    public function filter($aConditions)
    {
        $oCond = vmc::singleton('b2c_sales_goods_aggregator_combine');
        $sWhere = $oCond->filter($aConditions);
        $default_where = ' goods_type="normal" AND ';
        $end_where = $default_where.(empty($sWhere) ? ' 1 = 1' : $sWhere);
        #echo $end_where;exit;
        $sale_apply_service = vmc::service('sale_apply_service');
        if (is_object($sale_apply_service) && method_exists($sale_apply_service, 'filter')) {
            $extends_where = $sale_apply_service->filter($aConditions, $end_where);

            return $extends_where;
        } else {
            return $end_where;
        }
    }
    /**
     * 清空指定rule_id的goods promotion.
     *
     * @param int $rule_id
     *
     * @return bool
     */
    public function clear($rule_id)
    {
        $rule_id = intval($rule_id);
        $sSql = 'DELETE FROM vmc_b2c_goods_promotion_ref WHERE rule_id='.$rule_id;
        if ($this->db->exec($sSql)) {
            $sSql = 'UPDATE vmc_b2c_sales_rule_goods SET apply_time = 0 WHERE rule_id='.$rule_id;
            $this->db->exec($sSql);

            return true;
        }

        return false;
    }
    /**
     * 清空所有的goods promotion.
     *
     * @return bool
     */
    public function clearAll($aRes = array())
    {
        if (is_array($aRes)) {
            $rule_ids = implode(',', $aRes);
            $sql = sprintf('DELETE FROM vmc_b2c_goods_promotion_ref WHERE rule_id in (%s)', $rule_ids);
            if ($this->db->exec($sql)) {
                $sql = sprintf('UPDATE vmc_b2c_sales_rule_goods SET apply_time = 0 WHERE rule_id in (%s)', $rule_ids);

                return $this->db->exec($sql);
            }

            return false;
        } elseif (empty($aRes)) {
            //echo "all";
        } else {
            return false;
        }
        //exit;
        $sSql = 'TRUNCATE TABLE vmc_b2c_goods_promotion_ref';
        if ($this->db->exec($sSql)) {
            $sSql = 'UPDATE vmc_b2c_sales_rule_goods SET apply_time = 0 WHERE 1';
            $this->db->exec($sSql);

            return true;
        }

        return false;
    }
    /**
     * 预处理一条goods promotion.
     *
     * @param int $rule_id
     *
     * @return bool
     */
    public function apply($rule_id, &$msg)
    {
        if (empty($rule_id) && !is_int($rule_id)) {
            return false;
        }
        $sSql = "SELECT * FROM vmc_b2c_sales_rule_goods WHERE  rule_id='".intval($rule_id)."'";
        $aResult = $this->db->selectrow($sSql);
        if (empty($aResult)) {
            $msg = '促销应用失败!';
            return false;
        }

        return $this->_apply($aResult, $msg);
    }
    /**
     * 预处理规则(测试用例中直接使用_apply)
     * 现在是应用于所有的商品,是否只应用disabled='false'.
     *
     * @param array $aData // format as dbscheme/goods_promotion_ref
     *
     * @return bool
     */
    private function _apply($aData, &$msg)
    {
        $conditions = is_array($aData['conditions']) ? $aData['conditions'] : @unserialize($aData['conditions']);
        if (empty($conditions)) {
            return false;
        }
        // name可能会要 `name`, '".$aData['name']."',
        $apply_sql = 'INSERT INTO vmc_b2c_goods_promotion_ref(
                   `goods_id`,`rule_id`,`description`,`from_time`,`to_time`,`sort_order`,`stop_rules_processing`,`action_solution`,`member_lv_ids`,`status`
               )';

        $statics_col = array(
            "'".$aData['rule_id']."'",
            "'".$aData['description']."'",
            "'".$aData['from_time']."'",
            "'".$aData['to_time']."'",
            "'".$aData['sort_order']."'",
            "'".$aData['stop_rules_processing']."'",
            "'".$aData['action_solution']."'",
            "'".$aData['member_lv_ids']."'",
            "'".$aData['status']."'",
        );

        $apply_sql .= ' SELECT goods_id,'.implode(',', $statics_col).' FROM vmc_b2c_goods WHERE '.$this->filter($conditions);
        //先清空历史
        // if (!$this->clear($aData['rule_id'])) {
        //     $msg = '无法正常清除之前的相关商品促销!';
        //
        //     return false;
        // }
        logger::info('商品促销规则保存时，应用SQL：'.$apply_sql);
        if ($this->db->exec($apply_sql)) {
            $this->db->exec('UPDATE vmc_b2c_sales_rule_goods SET  apply_time = '.time()." WHERE rule_id='".$aData['rule_id']."'");

            return true;
        }
        $msg = '应用失败!';

        return false;
    }
    /**
     * 匹配本规则的商品数
     * 一个很好玩的方法 传入相关数据得到此条件可以匹配到的商品数量
     * 可以用来_apply之前查看能否匹配的数量 没有就可以不做_apply了 only notice.
     *
     * @param array $aData // format as _apply
     *
     * @return int
     */
    public function test($aData)
    {
        $conditions = is_array($aData['conditions']) ? $aData['conditions'] : @unserialize($aData['conditions']);
        // todo 如果条件为空返回 0  如果可以设置为空(全场商品应用的规则) 再进行修改(可以不用filter处理了)
        if (empty($conditions)) {
            return 0;
        }
        #echo $this->filter($conditions);exit;
        $sSql = 'SELECT count(*) AS num FROM vmc_b2c_goods WHERE '.$this->filter($conditions);
        $aResult = $this->db->selectrow($sSql);

        return $aResult['num'];
    }
    /**
     * 获取可应用到的商品数量.
     *
     * @param int $rule_id
     *
     * @return int
     */
    public function getApplyNum($rule_id)
    {
        if (empty($rule_id) && !is_int($rule_id)) {
            return false;
        }
        // todo 通过ID取得goods promotion相关信息 mdl.sales_goods_rule.php
        $sSql = "SELECT * FROM vmc_b2c_sales_rule_goods WHERE rule_id='".$rule_id."'";
        $aResult = $this->db->selectrow($sSql); #
        if (empty($aResult)) {
            return 0;
        }

        return $this->test($aResult);
    }
    public function makeTemplate($aTemplate = array(), $aData = array(), $vpath = 'conditions', $is_auto = false)
    {
        $aTemplate['type'] = $this->default_aggregator;
        if (!isset($aTemplate['conditions'])) { // 第一次自定义的载入 如果没有conditions 也得补上一个
            $aTemplate['conditions'] = array();
        }

        return parent::makeTemplate($aTemplate, $aData, $vpath, $is_auto);
    }
    /**
     * 获取模板列表信息.
     */
    public function getTemplateList()
    {
        $aResult = array();
        foreach (vmc::servicelist('b2c_promotion_tpl_goods_apps') as $object) {
            $aResult[get_class($object) ] = array(
                'name' => $object->tpl_name,
                'type' => $object->type,
            );
        }

        return $aResult;
    }
    public function getTemplate($tpl_name, $aData = array())
    {
        $oTC = vmc::singleton($tpl_name);
        // todo 这里可以改成service来进行处理的 2010-05-16 13:00
        switch ($oTC->tpl_type) {
            case 'html':
                return $oTC->getConfig($aData);
            break;
            case 'config':
            case 'auto':
                $flag = ($oTC->tpl_type == 'auto');

                return $this->makeTemplate($oTC->getConfig(), $aData, 'conditions', $flag);
            break;
        }

        return false;
    }
    public function makeCondition($aData)
    {
        $oSGAC = vmc::singleton($this->default_aggregator);
        $aAttribute = $oSGAC->getAttributes();
        if (array_key_exists($aData['condition'], $aAttribute)) {
            $html = vmc::singleton($aAttribute[$aData['condition']]['object'])->view(array(
                'type' => $aAttribute[$aData['condition']]['object'],
                'attribute' => $aData['condition'],
            ), array(), $aData['path'], $aData['level'], $aData['position'], true);
        } else { // item
            $html = vmc::singleton($aData['condition'])->view(array(
                'type' => $aData['condition'],
            ), array(), $aData['path'], $aData['level'], $aData['position'], true);
        }

        return $html.$oSGAC->create_remove();
    }
    /**
     *商品促销信息保存到KV 减少前台数据库操作 by hanbingshu 12-06-15.
     */
    public function save_sale_goods_info($conditions)
    {
        if (empty($conditions)) {
            return false;
        }
        $sql = 'SELECT goods_id FROM vmc_b2c_goods WHERE '.$this->filter($conditions);
        $aGoods = $this->db->select($sql);
        if (empty($aGoods)) {
            return false;
        }
        $order = vmc::singleton('b2c_cart_prefilter_promotion_goods')->order();
        $goods_promotion_ref = $this->app->model('goods_promotion_ref');
        foreach ((array) $aGoods as $g_k => $g_v) {
            $aResult = $goods_promotion_ref->getList('*', array(
                'goods_id' => $g_v['goods_id'],
            ), 0, -1, $order);
            base_kvstore::instance('b2c_sale_goods_info')->store('b2c_sale_goods_info_'.$g_v['goods_id'], $aResult);
            unset($aResult);
        }
    }
}
