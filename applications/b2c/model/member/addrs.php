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


class b2c_mdl_member_addrs extends dbeav_model
{
    public function exists($data = array())
    {
        if (!$data || !is_array($data) || !$data['member_id']) {
            return false;
        }

        return $this->count(array(
            'member_id' => $data['member_id'],
            'name' => $data['name'],
            'area' => $data['area'],
            'addr' => $data['addr'],
        ));
    }

    public function save(&$data, $mustUpdate = null, $mustInsert = false)
    {
        if ($data['member_id'] && $data['is_default'] == 'true') {
            $this->update(array('is_default' => 'false'), array('member_id' => $data['member_id']));
        }
        if (!isset($data['is_default']) || empty($data['is_default'])) {
            $data['is_default'] = 'false';
        }

        return parent::save($data, $mustUpdate = null, $mustInsert = false);
    }
    /**
     * 设置某个地址为默认收货地址.
     *
     * @param $addr_id 会员收货地址ID
     * @param $member_id 会员ID
     */
    public function set_default($addr_id, $member_id)
    {
        $sdf = array('addr_id' => $addr_id,'member_id' => $member_id,'is_default' => 'true');

        return $this->save($sdf);
    }
}
