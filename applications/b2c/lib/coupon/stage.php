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


class b2c_coupon_stage
{
    public function __construct()
    {
        $this->app = app::get('b2c');
    }

    /**
     * 验证某个会员是否可以使用某张优惠券.
     * 主要用于使用优惠券前的验证.以及把有效站外优惠券添加至会员优惠券账户.
     *
     * @param $member_id mixd $member_id 会员ID
     * @param $code mixd $code 优惠券号码
     * @param &$msg 错误提示
     */
    public function isAvailable($member_id, $code, &$msg = '')
    {
        $mdl_member_coupon = $this->app->model('member_coupon');
        $mdl_coupons = $this->app->model('coupons');

        if (!$member_id) {
            $msg = '未知会员身份!';
            return false;
        }

        if (!$mdl_coupons->verifyCouponCode($code)) {
            logger::warning('member_id:'.$member_id.'尝试错误优惠券号码:'.$code);
            $msg = '无效的优惠券!';
            return false;
        }
        switch ($mdl_coupons->getFlagFromCouponCode($code)) {
            case 'A':
                    $coupon_detail = $mdl_coupons->getCouponA($code);//获得A类优惠券含促销规则详情
                    if (!$coupon_detail) {
                        $msg = '无效的优惠券!';
                        return false;
                    }
                break;
            case 'B':
                    //以下为B类优惠券
                    $coupon_issue = $this->app->model('coupons_issue')->getRow('cpns_id', array('cpns_no' => $code));
                    if (!$coupon_issue) {
                        logger::warning('member_id:'.$member_id.'正在尝试使用未发行过的优惠券:'.$code);
                        $msg = ('优惠券存在异常!');
                        return false;
                    }
                    $coupon_detail = $mdl_coupons->getMemberCoupon($member_id, $code);//获得B类优惠券含促销规则详情
                    if (!$coupon_detail) {
                        //判断是否该券在其他会员账户
                        if( $mdl_member_coupon->count(array('memc_code' => $code)) ) {
                            logger::warning('member_id:'.$member_id.'正在尝试使用已被认领的优惠券:'.$code);
                            $msg = ('该优惠券已被其他会员认领!');
                            return false;
                        }
                        //应该是站外获得优惠券，进行认领操作
                        $memc = array(
                            'member_id' => $member_id,
                            'cpns_id' => $coupon_issue['cpns_id'],
                            'memc_code' => $code,
                            'memc_gen_time' => time(),
                        );
                        $mdl_member_coupon->save($memc);
                        $coupon_detail = $mdl_coupons->getMemberCoupon($member_id, $code);
                    }
                    if (!$coupon_detail) {
                        $msg = ('无效的优惠券!');
                        return false;
                    }
                break;
            default:
                //暂只开发AB型券
                $msg = ('无效的优惠券!');
                return false;
                break;
        }//end switch

        //深度验证，验证背后促销相关
        if (!$mdl_coupons->getAvailable($member_id, $coupon_detail)) {
            //$coupon_detail 被引用传递
            $msg = $coupon_detail[0]['forbidden'];

            return false;
        }
        return true;
    }
    /**
     * 获得会员优惠券列表
     * @param $member_id 会员ID
     * @param &$allcoupons 返回会员全部优惠券，不可用的将同时返回不可用提示
     * @return array 当前可用优惠券列表   array(arrray('优惠券号码'=>'详情'));
     */
    public function get_member_couponlist($member_id,&$allcoupons){
        //加载可用优惠券
        $allcoupons = $this->app->model('coupons')->getMemberCoupon($member_id);
        //$my_coupons 会在getAvailable内发生变化,如果无效,$my_coupons当条数据将会被赋予forbidden 错误提示
        $available_coupons = $this->app->model('coupons')->getAvailable($member_id,$allcoupons);
        $available_coupons = utils::array_change_key($available_coupons,'memc_code');
        return $available_coupons;
    }

