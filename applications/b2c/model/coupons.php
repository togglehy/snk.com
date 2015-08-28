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


class b2c_mdl_coupons extends dbeav_model
{
    public $idColumn = 'cpns_id'; //表示id的列
    public $textColumn = 'cpns_name';
    public $defaultCols = 'cpns_name,cpns_prefix,pmt_time_begin,pmt_time_end,cpns_id_c,cpns_type,cpns_status,cpns_gen_quantity,cpns_point';
    public $adminCtl = 'coupons';
    public $defaultOrder = array('cpns_id','desc');
    public $tableName = 'vmc_b2c_coupons';
    public $__all_filter = array('cpns_status' => '1');

    public function modifier_cpns_point($col)
    {
        if (!$col || $col == '' || $col == null) {
            return '不可用积分兑换';
        }

        return $col;
    }

    public function modifier_cpns_type($col)
    {
        if ($col == '1') {
            return '红包<strong class="text-danger">B</strong>型';
        } else {
            return '暗号<strong class="text-success">A</strong>型';
        }
    }

    public function modifier_cpns_gen_quantity($col,$row)
    {
        if($row['cpns_type'] == '0')return '<label class="badge badge-success">1</label>';
        return $col>0?'<a href="index.php?app=b2c&ctl=admin_sales_coupon&act=issue_list&p[0]='.$row['cpns_id'].'" class="badge badge-danger">'.$col.'</a>':'未发行';
    }

    /**
     * 验证B类优惠券加密位是否正确.
     *
     * @param $aCoupon
     *
     * @return unknown_type
     */
    public function validCheckNum($aCoupon, $couponCode, $prefix = null)
    {
        $cout_len = $this->app->getConf('coupon_code_count_len');
        $encrypt_len = $this->app->getConf('coupon_code_encrypt_len');

        if (!$cout_len) {
            $cout_len = 5;
        }
        if (!$encrypt_len) {
            $encrypt_len = 5;
        }

        if (!$prefix) {
            $prefix = $this->getPrefixFromCouponCode($couponCode);
        }
        $serial_number = substr($couponCode, -$cout_len);
        $check_number = substr($couponCode, strlen($prefix), $encrypt_len);
        $new_check_number = strtoupper(substr(md5($aCoupon['cpns_key'].$serial_number.$prefix), 0, $encrypt_len));
        if ($check_number == $new_check_number) {
            return true;
        }

        return false;
    }
    /**
     * 由优惠券号去获得数据库中的优惠券信息，只取一条，这就要保证录入优惠券的时候，优惠券名不能重复.
     *
     * @param $couponCode
     *
     * @return unknown_type
     */
    public function getCouponByCouponCode($couponCode)
    {
        $couponFlag = $this->getFlagFromCouponCode($couponCode);
        $cpns_prefix = $couponCode;
        if (strtoupper($couponFlag) == 'B') {
            $cpns_prefix = $this->getPrefixFromCouponCode($couponCode);
        }

        return $this->getCouponByPrefix($cpns_prefix);
    }
    /**
     * 由前缀取数据库中的一条记录.
     *
     * @param $prefix
     *
     * @return unknown_type
     */
    public function getCouponByPrefix($prefix)
    {
        $filter = array(
            'cpns_prefix' => trim($prefix),
        );

        return $this->getList('*', $filter, 0, 1);
    }

    /**
     * 获得优惠券首字母（大写).
     */
    public function getFlagFromCouponCode($couponCode)
    {
        return strtoupper(substr($couponCode, 0, 1));
    }
    /**
     * 对B类优惠券，返回他的前缀 = 优惠券号  - 序列号（默认5位）- 加密长度（默认五位）.
     *
     * @param $couponCode
     *
     * @return unknown_type
     */
    public function getPrefixFromCouponCode($couponCode)
    {
        $cout_len = $this->app->getConf('coupon_code_count_len');
        $encrypt_len = $this->app->getConf('coupon_code_encrypt_len');
        if (!$cout_len) {
            $cout_len = 5;
        }
        if (!$encrypt_len) {
            $encrypt_len = 5;
        }
        $prefix = substr($couponCode, 0, strlen($couponCode) - ($cout_len + $encrypt_len));

        return $prefix;
    }

