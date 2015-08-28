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


class b2c_ctl_admin_sales_goods extends desktop_controller
{
    public function index()
    {
        $this->finder('b2c_mdl_sales_rule_goods', array(
            'title' => ('商品促销规则'),
            'use_buildin_recycle' => true,
            'actions' => array(
                array(
                    'label' => ('添加规则'),
                    'icon'=>'fa fa-plus',
                    'href' => 'index.php?app=b2c&ctl=admin_sales_goods&act=add',
                ),

            ),
        ));
    }
    /**
     * 添加新规则.
     */
    public function add()
    {
        $this->_editor();
    }
    /**
     * 修改规则.
     *
     * @param int $rule_id
     */
    public function edit($rule_id)
    {
        $oGoodsPromotion = $this->app->model('sales_rule_goods');
        $aRule = $oGoodsPromotion->dump($rule_id, '*', 'default');
        $aRule['member_lv_ids'] = empty($aRule['member_lv_ids']) ? null : explode(',', $aRule['member_lv_ids']);
        ///////////////////////////// 过滤条件 ////////////////////////////
        $aRule['conditions'] = empty($aRule['conditions']) ? null : ($aRule['conditions']);
        $html = vmc::singleton('b2c_sales_goods_process')->getTemplate($aRule['c_template'], $aRule['conditions']);
        $this->pagedata['conditions'] = $html;
        ///////////////////////////// 优惠方案 ////////////////////////////
        $aRule['action_solution'] = empty($aRule['action_solution']) ? null : ($aRule['action_solution']);
        $html = vmc::singleton('b2c_sales_solution_process')->getTemplate($aRule['s_template'], $aRule['action_solution'], 'goods');
        $this->pagedata['action_solution'] = $html;
        $this->pagedata['rule'] = $aRule;
        $this->_editor($rule_id);
    }
    /**
     * add & edit 公共部分.
     */
    private function _editor($rule_id = 0)
    {
        $time = time();

        $filter = array(
            'from_time|sthan' => $time,
            'to_time|bthan' => $time,
            'status' => 'true',
            'rule_type' => 'N',
        );
        if ($rule_id) {
            $filter['rule_id|noequal'] = $rule_id;
        }
        $arr = $this->app->model('sales_rule_goods')->getList('name,sort_order,stop_rules_processing', $filter, 0, -1, 'sort_order ASC');
        $this->pagedata['sales_list'] = $arr;
        $this->pagedata['rule']['sort_order'] = $this->pagedata['rule']['sort_order'] ? $this->pagedata['rule']['sort_order'] : ($arr[0]['sort_order'] ? $arr[0]['sort_order'] - 1 : 50);
        //end
        $this->pagedata['promotion_type'] = 'goods'; // 规则类型 用于公用模板
        //////////////////////////// 会员等级 //////////////////////////////
        $oMemberLevel = $this->app->model('member_lv');
        $this->pagedata['member_level'] = $oMemberLevel->getList('member_lv_id,name', array(), 0, -1, 'member_lv_id ASC');
        //////////////////////////// 过滤条件模板 //////////////////////////////
        $oSGP = vmc::singleton('b2c_sales_goods_process');
        $this->pagedata['pt_list'] = $oSGP->getTemplateList();
        //////////////////////////// 优惠方案模板 //////////////////////////////
        $this->pagedata['perfilter'] = true;
        $oSSP = vmc::singleton('b2c_sales_solution_process');
        $this->pagedata['stpl_list'] = $oSSP->getTemplateList(false);
        $this->pagedata['solution_type'] = 'goods';
        $this->page('admin/sales/promotion/goods.html');
    }
    public function toAdd()
    {
        $this->begin('index.php?app=b2c&ctl=admin_sales_goods&act=index');
        $post_data = $_POST;
        $aData = $this->_prepareRuleData($_POST);
        $oGoodsPromotion = $this->app->model('sales_rule_goods');
        $bResult = $oGoodsPromotion->save($aData);
        //保存即应用
        if($bResult && vmc::singleton('b2c_sales_goods_process')->apply($aData['rule_id'],$msg)){
            $this->end(true, ('操作成功,'));
        }else{
            $this->end(false,'失败,'.$msg);
        }

    }
    /**
     * 这个MS可以放入model里处理.
     */
    private function _prepareRuleData($aData)
    {
        ///////////////////////////// 基本信息 //////////////////////////////////
        $aResult = $aData['rule'];
        if (!$aResult['name']) {
            $this->end(false, '促销名称不能为空！');
        }

        $aResult['from_time'] = strtotime($aData['from_time']);
        $aResult['to_time'] = strtotime($aData['to_time']);
        if ($aResult['to_time'] <= $aResult['from_time']) {
            $this->end(false, '结束时间不能小于或等于开始时间！');
        }
        // 会员等级
        $aResult['member_lv_ids'] = empty($aResult['member_lv_ids']) ? null : implode(',', $aResult['member_lv_ids']);
        // 创建时间 (修改时不处理)
        //if(empty($aResult['rule_id']))
        $aResult['create_time'] = time();
        ////////////////////////////// 过滤规则 //////////////////////////////////
        $aResult['conditions'] = empty($aData['conditions']) ? null : ($aData['conditions']);

        //if(is_null($aResult['conditions'])) $aResult['c_template'] = null;
        ////////////////////////////// 优惠方案 //////////////////////////////////
        $aResult['action_solution'] = empty($aData['action_solution']) ? null : ($aData['action_solution']);
        //$aResult['action_solution'] = empty($aData['action_solution'])? null : ($aData['action_solution']);
        if (empty($aResult['sort_order']) && $aResult['sort_order'] !== 0) {
            $aResult['sort_order'] = 50;
        }
        if ($aResult['sort_order']) {
            $aResult['sort_order'] = (int) $aResult['sort_order'];
        }
        if (isset($aData['action_solution']['progetcoupon_promotion_solutions_getcoupon']['cpns_id']) && !is_array($aData['action_solution']['progetcoupon_promotion_solutions_getcoupon']['cpns_id'])) {
            $this->end(false, ('请选择优惠卷!'));
        }
        /*
         * 校验删选相应的表单元素
         */
        if (is_null($aData['rule']['c_template'])) {
            $this->end(false, '优惠条件必选一项');
        }
        if (is_null($aData['rule']['s_template'])) {
            $this->end(false, '优惠方案必选一项');
        }
        $obj_rule_c_template = vmc::singleton($aData['rule']['c_template']);
        if ($obj_rule_c_template) {
            if (method_exists($obj_rule_c_template, 'verify_form')) {
                if (!$obj_rule_c_template->verify_form($aData, $msg)) {
                    $this->end(false, $msg);
                }
            }
        }
        $obj_rule_s_template = vmc::singleton($aData['rule']['s_template']);
        if ($obj_rule_s_template) {
            if (method_exists($obj_rule_s_template, 'verify_form')) {
                if (!$obj_rule_s_template->verify_form($aData, $msg)) {
                    $this->end(false, $msg);
                }
            }
        }

        return $aResult;
    }
    /**
     * 获取指定模板(ajax).
     */
    public function template()
    {
        $oSGP = vmc::singleton('b2c_sales_goods_process');
        // 只载入模板 这里只是选择模板
        $html = $oSGP->getTemplate($_POST['template']);
        if (empty($html)) {
            die('<b align="center">'.('模板生成失败').'</b>');
        }
        $this->pagedata['conditions'] = $html;
        $this->display('admin/sales/promotion/goods_rule.html');
    }
    /**
     * 用于优惠方案获取模板
     */
    public function solution()
    {
        $oSSP = vmc::singleton('b2c_sales_solution_process');
        // 只载入模板 这里只是选择模板
        $html = $oSSP->getTemplate($_POST['template'], array(), $_POST['type']);
        if (empty($html)) {
            die('<b align="center">'.('模板生成失败').'</b>');
        }
        $this->pagedata['conditions'] = $html;
        $this->display('admin/sales/promotion/goods_rule.html');
    }
    /**
     * 选择条件 里面的操作有必在封装在model里.
     */
    public function conditions()
    {
        // 传入的值为空的处理
        if (empty($_POST)) {
            exit;
        }
        // vpath
        $_POST['path'] .= '[conditions]['.$_POST['position'].']';
        $_POST['level'] += 1;
        $oSGP = vmc::singleton('b2c_sales_goods_process');
        echo $oSGP->makeCondition($_POST);
    }
    public function clear($rule_id)
    {
        $this->begin('index.php?app=b2c&ctl=admin_sales_goods&act=index');
        $oSGP = vmc::singleton('b2c_sales_goods_process');
        if ($oSGP->clear((int) $rule_id)) {
            $this->end(true, ('取消应用成功'));
        } else {
            $this->end(true, ('取消应用失败'));
        }
    }
    public function apply($rule_id)
    {
        $oSGP = vmc::singleton('b2c_sales_goods_process');
        if ($_POST) {
            $this->begin('index.php?app=b2c&ctl=admin_sales_goods&act=index');
            if (!isset($_POST['status'])) {
                $this->end(false, ('应用失败'));
            }
            if ($oSGP->apply($_POST['rule_id'])) {
                $this->end(true, ('应用成功'));
            } else {
                $this->end(false, ('应用失败'));
            }
        }
        $mSRG = $this->app->model('sales_rule_goods');
        $aRule = $mSRG->dump($rule_id);
        $this->pagedata['rule'] = $aRule;
        $this->pagedata['rule_id'] = $rule_id;
        $this->pagedata['num'] = $oSGP->getApplyNum($rule_id);
        $this->display('admin/sales/promotion/apply.html');
    }
    public function applyAll($bFlag = true)
    {
        $this->begin('index.php?app=b2c&ctl=admin_sales_goods&act=index');
        $arr_rule_id = $_POST['rule_id'];
        if (!$arr_rule_id) {
            if ($_POST['isSelectedAll'] == '_ALL_') {
                $arr = $this->app->model('sales_rule_goods')->getList('rule_id');
                $arr_rule_id = array_map('current', $arr);
            }
        }
        $oSGP = vmc::singleton('b2c_sales_goods_process');
        // 先全部全部取消应用
        $oSGP->clearAll($arr_rule_id);
        // 如果是全部取消到这结束
        if (!$bFlag) {
            $this->end(true, ('取消成功'));
        }
        // todo 这里可能得做任务处理 多条的处理促销规则,暂时取所有的商品规则进行处理  2010-04-19 10:45 wubin
        $mSRG = $this->app->model('sales_rule_goods');
        //$aGPR = $mSRG->getList('rule_id',array('status'=>'true'),0,9999); // 默认20条件 现在取9999 应该取完了
        foreach ($arr_rule_id as $rule_id) {
            $oSGP->apply($rule_id);
        }
        $this->end(true, ('应用成功'));
    }
    public function applyAllSelect($bFlag = true)
    {
        $oSGP = vmc::singleton('b2c_sales_goods_process');
        // 先全部全部取消应用
        $oSGP->clearAll();
        // 如果是全部取消到这结束
        if (!$bFlag) {
            die(('取消成功'));
        }
        // todo 这里可能得做任务处理 多条的处理促销规则,暂时取所有的商品规则进行处理  2010-04-19 10:45 wubin
        $mSRG = $this->app->model('sales_rule_goods');
        $aGPR = $mSRG->getList('rule_id', array(
            'status' => 'true',
        ), 0, 9999); // 默认20条件 现在取9999 应该取完了
        foreach ($aGPR as $key => $row) {
            $oSGP->apply($row['rule_id']);
        }
        die(('应用成功'));
    }
    public function goods_dialog($html = true, $aData = array(), $table_info = array())
    {
        if (empty($table_info) || !is_array($table_info)) {
            $table_info = $_GET['arg0'];
            $table_info = @unserialize(urldecode($table_info));
        }
        $serialize_table_info = @serialize($table_info);
        if (empty($table_info['table'])) {
            return false;
        }
        $filter = array();
        if ($this->app->getConf('site.storage.enabled') == 'true') {
            $filter['marketable'] = 'true';
        }
        //每页条数
        $pageLimit = 10;
        //当前页
        $page = $_GET['page'];
        $page or $page = 1;
        if (is_array($table_info['table'])) {
            $db = vmc::database();
            foreach ($table_info['table'] as $row) {
                $position = strpos($row, ':');
                if ($position === false) {
                    return false;
                }
                $de_table[] = substr($row, 0, $position);
                $table[] = $db->prefix.substr($row, 0, $position);
                $join_columns[] = $db->prefix.str_replace(':', '.', $row);
            }
            if (empty($table) || empty($join_columns)) {
                return false;
            }
            if (!is_array($table_info['columns'])) {
                return false;
            }
            foreach ($table_info['columns'] as $key => $val) {
                $val = str_replace($de_table, $table, $val);
                //$val = str_replace(':', '.', $val);
                if (strpos($val, ':')) {
                    $columns[] = 'CONCAT('.str_replace(':', ',"   ", ', $val).") AS $key";
                } else {
                    $columns[] = "$val as $key";
                }
                if ($key == 'id') {
                    $default_column = $val;
                }
            }
            if ($aData && is_array($aData) && !empty($aData)) {
                $filter[$default_column] = $aData;
            }
            //print_r($columns);
            $sql_table = implode(',', $table);
            $sql_join = implode('=', $join_columns);
            $sql_where = 1;
            if ($filter && is_array($filter)) {
                $sql_where = array(
                    1,
                );
                foreach ($filter as $key => $val) {
                    $prefix = (strpos($key, $table[0]) === false) ? $table[0].'.' : '';
                    if (is_array($val)) {
                        $sql_where[] = $prefix."$key IN (".implode(',', $val).')';
                    } else {
                        $sql_where[] = $prefix."$key='$val'";
                    }
                }
                $sql_where = implode(' AND ', $sql_where);
            }
            $sql = 'SELECT count(*) AS count FROM '.$sql_table.' WHERE '.$sql_join.' AND '.$sql_where;
            $count = $db->select($sql);
            $count = $count[0]['count'];
            $limit = ' LIMIT '.$pageLimit * ($page - 1).", $pageLimit";
            $sql = 'SELECT '.implode(',', $columns).' FROM '.$sql_table.' WHERE '.$sql_join.' AND '.$sql_where.$limit;
            $arr_products = $db->select($sql);
        } else {
            $table_name = explode('_', ltrim($table_info['table'], '_'));
            $app = array_shift($table_name);
            if (empty($table_name)) {
                return false;
            }
            $obj = app::get($app)->model(implode('_', $table_name));
            if (!is_array($table_info['columns'])) {
                return false;
            }
            foreach ($table_info['columns'] as $key => $val) {
                $val = str_replace($table_info['table'], vmc::database()->prefix.$table_info['table'], $val);
                $val = str_replace(':', '.', $val);
                $columns[] = "$val AS $key";
                if ($key == 'id') {
                    if (strpos($val, '.') !== false) {
                        $default_column = substr($val, strpos($val, '.') + 1);
                    } else {
                        $default_column = $val;
                    }
                }
            }
            if ($aData && is_array($aData) && !empty($aData)) {
                $filter[$default_column] = $aData;
            }
            if (empty($columns)) {
                return false;
            }
            //总数
            $count = $obj->count($filter);
            $arr_products = $obj->getList(implode(',', $columns), $filter, $pageLimit * ($page - 1), $pageLimit);
        }
        //标识用于生成url
        $token = md5("page{$page}");
        $this->pagedata['pager'] = array(
            'current' => $page,
            'total' => ceil($count / $pageLimit) ,
            'link' => app::get('desktop')->router()->gen_url(array(
                'app' => 'b2c',
                'ctl' => 'admin_sales_goods',
                'act' => 'goods_dialog',
                'page' => $token,
                'arg0' => $serialize_table_info,
            )) ,
            'token' => $token,
        );
        //$arr_products = $obj->getList('name, goods_id', $filter);
        $this->pagedata['arr_products'] = $arr_products;
        if ($html) {
            $this->display('admin/sales/dialog/goods.html');
        }
    }
    public function get_defaine_dialog($aData = array(), $table_info = array())
    {
        $aData['default'] = $aData['default'] ? $aData['default'] : array(
            'false',
        );
        $this->goods_dialog(false, $aData['default'], $table_info);
        $this->pagedata['name'] = $aData['name'];
        $this->display('admin/sales/dialog/goods_item.html');
        //$this->display('admin/sales/dialog.html');
    }
}
