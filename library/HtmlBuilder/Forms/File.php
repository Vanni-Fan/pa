<?php

namespace HtmlBuilder\Forms;

use HtmlBuilder\Element;

class File extends Element
{
    public $subtype = 'single';
    public $statistics = 1;//true;
    public $accept = '*/*';//image/*';
    public $corpWidth  = null;  // 裁剪最大宽
    public $corpHeight = null;  // 裁剪最大高
    public $uploadUrl  = null;  // 上传图片的URL，Ajax使用，如果有剪切，这个是必须的，否则使用 form 的 action
    
    public function __construct($name)
    {
        parent::__construct('file');
        $this->name = $name;
    }
    
    public function setCorpSize(int $width, int $height){
        $this->corpWidth = $width;
        $this->corpHeight = $height;
        return $this;
    }
    
    public function uploadUrl(string $url){
        $this->uploadUrl = $url;
        return $this;
    }
    
    public function accept(string $accept){
        $this->accept = $accept;
        return $this;
    }
}