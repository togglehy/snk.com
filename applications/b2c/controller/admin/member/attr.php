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


class b2c_ctl_admin_member_attr extends desktop_controller{



    public function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
    }

    function index(){
        $attr_model = $this->app->model('member_attr');
        $tmpdate =$attr_model->getList('*',null,0,-1,array('attr_order','asc'));
        #$t_num = count($tmpdate);
        foreach($tmpdate as $key=>$val){
            if($val['attr_type'] == "select" || $val['attr_type'] == "checkbox"){
                $val['attr_option'] = unserialize($val['attr_option']);
            }
            $n_tmpdate[$key] = $val;
        }
        $this->pagedata['tree'] = $n_tmpdate;
        $this->page('admin/member/attr_map.html');
    }

    function add_page(){
        $this->display('admin/member/attr_new.html');
    }

    function add(){
        $this->begin('index.php?app=b2c&ctl=admin_member_attr&act=index');
        $data = $_POST;
        if($data['attr_option'] == ''){
            unset($data['attr_option']);
        }else{
            $data['attr_option'] = explode(',',$data['attr_option']);
            foreach ($data['attr_option'] as $key => $value) {
                $data['attr_option'][$value] = $value;
                unset($data['attr_option'][$key]);
            }
        }
        if($data['attr_id']){
            $data['attr_option'] = serialize($data['attr_option']);
        }
        
        $attr_model = $this->app->model('member_attr');
        if($this->check_column($data['attr_column'])){
            $this->end(false,('该注册项字段名已存在'));
        }
        $flag = $attr_model->save($data);
        if($flag!=''){
            $this->end(true,('保存成功！'));
        }else{
            $this->end(false,('保存失败！'));
        }
    }

    function check_column($column){
        $member = $this->app->model('members');
        $metaColumn = $member->metaColumn;
        if(in_array($column,(array)$metaColumn)){
            return true;
        }
        else{
            return false;
        }
    }

    function edit_page($attr_id){
        $attr_model = $this->app->model('member_attr');
        $data = $attr_model->dump($attr_id);
        if($data['attr_option'] && !is_array($data['attr_option'])){
            $data['attr_option'] = unserialize($data['attr_option']);
            $data['attr_option'] = implode(",",array_values($data['attr_option']));
        }
        $this->pagedata['memattr'] = $data;
        $this->page('admin/member/attr_edit.html');
    }

    // function edit(){
    //     if($data['attr_option'] == ''){
    //         unset($data['attr_option']);
    //     }else{
    //         $data['attr_option'] = explode(',',$data['attr_option']);
    //         foreach ($data['attr_option'] as $key => $value) {
    //             $data['attr_option'][$value] = $value;
    //             unset($data['attr_option'][$key]);
    //         }
    //     }
    //     $this->begin('index.php?app=b2c&ctl=admin_member_attr&act=index');
    //     $attr_model = $this->app->model('member_attr');
    //     if($attr_model->save($_POST)){
    //         $this->end(true,('编辑成功！'));
    //     }else{
    //        $this->end(false,('编辑失败！'));
    //     }
    // }

    function remove($attr_id){
        $this->begin();
        $attr_model = $this->app->model('member_attr');
        $this->end($attr_model->delete($attr_id),('删除成功'));
    }

    function show_switch($attr_id){
        $this->begin('index.php?app=b2c&ctl=admin_member_attr&act=index');
        $attr_model = $this->app->model('member_attr');
        $this->end( $attr_model->set_visibility($attr_id,true),('已启用'));
    }

    function hidden_switch($attr_id){
        $this->begin('index.php?app=b2c&ctl=admin_member_attr&act=index');
        $attr_model = $this->app->model('member_attr');
        $this->end( $attr_model->set_visibility($attr_id,false),('已禁用'));
    }

    function save_order(){
        $this->begin();
        $attr_model = $this->app->model('member_attr');
        $this->end( $attr_model->update_order($_POST['attr_order']),('排序已更新'));
    }
}
