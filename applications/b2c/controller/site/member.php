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

class b2c_ctl_site_member extends b2c_frontpage
{
    public $title = '会员中心';
    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
        $this->verify_member();
        $this->action = $this->_request->get_act_name();
        $this->member = $this->get_current_member();
        $this->set_tmpl('member');
        //刷新经验值和会员等级
        //vmc::singleton('b2c_member_exp')->renew($this->member['member_id']);
    }
    /**
     * 会员中心菜单排序.
     */
    public static function sort_menu($a, $b)
    {
        if ($a['ordernum'] == $b['ordernum']) {
            return 0;
        }

        return $a['ordernum'] > $b['ordernum'] ? +1 : -1;
    }
    /**
     * 会员中心基础菜单.
     */
    private function get_menu()
    {
        $menu = array(
            array(
                'label' => '会员中心',
                'ordernum' => -1,
                'link' => array(
                    'app' => 'b2c',
                    'ctl' => 'site_member',
                    'act' => 'index',
                ),
            ) ,
            array(
                'label' => ('交易') ,
                'ordernum' => 0,
                'items' => array(
                    array(
                        'label' => ('我的订单') ,
                        'ordernum' => 0,
                        'link' => array(
                            'app' => 'b2c',
                            'ctl' => 'site_member',
                            'act' => 'orders',
                        ),
                    ) ,
                    array(
                        'label' => ('我的购物车') ,
                        'ordernum' => 10,
                        'link' => array(
                            'app' => 'b2c',
                            'ctl' => 'site_cart',
                            'act' => 'index',
                        ),
                    ) ,
                ),
            ) ,
            array(
                'label' => ('购物') ,
                'ordernum' => 10,
                'items' => array(
                    array(
                        'label' => ('我的收货地址') ,
                        'ordernum' => 0,
                        'link' => array(
                            'app' => 'b2c',
                            'ctl' => 'site_member',
                            'act' => 'receiver',
                        ),
                    ) ,
                    array(
                        'label' => ('我的收藏夹') ,
                        'ordernum' => 10,
                        'link' => array(
                            'app' => 'b2c',
                            'ctl' => 'site_member',
                            'act' => 'favorite',
                        ),
                    ) ,
                    array(
                        'label' => ('我的优惠券') ,
                        'ordernum' => 20,
                        'link' => array(
                            'app' => 'b2c',
                            'ctl' => 'site_member',
                            'act' => 'coupon',
                        ),
                    ) ,
                ),
            ) ,
            // array(
            //     'label' => ('售后') ,
            //     'ordernum' => 20,
            //     'items' => array(
            //         array(
            //             'label' => ('商品评价') ,
            //             'ordernum'=>20,
            //             'link'=>array('app'=>'b2c','ctl'=>'site_member','act'=>'index')
            //         ) ,
            //         array(
            //             'label' => ('申请售后服务') ,
            //             'ordernum'=>0,
            //             'link'=>array('app'=>'b2c','ctl'=>'site_member','act'=>'index')
            //         ) ,
            //         array(
            //             'label' => ('退换货记录') ,
            //             'ordernum'=>10,
            //             'link'=>array('app'=>'b2c','ctl'=>'site_member','act'=>'index')
            //         ) ,
            //     )
            // ) ,
            array(
                'label' => ('账户') ,
                'ordernum' => 30,
                'items' => array(
                    array(
                        'label' => ('个人信息') ,
                        'ordernum' => 0,
                        'link' => array(
                            'app' => 'b2c',
                            'ctl' => 'site_member',
                            'act' => 'setting',
                        ),
                    ) ,
                    array(
                        'label' => ('安全中心') ,
                        'ordernum' => 10,
                        'link' => array(
                            'app' => 'b2c',
                            'ctl' => 'site_member',
                            'act' => 'securitycenter',
                        ),
                    ) ,
                    array(
                        'label' => ('消息通知') ,
                        'ordernum' => 20,
                        'link' => array(
                            'app' => 'b2c',
                            'ctl' => 'site_member',
                            'act' => 'message',
                        ),
                    ) ,
                ),
            ) ,
        ); //END BASEMENU ARRAY
        foreach (vmc::servicelist('b2c.member_menu_extends') as $obj) {
            if (method_exists($obj, 'get_extends_menu')) {
                $obj->get_extends_menu($menu);
            } //引用传递
        }
        foreach ($menu as &$i) {
            usort($i['items'], array(
                'b2c_ctl_site_member',
                'sort_menu',
            ));
        }
        usort($menu, array(
            'b2c_ctl_site_member',
            'sort_menu',
        ));

        return $menu;
    }
    /**
     * 会员中心框架统一输出.
     */
    protected function output($app_id)
    {
        $app_id = $app_id?$app_id:$this->app->app_id;
        $this->pagedata['member'] = $this->member;
        $this->pagedata['menu'] = $this->get_menu();
        $this->pagedata['current_action'] = $this->action;
        $this->action_view = 'action/'.$this->action.'.html';
        if ($this->pagedata['_PAGE_']) {
            $this->pagedata['_PAGE_'] = 'site/member/'.$this->pagedata['_PAGE_'];
        } else {
            $this->pagedata['_PAGE_'] = 'site/member/'.$this->action_view;
        }
        $this->pagedata['app_id'] = $app_id;
        $this->pagedata['_MAIN_'] = 'site/member/main.html';
        $this->page('site/member/main.html');
    }
    /**
     * 会员中心首页.
     */
    public function index()
    {
        $order_count = array(
            's1' => array(
                'member_id' => $this->member['member_id'],
                'status' => 'active',
                'pay_status' => array(
                    '0',
                    '3',
                    '5',
                ),
            ) ,
            's2' => array(
                'member_id' => $this->member['member_id'],
                'status' => 'active',
                'pay_status' => array(
                    '1',
                    '2',
                ) ,
                'ship_status|notin' => array(
                    '1',
                ),
            ) ,
            's3' => array(
                'member_id' => $this->member['member_id'],
                'status' => 'active',
                'ship_status' => array(
                    '1',
                    '2',
                ),
            ) ,
            's4' => array(
                'member_id' => $this->member['member_id'],
                'status' => 'active',
                'ship_status|notin'=>array(
                    '0',
                ),
            ),
        );
        $mdl_order = $this->app->model('orders');
        foreach ($order_count as $key => $filter) {
            $count = $mdl_order->count($filter);
            if ($count && $count > 0) {
                $order_count_arr[$key] = $count;
            }
        }
        $user_obj = vmc::singleton('b2c_user_object');
        $this->pagedata['pam_data'] = $user_obj->get_pam_data('*', $this->member['member_id']);
        $this->pagedata['order_count_arr'] = $order_count_arr;
        $this->output();
    }

    /**
     * 会员头像
     */
    public function avatar($action = false){
        if($action == 'upload'){
            $redirect_here = array('app' => 'b2c','ctl' => 'site_member','act' => 'avatar');
            $mdl_image = app::get('image')->model('image');
    		$image_name = $_FILES['avatar_file']['name'];
            $ready_tmp_file = $_FILES['avatar_file']['tmp_name'];
            $bt_size = filesize($ready_tmp_file);
            $max_conf = $this->app->getConf('member_avatar_max_size').'M';
            $max_size = utils::parse_str_size($max_conf); //byte
            if($_FILES['avatar_file']['error']){
                $this->splash('error',$redirect_here,'头像上传失败!'.$_FILES['avatar_file']['error']);
            }
            if($bt_size>$max_size){
                $this->splash('error',$redirect_here,'头像文件大小不能超过'.$max_conf);
            }
            list($w, $h, $t) = getimagesize($ready_tmp_file);
            if(!in_array($t,array(1,2,3,6))){
                //1 = GIF,2 = JPG，3 = PNG,6 = BMP
                $this->splash('error',$redirect_here,'文件类型错误');
            }
            $image_id = $mdl_image->store($_FILES['avatar_file']['tmp_name'],$this->member['avatar'],null,$image_name);
            logger::info('前台会员头像上传操作'.'TMP_NAME:'.$_FILES['avatar_file']['tmp_name'].',FILE_NAME:'.$image_name);
            if(!$image_id){
                $this->splash('error',$redirect_here,'头像上传失败!');
            }
            $mdl_image->rebuild($image_id,array('S','XS'));
            if($this->app->model('members')->update(array('avatar'=>$image_id),array('member_id'=>$this->member['member_id']))){
                $this->splash('success',$redirect_here,'上传并保存成功!');
            }else{
                $this->splash('error',$redirect_here,'保存失败!');
            }

        }
        $system_max = get_cfg_var("upload_max_filesize");
        $conf_max = $this->app->getConf('member_avatar_max_size').'M';
        if(utils::parse_str_size($conf_max)>utils::parse_str_size($system_max)){
            $this->pagedata['upload_max'] = $system_max;
        }else{
            $this->pagedata['upload_max'] = $conf_max;
        }
        $this->output();
    }
    public function set_pam_uname($action=false){

        $user_obj = vmc::singleton('b2c_user_object');
        $redirect_member_index = array('app' => 'b2c','ctl' => 'site_member');
        $redirect_here = array('app' => 'b2c','ctl' => 'site_member','act' => 'set_pam_uname');
        $pam_data = $user_obj->get_pam_data('*', $this->member['member_id']);
        if($pam_data['local']){
            $this->splash('success',$redirect_member_index,'已设置用户名');
        }

        if($action == 'save'){
            $local_uname = $_POST['local_uname'];
            if(!vmc::singleton('b2c_user_passport')->set_local_uname($local_uname,$msg)){
                $this->splash('error',$redirect_here,$msg);
            }else{
                $this->splash('success',$redirect_member_index,$msg);
            }
        }
        $this->pagedata['member'] = $this->member;
        $this->pagedata['pam_data'] = $pam_data;
        $pam_data_schema = app::get('pam')->model('members')->get_schema();
        $this->pagedata['pam_type'] = $pam_data_schema['columns']['login_type']['type'];
        $this->page('site/member/set_pam_local.html');
    }

    public function set_pam_mobile($action=false){

        $user_obj = vmc::singleton('b2c_user_object');
        $redirect_member_index = array('app' => 'b2c','ctl' => 'site_member');
        $redirect_here = array('app' => 'b2c','ctl' => 'site_member','act' => 'set_pam_mobile');
        $pam_data = $user_obj->get_pam_data('*', $this->member['member_id']);
        if($pam_data['mobile']){
            $this->splash('success',$redirect_member_index,'已绑定手机');
        }

        if($action == 'save'){
            $mobile = $_POST['mobile'];
            $vcode = $_POST['vcode'];
            if(!vmc::singleton('b2c_user_passport')->set_mobile($mobile,$vcode,$msg)){
                $this->splash('error',$redirect_here,$msg);
            }else{
                $this->splash('success',$redirect_member_index,$msg);
            }
        }
        $this->pagedata['member'] = $this->member;
        $this->pagedata['pam_data'] = $pam_data;
        $pam_data_schema = app::get('pam')->model('members')->get_schema();
        $this->pagedata['pam_type'] = $pam_data_schema['columns']['login_type']['type'];
        $this->page('site/member/set_mobile.html');
    }

    public function set_pam_email($action=false){

        $user_obj = vmc::singleton('b2c_user_object');
        $redirect_member_index = array('app' => 'b2c','ctl' => 'site_member');
        $redirect_here = array('app' => 'b2c','ctl' => 'site_member','act' => 'set_pam_email');
        $pam_data = $user_obj->get_pam_data('*', $this->member['member_id']);
        if($pam_data['email']){
            $this->splash('success',$redirect_member_index,'已绑定邮箱');
        }

        if($action == 'save'){
            $email = $_POST['email'];
            $vcode = $_POST['vcode'];
            if(!vmc::singleton('b2c_user_passport')->set_email($email,$vcode,$msg)){
                $this->splash('error',$redirect_here,$msg);
            }else{
                $this->splash('success',$redirect_member_index,$msg);
            }
        }
        $this->pagedata['member'] = $this->member;
        $this->pagedata['pam_data'] = $pam_data;
        $pam_data_schema = app::get('pam')->model('members')->get_schema();
        $this->pagedata['pam_type'] = $pam_data_schema['columns']['login_type']['type'];
        $this->page('site/member/set_email.html');
    }

    public function active_pam_email($action=false){
        if($action == 'active'){
            $params = $_POST;
            if(!vmc::singleton('b2c_user_vcode')->verify($params['vcode'], $params['email'], 'reset')){
                $this->splash('error','','验证码错误！');
            }
            $mdl_pm = app::get('pam')->model('members');
            $p_m = $mdl_pm->getRow('member_id,login_type',array('login_account'=>$params['email']));
            if(empty($p_m['member_id']) || $p_m['login_type']!='email'){
                $this->splash('error','','账号异常!');
            }
            if($mdl_pm->update(array('disabled'=>'false'),array('member_id'=>$p_m['member_id'],'login_type'=>$p_m['login_type']))){
                $this->splash('success',array('app'=>'b2c','ctl'=>'site_member','act'=>'securitycenter'),$params['email'].'已成功激活!');
            }else{
                $this->splash('error','','激活异常!');
            }
        }else{
            $user_obj = vmc::singleton('b2c_user_object');
            $pam_data = $user_obj->get_pam_data('*', $this->member['member_id']);
            $this->pagedata['pam_data'] = $pam_data;
            $this->page('site/member/active_email.html');
        }

    }

    public function setting()
    {
        $user_obj = vmc::singleton('b2c_user_object');
        $pam_data = $user_obj->get_pam_data('*', $this->member['member_id']);
        $this->pagedata['pam_data'] = $pam_data;
        $attr = vmc::singleton('b2c_user_passport')->get_signup_attr($this->member['member_id']);
        $this->pagedata['attr'] = $attr;
        $this->output();
    }

    public function save_setting()
    {
        $url = $this->gen_url(array(
            'app' => 'b2c',
            'ctl' => 'site_member',
            'act' => 'setting',
        ));
        $member_model = $this->app->model('members');

        foreach ($_POST as $key => $val) {
            if (strpos($key, 'box:') !== false) {
                $aTmp = explode('box:', $key);
                $_POST[$aTmp[1]] = serialize($val);
            }
        }
        //--防止恶意修改
        $arr_colunm = array(
            'contact',
            'profile',
            'pam_account',
            'currency',
        );
        $attr = $this->app->model('member_attr')->getList('attr_column');
        foreach ($attr as $attr_colunm) {
            $colunm = $attr_colunm['attr_column'];
            $arr_colunm[] = $colunm;
        }
        foreach ($_POST as $post_key => $post_value) {
            if (!in_array($post_key, $arr_colunm)) {
                unset($_POST[$post_key]);
            }
        }
        //---end
        $_POST['member_id'] = $this->member['member_id'];
        if ($member_model->save($_POST)) {
            $this->splash('success', $url, ('保存成功'));
        } else {
            $this->splash('failed', $url, ('保存失败'));
        }
    }
    /**
     * 我的订单.
     */
    public function orders($status = 'all', $page = 1)
    {
        $limit = 10;
        $status_filter = array(
            'all' => array(
                'member_id' => $this->member['member_id'],
            ) ,
            's1' => array(
                'member_id' => $this->member['member_id'],
                'status' => 'active',
                'pay_status' => array(
                    '0',
                    '3',
                    '5',
                ),
            ) ,
            's2' => array(
                'member_id' => $this->member['member_id'],
                'status' => 'active',
                'pay_status' => array(
                    '1',
                    '2',
                ) ,
                'ship_status|notin' => array(
                    '1',
                ),
            ) ,
            's3' => array(
                'member_id' => $this->member['member_id'],
                'status' => 'active',
                'ship_status' => array(
                    '1',
                    '2',
                ),
            ) ,
            's4' => array(
                'member_id' => $this->member['member_id'],
                'status|notin' => array('dead'),
                'ship_status|notin'=>array(
                    '0',
                ),
            ),
        );
        if ($filter = $status_filter[$status]) {
        } else {
            $filter = array(
                'member_id' => $this->member['member_id'],
            );
        }
        $mdl_order = $this->app->model('orders');
        $mdl_order_items = $this->app->model('order_items');
        $order_list = $mdl_order->getList('*', $filter, ($page - 1) * $limit, $limit);
        $oids = array_keys(utils::array_change_key($order_list, 'order_id'));
        $order_items = $mdl_order_items->getList('*', array(
            'order_id' => $oids,
        ));
        $order_items_group = utils::array_change_key($order_items, 'order_id', true);
        $order_count = $mdl_order->count($filter);
        $this->pagedata['current_status'] = $status;
        $this->pagedata['status_map'] = $status_filter;
        $this->pagedata['order_list'] = $order_list;
        $this->pagedata['order_count'] = $order_count;
        $this->pagedata['order_items_group'] = $order_items_group;
        $this->pagedata['pager'] = array(
            'total' => ceil($order_count / $limit) ,
            'current' => $page,
            'link' => array(
                'app' => 'b2c',
                'ctl' => 'site_member',
                'act' => 'orders',
                'args' => array(
                    $status,
                    ($token = time()),
                ) ,
            ) ,
            'token' => $token,
        );
        $this->output();
    }

    /**
     * 我的收藏.
     */
    public function favorite($action = 'list', $gid = false)
    {
        $member_id = $this->member['member_id'];
        $member_discout = $this->member['member_discout'];
        $mdl_member_goods = app::get('b2c')->model('member_goods');
        $redirect_here = array('app' => 'b2c','ctl' => 'site_member','act' => 'favorite');

        switch ($action) {
            case 'del':
                if (!$gid) {
                    $this->splash('error', $redirect_here, '删除收藏失败!');
                } else {
                    if ($mdl_member_goods->delete(array('member_id' => $member_id, 'goods_id' => $gid,'type'=>'fav'))) {
                        $this->splash('success', $redirect_here, '删除成功!');
                    } else {
                        $this->splash('error', $redirect_here, '删除收藏失败!');
                    }
                }
                break;
            case 'add':
                if (!$mdl_member_goods->add_fav($member_id, $gid)) {
                    $this->splash('error', '', '加入收藏失败!');
                } else {
                    $this->splash('success', '', '加入收藏成功!');
                }
            default:
            $list = $mdl_member_goods->getList('*', array('member_id' => $member_id,'type'=>'fav'));
            $this->pagedata['member_lv_name'] = $this->member['levelname'];
            $this->pagedata['member_lv_discount'] = $this->member['lv_discount'];
            $this->pagedata['data'] = $list;
            $this->output();
            break;
        }
    }

    /**
     * 消息中心.
     */
    public function message($msg_id = 0,$page = 1)
    {
        $limit = 10;
        $filter = array(
            'member_id'=>$this->member['member_id'],
            'msg_type'=>'normal',
        );
        if($msg_id>0){
            $filter['msg_id'] = $msg_id;
        }
        $mdl_member_msg = $this->app->model('member_msg');
        if(isset($filter['msg_id'])){
            $msg = $mdl_member_msg->getRow('*',$filter);
            $mdl_member_msg->update(array('status'=>'received'),$filter);
            $this->pagedata['msg'] = $msg;
            return $this->display('site/member/action/message-detail.html');
        }
        $msg_list = $mdl_member_msg->getList('*', $filter, ($page - 1) * $limit, $limit);
        $msg_count = $mdl_member_msg->count($filter);
        $this->pagedata['msg_list'] = $msg_list;
        $this->pagedata['msg_count'] = $msg_count;
        $this->pagedata['pager'] = array(
            'total' => ceil($msg_count / $limit) ,
            'current' => $page,
            'link' => array(
                'app' => 'b2c',
                'ctl' => 'site_member',
                'act' => 'message',
                'args' => array(
                    $msg_id,
                    ($token = time()),
                ) ,
            ) ,
            'token' => $token,
        );
        $this->output();
    }

    /*
     *会员中心收货地址
     * */
    public function receiver($action = 'list', $addr_id = false)
    {
        $this->pagedata['action'] = $action;
        $mdl_maddr = $this->app->model('member_addrs');
        $member_id = $this->member['member_id'];
        $redirect = array('app' => 'b2c', 'ctl' => 'site_member', 'act' => 'receiver');
        switch ($action) {
            case 'set_default':
                if (!$mdl_maddr->set_default($addr_id, $member_id)) {
                    $this->splash('error', '', '设置失败');
                }
                $this->splash('success', $redirect, '设置成功');
                break;
            case 'delete':
                if (!$mdl_maddr->delete(array('member_id' => $member_id, 'addr_id' => $addr_id))) {
                    $this->splash('error', '', '删除失败');
                }
                $this->splash('success', $redirect, '删除成功');
                break;
            case 'edit':
                $this->pagedata['maddr'] = $mdl_maddr->getRow('*', array('member_id' => $member_id, 'addr_id' => $addr_id));
                $this->output();
                break;
            case 'save':
                $addr = $_POST['maddr'];
                $addr['member_id'] = $member_id;
                if (!$mdl_maddr->save($addr)) {
                    $this->splash('error', '', '保存失败');
                }
                $this->splash('success', $redirect, '保存成功');
                break;
            default:
                $this->pagedata['list'] = $mdl_maddr->getList('*', array('member_id' => $member_id));
                $this->output();
                break;
        }
    }
    /**
     *会员收货地址 购物流程快捷设置专用.
     */
    public function quick_maddr($action = 'list', $addr_id = false)
    {
        $this->pagedata['action'] = $action;
        $mdl_maddr = $this->app->model('member_addrs');
        $member_id = $this->member['member_id'];
        $redirect = array('app' => 'b2c', 'ctl' => 'site_member', 'act' => 'receiver');
        switch ($action) {
            case 'set_default':
                if (!$mdl_maddr->set_default($addr_id, $member_id)) {
                    $this->splash('error', '', '设置失败');
                }
                $this->splash('success', $redirect, '设置成功');
                break;
            case 'delete':
                if (!$mdl_maddr->delete(array('member_id' => $member_id, 'addr_id' => $addr_id))) {
                    $this->splash('error', '', '删除失败');
                }
                $this->splash('success', $redirect, '删除成功');
                break;
            case 'edit':
                $data = $mdl_maddr->getRow('*', array('member_id' => $member_id, 'addr_id' => $addr_id));
                unset($data['updatetime']);
                unset($data['day']);
                unset($data['time']);
                $this->splash('success', $redirect, $data);
                break;
            case 'save':
                $addr = $_POST['maddr'];
                $addr['member_id'] = $member_id;
                if (!$mdl_maddr->save($addr)) {
                    $this->splash('error', '', '保存失败');
                }
                $addr['area'] = vmc::singleton('base_view_helper')->modifier_region($addr['area']);
                $this->splash('success', $redirect, $addr);
                break;
        }
    }

    /**
     * 我的优惠券.
     */
    public function coupon($action = 'list')
    {
        $member_id = $this->member['member_id'];
        $this->pagedata['available_coupons'] = vmc::singleton('b2c_coupon_stage')->get_member_couponlist($member_id, $mycoupons);

        $this->pagedata['mycoupons'] = $mycoupons;
        $memc_code_arr = array();
        foreach ($mycoupons as $coupon) {
            $memc_code_arr[] = $coupon['memc_code'];
        }
        $couponlogs = $this->app->model('member_couponlog')->getList('*', array('member_id' => $member_id, 'memc_code' => $memc_code_arr));
        $this->pagedata['couponlogs'] = utils::array_change_key($couponlogs, 'memc_code');

        $this->output();
    }

    /**
     * 安全中心.
     */
    public function securitycenter()
    {
        $user_obj = vmc::singleton('b2c_user_object');
        $this->pagedata['pam_data'] = $user_obj->get_pam_data('*', $this->member['member_id']);
        $this->output();
    }

    /**
     * 我的积分概览
     */

     public function integral($page=1){
         $limit = 20;
         $mdl_mintegral = app::get('b2c')->model('member_integral');
         $filter = array(
             'member_id'=>$this->member['member_id']
         );
         $count = $mdl_mintegral->count($filter);
         $this->pagedata['integral_list'] = $mdl_mintegral->getList('*', $filter, ($page - 1) * $limit, $limit);
         $this->pagedata['pager'] = array(
             'total' => ceil($count / $limit) ,
             'current' => $page,
             'link' => array(
                 'app' => 'b2c',
                 'ctl' => 'site_member',
                 'act' => 'integral',
                 'args' => array(
                     ($token = time()),
                 ) ,
             ) ,
             'token' => $token,
         );
         $this->output();


     }

}
