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




abstract class base_kvstore_abstract
{

    /*
     * 生成经过处理的唯一key
     * @var string $key
     * @access public
     * @return string
     */
    public function create_key($key)
    {
        return md5(base_kvstore::kvprefix() . $this->prefix . $key);
    }//End Function

}//End Class
