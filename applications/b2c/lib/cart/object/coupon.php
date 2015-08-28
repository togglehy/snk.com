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


class b2c_cart_object_coupon implements b2c_interface_cart_object
{
    private $app;
    private $member_ident; // 用户标识
    private $oCartObject;
    public function __construct()
    {
        $this->app = app::get('b2c');
        $this->session = vmc::singleton('base_session');
        $this->session->start();
        $this->member_info = vmc::singleton('b2c_cart_stage')->get_member();
        if ($this->member_info) {
            $this->member_id = $this->member_info['member_id'];
        }
        // if (!$this->member_id){
        //     // trigger_error('未知身份优惠券操作!',E_USER_ERROR);
        //     // exit;
        // }
        $this->oCartObjects = $this->app->model('cart_objects');
    }
    public function get_type()
    {
        return 'coupon';
    }
    public function get_part_type()
    {
        return array(
            'coupon',
        );
    }
    public function add_object($aData, &$msg = '', $append = true, $is_fastbuy = false)
    {
        if (!$this->_check($aData, $msg)) {
            return false;
        }
        $aSave = array(
            'obj_ident' => 'coupon_'.$aData['coupon'],
            'obj_type' => 'coupon',
            'params' => array(
                'code' => $aData['coupon'],
            ) ,
            'quantity' => 1, // 一张优惠券只能使用一次不能叠加

        );
        $aSave['member_ident'] = md5($this->session->sess_id());
        if ($this->member_id) {
            $aSave['member_ident'] = md5($this->member_id);
            $aSave['member_id'] = $this->member_id;
        }
        if ($is_fastbuy) {
            $aSave['is_fastbuy'] = 'true';
        }
        $is_save = $this->oCartObjects->save($aSave);
        if (!$is_save) {
            $msg = ('优惠券使用失败！');

            return false;
        }

        return $aSave['obj_ident'];
    }
    // 优惠券无此操作
    public function update($sIdent, $quantity, &$msg)
    {
        return false;
    }
    /**
     * 指定的购物车优惠券.
     *
     * @param string $sIdent
     * @param bool   $rich       // 是否只取cart_objects中的数据 还是完整的sdf数据
     * @param bool   $is_fastbuy 是否是立即购买过程中操作
     *
     * @return array
     */
    public function get($sIdent = null, $rich = false, $is_fastbuy = false)
    {
        if (empty($sIdent)) {
            return $this->getAll($rich, $is_fastbuy);
        }
        $filter = array(
            'obj_ident' => $sIdent,
        );
        if ($is_fastbuy) {
            $filter['is_fastbuy'] = 'true';
        }
        $filter['member_ident'] = md5($this->session->sess_id());
        if ($this->member_id) {
            $filter['member_ident'] = md5($this->member_id);
            $filter['member_id'] = $this->member_id;
        }
        $aResult = $this->oCartObjects->getList('*', $filter);
        if (empty($aResult)) {
            return array();
        }
        if ($rich) {
            $aResult = $this->_get($aResult);
            $aResult = $aResult[0];
        }

        return $aResult;
    }
    public function _get($aData)
    {
        $mdl_coupon = app::get('b2c')->model('coupons');
        foreach ($aData as $row) {
            $params = $row['params'];
            if (!is_array($params)) {
                $params = unserialize($params);
            }
            //AB类券数据取法不同 @see applications/b2c/model/coupons.php

            if(strtoupper(substr($params['code'],0,1))=='A'){
                $coupon_detail = $mdl_coupon->getCouponA($params['code']);
            }elseif(strtoupper(substr($params['code'],0,1))=='B'){
                $coupon_detail = $mdl_coupon->getMemberCoupon($this->member_id, $params['code']);
            }
            $coupon_detail = current($coupon_detail);
            $aResult[] = array(
                'obj_ident' => $row['obj_ident'],
                'obj_type' => 'coupon',
                'quantity' => 1,
                'coupon' => $params['code'],
                'params' => array(
                    'code' => $params['code'],
                    'name' => $coupon_detail['cpns_name'],
                    'cpns_id' => $coupon_detail['cpns_id'],
                    'cpns_type' => $coupon_detail['cpns_type'],
                    'rule_id' => $coupon_detail['rule_id'],
                    'rule_description' => $coupon_detail['description'],
                ),
            );
        }
        $this->_warning($aResult);

        return $aResult;
    }
    // 购物车里的所有优惠券
    public function getAll($rich = false, $is_fastbuy = false)
    {
        $filter = array(
            'obj_type' => 'coupon',
        );
        if ($is_fastbuy) {
            $filter['is_fastbuy'] = 'true';
        }
        $filter['member_ident'] = md5($this->session->sess_id());
        if ($this->member_id) {
            $filter['member_ident'] = md5($this->member_id);
            $filter['member_id'] = $this->member_id;
        }
        $aResult = $this->oCartObjects->getList('*', $filter);
        if (empty($aResult)) {
            return array();
        }
        if (!$rich) {
            return $aResult;
        }

        return $this->_get($aResult);
    }
    /**
     * 删除指定优惠券.
     *
     * @param $sIdent 优惠券在购物车里的ID
     * @param $is_fastbuy 在快速购买过程中进行优惠券删除操作
     */
    public function delete($sIdent = null, $is_fastbuy = false)
    {
        if (empty($sIdent)) {
            return $this->deleteAll();
        }
        $filter = array(
            'obj_ident' => $sIdent,
            'obj_type' => 'coupon',
        );
        if ($is_fastbuy) {
            $filter['is_fastbuy'] = 'true';
        }
        $filter['member_ident'] = md5($this->session->sess_id());
        if ($this->member_id) {
            $filter['member_ident'] = md5($this->member_id);
            $filter['member_id'] = $this->member_id;
        }

        return $this->oCartObjects->delete($filter);
    }
    // 清空购物车中优惠券数据
    public function deleteAll($is_fastbuy = false)
    {
        $filter = array(
            'obj_type' => 'coupon',
        );
        if ($is_fastbuy) {
            $filter['is_fastbuy'] = 'true';
        }
        $filter['member_ident'] = md5($this->session->sess_id());
        if ($this->member_id) {
            $filter['member_ident'] = md5($this->member_id);
            $filter['member_id'] = $this->member_id;
        }

        return $this->oCartObjects->delete($filter);
    }
    // 统计购物车中优惠券数据
    public function count(&$aData)
    {
    }
    //使用优惠券时验证
    private function _check(&$aData, &$msg)
    {
        if (empty($aData) || empty($aData['coupon'])) {
            $msg = '使用失败,请提交正确优惠券!';

            return false;
        }

        return vmc::singleton('b2c_coupon_stage')->isAvailable($this->member_id, $aData['coupon'], $msg);
    }
    //查询优惠券时验证
    private function _warning(&$coupon_arr)
    {
    }
}
