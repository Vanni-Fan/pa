<?php

namespace HtmlBuilder\Forms;

use HtmlBuilder\Element;

class File extends Element
{
    public $subtype = 'single';
    public $statistics = false;//true;
    public $accept = '*/*';//image/*';
    public $corpWidth  = null;  // 裁剪最大宽
    public $corpHeight = null;  // 裁剪最大高
    public $uploadUrl  = null;  // 上传图片的URL，Ajax使用，如果有剪切，这个是必须的，否则使用 form 的 action

    /**
     * File constructor.
     * @param string $name
     * @param string $label
     * @param null $value
     * @param string $subtype
     */
    public function __construct(string $name='', string $label='', $value=null, string $subtype='single')
    {
        parent::__construct('file', $name);
        $this->label = $label;
        $this->subtype = $subtype;
        if(null !== $value) $this->value = $value;
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