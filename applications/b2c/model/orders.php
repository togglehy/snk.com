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




class b2c_mdl_orders extends dbeav_model{
    var $has_tag = true;
    var $defaultOrder = array('createtime','DESC');
    var $has_many = array(
        'items'=>'order_items',
        'promotions'=>'order_pmt'
    );



    /**
     *
     * @params null
     * @return string 订单编号
     */
    public function apply_id()
    {
        $tb = $this->table_name(1);
        do{
            $microtime = utils::microtime();
            mt_srand($microtime);
            $i = substr(mt_rand() , -5);
            $new_order_id = (date('y')+date('m')+date('d')).date('His').$i;
            $row = $this->db->selectrow('SELECT order_id from '.$tb.' where order_id ='.$new_order_id);
        }while($row);

        return $new_order_id;
    }

    /**
     * 重载订单标准数据
     * @params array - standard data format
     * @params boolean 是否必须强制保存
     */
    public function save(&$sdf,$mustUpdate = null,$mustInsert=false)
    {
		$info_object = vmc::service('sensitive_information');
		if(is_object($info_object)) $info_object->opinfo($sdf,'b2c_mdl_orders',__FUNCTION__);
        $is_save = parent::save($sdf, $mustUpdate,$mustInsert);
        return $is_save;
    }


    /**
     * 返回订单字段的对照表
     * @params string 状态
     * @params string key value
     */
    public function trasform_status($type='status', $val)
    {
        switch($type){
            case 'status':
                $tmpArr = array(
                            'active' => ('活动'),
                            'finish' => ('完成'),
                            'dead' => ('死单'),
                );
                return $tmpArr[$val];
            break;
            case 'pay_status':
                $tmpArr = array(
                            0 => ('未付款'),
                            1 => ('已付款'),
                            2 => ('付款至担保方'),
                            3 => ('部分付款'),
                            4 => ('部分退款'),
                            5 => ('已退款'),
                );
                return $tmpArr[$val];
            break;
            case 'ship_status':
                $tmpArr = array(
                            0 => ('未发货'),
                            1 => ('已发货'),
                            2 => ('部分发货'),
                            3 => ('部分退货'),
                            4 => ('已退货'),
                );
                return $tmpArr[$val];
            break;
        }
    }




    /**
     * 重写getList方法
     */
    public function getList($cols='*', $filter=array(), $offset=0, $limit=-1, $orderType=null)
    {
        $arr_list = parent::getList($cols,$filter,$offset,$limit,$orderType);
        $obj_extends_order_service = vmc::serviceList('b2c_order_extends_actions');
        if ($obj_extends_order_service)
        {
            foreach ($obj_extends_order_service as $obj)
                $obj->extend_list($arr_list);
        }
		$info_object = vmc::service('sensitive_information');
		if(is_object($info_object)) $info_object->opinfo($arr_list,'b2c_mdl_orders',__FUNCTION__);
        return $arr_list;
    }




    public function modifier_pay_app($col)
    {
        $mdl_papp = app::get('ectools')->model('payment_applications');
        $papp = $mdl_papp->dump($col);
        return $papp['name'] ? $papp['name'] : $col;
    }

    public function modifier_member_id($row)
    {
        if ($row === 0 || $row == '0'){
            return ('非会员顾客');
        }
        else{
            return vmc::singleton('b2c_user_object')->get_member_name(null,$row);
        }
    }

    public function modifier_need_invoice($col)
    {
        $_return =  $col=='true'?'<span>是</span>':'<span class="text-muted">否</span>';
        return $_return;
    }


    function _filter($filter,$tableAlias=null,$baseWhere=null){
        if (isset($filter) && $filter && is_array($filter) && array_key_exists('member_login_name', $filter))
        {
            $obj_pam_account = app::get('pam')->model('members');
            $pam_filter = array(
                'login_account|has'=>$filter['member_login_name'],
            );
            $row_pam = $obj_pam_account->getList('*',$pam_filter);
            $arr_member_id = array();
            if ($row_pam)
            {
                foreach ($row_pam as $str_pam)
                {
                    $arr_member_id[] = $str_pam['member_id'];
                }
                $filter['member_id|in'] = $arr_member_id;
            }
			else
			{
				if ($filter['member_login_name'] == ('非会员顾客'))
					$filter['member_id'] = 0;
			}
            unset($filter['member_login_name']);
        }

        foreach(vmc::servicelist('b2c_mdl_orders.filter') as $k=>$obj_filter){
            if(method_exists($obj_filter,'extend_filter')){
                $obj_filter->extend_filter($filter);
            }
        }
		$info_object = vmc::service('sensitive_information');
		if(is_object($info_object)) $info_object->opinfo($filter,'b2c_mdl_orders',__FUNCTION__);
        $filter = parent::_filter($filter);
        return $filter;
    }

    /**
     * 重写搜索的下拉选项方法
     * @param null
     * @return null
     */
    public function searchOptions(){
        $columns = array();
        foreach($this->_columns() as $k=>$v){
            if(isset($v['searchtype']) && $v['searchtype']){
                $columns[$k] = $v['label'];
            }
        }
        /** 添加用户名搜索 **/
        $columns['member_login_name'] = ('会员用户名');
        /** end **/

        /** 添加额外的搜索列 **/
        $arr_extends_options = array();
        foreach (vmc::servicelist('b2c.order.searchOptions.addExtends') as $object)
        {
            if (!isset($object) || !is_object($object)) continue;
            if (method_exists($object, 'get_order'))
                $index = $object->get_order();
            else
                $index = 10;

            $arr_extends_options[$index] = $object;
        }
        if ($arr_extends_options)
        {
            ksort($arr_extends_options);
            foreach ($arr_extends_options as $obj)
            {
                $obj->get_extends_cols($columns);
            }
        }
        /** end **/

        return $columns;
    }

    /**
     * 订单删除之后做的事情
     * @param array post
     * @return boolean
     */
    public function suf_recycle($filter=array())
    {
        if (!$filter)
            $filter = $_GET['p'][0];

        $is_update = true;
        $obj_suf_recycles = vmc::servicelist('b2c.order.after_delete');
        if ($obj_suf_recycles)
        {
            foreach ($obj_suf_recycles as $obj_suf)
            {
                $is_update = $obj_suf->dorecycle($filter);
            }
        }

        return $is_update;
    }

    /**
     * 订单恢复之后做的事情
     * @param array post
     * @return boolean
     */
    public function suf_restore($filter=array())
    {
        $is_update = true;
        $obj_suf_restores = vmc::servicelist('b2c.order.after_restore');
        if ($obj_suf_restores)
        {
            foreach ($obj_suf_restores as $obj_suf)
            {
                $is_update = $obj_suf->dorestore($filter);
            }
        }

        return $is_update;
    }


    /**
     * 获得相邻订单
     */

     public function get_border($order_id){
         $table = $this->table_name(1);
         $sql = "(SELECT order_id FROM $table WHERE order_id<$order_id ORDER BY order_id DESC LIMIT 1) UNION (SELECT order_id FROM $table WHERE order_id>$order_id ORDER BY order_id ASC LIMIT 1) ORDER BY order_id DESC";
         $dorder = $this->db->select($sql);
         if(count($dorder)<2){
             array_unshift($dorder,array('order_id'=>null));
         }
         return $dorder;
     }


}
