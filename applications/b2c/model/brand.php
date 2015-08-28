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


class b2c_mdl_brand extends dbeav_model
{
    public $defaultOrder = array(
        'brand_initial ASC,ordernum ASC',
    );
    public function __construct($app)
    {
        parent::__construct($app);
        $this->use_meta();
        // foreach($this->getList() as $item){
        //     $item['ordernum'] = 30;
        //     $this->save($item);
        // }
    }
    public function brand_meta_register()
    {
        $col = array(
            'seo_info' => array(
                'type' => 'serialize',
                'label' => ('seo设置'),
            ) ,
        );
        $this->meta_register($col);
    }
    public function save(&$data, $mustUpdate = null, $mustInsert = false)
    {
        $data['brand_initial'] = $this->getinitial($data['brand_name']);
        $rs = parent::save($data, $mustUpdate);

        return $rs;
    }
    public function pre_recycle($rows)
    {
        $oGoods = $this->app->model('goods');
        if (is_array($rows)) {
            foreach ($rows as $bk => $bv) {
                $cbrand = $oGoods->count(array(
                    'brand_id' => $bv['brand_id'],
                ));
                if ($cbrand > 0) {
                    $this->recycle_msg = '该品牌下有商品';

                    return false;
                }
            }
        }

        return true;
    }
    public function getinitial($str)
    {
        $py = new base_py('utf-8');
        $pi = $py->getInitials($str);
        preg_match('/^[A-Za-z]/', $pi, $result);
        $inital = current($result);
        if ($inital) {
            return $inital;
        }

        return '~';
    }
}
