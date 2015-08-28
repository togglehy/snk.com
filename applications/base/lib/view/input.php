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



class base_view_input{

    function input_bool($params){
        $params['type'] = 'radio';
        $value = $params['value'];
        unset($params['value']);
        $id = $params['id'];
        $params['id']=$id.'-t';
        $return = utils::buildTag($params,'input value="true"'.(($value==='1' || $value==='true' || $value===true || $value===1)?' checked="checked"':'')).'<label for="'.$params['id'].'">'.'是'.'</label>';

        $params['id']=$id.'-f';
        //$return .='<br />'.utils::buildTag($params,'input value="false"'.(($value==='false' || !$value )?' checked="checked"':'')).'<label for="'.$params['id'].'">否</label>';
        $return .= utils::buildTag($params,'input value="false"'.(($value==='0' || $value==='false' || !$value)?' checked="checked"':'')).'<label for="'.$params['id'].'">'.'否'.'</label>';
        return $return.'<input type="hidden" name="_DTYPE_BOOL[]" value="'.htmlspecialchars($params['name']).'" />';
    }

    function input_color($params){
        $domid = $params['id'];
        if($params['value']==''){
            $params['value']='#cccccc';
        }

        return $this->input_default($params);
    }



    function input_default($params){
        $ignore = array(
            'password'=>1,
            'file'=>1,
            'hidden'=>1,
            'checkbox'=>1,
            'radio'=>1,
        );
        if(!isset($ignore[$params['type']])){
            $params['type'] = 'text';
        }

        return utils::buildTag($params,'input class="form-control '.($params['class'] ? ' '.$params['class'] : '').'"');
    }


    function input_gender($params){
        $params['type'] = 'radio';
        $value = $params['value'];
        unset($params['value']);
        $id = $params['id'];

        $return = '<label class="control-label">'.utils::buildTag($params,'input value="male"'.($value=='male'?' checked="checked"':'')).'男'.'</label>';
        $return .= '&nbsp;&nbsp;<label class="control-label">'.utils::buildTag($params,'input value="female"'.($value=='female'?' checked="checked"':'')).'女'.'</label>';
        return $return;
    }
    function input_intbool($params){
        $params['type'] = 'radio';
        $value = $params['value'];
        unset($params['value']);
        $id = $params['id'];

        $return = '<label class="control-label">'.utils::buildTag($params,'input value="1"'.($value==1?' checked="checked"':'')).'是'.'</label>';

        $return .='&nbsp;&nbsp;<label class="control-label">'.utils::buildTag($params,'input value="0"'.($value==0?' checked="checked"':'')).'否'.'</label>';
        return $return;
    }


    function input_radio($params){

        $params['type'] = 'radio';
        $options = $params['options'];
        $value = $params['value'];
        unset($params['options'],$params['value']);
        $input_tpl = utils::buildTag($params,'input ',true);

        $htmls = array();

        foreach($options as $k=>$item){

            if($value==$k){
                $html = str_replace('/>',' value="'.htmlspecialchars($k).'" checked="checked" />',$input_tpl);
            }else{
                $html = str_replace('/>',' value="'.htmlspecialchars($k).'" />',$input_tpl);
            }
            $html = str_replace('id="'.$id_base.'"', 'id="'.$id.'"', $html);
            $htmls[]= '<label class="label-control">'.htmlspecialchars($item).$html.'</label>';
        }
        $params['separator'] = $params['separator']?$params['separator']:'<br>';
        $return = implode($params['separator'],$htmls);

        return $return;
    }

    function input_checkbox($params){

        $params['type'] = 'checkbox';
        $options = $params['options'];
        $value = $params['value'];
        unset($params['options'],$params['value']);
        $input_tpl = utils::buildTag($params,'input ',true);

        $htmls = array();

        foreach($options as $k=>$item){

            if($value==$k){
                $html = str_replace('/>',' value="'.htmlspecialchars($k).'" checked="checked" />',$input_tpl);
            }else{
                $html = str_replace('/>',' value="'.htmlspecialchars($k).'" />',$input_tpl);
            }
            $html = str_replace('id="'.$id_base.'"', 'id="'.$id.'"', $html);
            $htmls[]= '<label class="label-control">'.htmlspecialchars($item).$html.'</label>';
        }
        $params['separator'] = $params['separator']?$params['separator']:'&nbsp;&nbsp;';
        $return = implode($params['separator'],$htmls);

        return $return;
    }

