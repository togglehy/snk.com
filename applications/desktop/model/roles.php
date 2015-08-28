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


class desktop_mdl_roles extends dbeav_model {
    ##进回收站前操作
    function pre_recycle($data) {
        $falg = true;
        $obj_hasrole = app::get('desktop')->model('hasrole');
        $arr_role = array();
        foreach ($data as $val) {
            $arr_role[] = $val['role_id'];
        }
        $row = $obj_hasrole->getList('role_id', array(
            'role_id' => $arr_role
        ));
        if ($row) {
            $this->recycle_msg = '角色下存在管理员,不能删除';
            $falg = false;
        }
        return $falg;
    }
    /*从回收站恢复*/
    function pre_restore(&$data, $restore_type = 'add') {
        if (!$this->is_exists($data['role_name']) && $restore_type == 'add') {
            $data['need_delete'] = true;
            return true;
        } elseif ($this->is_exists($data['role_name'])) {
            if ($restore_type == 'add') {
                $new_name = $data['role_name'] . '_1';
                while ($this->is_exists($new_name)) {
                    $new_name = $new_name . '_1';
                }
                $data['role_name'] = $new_name;
                $data['need_delete'] = true;
                return true;
            }
            if ($restore_type == 'none') {
                return true;
            }
        } else {
            $data['need_delete'] = true;
            return true;
        }
    }
    function is_exists($role_name) {
        $row_data = $this->getRow('role_id', array(
            'role_name' => $role_name
        ));
        if ($row_data && $row_data['role_id']) return true;
        else return false;
    }
    function validate($aData, &$msg) {
        if ($aData['role_name'] == '') {
            $msg = "工作组名称不能为空";
            return false;
        }
        if (!$aData['permissions']) {
            $msg = "请至少选择一个权限";
            return false;
        }
        if (!$aData['role_id'] && $this->is_exists($aData['role_name'])) {
            $msg = "该名称已经存在";
            return false;
        }
        return true;
    }
}
?>
