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


class b2c_messenger_tmpl
{
     public function last_modified($tplname)
     {
         $systmpl = app::get('b2c')->model('member_systmpl');
         $aRet = $systmpl->getList('*', array('active' => 'true', 'tmpl_name' => $tplname));
         if ($aRet) {
             return $aRet[0]['edittime'];
         }

         return time();
     }

    public function get_file_contents($tplname)
    {
        $systmpl = app::get('b2c')->model('member_systmpl');
        $aRet = $systmpl->getList('*', array('active' => 'true', 'tmpl_name' => $tplname));
        if ($aRet) {
            return $aRet[0]['content'];
        }

        return;
    }
}
