<?php

namespace HtmlBuilder\Layouts;

use HtmlBuilder\Element;

class Columns extends Element{
    /**
     * @var int 列宽多少，范围 [0,12]
     */
    public $width;
    /**
     * @var int 偏移多少，范围 [0,12]
     */
    public $offset;
    /**
     * @var int 前推多少，范围 [0,12]
     */
    public $push;
    /**
     * @var int 后拉多少，范围 [0,12]
     */
    public $pull;
    public function __construct()
    {
        parent::__construct('columns');
    }
    
    /**
     * 添加一个列
     * @param Element $element
     * @param int     $width
     * @param int     $offset
     * @param int     $push
     * @param int     $pull
     * @return $this
     */
    public function column(Element $element,int $width, int $offset=0, int $push=0, int $pull=0){
        $this->add((new static())->width($width)->offset($offset)->pull($pull)->push($push)->add($element));
        return $this;
    }
}

