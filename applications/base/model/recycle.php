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


class base_mdl_recycle extends base_db_model
{
    public function save(&$data, $mustUpdate = null, $mustInsert = false)
    {
        $return = parent::save($data, $mustUpdate);
    }
    public function get_item_type()
    {
        $rows = $this->db->select('select distinct(item_type) from '.$this->table_name(1).' ');

        return $rows;
    }
}
