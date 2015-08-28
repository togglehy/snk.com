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




class wechat_mdl_media extends dbeav_model
{

    public function getRemoteList($access_token,$type = 'news',$page = 1,&$count){
        //$media_count_action="https://api.weixin.qq.com/cgi-bin/material/get_materialcount?access_token=$access_token";
        //$media_count = $http_client->get($media_count_action);
        //$media_count = json_decode($media_count,1);
        //$count = $media_count[$type.'_count'];

        $media_action = "https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=$access_token";
        $http_client = vmc::singleton('base_httpclient');
        $media_res = $http_client->post($media_action,json_encode(array(
            'type'=>$type,
            'offset'=>($page-1)*20,
            'count'=>20
        )));
        $media_res = json_decode($media_res,1);
        $count = $media_res['total_count'];
        return $media_res['item'];
    }

}
