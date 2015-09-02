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

header('Content-Type:application/json; charset=utf-8');
class seller_frontpage extends site_controller {

    protected $seller = array();

    function __construct(&$app) {		
        parent::__construct($app);		
        $this->_response->set_header('Cache-Control', 'no-store');		
        vmc::singleton('base_session')->start();		
		//$this->verify();
		$this->action = $this->_request->get_act_name();		
        $this->seller = $this->get_current_seller();
		$this->_menus = $this->get_menu();
        $this->user_obj = vmc::singleton('seller_user_object');		
        $this->passport_obj = vmc::singleton('seller_user_passport');		
    }

    
	/*
     * 如果是登录状态则直接跳转到商家中心
     * 
	*/    
    public function set_forward(&$forward)
    {
        $params = $this->_request->get_params(true);
        $forward = ($forward ? $forward : $params['forward']);
        if (!$forward) {
            $forward = $_SERVER['HTTP_REFERER'];
        }
        if (!strpos($forward, 'passport')) {
            $this->pagedata['forward'] = $forward;
        }
    }

    function verify() {
        $user_obj = vmc::singleton('seller_user_object');
		exit($this->gen_url(array(
            'app' => 'seller',
            'ctl' => 'site_passport',
            'act' => 'login' //
        )));
        if ($this->app->seller_id = $user_obj->get_seller_id()) {
            $data = $user_obj->get_sellers_data(array(
                'sellers' => 'seller_id'
            ));
            if ($data) {
                //登录受限检测
                $res = $this->loginlimit($data['sellers']['seller_id'], $redirect);
                if ($res) {
                    $this->splash('error', $redirect, '登陆受限');
                } else {
                    return true;
                }
            }
        }

		

        $this->splash('error', $this->gen_url(array(
            'app' => 'seller',
            'ctl' => 'site_passport',
            'act' => 'login' //
        )) , '未登录');
    }
    /**
     * loginlimit-登录受限检测
     *
     * @param      none
     * @return     void
     */
    function loginlimit($mid, &$redirect) {
        $services = vmc::servicelist('loginlimit.check');
        if ($services) {
            foreach ($services as $service) {
                $redirect = $service->checklogin($mid);
            }
        }
        return $redirect ? true : false;
    } //End Function
    public function bind_seller($seller_id) {
        $columns = array(
            'account' => 'seller_id,login_account,login_password,checkin',
            'sellers' => 'seller_id',
        );
        $user_obj = vmc::singleton('seller_user_seller');
        $cookie_expires = $user_obj->cookie_expires ? time() + $user_obj->cookie_expires * 60 : 0;
        $seller_data = $user_obj->get_sellers_data($columns,$seller_id);
        $login_name = $user_obj->get_seller_name($data['account']['login_name'],$seller_id);
        $this->cookie_path = vmc::base_url() . '/';
        $this->set_cookie('UNAME', $login_name, $cookie_expires);
        $this->set_cookie('SELLER_IDENT', $seller_id, $cookie_expires);
    }
    public function get_current_seller() {
        return vmc::singleton('seller_user_object')->get_current_seller();
    }
    function set_cookie($name, $value, $expire = false, $path = null) {
        if (!$this->cookie_path) {
            $this->cookie_path = vmc::base_url() . '/';
            #$this->cookie_path = substr(PHP_SELF, 0, strrpos(PHP_SELF, '/')).'/';
            $this->cookie_life = $this->app->getConf('system.cookie.life');
        }
        $this->cookie_life = $this->cookie_life > 0 ? $this->cookie_life : 315360000;
        $expire = $expire === false ? time() + $this->cookie_life : $expire;
        setcookie($name, $value, $expire, $this->cookie_path);
        $_COOKIE[$name] = $value;
    }
    function check_login() {
        vmc::singleton('base_session')->start();
        if ($_SESSION['account'][pam_account::get_account_type($this->app->app_id) ]) {
            return true;
        } else {
            return false;
        }
    }

    function setSeo($app, $act, $args = null) {
        $seo = vmc::singleton('site_seo_base')->get_seo_conf($app, $act, $args);
        $this->title = $seo['seo_title'];
        $this->keywords = $seo['seo_keywords'];
        $this->description = $seo['seo_content'];
        $this->nofollow = $seo['seo_nofollow'];
        $this->noindex = $seo['seo_noindex'];
    } //End Function

	public function get_menu()
	{
		$xmlfile = $this->app->app_dir . "/menu.xml";		
		//$xsd = vmc::singleton('base_xml')->xml2array(file_get_contents($xmlfile), $tags);
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, file_get_contents($xmlfile), $tags);
        xml_parser_free($parser);
		$group = Array();
		$menus = Array();
		$count = count($tags);		
		foreach($tags as $key => $item)
		{
			if($item['tag'] == 'menugroup')
			{
				$menuItem = $item['attributes'];				
				for($i=$key+1; $i<$count; $i++)
				{
					if($tags[$i]['tag'] == 'menu')
					{
						$tags[$i]['attributes']['label'] = $tags[$i]['value'];
						$menuItem['items'][] = $tags[$i]['attributes'];
						continue;
					}
					break;
				}
				$menus[] = $menuItem;
			}
		}
		return $menus;
	}

	//
	protected function output($app_id)
    {
        $app_id || $app_id = $this->app->app_id;
        $this->pagedata['seller'] = $this->seller;
        $this->pagedata['menu'] = $this->get_menu();
        $this->pagedata['current_action'] = $this->action;
        $this->action_view = 'action/'.$this->action.'.html';
        if ($this->pagedata['_PAGE_']) {
            $this->pagedata['_PAGE_'] = 'site/seller/'.$this->pagedata['_PAGE_'];
        } else {
            $this->pagedata['_PAGE_'] = 'site/seller/'.$this->action_view;
        }
        $this->pagedata['app_id'] = $app_id;
        $this->pagedata['_MAIN_'] = 'site/seller/main.html';
        $this->page('site/seller/main.html');
    }
}
