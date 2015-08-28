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



class b2c_mdl_member_account extends dbeav_model{

    var $name='会员';
       var $typeName = null;
      function __construct(&$app){
        $this->app = $app;
         if(!$this->typeName) $this->typeName = substr(strstr(get_class($this),'_'),1);
        #print_r($this->typeName);
    }


}
?>
