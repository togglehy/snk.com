<?php

class importexport_data_b2c_goods
{
    public function get_title($title = array())
    {
        //定义表头
        return array(
            'gid' => '商品编号(gid)',
            'name' => '商品名称(name)',
            'brief' => '商品简介(brief)',
            'cat_id' => '商品分类(cat_id)',
            'brand_id' => '品牌(brand_id)',
            'intro' => '图文介绍(intro)',
            'comment_count'=>'评论数(comment_cout)',
            'view_w_count'=>'周访问数(view_w_count)',
            'view_count'=>'总访问数(view_w_count)',
            'buy_w_count'=>'周购买数(view_w_count)',
            'buy_count'=>'总购买数(view_w_count)',
            'nostore_sell' => '无库存也可销售(nostore_sell)',
            'marketable' => '立即上架(marketable)',
        );
    }

    // public function get_content_row($row)
    // {
    //     return $row;
    // }

    /*-----------------------以下为导入函数-----------------------*/

    /**
     * 如果是最后一条记录和倒数第二天记录属于同一个商品则继续下去.
     *
     * @return bool true 继续获取下一行 false 不需要在获取,已经完整的获取到一条商品数据
     */
    public function check_continue(&$contents, $line)
    {
        if (count($contents) == 1 || ($contents[$line]['gid'] && $contents[$line - 1]['gid'] && $contents[$line]['gid'] == $contents[$line - 1]['gid'])) {
            return true;
        } else {
            array_pop($contents);

            return false;
        }
    }

    /**
     *将导入的数据转换为sdf.
     *
     * @param array  $contents 导入的一条商品数据
     * @param string $msg      传引用传出错误信息
     */
    public function dataToSdf($contents, &$msg)
    {
        return $save_data;
    }
}
