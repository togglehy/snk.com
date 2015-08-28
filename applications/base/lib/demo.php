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


class base_demo
{
    public function init()
    {
        cachemgr::init(false);
        set_error_handler(array(&$this, '_errorHandler'));
        $demo_dir = ROOT_DIR.'/demodata';
        if (is_dir($demo_dir)) {
            $handle = opendir($demo_dir);
            while ($file = readdir($handle)) {
                $realfile = $demo_dir.'/'.$file;
                $data_arr = array();
                $data = null;
                if (is_file($realfile)) {
                    list($app_id, $model, $ext) = explode('.', $file);
                    switch ($ext) {
                        case 'sdf':
                            $this->init_sdf($app_id, $model, $realfile);
                            break;
                        case 'php':
                            $setting = include $realfile;
                            $this->init_setting($app_id, $setting);
                        case 'json':
                            $json_str = file_get_contents($realfile);
                            $json_str = $this->json_fix($json_str);
                            $data = json_decode($json_str, true);
                            $data && $data_arr = $data['data'];
                            if(count($data_arr)>0){
                                $this->init_json($app_id, $model, $data_arr);
                            }else {
                                if($json_str){
                                    echo "---$realfile---JSON STR BEGIN------\n\n";
                                    echo $json_str;
                                    echo "---$realfile---JSON STR END------\n\n";
                                    logger::info($realfile.' JSON_DECODE ERROR');
                                }else{
                                    logger::info($realfile.' NO DATA');
                                }

                            }
                            break;
                        default:
                            # code...
                            break;
                    }
                }
            }
            restore_error_handler();
            closedir($handle);
        }
    }//End Function
    /**
     * 通过php数组进行 conf初始化.
     *
     * @param $app_id
     * @param $setting 数组
     */
    public function init_setting($app_id, $setting)
    {
        $app = app::get($app_id);
        if (is_array($setting)) {
            foreach ($setting as $key => $value) {
                $app->setConf($key, $value);
            }
        }
    }//End Function
    /**
     * 通过序列化数据 进行演示数据安装.
     *
     * @param $app_id
     * @param $model
     * @param $file 序列化数据文件
     */
    public function init_sdf($app_id, $model, $file)
    {
        $handle = fopen($file, 'r');
        if ($handle) {
            while (!feof($handle)) {
                $buffer .= fgets($handle);
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && $model == 'goods') {
                    $p = preg_match_all('/i:\d{8,12};/', $buffer, $out);
                    if ($p) {
                        foreach ($out[0] as $val) {
                            $str = explode(':', $val);
                            if ($str[1] < 2147483647) {
                                continue;
                            }
                            $num = strlen(strval($str[1])) - 1;
                            $s = explode(';', $str[1]);
                            $pattern[] = '/'.$val.'/';
                            $replacement[] = 's:'.$num.':"'.$s[0].'";';
                        }
                        $buffer = preg_replace($pattern, $replacement, $buffer);
                    }
                }
                if (!($sdf = unserialize($buffer))) {
                    continue;
                }
                app::get($app_id)->model($model)->db_save($sdf);
                $buffer = '';
            }
            fclose($handle);
        }
    }//End Function
    /**
     * 通过json 进行演示数据安装.
     *
     * @param $app_id
     * @param $model
     * @param $data_arr php 表数组
     */
    public function init_json($app_id, $model, $data_arr)
    {
        $table_name = 'vmc_'.$app_id.'_'.$model;
        $ct_arr = app::get('base')->getConf('init_demo_complete');
        if (!$ct_arr || !is_array($ct_arr)) {
            $ct_arr = array();
        }
        if (in_array($table_name, $ct_arr)) {
            logger::info($table_name.' Has insert Demodata.');

            return true;
        }
        logger::info('Inserting Datatable '.$table_name.' Start...');
        $db = vmc::database();
        $ts = $db->beginTransaction();
        foreach ($data_arr as $sdf) {
            
            if (!app::get($app_id)->model($model)->db_save($sdf)) {
                $db->rollback();
                logger::warning('出现异常!'.$table_name.'DEMO数据导入失败!');

                return fasle;
            }
        }
        $ct_arr[] = $table_name;
        app::get('base')->setConf('init_demo_complete', $ct_arr);
        $db->commit($ts);
        logger::info('Inserting Datatable '.$table_name.' Complete!');
    }

    public function _errorHandler($errno, $errstr, $errfile, $errline)
    {
        switch ($errno) {
            case E_ERROR:
            case E_USER_ERROR:
            logger::info(sprintf('error: %s, severity:%s, file:%s, line:%s', $errstr, $errno, $errfile, $errline));
            exit;
            break;
            default:
            //logger::info(sprintf('error: %s, severity:%s, file:%s, line:%s', $errstr, $errno, $errfile, $errline));
            break;
        }
    }

    private function json_fix($json){

    	//		http://php.net/manual/ru/function.json-decode.php#112735
    	//	comments
    	$json = preg_replace('~(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)~', '', $json);
    	//	trailing commas
    	$json=preg_replace('~,\s*([\]}])~mui', '$1', $json);
    	//	empty cells
    	$json = preg_replace('~(.+?:)(\s*)?([\]},])~mui', '$1null$3', $json);
    	// $json = preg_replace('~.+?({.+}).+~', '$1', $json);
        //$json = str_replace("\\","\\\\",$json);
        return $json;
    }
}//End Class
