<?php

namespace HtmlBuilder\Components;

use HtmlBuilder\Element;

class MultiSelect extends Element
{
    public $selects = [];
    public $rootApi = '';
    public $style;
    
    public function __construct(string $rootApi, string $style='single')
    {
        parent::__construct('multiSelect');
        $this->style = $style;
        $this->rootApi = $rootApi;
        $this->id = 'E'.uniqid();
    }
    
    /**
     * items 和 subItemsApi 的每一个元素特定key： 必须（text：显示的文本, value：选择时的值）,可选（icon：文本旁边的图标, group：是否为分组标签）
     * @param string      $name
     * @param null        $default
     * @param int         $maxSelect
     * @param string|null $subItemsApi
     * @return $this
     */
    public function addSelect(string $name, $default=null, int $maxSelect=1, string $subItemsApi=null): self
    {
        $item = [
            'name'      => $name,
            'maxSelect' => $maxSelect,
        ];
        if($default !== null) {
            $item['default'] = $default;
        }
        if($subItemsApi !== null) {
            $item['subItemsApi'] = $subItemsApi;
        }
        
        $this->selects[] = $item;
        
        return $this;
    }
}