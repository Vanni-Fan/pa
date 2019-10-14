<?php

class AdminHelper{
    /**
     * @param        $menu 要展示的菜单
     * @param string $current_url 当前URL
     * @param bool   $frist 是否是第一个菜单
     * @param bool   $show 是显示还是隐藏ul元素
     *
     */
    public static function outputMenu(&$menu, array $path, int $rule_id=0, int $level=0, array $menuBadges=[])
    {
        if($level){ // 子菜单:打开条件 当前的 parent_id 等于 rule_id
            $show = ($path[$level]['parent_id']??null)==$rule_id? true : false;
            echo '<ul class="treeview-menu"' .($show?' style="display: block;"':''). '>';
        }else{ // 主菜单
            echo '<ul class="sidebar-menu" data-widget="tree">';
        }

        foreach ($menu as $i) {
            $has_sub = !empty($i['sub']);
            if($has_sub){
                $active = ($path[$level]['rule_id']??0)==$i['rule_id']? ' menu-open' : '';
            }else{
                $active = ($path[$level]['rule_id']??0)==$i['rule_id']? ' active' : '';
            }
            #echo "\n<!-- $active -->\n";
//            echo '<li class="', ($has_sub ? 'treeview' : ''), $active, '"><a href="', ($i['url'].'?rule_id='.$i['rule_id']), '">', "\n";
            $url = $i['url_suffix'] ?: (PA_URL_PATH.'menu/'.$i['rule_id'].'/index');
            echo '<li class="', ($has_sub ? 'treeview' : ''), $active, '"><a href="', $url , '">', "\n";
            if ($i['icon']) echo '<i class="', $i['icon'], '"></i>', "\n";
            echo '<span>', $i['name'], '</span>', "\n";
            if ($has_sub || !empty($menuBadges[$i['rule_id']])) {
                echo '<span class="pull-right-container">', "\n";
                echo $menuBadges[$i['rule_id']]??'', "\n";
                if ($i['sub']) echo '<i class="fa fa-angle-left pull-right"></i>', "\n";
                echo '</span>', "\n";
            }
            echo '</a>', "\n";
            if ($has_sub) self::outputMenu($i['sub'], $path, $i['rule_id'], $level+1, $menuBadges);
            echo '</li>', "\n";
        }
        echo '</ul>';
    }
    
    public static function getExtensionsHtml(array $all_extends, array $default, array $wrapper=['','']):array{
        $out = [];
        foreach($all_extends as $rule_id => $extends) {
            $tmp = $wrapper[0];
            foreach($extends as $extend) {
                $tmp .= '<div class="form-group">';
//                $tmp .= '<label for="" class="col-sm-4 control-label must-start">' . $extend['name'] . '【' . $extend['extend_name'] . '】</label>';
                $tmp .= '<label for="" class="col-sm-4 control-label must-start">' . $extend['name'] . '</label>';
                
                $no_items  = $extend['extend_items_type'] === 'null';                                       # 是否没有备选值
                $single    = $extend['extend_value_type'] === 'text';                                       # 是否为单一值
                $input     = '';                                                                            # 输入的组件
                $form_name = 'extend['.$extend['rule_id'].']['.$extend['extend_name'].']';                  # 表单名
                $id        = strtr($form_name,['['=>'_',']'=>'_']);                                         # 表单ID
                if(isset($default[$extend['rule_id']][$extend['extend_name']])){
                    $def_value = $default[$extend['rule_id']][$extend['extend_name']];
                }else{
                    $def_value = json_decode($extend['extend_value'],1);
                }
                
                if($no_items){ # 没有备选值，那么直接让用户输入
                    $input .= '<input name="'.$form_name.'" type="text" class="form-control" id="'.$id.'" value="'.$def_value.'">';
                }else{ # 有备选值，则需要输出 select 或 checkbox 或 radio
                    $items = $extend['extend_items_type']==='callback' ? call_user_func($extend['extend_items']) : json_decode($extend['extend_items'],1);
                    $start = $end = $template = $content = '';
                    if(count($items)<5){ # 使用 checkbox or radio
                        $template = '<label><input value="_KEY_" name="'.$form_name.($single?'':'[]').'" type="'.($single?'radio':'checkbox').'" _CHECKED_>_VAL_ 　</label>';
                    }else{ # 使用 select
                        $start = '<select class="form-control" name="'.$form_name.($single?'':'[]').'" id="'.$id.'" '.($single?'':' multiple="multiple" size="5"').' >';
                        $end   = '</select>';
                        $template   = '<option value="_KEY_" _SELECTED_>_VAL_</option>';
                    }
                    
                    foreach($items as $key=>$val){
                        $replace = ['_KEY_'=>$key, '_VAL_'=>$val];
                        if($single){
                            $matched = $def_value == $key;
                            $replace['_CHECKED_']  = $matched ? 'checked' : '';
                            $replace['_SELECTED_'] = $matched ? 'selected' : '';
                        }else{
                            $matched = in_array($key, $def_value);
                            $replace['_CHECKED_']  = $matched ? 'checked' : '';
                            $replace['_SELECTED_'] = $matched ? 'selected' : '';
                        }
                        $content .= strtr($template,$replace);
                    }
                    $input .= $start.$content.$end;
                }
                $tmp .= '<div class="col-sm-8">' . $input . '</div>';
                $tmp .= '</div>';
            }
            $tmp .= $wrapper[1];

            $out[$rule_id] = $tmp;
        }
        return $out;
    }
    
}
