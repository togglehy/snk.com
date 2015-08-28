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


class b2c_mdl_goods_type extends dbeav_model
{
    public $has_many = array(
        'props' => 'goods_type_props:contrast',
    );

    public $subSdf = array(
        'default' => array(
            'props' => array('*',array('props_value' => array('*',null, array(0,-1,'order_by ASC'))),array(0,-1,'ordernum ASC')),
        ),
    );
    public function checkDefined()
    {
        return $this->count(array('is_def' => 'false'));
    }

    public function getDefault()
    {
        return $this->getList('*', array('is_def' => 'true'));
    }

    public function save(&$data, $mustUpdate = null, $mustInsert = false)
    {
        if ($data['props']) {
            foreach ($data['props'] as $k => $v) {
                $v['goods_p'] = $k;
                if ($v['options']) {
                    $i = 0;
                    foreach ($v['options'] as $vk => $vv) {
                        if ($v['optionIds'][$vk]) {
                            $data['props'][$k]['props_value'][$vk]['props_value_id'] = $v['optionIds'][$vk];
                        }
                        $data['props'][$k]['props_value'][$vk]['name'] = $vv;
                        $data['props'][$k]['props_value'][$vk]['alias'] = $v['optionAlias'][$vk];
                        $data['props'][$k]['props_value'][$vk]['order_by'] = $i++;
                    }
                }
                unset($data['props'][$k]['options']);
            }
        }

        return parent::save($data, $mustUpdate);
    }

    public function dump2($filter, $field = '*')
    {
        $res = parent::getList($field, $filter);
        $rs = $res[0];
        if ($rs) {
            $propsData = app::get('b2c')->model('goods_type_props')->getList('*', array('type_id' => $rs['type_id']));
            $props = array();
            foreach ((array) $propsData as $row) {
                $props_ids[] = $row['props_id'];
                $props[$row['props_id']] = $row;
            }
            if ($props_ids) {
                $props_value_data = app::get('b2c')->model('goods_type_props_value')->getList('*', array('props_id' => $props_ids));
            }
            foreach ((array) $props_value_data as $row) {
                $props[$row['props_id']]['props_value'][$row['props_value_id']] = $row;
            }
        }
        $rs['props'] = $props;

        $props = array();
        if ($rs['props']) {
            foreach ($rs['props'] as $k => $v) {
                $props[$v['goods_p']] = $v;
                if ($v['props_value']) {
                    foreach ($v['props_value'] as $vk => $vv) {
                        $props[$v['goods_p']]['options'][$vv['props_value_id']] = $vv['name'];
                        $props[$v['goods_p']]['optionAlias'][$vv['props_value_id']] = $vv['alias'];
                        $props[$v['goods_p']]['optionIds'][$vv['props_value_id']] = $vv['props_value_id'];
                    }
                }
                unset($props[$v['goods_p']]['props_value']);
            }
            unset($rs['props']);
            $rs['props'] = $props;
        }

        return $rs;
    }

    public function dump($filter, $field = '*', $subSdf = null)
    {
        if ((strpos($field, '*') !== false || strpos($field, 'props') !== false) && !isset($subSdf['props'])) {
            $subSdf = array_merge((array) $subSdf, array('props' => array('*', array('props_value' => array('*', null, array(0, -1, 'order_by ASC'))), array(0, -1, 'ordernum ASC'))));
        }
        $rs = parent::dump($filter, $field, $subSdf);
        $props = array();
        if ($rs['props']) {
            foreach ($rs['props'] as $k => $v) {
                $props[$v['goods_p']] = $v;
                if ($v['props_value']) {
                    foreach ($v['props_value'] as $vk => $vv) {
                        $props[$v['goods_p']]['options'][$vv['props_value_id']] = $vv['name'];
                        $props[$v['goods_p']]['optionAlias'][$vv['props_value_id']] = $vv['alias'];
                        $props[$v['goods_p']]['optionIds'][$vv['props_value_id']] = $vv['props_value_id'];
                    }
                }
                unset($props[$v['goods_p']]['props_value']);
            }
            unset($rs['props']);
            $rs['props'] = $props;
        }

        return $rs;
    }
    public function pre_recycle($rows)
    {
        foreach ($rows as $v) {
            $type_ids[] = $v['type_id'];
        }

        // $o_type_brand = $this->app->model('goods_cat');
        // $rows = $o_type_brand->getList('cat_id',array('type_id'=>$type_ids));
        // if( $rows ){
        //     $this->recycle_msg = ('该类型已被分类关联');
        //     return false;
        // }

        $o = $this->app->model('goods');
        $g = $o->getRow('name', array('type_id' => $type_ids), 0, 1);
        if ($g) {
            $this->recycle_msg = ('存在使用该类型的商品:'.$g['name']);

            return false;
        }

        return true;
    }