    /**
     * 优惠券基本验证
     * @param 优惠券号码
     *
     */
    public function verifyCouponCode($couponCode)
    {
        $couponFlag = $this->getFlagFromCouponCode($couponCode);
        if ($this->_verifyCouponType($couponFlag)) {
            switch ($couponFlag) {
            case 'A':
            case 'S':
                return true;
                break;
            case 'B':
                $prefix = $this->getPrefixFromCouponCode($couponCode);
                $aCoupon = $this->getCouponByPrefix($prefix);
                $aCoupon = $aCoupon[0];
                return $this->validCheckNum($aCoupon, $couponCode, $prefix);
            }
        } else {
            return false;
        }
    }
    public function _verifyCouponType($couponFlag)
    {
        //A：通用优惠券 B:使用一次优惠券
        $_allCouponType = array('A', 'B', 'S');

        return in_array($couponFlag, $_allCouponType);
    }

    public function _filter($filter, $tableAlias = null, $baseWhere = null)
    {
        $where = array(1);
        if ($filter['cpns_name']) {
            $where[] = 'cpns_name like\'%'.$filter['cpns_name'].'%\'';
        }

        if (is_array($filter['cpns_id'])) {
            foreach ($filter['cpns_id'] as $cpns_id) {
                if ($cpns_id != '_ANY_') {
                    $coupons[] = 'vmc_b2c_coupons.cpns_id='.intval($cpns_id);
                }
            }
            if (count($coupons) > 0) {
                $where[] = '('.implode($coupons, ' or ').')';
            }
            unset($filter['cpns_id']);
        }

        if (!empty($filter['cpns_type']) && is_string($filter['cpns_type'])) {
            $filter['cpns_type'] = explode(',', $filter['cpns_type']);
        }
        if (is_array($filter['cpns_type'])) {
            foreach ($filter['cpns_type'] as $type) {
                if ($type != '_ANY_') {
                    $cpns_type[] = 'vmc_b2c_coupons.cpns_type=\''.intval($type).'\'';
                }
            }
            if (count($cpns_type) > 0) {
                $where[] = '('.implode($cpns_type, ' or ').')';
            }
            unset($filter['cpns_type']);
        }

        if (isset($filter['ifvalid'])) {
            if ($filter['ifvalid'] == 1) {
                $curTime = time();
                $where[] = 'cpns_status=\'1\' and pmt_time_begin <= '.$curTime.' and pmt_time_end >'.$curTime;
            }
        }

        return parent::_filter($filter).' and '.implode($where, ' and ');
    }

    public function checkPrefix($prefix)
    {
        if ($this->db->select('SELECT cpns_id from vmc_b2c_coupons WHERE cpns_prefix='.$this->db->quote($prefix).' limit 1')) {
            return true;
        } else {
            return false;
        }
    }

    public function pre_restore(&$data, $restore_type = 'add')
    {
        if (!($this->is_exists($data['cpns_prefix']))) {
            $data['need_delete'] = true;

            return true;
        } else {
            if ($restore_type == 'add') {
                $cpns_prefix = $data['cpns_prefix'].'_1';
                while ($this->is_exists($cpns_prefix)) {
                    $cpns_prefix = $cpns_prefix.'_1';
                }
                $data['cpns_prefix'] = $cpns_prefix;
                $data['need_delete'] = true;

                return true;
            }
            if ($restore_type == 'none') {
                $data['need_delete'] = false;

                return false;
            }
        }
    }

