<?php

namespace HtmlBuilder\Parser\AdminLte;
use HtmlBuilder\Element;

class Parser{
    private $elements;
    private $js_files  = [];
    private $css_files = [];
    private $styles    = '';
    private $scripts   = '';
    private $contents  = '';
    public function __construct(Element $element) {
        $this->elements = $element;
    }
    
    public function parse(){
        /**
         * 1、 找到对应的模板
         * 2、 替换模板中的变量
         * 3、 如果有验证器，那么设置 scripts
         * 4、 如果需要css，js，那么设置 js_files,css_files
         */
        foreach($this->elements as $element){
            if($element->id) $element->id = uniqid('E');
            if($element->type === ''){
                $template = __DIR__.'/templates/'.$element->type.'.inc';

            }
        }
    }

    public function render(): string {
        # 变量替换 {{ aaa.bbb|par=xx }}
        $contents = preg_replace_callback('#\{\{(?!\}\})+\}\}#i',function($match){
            $key = $match[1];
            if(!isset($this->{$key})) return '';
            $value  = $this->{$key};
            $return = '';
            if($key==='elements' || $key==='attributes'){
                foreach($value as $_k=>$_v) $return .= $_v;
            }elseif($key==='validators'){
                # todo ?
            }else{
                $return = $value;
            }
            return $return;
        },$this->template);

        # 分析JS
        if(preg_match_all('#<script[^>]*>(.*)</script>#i', $contents, $matches)){
            if($matches[1]) $this->scripts .= "\n".$matches[1];
            if(preg_match('#src="([^"]+)"#i"', $matches[0],$match)) $this->js_files[] = $match[1];
        };

        # 分析CSS
        if(preg_match_all('#<style[^>]*>(.+)</style>#i', $contents, $matches)){
            $this->styles .= "\n".$matches[1];
        };
        if(preg_match_all('#<link.+href="([^"]+)".*>#i', $contents, $matches)){
            if(stripos($matches[1],'.css') || stripos($matches[0],'"stylesheet"')){
                $this->css_files[] = $matches[1];
            }
        }

    }


}