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

class b2c_ctl_admin_goods_type extends desktop_controller
{
    public function index()
    {
        $this->finder('b2c_mdl_goods_type', array(
            'actions' => array(
                array(
                    'label' => ('添加商品类型'),
                    'icon' => 'fa fa-plus',
                    'href' => 'index.php?app=b2c&ctl=admin_goods_type&act=add',
                ),
            ),
            'title' => ('商品类型'),
            'use_buildin_recycle' => true,
        ));
    }
    public function add()
    {
        $this->edit();
    }
    public function set($typeId = 0)
    {
        $this->edit($typeId);
    }
    public function edit($type_id)
    {
        $gtype = $_POST['gtype'];
        if ($type_id) {
            $gtype['type_id'] = $type_id;
        }
        if ($gtype['type_id']) {
            $oType = $this->app->model('goods_type');
            $subsdf = array(
                'props' => array(
                    '*',
                    array(
                        'props_value' => array(
                            '*',
                            null,
                            array(
                                0, -1,
                                'order_by ASC',
                            ),
                        ),
                    ),
                ),
            );
            $gtype = array_merge($oType->dump($gtype['type_id'], '*', $subsdf), $gtype);
        }
        if (is_array($gtype['props'])) {
            foreach ($gtype['props'] as $k => $v) {
                if (empty($k)) {
                    $gtype['props'] = null;
                }
            }
        }
        $this->pagedata['gtype'] = $gtype;

        $this->page('admin/goods/goods_type/edit_type_edit.html');
    }

    public function save()
    {
        $gtype = $_POST['gtype'];
        $this->begin('index.php?app=b2c&ctl=admin_goods_type&act=index');
        if (!$gtype['name']) {
            //trigger_error(('请输入类型名称'),E_USER_ERROR);
            $this->end(false, ('请输入类型名称'));
        }
        $oGtype = $this->app->model('goods_type');
        $typeId = current((array) $oGtype->dump(array(
            'name' => $gtype['name'],
            'type_id',
        )));
        if ($typeId && $gtype['type_id'] != $typeId) {
            //trigger_error(('类型名称已存在'),E_USER_ERROR);
            $this->end(false, ('类型名称已存在'));
        }
        //品牌
        if (!$gtype['brand']) {
            $gtype['brand'] = null;
        }
        //属性
        $this->_preparedProps($gtype);
        //参数
        $this->_preparedParams($gtype, $errorMsg);
        if ($errorMsg) {
            $this->end(false, $errorMsg);
        }

        $this->end($oGtype->save($gtype), ('操作成功'));
    }

    public function _preparedProps(&$gtype)
    {
        if (!$gtype['props']) {
            $gtype['props'] = array();

            return;
        }
        $searchType = array(
            '0' => array(
                'type' => 'input',
                'search' => 'input',
            ) ,
            '1' => array(
                'type' => 'input',
                'search' => 'disabled',
            ) ,
            '2' => array(
                'type' => 'select',
                'search' => 'nav',
            ) ,
            '3' => array(
                'type' => 'select',
                'search' => 'select',
            ) ,
            '4' => array(
                'type' => 'select',
                'search' => 'disabled',
            ) ,
        );
        $props = array();
        $inputIndex = 21;
        $selectIndex = 1;
        foreach ($gtype['props'] as $aProps) {
            if (!$aProps['name']) {
                continue;
            }
            if (is_numeric($aProps['type'])) {
                $aProps = array_merge($aProps, $searchType[$aProps['type']]);
                if ($aProps['type'] == 'input') {
                    unset($aProps['options']);
                }
            }
            if (!$aProps['options']) {
                unset($aProps['options']);
            } else {
                $tAProps = array();
                $aProps['optionIds'] = $aProps['options']['id'];
                foreach ($aProps['options']['value'] as $opk => $opv) {
                    $opv = explode('|', $opv);
                    $tAProps['options'][$opk] = $opv[0];
                    unset($opv[0]);
                    $tAProps['optionAlias'][$opk] = implode('|', (array) $opv);
                }
                $aProps['options'] = $tAProps['options'];
                $aProps['optionAlias'] = $tAProps['optionAlias'];
            }
            $aProps['ordernum'] = intval($aProps['ordernum']);
            if ($aProps['type'] == 'input') {
                $propskey = $inputIndex++;
            } else {
                $propskey = $selectIndex++;
            }
            $aProps['goods_p'] = $propskey;
            if (!isset($aProps['show'])) {
                $aProps['show'] = '';
            }
            $props[$propskey] = $aProps;
        }
        if ($inputIndex > 51) {
            //trigger_error(('输入属性不能超过30项'),E_USER_ERROR);
            $this->end(false, ('输入属性不能超过30项'));
        }
        if ($selectIndex > 21) {
            //trigger_error(('选择属性不能超过20项'),E_USER_ERROR);
            $this->end(false, ('选择属性不能超过20项'));
        }
        $gtype['props'] = $props;
        $props = null;
    }
    public function _preparedParams(&$gtype, &$errorMsg = '')
    {
        if (!$gtype['params']) {
            $gtype['params'] = array();

            return;
        }
        $params = array();
        foreach ($gtype['params'] as $aParams) {
            if (!$aParams['name']) {
                $errorMsg = ('请为参数表中参数组添加参数名');
                break;
            }
            $paramsItem = array();
            foreach ($aParams['name'] as $piKey => $piName) {
                if (!$piName) {
                    $errorMsg = ('请完成参数表中参数名');
                    break 2;
                }
                $paramsItem[$piName] = $aParams['alias'][$piKey];
            }
            if (!$aParams['group']) {
                $errorMsg = ('请完成参数表中参数组名称');
                break;
            }
            $params[$aParams['group']] = $paramsItem;
        }
        $gtype['params'] = $params;
        $params = null;
    }



}
