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


class b2c_promotion_solutions_freeshipping implements b2c_interface_promotion_solution {
    public $name = "免运费
"; // 名称
    public $type = 'order'; //goods\order\general
    public $desc_pre = '免运费';
    public $desc_post = '';
    public $desc_tag = '包邮';
    private $description = '';
    private $save = 0;
    public function __construct($app) {
        $this->app = $app;
        $this->name = ($this->name);
        $this->desc_pre = ($this->desc_pre);
    }
    /**
     * 优惠方案模板
     * @param array $aConfig // 设置信息(修改的时候传入)
     * @return string // 返回要输出的模板html
     */
    public function config($aData = array()) {
        return <<<EOF
            免运费
            <input name="action_solution[b2c_promotion_solutions_freeshipping][solution]" type="hidden" value="true" />
EOF;

    }
    public function apply(&$cart_object, $solution, &$cart_result) {
        //订单级专属

    }
    public function apply_order(&$cart_object, $solution, &$cart_result) {
        $cart_result['free_shipping'] = $this->rule_id;
        $this->setString($solution);
    }
    public function setString($aData) {
        $this->description = $this->desc_pre;
    }
    public function getString() {
        return $this->description;
    }
    public function getSave(){
        return 0;
    }
    public function get_status() {
        return true;
    }
    public function allow($is_order) {
        return 'order';
    }
    public function verify_form($data = array() , &$msg = '') {
        return true;
    }
    function get_desc_tag() {
        if (isset($this->cart_display)) {
            $desc_tag['display'] = $this->cart_display;
        } else {
            $desc_tag['display'] = true;
        }
        $desc_tag['name'] = $this->desc_tag;
        return $desc_tag;
    }
}