    public function pre_restore(&$data, $restore_type = 'add')
    {
        if (!($this->is_exists($data['name']))) {
            $data['need_delete'] = true;

            return true;
        } else {
            if ($restore_type == 'add') {
                $new_name = $data['name'].'_1';
                while ($this->is_exists($new_name)) {
                    $new_name = $new_name.'_1';
                }
                $data['name'] = $new_name;
                $data['need_delete'] = true;

                return true;
            }
            if ($restore_type == 'none') {
                $data['need_delete'] = false;

                return false;
            }
        }
    }

    public function is_exists($name)
    {
        $row = $this->getList('*', array('name' => $name));
        if (!$row) {
            return false;
        } else {
            return true;
        }
    }

    public function fetchSave($data, &$msg = '')
    {
        if ($data['props']) {
            foreach ($data['props'] as $key => $val) {
                $data['props'][$key]['show'] = 'on';
                $data['props'][$key]['goods_p'] = $key + 1;
                if ($val['options']) {
                    $i = 0;
                    foreach ($val['options'] as $valKey => $valVal) {
                        if ($val['optionIds'][$valKey]) {
                            $data['props'][$key]['props_value'][$valKey]['props_value_id'] = $val['optionIds'][$valKey];
                        }
                        $data['props'][$key]['props_value'][$valKey]['name'] = $valVal;
                        $data['props'][$key]['props_value'][$valKey]['alias'] = $val['optionAlias'][$valKey];
                        $data['props'][$key]['props_value'][$valKey]['order_by'] = $i++;
                    }
                }
                unset($data['props'][$key]['options']);
            }
        }
        $data['params'] = $this->params_modifier($data['params'], false);
        if ($this->db->selectrow('select * from vmc_b2c_goods_type where name='.$this->db->quote($data['name']).'')) {
            $msg = ('对不起，本类型名已存在，请重新输入。');

            return false;
        }

        if ($data['spec'] == '') {
            $data['spec'] = array();
        }

        if (parent::save($data)) {
            $this->checkDefined();

            return true;
        } else {
            return false;
        }
    }

    public function params_modifier($data, $forxml = true)
    {
        $return = array();
        if (is_array($data)) {
            if ($forxml) {
                $i = 0;
                foreach ($data as $group => $cont) {
                    $return[$i] = array('groupname' => $group);
                    if (is_array($cont)) {
                        foreach ($cont as $k => $v) {
                            $item['itemname'] = $k;
                            $item['itemalias'] = explode('|', $v);
                            $return[$i]['groupitems'][] = $item;
                        }
                    }
                    $i++;
                }
            } else {
                foreach ($data as $k => $group) {
                    $return[$group['groupname']] = array();
                    if ($group['groupitems'] && is_array($group['groupitems'])) {
                        foreach ($group['groupitems'] as $k1 => $v1) {
                            $return[$group['groupname']][$v1['itemname']] = implode('|', $v1['itemalias']);
                        }
                    }
                }
            }
        }

        return $return;
    }
}
