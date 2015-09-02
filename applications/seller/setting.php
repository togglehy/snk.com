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

/**
 *  getConf 默认值.
 */
$setting = array(
	'avatar_max_size' => array(
		'type' => 'number',
		'default' => get_cfg_var('upload_max_filesize') ? intval(get_cfg_var('upload_max_filesize')) : '5',
		'desc' => '头像上传大小限制(单位：MB)',
		'helpinfo'=>(get_cfg_var('upload_max_filesize') ? '<span class="text-danger">服务器当前限制'.get_cfg_var('upload_max_filesize').'</span>' : '')
	),	
);