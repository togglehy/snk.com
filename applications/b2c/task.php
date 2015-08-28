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



class b2c_task{


    function install_options(){
        return array();
    }

    function post_update( $dbver ){

    }

    function post_install($options)
    {
        pam_account::register_account_type('b2c','member',('前台会员系统'));
        logger::info('Init Initial');
        vmc::singleton('base_initial', 'b2c')->init();
        logger::info('Init  member attribute');
        app::get('b2c')->model('member_attr')->init();
        logger::info('Init  seo meta');
        //SEO 字段初始化
        $obj_goods = app::get('b2c')->model('goods');
        $obj_brand = app::get('b2c')->model('brand');
        $obj_goodscat = app::get('b2c')->model('goods_cat');
        $col = array(
            'seo_info' => array(
                  'type' => 'serialize',
                  'label' => app::get('b2c')->_('seo设置'),
                  'width' => 110,
                  'editable' => false,
             ),
        );
        $obj_goods->meta_register($col);
        $obj_brand->meta_register($col);
        $obj_goodscat->meta_register($col);


        //Application
        $rows = app::get('base')->model('apps')->getList('app_id',array('installed'=>1));
        foreach($rows as $r){
			if($r['app_id'] == 'base')  continue;
            $this->xml_update($r['app_id']);
		}
    }

    function post_uninstall(){
        pam_account::unregister_account_type('member');
    }

    /**
	* xml文件的更新操作
	* @param object $app app对象实例
	*/
	private function xml_update($app_id)
	{
		if (!$app_id) return;



	}



}
