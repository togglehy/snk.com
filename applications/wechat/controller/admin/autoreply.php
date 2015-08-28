<?php
class wechat_ctl_admin_autoreply extends desktop_controller {

    function __construct($app) {
        parent::__construct($app);
    } //End Function
    //关注自动回复信息设置

    public function index(){
        $this->finder('wechat_mdl_replyrule', array(
            'title' => '自动回复' ,
            'actions' => array(
                array(
                    'label' => '新建回复规则' ,
                    'icon'=>'fa-plus',
                    'href' => 'index.php?app=wechat&ctl=admin_autoreply&act=edit',
                ) ,
            ) ,
            'use_buildin_recycle' => true,
            'use_buildin_filter'=>true
        ));
    }

    public function edit($replyrule_id){

        $wx_paccounts = app::get('wechat')->model('bind')->getList('id,avatar,name');
        $this->pagedata['wx_paccounts'] = $wx_paccounts;
        if($replyrule_id){
            /**
             * @see replyrule model $sub_sdf、$has_one define
             */
            $this->pagedata['rule'] = app::get('wechat')->model('replyrule')->dump($replyrule_id,'*','default');

        }
        $this->page('admin/replyrule.html');
    }

    public function get_media($bind_id,$type = 'news',$page = 1){
        $access_token = vmc::singleton('wechat_stage')->get_access_token($bind_id);
        $media_list = app::get('wechat')->model('media')->getRemoteList($access_token,$type,$page,$count);
        $this->pagedata['type'] = $type;
        $this->pagedata['bind_id'] = $bind_id;
        $this->pagedata['medialist'] = $media_list;
        $this->pagedata['access_token'] = $access_token;
        $this->pagedata['pager'] = array(
            'current' => $page,
            'total' => ceil($count / 20) ,
            'link' => 'index.php?app=wechat&ctl=admin_autoreply&act=get_media&p[0]='.$bind_id.'&p[1]='.$type.'&p[2]='.time(),
            'token' => time(),
        );
        $this->display('admin/media_list.html');
    }

    //保存绑定消息
    public function save() {
        $this->begin('index.php?app=wechat&ctl=admin_autoreply&act=index');
        $data = $_POST;
        $mdl_rr = app::get('wechat')->model('replyrule');
        if(isset($data['keywords']) && trim($data['keywords'])!=''){
                $data['keywords'].=',';
        }
        if(!$data['media']['wmedia_id']){
            $data['media']['update_time'] = time();
        }
        if($data['media']['wtype'] == 'news'){
            $data['media']['wcontent']['Articles']['item']['PicUrl'] = base_storager::image_path($data['media']['wcontent']['Articles']['item']['PicUrl_image_id']);
        }
        if ($mdl_rr->save($data)) {
            $this->end(true, '保存成功！');
        } else {
            $this->end(false, '保存失败！');
        }
    }
}
