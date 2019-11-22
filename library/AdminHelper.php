<?php
use HtmlBuilder\Forms\Input;
use HtmlBuilder\Forms\Select;
use HtmlBuilder\Forms\TextArea;
use HtmlBuilder\Forms\File;
use HtmlBuilder\Forms\Check;
use Phalcon\Mvc\Controller;
use HtmlBuilder\Parser\AdminLte\Parser;

class AdminHelper{
    /**
     * @param        $menu 要展示的菜单
     * @param string $current_url 当前URL
     * @param bool   $frist 是否是第一个菜单
     * @param bool   $show 是显示还是隐藏ul元素
     *
     */
    public static function outputMenu(&$menu, array $path, int $menu_id=0, int $level=0, array $menuBadges=[])
    {
        if($level){ // 子菜单:打开条件 当前的 parent_id 等于 menu_id
            $show = ($path[$level]['parent_id']??null)==$menu_id? true : false;
            echo '<ul class="treeview-menu"' .($show?' style="display: block;"':''). '>';
        }else{ // 主菜单
            echo '<ul class="sidebar-menu" data-widget="tree">';
        }

        foreach ($menu as $i) {
            $has_sub = !empty($i['sub']);
            if($has_sub){
                $active = ($path[$level]['menu_id']??0)==$i['menu_id']? ' menu-open' : '';
            }else{
                $active = ($path[$level]['menu_id']??0)==$i['menu_id']? ' active' : '';
            }
            #echo "\n<!-- $active -->\n";
//            echo '<li class="', ($has_sub ? 'treeview' : ''), $active, '"><a href="', ($i['url'].'?menu_id='.$i['menu_id']), '">', "\n";
            $url = $i['url_suffix'] ?: (PA_URL_PATH.'menu/'.$i['menu_id'].'/index');
            echo '<li class="', ($has_sub ? 'treeview' : ''), $active, '"><a href="', $url , '">', "\n";
            if ($i['icon']) echo '<i class="', $i['icon'], '"></i>', "\n";
            echo '<span>', $i['name'], '</span>', "\n";
            if ($has_sub || !empty($menuBadges[$i['menu_id']])) {
                echo '<span class="pull-right-container">', "\n";
                echo $menuBadges[$i['menu_id']]??'', "\n";
                if ($i['sub']) echo '<i class="fa fa-angle-left pull-right"></i>', "\n";
                echo '</span>', "\n";
            }
            echo '</a>', "\n";
            if ($has_sub) self::outputMenu($i['sub'], $path, $i['menu_id'], $level+1, $menuBadges);
            echo '</li>', "\n";
        }
        echo '</ul>';
    }
    
    public static function getConfigsHtmlGroup(array $configs, array $default, Controller $controller):array{
        if(empty($configs)) return [];
        $parser = new Parser();
        $contents = [];
        foreach($configs as $config) {
            $menu_id = $config['menu_id'] ?: 0;
            if(!isset($contents[$menu_id])) $contents[$menu_id] = '';
            $contents[$menu_id] .= $parser->parse(self::configToHtmlBuilder($config, $default[$config['menu_id']] ?? null));
        }
        $parser->setResources($controller);
        return $contents;
    }

    public static function getConfigsHtml(array $configs, array $default, Controller $controller, string $form_prefix = 'menu_configs'):string{
        if(empty($configs)) return '';
        $parser = new Parser();
        $contents = '';
        foreach($configs as $config) {
            $contents .= $parser->parse(self::configToHtmlBuilder($config, $default[$config['menu_id']] ?? null, $form_prefix));
        }
        $parser->setResources($controller);
        return $contents;
    }

    # 将 configs 中信息转换成 HtmlBuilder 对象
    public static function configToHtmlBuilder($a_config_of_configs_table, $default_value=null, string $form_prefix = 'menu_configs'){
        $row = $a_config_of_configs_table;
        $form_name = $form_prefix.'['.($row['menu_id']?:0).']['.$row['var_name'].']'.(in_array($row['var_type'],['list','hash'])?'[]':'');
        if($row['var_type'] !== 'text'){
            $row['var_default'] = json_decode($row['var_default'],1);
        }
        $default = $default_value[$row['var_name']]??$row['var_default'];
        switch($row['options_type']){
            case 'Input:text':
            case 'Input:mail':
            case 'Input:url':
            case 'Input:tel':
            case 'Input:mobile':
            case 'Input:currency':
            case 'Input:number':
            case 'Input:password':
            case 'Input:time':
            case 'Input:date':
            case 'Input:color':
                $sub_type = substr($row['options_type'],6);
                return new Input($form_name, $row['name'], $default, $sub_type);
                break;
            case 'Select:single':
            case 'Select:multiple':
            case 'Select:tags':
                $params   = json_decode($row['options'],1);
                $sub_type = substr($row['options_type'],7);
                $obj = new Select($form_name, $row['name'], $default);
                if($sub_type==='tags'){
                    $sub_type = 'multiple';
                    $obj->isTags = true;
                }
                $obj->subtype = $sub_type;
                $obj->choices(self::getChoices($params));
                return $obj;
                break;
            case 'TextArea:simple':
            case 'TextArea:ckeditor':
            case 'TextArea:wyihtml5':
                return new TextArea($form_name, $row['name'], $default);
                break;
            case 'File:image':
            case 'File:file':
            case 'File:multipeImages':
            case 'File:multipeFiles':
                $obj = new File($form_name,$row['name']);//, $row['name'],$default_value??$row['var_default']);
                if(strrpos($row['options_type'],'mage')){
                    $obj->accept('image/*');
                    $obj->corpWidth = 200;
                }
                return $obj;
                break;
            case 'Check:checkbox':
            case 'Check:radio':
                $params   = json_decode($row['options'],1);
                $sub_type = ($row['var_type'] === 'text') ? 'radio' : 'checkbox';
                $obj = new Check($form_name, $row['name'], $default, $sub_type);
                $obj->choices(self::getChoices($params));
                $obj->iCheckStyle = $params['style']??'blue';
                $obj->flat = $params['flat']??'square';
                $obj->colCount = $params['colCount']??3;
                return $obj;
                break;
            default:
                throw new \Exception('没有定义'.print_r($row,1));
        }
    }

    private static function getChoices($options){
        if(isset($options['options'])){ // 直接用它的值
            $choices = $options['options'];
        }elseif(isset($options['options_fun'])){ // 通过回调获得选项值
            try {
                $choices = call_user_func($options['options_fun']);
            }catch(\Exception $e){
                $choices = ['你配置的函数无法正常返回'];
            }
        }elseif(isset($options['options_url'])){
            try {
                $choices = json_decode(file_get_contents($options['options_url']), 1);
            }catch(\Exception $e){
                $choices = ['你配置的URL无法获取内容'];
            }
        }else{
            $choices = ['你还没有配置相应的值'];
        }

        $fixed_choices = [];
        foreach($choices as $key=>$item){
            if(is_array($item)){
                $fixed_choices[] = $item;
            }else{
                $fixed_choices[] = ['value'=>is_int($key)?$item:$key,'text'=>$item];
            }
        }
        return $fixed_choices;
    }
}
