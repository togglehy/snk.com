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


class b2c_openapi_goods
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
     * 确认商品收藏情况.
     */
    public function check_fav($args = array())
    {
        $args = array_merge((array) $args, $this->req_params);
        $mdl_member_goods = app::get('b2c')->model('member_goods');
        $gid = $args['goods_id'] ? $args['goods_id'] : $req_params['goods_id'];
        $member_id = $args['member_id'] ? $args['member_id'] : $req_params['member_id'];
        $fav_check_result = $mdl_member_goods->check_fav($member_id, $gid);
        $this->_success($fav_check_result);
    }
    /**
     * 获得相关商品.
     */
    public function related($args = array())
    {
        $args = array_merge((array) $args, $this->req_params);
        $gid = $args['goods_id'];
        if (!$gid) {
            $this->_failure('缺少参数');
        }
        $mdl_goods = app::get('b2c')->model('goods');
        $req_params = vmc::singleton('base_component_request')->get_params(true);
        $rate = $mdl_goods->getLinkList($gid);
        $gids = array_keys(utils::array_change_key($rate, 'goods_id'));
        if (count($gids) < 1) {
            $this->_failure('空数据');
        }
        $glist = $mdl_goods->getList('goods_id,gid,name,type_id,cat_id,brand_id,brief,image_default_id,spec_desc', array('goods_id' => $gids));
        //数据包装
        vmc::singleton('b2c_goods_stage')->gallery($glist);
        foreach ($glist as &$item) {
            $this->_format($item);
        }
        $this->_success(array_values($glist));
    }
    /**
     * 获得商品促销规则信息.
     */
    public function promotion($args = array())
    {
        $args = array_merge((array) $args, $this->req_params);
        $goods_id = $args['goods_id'];
        if (!$goods_id) {
            $this->_failure('缺少参数');
        }
        $plist = vmc::singleton('b2c_goods_stage')->promotion($goods_id);
        $this->_success($plist);
    }
    /**
     * 不破坏缓存情况下的商品统计
     */
    public function counter($args = array())
    {
        $args = array_merge((array) $args, $this->req_params);
        $mdl_goods = app::get('b2c')->model('goods');
        $gid = $args['goods_id'];
        if (!$gid) {
            return false;
        }
        $db = vmc::database();
        $kv = base_kvstore::instance('b2c_counter');

        foreach ($args as $key => $value) {
            $value = intval($value);
            $update_sql = false;
            if ($value < 1) {
                $value = 1;
            }
            switch ($key) {
                case 'view_count':
                    $this->history($gid);
                    //UV型统计 24小时同一IP记录一次
                    $c_key = 'view_count_uv_'.$gid.'_'.base_request::get_remote_addr();

                    cacheobject::get($c_key, $time);
                    $kv->fetch('view_w_count_time', $vw_last_update);
                    if (!$time || strtotime('+1 day', $time) < time()) {

                        //获得周标记
                        if ($vw_last_update > strtotime('-1 week')) {
                            $update_sql = "UPDATE vmc_b2c_goods SET view_count=view_count+$value,view_w_count=view_w_count+$value WHERE goods_id=$gid";
                        } else {
                            $update_sql = "UPDATE vmc_b2c_goods SET view_count=view_count+$value,view_w_count=$value WHERE goods_id=$gid";
                            $kv->store('view_w_count_time', time());
                        }

                        cacheobject::set($c_key, time(), 86400 + time());
                    }
                    break;
                case 'buy_count':
                    //验证
                    if (md5($gid.'buy_count'.($value * 1024)) != $args['buy_count_sign']) {
                        break;
                    }
                    //获得周标记
                    $kv->fetch('buy_w_count_time', $bw_last_update);
                    if ($bw_last_update > strtotime('-1 week')) {
                        $update_sql = "UPDATE vmc_b2c_goods SET buy_count=buy_count+$value,buy_w_count=buy_w_count+$value WHERE goods_id=$gid";
                    } else {
                        $update_sql = "UPDATE vmc_b2c_goods SET buy_count=buy_count+$value,buy_w_count=$value WHERE goods_id=$gid";
                        $kv->store('buy_w_count_time', time());
                    }
                    break;
                case 'comment_count':
                    if (md5($gid.'comment_count'.($value * 1024)) == $args['comment_count_sign']) {
                        $update_sql = "UPDATE vmc_b2c_goods SET comment_count=comment_count+$value WHERE goods_id=$gid";
                    }
                    break;
            }

            if ($update_sql) {
                logger::info($update_sql);
                $db->exec($update_sql, true);
            }
        }
    }
    /**
     * 浏览历史.
     */
    public function history($gid = flase)
    {
        $exits_gv_history = $_COOKIE['goods_view_history'];
        $exits_gv_history = explode('G',$exits_gv_history);
        if ($gid) {
            $exits_gv_history = $exits_gv_history?$exits_gv_history:array();
            array_unshift($exits_gv_history, $gid);
            $exits_gv_history = array_unique($exits_gv_history);
            $exits_gv_history = array_slice($exits_gv_history, 0, 20);//限制20个
            return setcookie('goods_view_history', implode('G', $exits_gv_history), time() + 315360000, vmc::base_url().'/');
        }
        $glist = app::get('b2c')->model('goods')->getList('goods_id,gid,name,type_id,cat_id,brand_id,brief,image_default_id,spec_desc', array('goods_id' => $exits_gv_history));
        //数据包装
        vmc::singleton('b2c_goods_stage')->gallery($glist);
        foreach ($glist as &$item) {
            $this->_format($item);
        }
        $this->_success(array_values($glist));
    }

    /**
     * 格式化价格.
     */
    private function _format(&$item, $router_app = 'site')
    {
        foreach ($item as $key => $value) {
            $item['item_url'] = app::get($router_app)->router()->gen_url(array('app' => 'b2c', 'ctl' => 'site_product', 'args' => array($item['product']['product_id'])));
            if (is_array($value)) {
                return $this->_format($item[$key]);
            }
            switch ($key) {
                case 'image_default_id':
                    $item['image_default_url'] = base_storager::image_path($value, 'm');
                    break;
                default:
                    # code...
                    break;
            }
            foreach ($item['product'] as $j => $v) {
                switch ($j) {
                    case 'price':
                    case 'mktprice':
                    case 'buy_price':
                    $item[$j] = $item['product'][$j] = vmc::singleton('ectools_math')->formatNumber($v, app::get('ectools')->getConf('site_decimal_digit_count'), app::get('ectools')->getConf('site_decimal_digit_count'));
                    default:
                        # code...
                        break;
                }
            }
        }
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