    public function is_exists($cpns_prefix)
    {
        $row = $this->getList('cpns_id', array('cpns_prefix' => $cpns_prefix));
        if (!$row) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 获得指定会员优惠券列表，及优惠券相关信息，促销信息.
     *
     * @param $member_id mixd 会员ID
     * @param $coupon 取得指定某个优惠券，不填则取出全部
     *
     * @todo 当前优惠券只支持 订单级促销触发，后期考虑商品级促销触发
     */
    public function getMemberCoupon($member_id, $code = false)
    {
        $sql_where = ' WHERE member_id='.intval($member_id).' ';
        if ($code) {
            $sql_where .= " AND mc.memc_code='".$code."'";
        }

        return $this->db->select('SELECT * FROM vmc_b2c_member_coupon as mc
                                            left join vmc_b2c_coupons as c on c.cpns_id=mc.cpns_id
                                            left join vmc_b2c_sales_rule_order as r on r.rule_id=c.rule_id
                                            '.$sql_where.' ORDER BY mc.memc_gen_time DESC');
    }
    /**
     * 获得指定A类优惠券详情
     */
    public function getCouponA($code){
        $sql_where = ' WHERE cpns_prefix="'.$code.'"';
        return $this->db->select('SELECT * FROM vmc_b2c_coupons as c
                                            left join vmc_b2c_sales_rule_order as r on r.rule_id=c.rule_id
                                            '.$sql_where);
    }


    /**
     * 返回会员当前可用的优惠券列表.
     *
     * @param $member_id  会员ID
     * @param &$coupons 全部优惠券
     */
    public function getAvailable($member_id, &$coupons)
    {
        $current_time = time();//当前时间

        $coupons = $coupons ? $coupons : $this->getMemberCoupon($member_id);
        //获得会员等级最新信息
        $member_lv_info = $this->app->model('members')->get_lv_info($member_id);
        if (!$coupons) {
            return false;
        }
        $available_arr = array();
        foreach ($coupons as &$coupon) {

            //优惠券本身禁用
            if( $coupon['cpns_status'] != '1'){
                $coupon['forbidden'] = '优惠券已失效!';
                continue;
            }

            if ($current_time > $coupon['to_time'] || $coupon['status'] != 'true') {
                $coupon['forbidden'] = '该优惠券的优惠方案已失效!';
                continue;
            }
            if ($current_time < $coupon['from_time']) {
                $coupon['forbidden'] = '优惠券未到使用时间!';
                continue;
            }
            if (!in_array($member_lv_info['member_lv_id'], explode(',', $coupon['member_lv_ids']))) {
                $coupon['forbidden'] = '抱歉,'.$member_lv_info['name'].'暂无法使用该优惠券.';
                continue;
            }
            if($coupon['cpns_type']=='1'){
                //B类券的特殊验证
                if ($coupon['memc_used_times'] > 0) {
                    $coupon['forbidden'] = '优惠券已经使用,B类券只能使用一次!';
                    continue;
                }
                //B类优惠券已经冻结
                if ($coupon['memc_isvalid'] != 'true'  || $coupon['disabled'] != 'false' || $coupon['memc_enabled'] != 'true') {
                    $coupon['forbidden'] = '优惠券已失效!';
                    continue;
                }
            }
            //有效券
            $available_arr[] = $coupon;
        }
        if (count($available_arr) > 0) {
            return $available_arr;
        } else {
            return false;
        }
    }

    public function getCouponById($cpnsId)
    {
        $filter = array(
            'cpns_id' => intval($cpnsId),
        );
        $coupons = $this->getList('*', $filter, -1, -1);
        //TODO 如果没有找到相应的数据，返回值的设定，已经ctl_coupon中程序的进行怎么处理
        return $coupons[0];
    }

    /**
     * 增加coupon.
     *
     * @param $aData
     *
     * @return unknown_type
     */
    public function addCoupon($aData)
    {
        //TODO 保存的方法使用对象形式
        switch ($aData['cpns_type']) {
        case 0:
            $flag = 'A';
            break;
        case 1:
            $flag = 'B';
            break;
        case 2:
            break;
        }
        $aData['cpns_prefix'] = $flag.$aData['cpns_prefix'];
        if ($aData['cpns_id']) {
            $sSql = $this->db->getUpdateSql('vmc_b2c_coupons', $aData , 'cpns_id='.intval($aData['cpns_id']));
            return ($sSql && $this->db->exec($sSql));
        } else {
            $aData['cpns_key'] = $this->generate_key();
            $aData['cpns_gen_quantity'] = intval($aData['cpns_gen_quantity']);
            $sSql = $this->db->getInsertSql('vmc_b2c_coupons', $aData);
            if ($sSql && $this->db->exec($sSql)) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        }
    }

    /**
     * 生成加密的字符串附加到优惠券后面.
     *
     * @return unknown_type
     */
    public function generate_key()
    {
        $n = rand(4, 7);
        $str = '';
        for ($j = 0; $j < $n; ++$j) {
            $str .= chr(rand(21, 126));
        }

        return $str;
    }

    public function generateCoupon($cpnsId, $userId, $nums, $orderId = '')
    {
        //原则,只要能使用就允许生成,
        $curTime = time();
        $cpnsId = intval($cpnsId);
        $sSql = 'select * from vmc_b2c_coupons as c
            left join vmc_b2c_promotion as p on c.pmt_id=p.pmt_id
            where cpns_status=\'1\' and cpns_type=\'1\' and c.cpns_id='.$cpnsId.' and
            pmt_time_begin <= '.$curTime.' and pmt_time_end >'.$curTime;

        if ($aCoupon = $this->db->selectRow($sSql)) {
            for ($i = 1; $i <= $nums; $i++) {
                if ($couponCode = $this->_makeCouponCode($aCoupon['cpns_gen_quantity'] + $i, $aCoupon['cpns_prefix'], $aCoupon['cpns_key'])) {
                    $aData = array('memc_code' => $couponCode,
                        'cpns_id' => $cpnsId,
                        'member_id' => $userId,
                        'memc_gen_orderid' => $orderId,
                        'memc_gen_time' => time(), );
                    $sSql = $this->db->GetInsertSQL('vmc_b2c_member_coupon', $aData);
                    $this->db->exec($sSql);
                    $aData = array('cpns_gen_quantity' => $aCoupon['cpns_gen_quantity'] + $i);
                    $sSql = $this->db->GetUpdateSQL('vmc_b2c_coupons', $aData,'cpns_id='.intval($cpnsId));
                    
                    return ($sSql && $this->db->exec($sSql));

                } else {
                    return false;
                }
            }

            return true;
        } else {
            return false;
        }
    }

    public function coupon_issue($coupon_obj, $coupon_code, $remark, $issue_op)
    {
        $mdl_coupons_issue = app::get('b2c')->model('coupons_issue');
        $new_issue = array(
            'cpns_prefix' => $coupon_obj['cpns_prefix'],
            'cpns_no' => $coupon_code,
            'cpns_id' => $coupon_obj['cpns_id'],
            'remark' => $remark,
            'issue_op' => $issue_op,
        );
        $mdl_coupons_issue->save($new_issue);
    }

    /**
     * 发行优惠券.
     *
     * @param 优惠券主表ID
     * @param 发行数量
     * @param 优惠券可用状态 传‘1’
     * @param 发行备注
     * @param 发行者
     */
    public function downloadCoupon($cpnsId, $nums, $cpns_status = '1', $remark = '', $op = '')
    {
        $curTime = time();
        $aRes = array();

        if ($aCoupon = $this->getList('*', array('cpns_status' => $cpns_status, 'cpns_id' => intval($cpnsId)))) {
            $aCoupon = $aCoupon[0];
            for ($i = 1; $i <= $nums; $i++) {
                if ($couponCode = $this->_makeCouponCode($aCoupon['cpns_gen_quantity'] + $i, $aCoupon['cpns_prefix'], $aCoupon['cpns_key'])) {
                    $this->coupon_issue($aCoupon, $couponCode, $remark, $op);
                    $aRes[] = $couponCode;
                } else {
                    return false;
                }
            }
            $aData = array('cpns_gen_quantity' => $aCoupon['cpns_gen_quantity'] + $nums);
            //$rRs = $this->db->query('SELECT * FROM vmc_b2c_coupons WHERE cpns_id='.intval($cpnsId));

            $aData['cpns_gen_quantity'] = $aCoupon['cpns_gen_quantity'] + $nums;
            $aData['cpns_id'] = intval($cpnsId);

            $this->save($aData);

            return $aRes;
        } else {
            return false;
        }
    }

    public function _makeCouponCode($iNo, $prefix, $key)
    {
        $cout_len = $this->app->getConf('coupon_code_count_len');
        $encrypt_len = $this->app->getConf('coupon_code_encrypt_len');
        if (!$cout_len) {
            $cout_len = 5;
        }
        if (!$encrypt_len) {
            $encrypt_len = 5;
        }
        if ($cout_len >= strlen(strval($iNo))) {
            $iNo = str_pad($this->dec2b36($iNo), $cout_len, '0', STR_PAD_LEFT);
            $checkCode = md5($key.$iNo.$prefix);
            $checkCode = strtoupper(substr($checkCode, 0, $encrypt_len));
            $memberCoupon = $prefix.$checkCode.$iNo;

            return $memberCoupon;
        } else {
            return false;
        }
    }

    public function dec2b36($int)
    {
        $b36 = array(0 => '0',1 => '1',2 => '2',3 => '3',4 => '4',5 => '5',6 => '6',7 => '7',8 => '8',9 => '9',10 => 'A',11 => 'B',12 => 'C',13 => 'D',14 => 'E',15 => 'F',16 => 'G',17 => 'H',18 => 'I',19 => 'J',20 => 'K',21 => 'L',22 => 'M',23 => 'N',24 => 'O',25 => 'P',26 => 'Q',27 => 'R',28 => 'S',29 => 'T',30 => 'U',31 => 'V',32 => 'W',33 => 'X',34 => 'Y',35 => 'Z');
        $retstr = '';
        if ($int > 0) {
            while ($int > 0) {
                $retstr = $b36[($int % 36)].$retstr;
                $int = floor($int / 36);
            }
        } else {
            $retstr = '0';
        }

        return $retstr;
    }

    public function pre_recycle($data = array())
    {
        $oGPR = $this->app->model('sales_rule_order');
        $param = array('status' => 'false');
        if (is_array($data)) {
            $filter = array();
            foreach ($data as $key => $value) {
                if (!$value['cpns_id']) {
                    continue;
                }
                $filter['cpns_id'][] = $value['cpns_id'];
                $tmp = $this->getList('rule_id', $filter);
            }
            $this->app->model('member_coupon')->update(array('disabled' => 'true'), $filter);
            $filter = array();
            if ($tmp && is_array($tmp)) {
                $filter['rule_id'] = array_map('current', $tmp);
            }

            if ($filter['rule_id']) {
                $param = array('status' => 'false');

                return $oGPR->update($param, $filter);
            }

            return false;
        }

        return false;
    }

    public function suf_restore($sdf = 0)
    {
        $id = $sdf['cpns_id'];
        if (!$sdf) {
            return false;
        }
        $oGPR = $this->app->model('sales_rule_order');
        $param = array('status' => 'true');
        $filter = array('cpns_id' => $id);
        $this->app->model('member_coupon')->update(array('disabled' => 'false'), $filter);
        $tmp = $this->getList('rule_id', $filter);
        $filter = $tmp[0];
        $rule = $oGPR->getList('conditions', $filter);
        if (!$rule) {
            return false;
        }
        $conditions = isset($rule[0]['conditions']) ? $rule[0]['conditions'] : array();
        //同时更新sales_rule_order表里的优惠券号码
        foreach ($conditions['conditions'] as &$condition) {
            if (isset($condition['attribute']) && $condition['attribute'] == 'coupon') {
                $condition['value'] = $sdf['cpns_prefix'];
            }
        }
        $param['conditions'] = $conditions;

        return $oGPR->update($param, $filter);
    }

    public function pre_delete($id = 0)
    {
        if (!$id) {
            return false;
        }
        $o = app::get('desktop')->model('recycle');
        $rows = $o->getList('*', array('item_id' => $_POST['item_id']), 0, -1);
        $tmp = is_array($rows[0]['item_sdf']) ? $rows[0]['item_sdf'] : @unserialize($rows[0]['item_sdf']);

        $filter = array('rule_id' => $tmp['rule']['rule_id']);
        $oGPR = $this->app->model('sales_rule_order');

        return $oGPR->delete($filter);
    }

    public function suf_delete($arrId)
    {
        is_array($arrId) or $arrId = array($arrId);
        if ($arrId) {
            $sSql = 'delete from vmc_b2c_coupons where  cpns_id in ('.implode($arrId, ',').')';
            if ($this->db->exec($sSql)) {
                //$related_tables = array('vmc_b2c_member_coupon', 'vmc_b2c_pmt_gen_coupon');
                $related_tables = array('vmc_b2c_member_coupon');
                foreach ($related_tables as $table) {
                    $this->db->exec('delete from '.$table.' where  cpns_id in ('.implode($arrId, ',').')');
                }

                return true;
            } else {
                $msg = '数据删除失败！';

                return false;
            }
        } else {
            $msg = 'no select';

            return false;
        }
    }

    /**
     * 判断是否可以下载，只针对B类优惠券.
     *
     * @param $couponsCode：数据中保存的优惠券的前缀号码
     *
     * @return bool ture : 可以下载
     */
    public function isDownloadAble($couponsCode)
    {
        $couponsFlag = $this->getFlagFromCouponCode($couponsCode);
        if ($couponsFlag == 'B') {
            //TODO 以后这里要加载下载是否可以的逻辑，现在只有是B类优惠券就显示下载的按钮
            return true;
        }

        return false;
    }
    /**
     * 获得可兑换优惠券列表.
     */
    public function getlist_exchange()
    {
        $sql = "SELECT * FROM `vmc_b2c_coupons` WHERE cpns_point is not null AND cpns_status='1'";

        return $this->db->select($sql);
    }
}
