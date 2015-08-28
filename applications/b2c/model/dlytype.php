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


class b2c_mdl_dlytype extends dbeav_model {
    public $defaultOrder = array(
        'ordernum ASC',
    );
    var $has_many = array();
    public function save(&$data, $mustUpdate = null, $mustInsert = false) {
        if ($data['area_fee_conf'] && is_array($data['area_fee_conf'])) {
        }
        $return = parent::save($data, $mustUpdate);
        return $return;
    }
    // private function get_default_expressions($firstunit,$continueunit,$discount = '1.00') {
    //     return "{{w-0}-0.4}*{{{" . $firstunit . "-w}-0.4}+1}*fp*" . $discount . "+ {{w-" . $firstunit . "}-0.6}*[(w-" . $firstunit . ")/" . $continueunit . "]*cp*" . $discount . "";
    // }
    public function getAvailable($area_id) {

        $list = $this->getList('dt_id,dt_name,detail,has_cod,def_area_fee,area_fee_conf,setting',array('dt_status'=>'1'));//TODO 过滤无用数据
        if (!$area_id) return $list;
        $mdl_regions = app::get('ectools')->model('regions');
        $region = $mdl_regions->getRow('region_path', array(
            'region_id' => $area_id
        ));
        $root_area_id = reset(array_filter(explode(',', $region['region_path'])));

        foreach ($list as $i => $value) {
            unset($list[$i]['area_fee_conf']);
            unset($list[$i]['setting']);
            unset($list[$i]['def_area_fee']);
            $area = array();
            if ($value['setting'] == '1' || $value['def_area_fee'] == 'true') continue;
            if (!is_array($value['area_fee_conf'])) {
                $value['area_fee_conf'] = unserialize($value['area_fee_conf']);
            }
            foreach ($value['area_fee_conf'] as $k => $conf) {
                $area = array_merge($area, $conf['area']);
            }
            if (!empty($area) && !in_array($root_area_id, $area)) {
                unset($list[$i]);
            }

        }
        return $list;
    }
}
