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


//FASTBUY_CART  快速购买购物车
//GUEST_CART 游客购物车
class b2c_mdl_cart_objects extends dbeav_model
{
    public function save(&$data, $mu = null, $mi = false)
    {
        $data['time'] = time(); //最新时间
        if ($data['is_fastbuy'] == 'true') { //快速购买
            if ($data['obj_type'] == 'goods') { //每次都重置快速购买临时购物车商品
                unset($_SESSION['FASTBUY_CART']);
                $_SESSION['FASTBUY_CART'][$data['member_ident']][] = $data;
            } else {
                $_SESSION['FASTBUY_CART'][$data['member_ident']][] = $data;
            }

            return true;
        } elseif ($data['member_ident'] && $data['member_id']) { //会员购物车
            return parent::save($data, $mu, $mi);
        } elseif ($data['member_ident']) { //游客购物车
            $guest_cart = $_SESSION['GUEST_CART'][$data['member_ident']];
            if (is_array($guest_cart)) {
                $add = true;
                foreach ($guest_cart as $k => $object) {
                    if ($object['obj_ident'] == $data['obj_ident']) {
                        $add = false;
                        $guest_cart[$k] = $data;
                        break;
                    }
                }
                if ($add) {
                    $guest_cart[] = $data;
                }
            } else {
                $guest_cart[] = $data;
            }

            return $_SESSION['GUEST_CART'][$data['member_ident']] = $guest_cart;
        }
    }
    public function getList($cols = '*', $filter = array(), $offset = 0, $limit = -1, $orderType = null)
    {
        if ($filter['is_fastbuy'] == 'true') { //快速购买购物车
            $fastbuy_cart = $_SESSION['FASTBUY_CART'][$filter['member_ident']];

            return $this->_filter_list($fastbuy_cart, $filter);
        } elseif ($filter['member_ident'] && $filter['member_id']) { //会员持久化购物车
            $this->carry_guest_cart($filter['member_id'], $filter['member_ident']);
            $_return = parent::getList($cols, $filter, $offset, $limit, $orderType);

            return $_return;
        } elseif ($filter['member_ident']) { //游客购物车
            $guest_cart = $_SESSION['GUEST_CART'][$filter['member_ident']];
            return $this->_filter_list($guest_cart, $filter);
        }
    }
    //同步游客购物车到会员级账户
    public function carry_guest_cart($member_id, $member_ident)
    {
        $guest_cart = $_SESSION['GUEST_CART'];
        if (empty($guest_cart)) {
            return;
        }
        foreach ($guest_cart as $cart) {
            foreach ($cart as $key => $object) {
                $object['member_ident'] = $member_ident;
                $object['member_id'] = $member_id;
                $this->save($object);
            }
        }
        unset($_SESSION['GUEST_CART']);
    }
    public function count($filter = array())
    {
        $data_arr = $this->getList('*', $filter);

        return $data_arr ? count($data_arr) : 0;
    }
    public function delete($filter, $subsdf = 'delete')
    {
        //快速购买下的购物车项删除操作
        if ($filter['is_fastbuy'] == 'true') {
            foreach ($_SESSION['FASTBUY_CART'] as $k => $value) {
                if ($k != $filter['member_ident']) {
                    continue;
                }
                foreach ($value as $j => $obj) {
                    if ($obj['obj_ident'] == $filter['obj_ident']) {
                        unset($_SESSION['FASTBUY_CART'][$k][$j]);
                    }
                }
            }

            return true;
        }
        //会员
        if ($filter['member_id'] && $filter['member_ident']) {
            parent::delete($filter);
        } elseif ($filter['member_ident']) { //游客购物车
            $cart_objects = $_SESSION['GUEST_CART'][$filter['member_ident']];
            if ($filter['obj_ident']) {
                foreach ($cart_objects as $key => $object) {
                    if ($filter['obj_ident'] == $object['obj_ident']) {
                        unset($cart_objects[$key]); //游客购物车项
                    }
                }
                $_SESSION['GUEST_CART'][$filter['member_ident']] = $cart_objects;
            } else {
                unset($_SESSION['GUEST_CART'][$filter['member_ident']]); //购物车被清空
            }
        }
    }
    //本地SESSION存储购物车过滤
    private function _filter_list($list, $filter)
    {

        $return = array();
        if ($list && is_array($list)) {
            foreach ($list as $row) {
                if ($filter['obj_type'] && $row['obj_type'] != $filter['obj_type']) {
                    continue;
                }
                if ($filter['obj_ident'] && $row['obj_ident'] != $filter['obj_ident']) {
                    continue;
                }
                if ($filter['member_ident'] && $row['member_ident'] != $filter['member_ident']) {
                    continue;
                }
                $return[] = $row;
            }
        } else {
            $return = $list;
        }

        return $return;
    }
}
