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


 
class desktop_finder_builder_import extends desktop_finder_builder_prototype{

    function main(){
        $render = app::get('desktop')->render();
        /*
        $importType = array();
        foreach( vmc::servicelist('desktop_io') as $aio ){
            $importType[] = $aio->io_type_name;
        }
        $render->pagedata['importType'] = $importType;
         */
        if( !$render->pagedata['thisUrl'] )
            $render->pagedata['thisUrl'] = $this->url;
        echo $render->fetch('common/import.html');
    }
}
