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


class b2c_view_helper
{
    public function __construct($app)
    {
        $this->app = $app;
    }
    public function function_pagers($params, &$smarty)
    {
        $c = intval($params['data']['current']);
        $t = intval($params['data']['total']);
        if (!$c) {
            $c = 1;
        }
        if (!$t) {
            $t = 1;
        }
        if ($t < 2) {
            return '';
        }
        $l = $params['data']['link'];
        if (is_array($l)) {
            $l = app::get('site')->router()->gen_url($l);
        }
        $p = $params['data']['token'];
        $html = '<ul class="pagination">';
        if ($c > 1) {
            $html .= '<li class="prev"><a href="'.str_replace($p, $c - 1, $l).'">&laquo;</a></li>';
        } else {
            $html .= '<li class="prev disabled"><a href="javascript:;">&laquo;</a></li>';
        }
        if ($c <= 6) {
            for ($i = 1;$i < $c;$i++) {
                $html .= '<li><a href="'.str_replace($p, $i, $l).'">'.$i.'</a></li>';
            }
        } else {
            $html .= '<li><a href="'.str_replace($p, 1, $l).'" >1</a></li>';
            $html .= '<li><a href="'.str_replace($p, 2, $l).'" >2</a></li>';
            $html .= '<li class="disabled"><a href="javascript:;">...</a></li>';
            $html .= '<li><a href="'.str_replace($p, $c - 2, $l).'" >'.($c - 2).'</a></li>';
            $html .= '<li><a href="'.str_replace($p, $c - 1, $l).'" >'.($c - 1).'</a></li>';
        }
        $html .= '<li class="disabled"><a href="'.str_replace($p, $c, $l).'" >'.$c.'</a></li>';
        if (($t - $c) <= 5) {
            for ($i = $c + 1;$i <= $t;$i++) {
                $html .= '<li><a href="'.str_replace($p, $i, $l).'" >'.$i.'</a></li>';
            }
        } else {
            $html .= '<li><a href="'.str_replace($p, $c + 1, $l).'" >'.($c + 1).'</a></li>';
            $html .= '<li><a href="'.str_replace($p, $c + 2, $l).'" >'.($c + 2).'</a></li>';
            $html .= '<li class="disabled"><a href="javascript:;">...</a></li>';
            $html .= '<li><a href="'.str_replace($p, $t - 1, $l).'" >'.($t - 1).'</a></li>';
            $html .= '<li><a href="'.str_replace($p, $t, $l).'" >'.$t.'</a></li>';
        }
        if ($c < $t) {
            $html .= '<li class="next"><a href="'.str_replace($p, $c + 1, $l).'">&raquo;</a></li>';
        } else {
            $html .= '<li class="next disabled"><a href="javascript:;">&raquo;</a></li>';
        }

        return $html.'</ul>';
    }

    public function function_minipagers($params, &$smarty)
    {
        if (!$params['data']['current']) {
            $params['data']['current'] = 1;
        }
        if (!$params['data']['total']) {
            $params['data']['total'] = 1;
        }
        if ($params['data']['total'] < 2) {
            return '';
        }
        $c = $params['data']['current'];
        $t = $params['data']['total'];
        $l = $params['data']['link'];
        $p = $params['data']['token'];
        if (is_array($l)) {
            $l = app::get('site')->router()->gen_url($l);
        }
        $html .= '<ul class="pagination pagination-sm">';
        if ($c > 1) {
            $html .= '<li class="prev"><a href="'.str_replace($p, $c - 1, $l).'">&laquo;</a></li>';
        } else {
            $html .= '<li class="prev disabled"><a href="javascript:;">&laquo;</a></li>';
        }
        $html .= "<li class='disabled'><a href='javascript:;'>$c/$t</a></li>";
        if ($c < $t) {
            $html .= '<li class="next"><a href="'.str_replace($p, $c + 1, $l).'">&raquo;</a></li>';
        } else {
            $html .= '<li class="next disabled"><a href="javascript:;">&raquo;</a></li>';
        }

        return $html.'</ul>';
    }
    public function modifier_paddingleft($vol, $empty, $fill)
    {
        return str_repeat($fill, $empty).$vol;
    }
    public function modifier_ship_area_id($attrs)
    {
        $regions = explode(':', $attrs);

        return $regions[2];
    }
    //订单状态相关
    public function modifier_order_pay_status($v)
    {
        return app::get('b2c')->model('orders')->trasform_status('pay_status', $v);
    }
    public function modifier_order_ship_status($v)
    {
        return app::get('b2c')->model('orders')->trasform_status('ship_status', $v);
    }
    public function modifier_order_status($v)
    {
        return app::get('b2c')->model('orders')->trasform_status('status', $v);
    }
    //积分变更原因
    public function modifier_integral_reason($v)
    {
        switch ($v) {
            case 'order':
                return '订单积分兑现';
            case 'refund':
                return '退款后积分扣减';
            case 'recharge':
                return '积分赠送\充值';
            case 'exchange':
                return '积分兑换消耗';
            case 'deduction':
                return '积分抵扣现金';
            case 'sign':
                return '签到奖励';
            case 'comment':
                return '评价\评价奖励';
            default:
                return '其他';
        }
    }

    //满意度星级
    public function modifier_star($value = '', $pad = 5)
    {
        $value = round($value);
        $str = '';
        for ($i = 0; $i < $pad; $i++) {
            if ($i < $value) {
                $str .= '★';
            } else {
                $str .= '☆';
            }
        }

        return $str;
    }

    //会员等级名称
    public function modifier_lvname($m_lv_id)
    {
        $mdl_member_lv = app::get('b2c')->model('member_lv');
        $member_lv = $mdl_member_lv->dump($m_lv_id);

        return $member_lv['name'];
    }
    //处理查询参数
    public function modifier_merge_query($query, $k, $v = false)
    {
        parse_str($query, $arr);
        $arr = array_merge($arr, array($k => $v));
        foreach ($arr as $key => $value) {
            if (!$value || $value == '') {
                unset($arr[$key]);
            }
        }

        return http_build_query($arr);
    }

    //会员头像
    public function modifier_avatar($member_id,$size){
        $size = strtolower($size);
        if(!$member_id || intval($member_id)<=0){
            return "data:image/gif;base64,R0lGODlhAQABAIAAAO/v7////yH5BAAHAP8ALAAAAAABAAEAAAICRAEAOw==";
        }
        return '%AVATAR_'.$member_id.'_S_'.$size.'_AVATAR%';
    }

}
