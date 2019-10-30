<?php

namespace HtmlBuilder\Parser\AdminLte;
use HtmlBuilder\Element;
use Power\Controllers\AdminBaseController;

class Parser{
    /**
     * @var array 分析【parse()】后获得的js文件，通过 $this->js(...）添加的，文件名一样会去重
     */
    private $js_files  = [];
    /**
     * @var array 分析【parse()】后获得的css文件，通过 $this->css(...）添加的，文件名一样会去重
     */
    private $css_files = [];
    /**
     * @var array 分析【parse()】后获得的样式片段，通过 $this->style(...）添加的
     */
    private $styles    = [];
    /**
     * @var array 分析【parse()】后获得的脚本片段，通过 $this->script(...）添加的
     */
    private $scripts   = [];
    /**
     * @var array 缓存输出 HTML 内容，即相同HTML内容去重输出
     */
    private $html = [];
    /**
     * 获得所有的样式片段
     * @return string
     */
    public function getStyles(){
        return implode('',$this->styles);
    }
    
    /**
     * 获得所有的脚本片段
     * @return string
     */
    public function getScripts(){
        return implode(';',$this->scripts);
    }
    
    /**
     * 获得所有的脚本文件
     * @return array
     */
    public function getJs(){
        return $this->js_files;
    }
    
    /**
     * 获得所有的样式文件
     * @return array
     */
    public function getCss(){
        return $this->css_files;
    }
    
    /**
     * 分析元素，记得样式和脚本到对应的成员变量，并返回分析后的HTML片段
     * @param Element ...$elements
     * @return string
     */
    public function parse(Element ...$elements):string {
        $out = '';
        ob_start();

        foreach($elements as $element) {
            $template_dir  = POWER_BASE_DIR . 'library/HtmlBuilder/Parser/AdminLte/templates/';
            $template_file = $template_dir . $element->type . '.php';
            if (!file_exists($template_file)) $template_file = $template_dir . 'default.php';
    
            if (empty($element->id)) $element->id = 'HB_' . uniqid();
    
            //        $this->events[] = ['event'=>$event_name, 'code'=>$js_code, 'selector'=>$selector];
            // $('# $id $selector').on('event', function(event){  $code;  });
            foreach($element->events as $event){
                $this->script(
                    '$("#' . $element->id.$event['selector'] .'").on("'.$event['event'].'", function(event){ '.$event['code'].'; });'
                );
            }
//            ob_start();
//            $parse = function () use ($template_file, $element) {
                extract(get_object_vars($element), EXTR_OVERWRITE);
                require $template_file;
//            };
//                    $out .= ob_get_flush();//ob_get_clean();

//            ob_start();
//            $parse();
//             $parse->call($this);
        }
        $out .= ob_get_clean();
        return $out;
    }
    
    public function setResources(adminBaseController $controller){
        $controller->addStyle($this->getStyles());
        $controller->addScript($this->getScripts());
        foreach($this->getJs() as $js)   $controller->addJs($js);
        foreach($this->getCss() as $css) $controller->addCss($css);
    }
    
    /**
     * 设置css文件，文件相同会去重
     * @param string $file
     */
    public function css(string $file):void{
        if(!isset($this->css_files[$file])) $this->css_files[$file] = $file;
    }
    
    /**
     * 设置样式片段，内容相同会去重，前后的 <style> 标签会去除
     * @param $file
     */
    public function style(string $content):void{
        $hash = md5($content);
        if(!isset($this->styles[$hash])){
            $content = preg_replace('#^(\s*<style[^>]*>)|(</style>\s*)$#i','',$content);
            $this->styles[$hash] = $content;
        }
    }
    
    /**
     * 设置脚本片段，内容相同会去重，前后的 <script> 标签会去除
     * @param string $content
     */
    public function script(string $content):void{
        $hash = md5($content);
        if(!isset($this->scripts[$hash])){
            $content = preg_replace('#^(\s*<script[^>]*>)|(</script>\s*)$#i','',$content);
            $this->scripts[$hash] = $content;
//            $this->scripts[] = $content;
//            exit;
        }
        file_put_contents('d:/log.txt','[[[[[[[[[[[[[[[[HASH'.$hash.$content.print_r($this,1).']]]]]]]]]]]]]]]]]'."\n", FILE_APPEND);
        if(substr($content,0,9)==='/*vanni*/') {
            print_r($this->scripts);
            throw new \Exception('错误');
        }
    }
    
    /**
     * 添加js文件，文件相同会去重
     * @param string $file
     */
    public function js(string $file):void{
        if(!isset($this->js_files[$file])) $this->js_files[$file] = $file;
    }
    
    public function html(string $html):void{
        $hash = md5($html);
        if(!isset($this->html[$hash])){
            $this->html[$hash] = true;
            echo $html;
        }
    }
}