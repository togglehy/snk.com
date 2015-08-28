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


 
class base_application_tips{
    
    static function tip_apps(){
        $apps = array();
        $lang = vmc::get_lang();
        if ($handle = opendir(PUBLIC_DIR.'/app')) {
            while (false !== ($file = readdir($handle))) {
                if($file{0}!='.' && is_dir(PUBLIC_DIR.'/app/'.$file) && file_exists(PUBLIC_DIR.'/app/'.$file.'/lang/'.$lang.'/tips.txt')){
                    $apps[] = $file;
                }
                if(defined('EXTENDS_DIR') && $file{0}!='.' && is_dir(EXTENDS_DIR.'/'.$file) && file_exists(EXTENDS_DIR.'/'.$file.'/lang/'.$lang.'/tips.txt')){
                    $apps[] = $file;
                }
            }
            closedir($handle);
        }
        return $apps;
    }
    
    static function tips_item_by_app($app_id){
        $lang = vmc::get_lang();
        $tips = array();
        foreach(file(PUBLIC_DIR.'/app/'.$app_id.'/lang/'.$lang.'/tips.txt')  as $tip){
            $tip = trim($tip);
            if($tip){
                $tips[] = $tip;
            }
        }
        return $tips;
    }
    
    static function tip(){

        $apps = self::tip_apps();
        $key = array_rand($apps);
        $app_id = $apps[$key];
        if(empty($app_id)) return '';
        
        $tips = self::tips_item_by_app($app_id);
        $key = array_rand($tips);
        return $tips[$key];
    }
    
}
