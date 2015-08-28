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


 

class site_mdl_explorers extends dbeav_model 
{

    public function pre_recycle($params) 
    {
        trigger_error("此数据不能人为删除", E_USER_ERROR);
        return false;
    }//End Function
}//End Class
