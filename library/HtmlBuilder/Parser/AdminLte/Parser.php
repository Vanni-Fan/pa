<?php

namespace HtmlBuilder\Parser\AdminLte;
use HtmlBuilder\Element;
use PA;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt;

class Parser{
    private $js_files  = [];
    private $css_files = [];
    private $styles    = [];
    private $scripts   = [];
    
    public function getStyles(){
        return implode('',$this->styles);
    }
    public function getScripts(){
        return implode('',$this->scripts);
    }
    public function getJs(){
        return $this->js_files;
    }
    public function getCss(){
        return $this->css_files;
    }
    public function parse(Element $element){
        $_file = POWER_BASE_DIR.'library/HtmlBuilder/Parser/AdminLte/templates/'.$element->type.'.php';
        
        if(empty($element->id)) $element->id = 'E-'.uniqid();
        
        $parse = function()use($_file,$element){
            extract(get_object_vars($element),EXTR_OVERWRITE);
            require $_file;
        };
        
        ob_start();
        $parse(); // $parse->call($this);
        return ob_get_clean();
    }
    public function css($file){
        if(!isset($this->css_files[$file])) $this->css_files[$file] = $file;
    }
    public function style($type, $content){
        if(!isset($this->styles[$type])){
            $content = preg_replace('#^(\s*<style[^>]*>)|(</style>\s*)$#i','',$content);
            $this->styles[$type] = $content;
        }
    }
    public function script($content){
        $content = preg_replace('#^(\s*<script[^>]*>)|(</script>\s*)$#i','',$content);
        $this->scripts[] = $content;
    }
    public function js($file){
        if(!isset($this->js_files[$file])) $this->js_files[$file] = $file;
    }
}