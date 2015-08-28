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



class desktop_finder_builder_to_run_export{

    var $remote_file = null;

    //执行导出队列任务
    public function run(&$cursor_id,$params){
        $app_id = $params['app_id'];
        $model_name = $params['model'];
        $_POST = $params['post'];
        $oImportType = $params['oImportType'];
        $model = app::get($app_id)->model( $model_name );
        if( method_exists($model,'fgetlist_'.$_POST['_io_type']) ){
            /** 到处头部 **/
            $method_name = 'fgetlist_'.$_POST['_io_type'];
            while( $listFlag = $model->$method_name($data,$_POST,$offset,$_POST['_export_type']) ){
                $offset++;
            }
            $this->create_ftp_file($data,$params);
        }else{
            while( $listFlag = $oImportType->fgetlist($data,$model,$_POST,$offset,$_POST['_export_type']) ){
                $offset++;
                $this->create_ftp_file($data,$params,$offset);
            }
        }//end if
        return true;
    }

    public function create_ftp_file($data,$params,$page=1){
        $local_file = tempnam(DATA_DIR."/backup/",$params['model']);
        if(is_null($this->remote_file)){
            $this->remote_file = $params['model'].'.'.$params['post']['_io_type'];
        }
        $file = fopen($local_file,"w");
        $model = app::get($params['app_id'])->model( $params['model']);
        if(method_exists($model,'export_csv')){
            $rs = $model->export_csv($data);
        }else{
            $rs = '';
            if( is_array( $data ) ){
                $data = (array)$data;
                if( empty( $data['title'] ) && empty( $data['contents'] ) ){
                    $rs = implode( "\n", $data );
                }else{
                    if ($page==1)
                        $rs = $data['title']."\n".implode("\n",(array)$data['contents']);
                    else
                        $rs = implode("\n",(array)$data['contents']);
                }
            }else{
                $rs = (string)$data;
            }
        }
        fwrite($file,$rs);
        fclose($file);
        $obj_ftp = vmc::singleton('base_ftp');
        $resume = $obj_ftp->size($this->remote_file);
        $obj_ftp->nb_put($this->remote_file,$local_file,FTP_BINARY,$resume);
        unlink($local_file);
        return true;
    }

}
