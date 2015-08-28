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



class base_cache_filesystem extends base_cache_filesystem_model implements base_interface_cache
{
    public $name='文件系统缓存';

    function __construct()
    {
        $workat = DATA_DIR . '/cache';
        if(!is_dir($workat))    utils::mkdir_p($workat);
        $this->workat($workat . '/filecache');
        $this->check_vary_list();
    }//End Function

    public function status(&$curBytes, &$totalBytes)
    {
        $data = parent::status($cur, $total);
        foreach($data AS $val){
            $status[$val['name']] = $val['value'];
        }
        //$status['已使用缓存'] = $cur;
        $status['可使用缓存'] = $total;
        return $status;
    }//End Function

}//End Class
