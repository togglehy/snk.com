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



class desktop_ctl_pam extends desktop_controller{

    function index(){
        $this->finder('desktop_mdl_pam',array(
            'title'=>'登录认证',
            'use_buildin_recycle'=>false,
            'use_buildin_selectrow'=>false
            ));
    }


    function setting($passport){
        $passport_model =new $passport;
        if($_POST){
            $this->begin('index.php?app=desktop&ctl=pam&act=index');
            if($_POST['site_passport_status'] === 'false'){
                if(!$this->checkpassport($passport_model)){
                     $this->end(false,'配置失败,前台必须开启一种认证方式');
                }
            }
            #↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓记录管理员操作日志@lujy↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
            if($obj_operatorlogs = vmc::service('operatorlog.goods')){
                $_predate = $passport_model->get_config();
            }
            #↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑记录管理员操作日志@lujy↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
            if(!$passport_model->set_config($_POST)){
                if(!$_POST['error']){ $this->end(false,'配置失败');
                }else{
                    $this->end(false,$_POST['error']);
                }
            }else{
                #↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓记录管理员操作日志@lujy↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
                if($obj_operatorlogs = vmc::service('operatorlog.system')){
                    if(method_exists($obj_operatorlogs,'pamsetting')){
                        $obj_operatorlogs->pamsetting($_predate,$passport_model->get_config());
                    }
                }
                #↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑记录管理员操作日志@lujy↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
                $this->end(true,'配置成功');
            }

        }
        $len = strlen($html);
        foreach($passport_model->get_config() as $name=>$config){
            if($config['editable'] == 'false' || (isset($config['editable']) && !$config['editable'])) continue;
            $input['name'] = $name;
            $input['title'] = $config['label'];
            $input['type'] = $config['type'];
            $input['required'] = $config['required'];
            if($config['options']){
                $input['options'] = $config['options'];
            }
            if($config['value']){
               $input['value'] = $config['value'];
            }
            $input[]= $input;

        }

        if($len == strlen($html)){
            $this->pagedata['basic'] = "true";
        }
        $this->pagedata['html'] = $html;
        $this->pagedata['passport'] = $passport;
        echo 'TODO';
        $this->page('pam.html');
    }

    function checkpassport($model_passport){
         foreach(vmc::servicelist('passport') as $k=>$passport){
            if($model_passport != $passport){
                $config = $passport->get_config();
                if($config['site_passport_status']['value'] == 'true'){
                    $flag = true;
                    break;
                }
                else $flag = false;
            }
        }
        return $flag;
    }

}