    /**
     * 会员兑换以积分兑换优惠券.
     *
     * @return 消耗积分
     */
    public function exchange($cpns_id, $member_id, &$msg)
    {
        $current_time = time();
        $mdl_coupons = $this->app->model('coupons');
        $coupon = $mdl_coupons->dump($cpns_id, '*', array(':rule_order' => array('*')));
        if (!$coupon) {
            $msg = '异常优惠券兑换操作!';
            logger::warning($msg.$cpns_id.'|'.$member_id);

            return false;
        }
        if ($coupon['cpns_status'] != '1') {
            $msg = '优惠券暂不可兑换!';

            return false;
        }
        if ($coupon['cpns_type'] != '1') {
            $msg = '非B类优惠券不可兑换!';

            return false;
        }
        if ($coupon['rule_order']['status'] != 'true' || $current_time > $coupon['rule_order']['to_time'] || $current_time < $coupon['rule_order']['from_time']) {
            $msg = '优惠券促销未开启!';

            return false;
        }
        if (is_null($coupon['cpns_poin']) || $coupon['cpns_poin'] <= 0) {
            $msg = '该优惠券不可积分兑换!';

            return false;
        }
        if (!$mdl_coupons->downloadCoupon($cpns_id, 1, '1', '会员以'.$coupon['cpns_poin'].'积分兑换优惠券', $member_id)) {
            $msg = '优惠券兑换失败!';

            return fasle;
        }

        return $coupon['cpns_poin'];
    }

    /**
     * 冻结会员某张B优惠券.
     *
     * @param array params  会员ID，优惠券号码，订单号，金额，该优惠券优惠金额
     * @param 错误消息
     **/
    public function freeze_member_coupon($member_id, $memc_code, &$msg)
    {
        $coupon_detail = $this->app->model('coupons')->getMemberCoupon($member_id, $memc_code);
        $coupon_detail = $coupon_detail[0];
        if (!$coupon_detail) {
            $msg = $params['memc_code'].'不在会员账户!可能是站外获得优惠券';
            logger::info($msg.'member_id:'.$member_id);

            return false;
        }
        //冻结会员优惠券，标记memc_isvalid 为不可用
        if (!$this->app->model('member_coupon')->update(array('memc_isvalid' => 'false', 'memc_used_times' => 1),
        array('member_id' => $member_id, 'memc_code' => $memc_code))) {
            $msg = '冻结优惠券失败!';
            logger::warning($msg.'member_id:'.$member_id.',memc_code:'.$memc_code);
            return false;
        }
    }
    /**
     * 优惠券使用日志记录.
     */
    public function couponlog($params, &$msg)
    {
        if (!$params || !is_array($params) || empty($params['member_id']) || empty($params['order_id']) || empty($params['order_total']) || empty($params['coupon_save']) || empty($params['memc_code']) || empty($params['cpns_name']) || empty($params['cpns_id'])) {
            $msg = '优惠券日志记录缺少参数!';
            logger::warning($msg.$member_id.'|'.$memc_code.'|'.$order_id);

            return false;
        }
        $new_member_couponlog = array_merge($params, array('usetime' => time()));
        logger::debug('优惠券日志记录成功'.var_export($new_member_couponlog, 1));
        $this->app->model('member_couponlog')->save($new_member_couponlog);

        return true;
    }

    /**
     * 冻结会员某张优惠券.
     *
     * @param 会员ID
     * @param 优惠券号码
     * @param 在订单流程中冻结时，传入相关订单号，以操作couponlog
     * @param 错误消息
     **/
    public function unfreeze_member_coupon($member_id, $memc_code, &$msg)
    {
        $mdl_member_coupon = $this->app->model('member_coupon');
        //冻结会员优惠券，标记memc_isvalid 为不可用
        if (!$mdl_member_coupon->update(array('memc_isvalid' => 'true'),
        array('member_id' => $member_id, 'memc_code' => $memc_code))) {
            $msg = '解冻优惠券失败!';

            return false;
        }

        return true;
    }
}