    function input_select($params){

        if(is_string($params['options'])){
            $ui = new base_component_ui($this);
            $params['remote_url'] = $params['options'];
            $params['options'] = array($params['value']=>$params['value']);
        }
        if($params['rows']){
            foreach($params['rows'] as $r){
                $step[$r[$params['valueColumn']]]=intval($r['step']);
                $options[$r[$params['valueColumn']]] = $r[$params['labelColumn']];
            }
            unset($params['valueColumn'],$params['labelColumn'],$params['rows']);
        }else{
            $options = $params['options'];
            unset($params['options']);
        }
        $params['name'] = $params['search']?'_'.$params['name'].'_search':$params['name'];
        $params['class'] .= ' form-control';
        $value = $params['value'];
        unset($params['value']);
        $html=utils::buildTag($params,'select',false);
        if(!$params['required']){
            $html.='<option></option>';
        }
        foreach((array)$options as $k=>$item){
            if($k==='0' || $k===0){
                $selected = ($value==='0' || $value===0);
            }else{
                $selected = ($value==$k);
            }
            $t_step=$step[$k]?str_repeat('&nbsp;',($step[$k]-1)*3):'';
            $html.='<option'.($params['noselect']?' disabled=true ':' ').($selected?' selected="selected"':'').' value="'.htmlspecialchars($k).'">'.$t_step.htmlspecialchars($item).'</option>';
        }
        $html.='</select>';
        return $html.$script;
    }

    function input_textarea($params){
        $value = $params['value'];

        // $params['style'].=';width:'.($params['width']?$params['width']:'400').'px;';
        // $params['style'].=';height:'.($params['height']?$params['height']:'300').'px;';
        $params['class'] = 'form-control';
        unset($params['width'],$params['height'],$params['value']);
        return utils::buildTag($params,'textarea',false).htmlspecialchars($value).'</textarea>';
    }

    function input_date($params){

        if(is_numeric($params['value'])){
            $params['value'] = date('Y-n-j',$params['value']);
        }
        if(isset($params['concat'])){
            $params['name'] .= $params['concat'];
            unset($params['concat']);
        }
        $name = $params['name'];
        $value = $params['value'];
        $id = base_component_ui::new_dom_id();
        $_return = <<<EOF
        <div id="$id" class="input-group input-medium date date-picker">
				<input type="text" class="form-control"  name="$name" value="$value">
				<span class="input-group-btn">
				<button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
				</span>
		</div>
        <script charset="utf-8">
            $('#$id').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "left",
                language:'zh-CN',
                format:'yyyy-mm-dd',
                autoclose: true
            });
        </script>
EOF
        ;
        return $_return;
    }

    function input_time($params){

        $name = $params['name'];
        $value = $params['value'];
        $placehoder = $params['placehoder'];
        $id = base_component_ui::new_dom_id();
        if($value && !strpos($value,':')){
            $value = date('Y-m-d H:i',$value);
        }
        $_return = <<<EOF
        <div id="$id" class="input-group date">
				<input type="text" class="form-control" placehoder="$placehoder"  name="$name" value="$value">
				<span class="input-group-btn">
    				<button class="btn default date-set" type="button">
                    <i class="fa fa-clock-o"></i>
                    </button>
				</span>
		</div>
        <script charset="utf-8">
            $('#$id').datetimepicker({
    			autoclose: true,
    			isRTL: Metronic.isRTL(),
    			language:'zh-CN',
    			format: "yyyy-mm-dd hh:ii",
                pickerPosition: (Metronic.isRTL() ? "bottom-right" : "bottom-left")
		});
        </script>
EOF;
        return $_return;
    }






}
