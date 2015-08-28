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


class b2c_finder_goods
{
    public $detail_products = '货品信息';
    public $detail_qrcode = '二维码';
    public $column_control = '操作';

    public function __construct($app)
    {
        $this->app = $app;
    }

    public $column_goods_pic = '缩略图';
    public $column_goods_qrcode = '二维码';
    public $column_goods_pic_order = COLUMN_IN_HEAD;
    public $column_goods_pic_order_field = 'image_default_id';
    public function column_goods_qrcode($row){
        $gid = $row['goods_id'];
        return "<a target='_blank' class='btn btn-xs btn-default' href='index.php?app=b2c&ctl=admin_goods&act=index&action=detail&id=$gid&finderview=detail_qrcode'><i class='fa fa-qrcode'></i></a>";
    }

    public function column_goods_pic($row)
    {
        $img_src = base_storager::image_path($row['@row']['image_default_id'], 'xs');
        if (!$img_src) {
            return '';
        }

        return "<img class='img-thumbnail' src='$img_src' style='height:30px;'>";
    }

    public function column_control($row)
    {
        $url_preview = app::get('site')->router()->gen_url(array('app'=>'b2c','ctl'=>'site_product','args'=>array('g'.$row['goods_id'])));

        $returnValue = '<a class="btn btn-default btn-xs" href="index.php?app=b2c&ctl=admin_goods_editor&act=edit&p[0]='.$row['goods_id'].'"><i class="fa fa-edit"></i>编辑</a><a class="btn btn-default btn-xs" target="_blank" href="'.$url_preview.'"><i class="fa fa-external-link"></i> 浏览</a>';

        $returnValue.='<a class="btn btn-default btn-xs" target="_blank" href="index.php?app=b2c&ctl=admin_stock&act=index&p[0]='.$row['goods_id'].'"><i class="fa fa-th"></i> 库存</a>';

        return $returnValue;
    }

    public function detail_products($gid)
    {

        $mdl_products = app::get('b2c')->model('products');

        $products = $mdl_products->getList('*', array('goods_id' => $gid));

        $mdl_stock = app::get('b2c')->model('stock');

        $sku_bn = array_keys(utils::array_change_key($products, 'bn'));
        $stock_list = $mdl_stock->getList('*', array('sku_bn' => $sku_bn));
        $stock_list = utils::array_change_key($stock_list, 'sku_bn');
        $render = $this->app->render();
        $render->pagedata['data_detail'] = app::get('b2c')->model('goods')->dump($gid,'*','default');
        $render->pagedata['products'] = $products;
        $render->pagedata['stock_list'] = $stock_list;
        $render->pagedata['gpromotion_openapi'] = vmc::openapi_url('openapi.goods','promotion',array('goods_id'=>$gid));

        return $render->fetch('admin/goods/detail/detail.html');
    }

    public function detail_qrcode($gid){

            $mobile_url = vmc::singleton('mobile_router')->gen_url(array(
            'app'=>'b2c',
            'ctl'=>'mobile_product',
            'act'=>'index',
            'args'=>array('g'.$gid),
            'full'=>1
            ));

            $qrcode_image = vmc::singleton('wechat_qrcode')->create($mobile_url);
            $url = base_storager::image_path($qrcode_image['image_id']);
            return "<img src='$url' />";
    }


    public function day($time = null)
    {
        if (!isset($GLOBALS['_day'][$time])) {
            return $GLOBALS['_day'][$time] = floor($time / 86400);
        } else {
            return $GLOBALS['_day'][$time];
        }
    }

    public function row_style($row)
    {
        //$row = $row['@row'];
    }
}
