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



/**
 * 单商品减固定价格购买
 * $ 2010-05-04 16:51 $
 */
class b2c_promotion_solutions_toscore implements b2c_interface_promotion_solution
{
    public $name = "积分翻倍"; // 名称
    public $type = "general";//goods\order\general
    public $desc_pre = '获得';
    public $desc_post = '倍积分';
    public $score_add = true;
    private $description = '';
    public $desc_tag = '积分翻倍';
    private $save = 0;


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
        if( !$this->get_status() ) return ('商店未启用积分体系');
        if($this->type == 'goods'){
            return <<<EOF
        满<input class="input-sm input-xsmall" name="action_solution[b2c_promotion_solutions_toscore][quantity]" required=true value="{$aData['quantity']}" />件，单件{$this->desc_pre}<input class="input-sm input-xsmall" name="action_solution[b2c_promotion_solutions_toscore][gain_score]" required=true value="{$aData['gain_score']}" />{$this->desc_post}
EOF;

        }
        return <<<EOF
{$this->desc_pre}<input class="input-sm" name="action_solution[b2c_promotion_solutions_toscore][gain_score]" required=true value="{$aData['gain_score']}" />{$this->desc_post}
EOF;
    }


    public function apply(&$cart_object,$solution,&$cart_result) {

        $omath = vmc::singleton('ectools_math');
        //翻倍前
        $item_score = $omath->number_multiple(array(
             app::get('b2c')->getConf('score_convert',1),
             $cart_object['item']['product']['buy_price'],
        ));
        //翻倍后
        $pscore = $omath->number_multiple(array(
             $solution['gain_score'],//翻倍数
             app::get('b2c')->getConf('score_convert',1),
             $cart_object['item']['product']['buy_price'],
        ));
        //翻倍后额外多出部分
        $addscore = $omath->number_minus(array(
            $pscore,
            $item_score
        ));
        $cart_result['gain_score'] = $omath->number_plus(array($cart_result['gain_score'], $addscore));
        $this->setString($solution);
    }




    public function apply_order(&$cart_object,$solution,&$cart_result) {
        $cart_result['gain_score']*= $solution['gain_score'];
        $this->setString($solution);
    }

    public function setString($aData) {
        $this->description = $this->desc_pre . $aData['gain_score'] . $this->desc_post;
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
		if (!isset($data['action_solution']['b2c_promotion_solutions_toscore']['gain_score']) || !$data['action_solution']['b2c_promotion_solutions_toscore']['gain_score'])
		{
			$msg = ('请指定订单赠送积分的倍数！');
			return false;
		}

		if (preg_match('/[^\d]/',$data['action_solution']['b2c_promotion_solutions_addscore']['gain_score']) || $data['action_solution']['b2c_promotion_solutions_toscore']['gain_score'] < 0)
		{
			$msg = ('订单赠送积分必须是大等于0的数字！');
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
