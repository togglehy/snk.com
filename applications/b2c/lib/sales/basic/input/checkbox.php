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


class b2c_sales_basic_input_checkbox {
    public $type = 'checkbox';
    public function create($aData, $table_info = array()) {
        $aData['default'] = (is_null($aData['default']) || (empty($aData['default']))) ? array() : (is_array($aData['default']) ? $aData['default'] : explode(',', $aData['default']));
        // 目前是调试 改成functions 后可以用封装好的js框架接口做 现在使用原生js
        $event_check_all = "$(this).closest('.list-group-item').find(':checkbox').attr('checked',!!$(this).attr('checked'))";
        $html = '&nbsp;<label class="label-control"><input type="checkbox" onchange="' . $event_check_all . '"/>全选</label>';
        if (is_array($aData['options'])) {
            foreach ($aData['options'] as $key => $row) {
                $html.= '&nbsp;<label class="label-control"><input type="checkbox" ' . (isset($row['pid']) && $row['pid'] != 0 ? ' pid=' . $row['pid'] : '') . ' name="' . $aData['name'] . '[]" value="' . $key . '" ' . (in_array($key, $aData['default']) ? 'checked' : '') . (isset($row['pid']) ? ' onchange="' . $this->_ecp_helper($key) . '"' : '') . '/>' . $row['name'] . '';
                $html.= '</label>';
            }
        }
        return "{$html}";
    }
    private function _ecp_helper($v) {
        return "$(this).closest('.list-group-item').find(':checkbox[pid=$v]').attr('checked',!!$(this).attr('checked'))";
    }
}
?>
