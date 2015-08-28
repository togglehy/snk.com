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


class b2c_goods_filter extends dbeav_filter
{
    public $name = 'B2C商品筛选器';
    public function goods_filter(&$filter, &$object)
    {
        if (!$filter['goods_id']) {
            $filter['goods_id'] = array();
        }
        if (!is_array($filter['goods_id'])) {
            $filter['goods_id'] = (array) $filter['goods_id'];
        }
        if (count($filter['goods_id'])) {
            return $filter;//有goods_id filter 时，不折腾
        }
        $filter = utils::addslashes_array($filter);
        //拦截货品相关filter
        $filter = $this->fix_product_filter($filter, $object);
        //子类、扩展分类商品
        $filter = $this->_cat_merge($filter, $object);

        //关键词搜
        if ($filter['keyword']) {
            $keyword = $filter['keyword'];
            foreach ($object->app->model('goods_keywords')->getList('goods_id', array('keyword|has' => $filter['keyword'])) as $item) {
                array_push($filter['goods_id'], $item['goods_id']);
            }
            $goods_select_sql = 'SELECT goods_id FROM '.$object->table_name(1).' WHERE name LIKE "%'.$keyword.'%" OR brief LIKE "%'.$keyword.'%"';
            foreach ($object->db->select($goods_select_sql) as $item) {
                array_push($filter['goods_id'], $item['goods_id']);
            }
            unset($filter['keyword']);
            if (empty($filter['goods_id'])) {
                $filter['intro|has'] = $keyword;
            }
        }
        if ($filter['product_filter']) {
            foreach ($object->app->model('products')->getList('goods_id', $filter['product_filter']) as $item) {
                array_push($filter['goods_id'], $item['goods_id']);
            }
            unset($filter['product_filter']);
        }

        if ($filter['name']) {
            $filter['name|has'] = $filter['name'];
            unset($filter['name']);
        }

        if (!$filter['goods_type']) {
            $filter['goods_type'] = 'normal';
        }

        if (is_array($filter['goods_id'])) {
            $filter['goods_id'] = array_unique($filter['goods_id']);
        }

        foreach ($filter as $k => $v) {
            if (!isset($v) || empty($v)) {
                unset($filter[$k]);
            }
        }

        return $filter;
    }

    private function fix_product_filter($filter, $object)
    {
        $product_keys = array(
            'bn',//货号
            'barcode',//条码
            'price',//销售价
            'mktprice',//市场价
            'unit',//单位
        );
        $_product_filter = array();
        $tmpkey = '';
        foreach ($filter as $key => $value) {
            if (!$key) {
                continue;
            }
            $tmpkey = $key;
            $karr = explode('|', $tmpkey);
            if ($karr) {
                $k = array_shift($karr);
            } else {
                $k = $key;
            }
            if (in_array($k, $product_keys)) {
                $_product_filter[$key] = $value;
                unset($filter[$key]);
            }
        }

        $filter['product_filter'] = $_product_filter;

        return $filter;
    }

    private function _cat_merge($filter, $object)
    {
        //商品扩展分类
        if ($filter['cat_id']) {
            $extended_goods_id = array();
            $serialize_part = 's:'.strlen((string) $filter['cat_id']).':"'.$filter['cat_id'].'"';
            foreach ($object->lw_getList('goods_id', array('extended_cat|has' => $serialize_part)) as $row) {
                array_push($extended_goods_id, $row['goods_id']);
            }
        }

        //子类
        if ($filter['cat_id'] || $filter['cat_id'] === 0) {
            $mdl_cat = $object->app->model('goods_cat');
            $cats = $mdl_cat->getList('cat_path,cat_id', array(
                    'cat_id' => $filter['cat_id'],
                ));
            $path = '';
            if (count($cats)) {
                foreach ($cats as $v) {
                    $path .= ' cat_path LIKE \''.($v['cat_path']).$v['cat_id'].',%\' OR';
                }
            }
            if ($cat_ids = $object->db->select('SELECT cat_id FROM vmc_b2c_goods_cat WHERE '.$path.' cat_id in ('.implode((array) $filter['cat_id'], ' , ').')')) {
                $filter['cat_id'] = array_keys(utils::array_change_key($cat_ids, 'cat_id'));
            }
        }
        //当找到扩展分类时
        if (count($extended_goods_id)) {
            foreach ($object->lw_getList('goods_id', array('cat_id' => $filter['cat_id'])) as $row) {
                array_push($extended_goods_id, $row['goods_id']);
            }
            unset($filter['cat_id']);
            $filter['goods_id'] = array_merge($filter['goods_id'], $extended_goods_id);
        }

        return $filter;
    }
}
