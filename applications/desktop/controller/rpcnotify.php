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



class desktop_ctl_rpcnotify extends desktop_controller{

    
    var $limit = 20;

    public function __construct(&$app)
    {
        if(defined('WITHOUT_DESKTOP_RPCNOTIFY') && constant('WITHOUT_DESKTOP_RPCNOTIFY')){
            die('通知已被禁用');
        }
        parent::__construct($app);
    }//End Function

    function index(){
        $this->finder('base_mdl_rpcnotify',array(
            'title'=>'通知',
            'actions'=>array(
                            array('label'=>'标记为已读', 'id'=>'id-rpcynotify-submit', 'submit'=>'index.php?ctl=rpcnotify&act=read'),
                        ),
            'use_buildin_recycle' => false,
            #'use_buildin_setcol' => false,
            #'use_buildin_tagedit' => false,
            ));
    }

    function get() {
        $filter = array();
        $arr = app::get('base')->model('rpcnotify')->getList( '*',$filter, 0, $this->limit );
        echo json_encode($arr);
    }



    public function read() {
        $this->begin( vmc::router()->gen_url( array('app'=>'desktop','ctl'=>'rpcnotify','act'=>'index') ) );
        $id = $_POST['id'];
        $is_selected_all = $_POST['isSelectedAll'];

        if( !$id && !$is_selected_all)
            $this->end( false, '操作失败' );
        if ($id) {
            foreach( (array)$id as $val ) {
                $data = array('status'=>'true','id'=>$val);
                $flag = app::get('base')->model('rpcnotify')->save( $data );
                if( $flag == false ) break;
            }
        }else {
            $data = array('status'=>'true');
            $filter = array();
            $flag = app::get('base')->model('rpcnotify')->update( $data, $filter );
        }
        $this->end( $flag, ($flag ? '操作成功' : '操作失败') );
    }
}
