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



class b2c_promotion_solutions_topercent implements b2c_interface_promotion_solution
{


    public $name = "以固定折扣购买"; // 名称
    public $type = "general";//goods\order\general
    public $desc_pre = '价格乘以';
    public $desc_post = '%折扣出售';
    private $description = '';
    public $desc_tag= '打折';
    private $save = 0;

    //同种方案在同一商品上 适用 排他原则
    public $stop_rule_with_same_solution = true;



    public function __construct( $app ) {
        $this->app = $app;
        $this->name = ($this->name);
        $this->desc_pre = ($this->desc_pre);
        $this->desc_post = ($this->desc_post);
    }



    /**
     * 优惠方案模板
     * @param array $aConfig // 设置信息(修改的时候传入)
     * @return string // 返回要输出的模板html
     */
    public function config($aData = array()) {
        if($this->type == 'goods'){
            return <<<EOF
    满<input class="input-sm input-xsmall" name="action_solution[b2c_promotion_solutions_topercent][quantity]" required=true value="{$aData['quantity']}" />件，单件{$this->desc_pre}<input class="input-sm input-xsmall" name="action_solution[b2c_promotion_solutions_topercent][percent]" required=true value="{$aData['percent']}" />{$this->desc_post}
EOF;

        }
        return <<<EOF
{$this->desc_pre}<input class="input-sm" name="action_solution[b2c_promotion_solutions_topercent][percent]" required=true value="{$aData['percent']}" />{$this->desc_post}
EOF;
    }


    public function apply(&$cart_object,$solution,&$cart_result) {
        $omath = vmc::singleton('ectools_math');
        $percent = max(0, $solution['percent']);
        $percent = min($percent, 100);
        $p = ($percent / 100);
        $pre_buy_price = $cart_object['item']['product']['buy_price'];
        //修改购物车商品项成交价
        $cart_object['item']['product']['buy_price'] = $omath->number_multiple(array(
            $cart_object['item']['product']['buy_price'],
            $p
        ));
        $promotion_amount = $omath->number_multiple(array(
            $pre_buy_price - $cart_object['item']['product']['buy_price'],
            $cart_object['quantity']
        ));
        //购物车优惠总计同步
        $cart_result['goods_promotion_discount_amount'] = $omath->number_plus(array(
            $cart_result['goods_promotion_discount_amount'],
            $promotion_amount
        ));
        $cart_result['promotion_discount_amount'] = $omath->number_plus(array(
            $cart_result['promotion_discount_amount'],
            $promotion_amount
        ));
        /*$cart_result 是引用传递，意味着在计算流程中，已经影响了购物车优惠小计*/
        $this->setSave($promotion_amount);//设置本次优惠额度
        $this->setString($solution);
    }



    public function apply_order(&$cart_object,$solution,&$cart_result) {
        $omath = vmc::singleton('ectools_math');
        $percent = max(0, $solution['percent']);
        $percent = min($percent, 100);
        $p = 1-($percent / 100);
        $promotion_amount = $omath->number_multiple(array(
            $cart_result['finally_cart_amount'],
            $p
        ));


        //购物车优惠总计同步
        $cart_result['order_promotion_discount_amount'] = $omath->number_plus(array(
            $cart_result['order_promotion_discount_amount'],
            $promotion_amount
        ));
        $cart_result['promotion_discount_amount'] = $omath->number_plus(array(
            $cart_result['promotion_discount_amount'],
            $promotion_amount
        ));
        $cart_result['finally_cart_amount'] = $omath->number_minus(array(
            $cart_result['finally_cart_amount'],
            $promotion_amount
        ));
        /*$cart_result 是引用传递，意味着在计算流程中，已经影响了购物车优惠小计*/
        $this->setSave($promotion_amount);//设置本次优惠额度
        $this->setString($solution);
    }






    public function setString($aData) {
        $this->description = $this->desc_pre . $aData['percent'] . $this->desc_post;
    }

    public function getString() {
        return $this->description;
    }

    public function setSave($save){
        $this->save = $save;
    }
    public function getSave(){
        return $this->save;
    }

    public function get_status() {
        return true;
    }

    /**
	 * 校验参数是否正确
	 * @param mixed 需要校验的参数
	 * @param string error message
	 * @return boolean 是否成功
	 */
	public function verify_form($data=array(), &$msg='')
	{
		if (!$data) return true;

		/** 订单够满金额 **/
		if (!isset($data['action_solution']['b2c_promotion_solutions_topercent']['percent']) || !$data['action_solution']['b2c_promotion_solutions_topercent']['percent'])
		{
			$msg = ('请指定订单固定折扣的金额！');
			return false;
		}

		if (!is_numeric($data['action_solution']['b2c_promotion_solutions_topercent']['percent']) || $data['action_solution']['b2c_promotion_solutions_topercent']['percent'] < 0 || $data['action_solution']['b2c_promotion_solutions_topercent']['percent'] > 100)
		{
			$msg = ('固定折扣必须是在0-100的数字！');
			return false;
		}
		/** end **/

		return true;
	}

    function get_desc_tag(){
        if(isset($this->cart_display)){
            $desc_tag['display'] = $this->cart_display;
        }else{
            $desc_tag['display'] = true;
        }
        $desc_tag['name'] = $this->desc_tag;
        return $desc_tag;
    }

}
