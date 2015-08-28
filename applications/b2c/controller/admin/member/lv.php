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


class b2c_ctl_admin_member_lv extends desktop_controller{



    public function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
    }

    function index(){

        $this->finder('b2c_mdl_member_lv',array(
            'title'=>('会员等级'),
            'use_buildin_recycle'=>true,
            'actions'=>array(
                         array(
                             'icon'=>'fa-plus',
                             'label'=>('添加会员等级'),
                         'href'=>'index.php?app=b2c&ctl=admin_member_lv&act=addnew'),
                        )
            ));
    }

    function addnew($member_lv_id=null){



            if($member_lv_id!=null){
                $mem_lv = $this->app->model('member_lv');
                $aLv = $mem_lv->dump($member_lv_id);
                  $aLv['default_lv_options'] = array('1'=>('是'),'0'=>('否'));
              $this->pagedata['lv'] = $aLv;
            }



            $this->page('admin/member/lv.html');
    }

    function save(){
        $end_go = vmc::router()->gen_url( array('app'=>'b2c','ctl'=>'admin_member_lv','act'=>'index') ) ;
        $this->begin($end_go);
        $objMemLv = $this->app->model('member_lv');
        if($objMemLv->validate($_POST,$msg)){
            if($_POST['member_lv_id']){
                $olddata = app::get('b2c')->model('member_lv')->dump($_POST['member_lv_id']);
            }
            #↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑记录管理员操作日志@lujy↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
            if($objMemLv->save($_POST)){
                #↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓记录管理员操作日志@lujy↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
                if($obj_operatorlogs = vmc::service('operatorlog.members')){
                    if(method_exists($obj_operatorlogs,'member_lv_log')){
                        $newdata = app::get('b2c')->model('member_lv')->dump($_POST['member_lv_id']);
                        $obj_operatorlogs->member_lv_log($newdata,$olddata);
                    }
                }
                #↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑记录管理员操作日志@lujy↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
                $this->end(true,('保存成功'));
            }else{
                $this->end(false,('保存失败'));
           }
        }else{
            $this->end(false,$msg);
        }
    }

    function setdefault($lv_id){
        $end_go = vmc::router()->gen_url( array('app'=>'b2c','ctl'=>'admin_member_lv','act'=>'index') ) ;
        $this->begin($end_go);
        $objMemLv = $this->app->model('member_lv');
        $difault_lv = $objMemLv->dump(array('default_lv'=>1),'member_lv_id');
        if($difault_lv){
            $result1 = $objMemLv->update(array('default_lv'=>0),array('member_lv_id'=>$difault_lv['member_lv_id']));
            if($result1){
                $result = $objMemLv->update(array('default_lv'=>1),array('member_lv_id'=>$lv_id));
                $msg = ('默认会员等级设置成功');
            }else{
                $msg = ('默认会员等级设置失败');
            }
        }else{
            $result = $objMemLv->update(array('default_lv'=>1),array('member_lv_id'=>$lv_id));
            $msg = ('默认会员等级设置成功');
        }
        $this->end($result,$msg);

    }

}
