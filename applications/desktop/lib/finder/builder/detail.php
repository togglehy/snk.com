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


class desktop_finder_builder_detail extends desktop_finder_builder_prototype {

    //todo 
    function main() {
        if (isset($this->detail_pages)) {
            foreach ((array)$this->detail_pages as $k => $detail_func) {
                $str_detail_order = 'detail_' . $detail_func[1] . '_order';
                if (isset($detail_func[0]->$str_detail_order) && $detail_func[0]->$str_detail_order) {
                    switch ($detail_func[0]->$str_detail_order) {
                        case COLUMN_IN_HEAD:
                            $tmp = $this->detail_pages[$k];
                            unset($this->detail_pages[$k]);
                            $this->detail_pages = array_reverse($this->detail_pages);
                            $this->detail_pages[$k] = $tmp;
                            $this->detail_pages = array_reverse($this->detail_pages);
                        break;
                        case COLUMN_IN_TAIL:
                            $tmp = $this->detail_pages[$k];
                            unset($this->detail_pages[$k]);
                            $this->detail_pages[$k] = $tmp;
                        break;
                    }
                }
            }
            foreach ($this->detail_pages as $k => $detail_func) {
                if ($_GET['finderview'] == $k) {
                    $html = $detail_func[0]->$detail_func[1]($_GET['id']);
                    echo $html;exit;
                }
            }
        }
    }
}
