<?php

namespace HtmlBuilder\Components;

use HtmlBuilder\Element;

class Table extends Element
{
    public $fixedTop       = false;
    public $height         = false; // 最大高度如果定义，那么则固定头部 fixedTop = 1
    
    public $fixedLeft      = true;
    public $fixedRight     = true;
    
    public $canEdit        = true;   // 是否可以编辑，1 表示永远可用，字符串表示数据行的字段为真，比如 canEdit = 'canEdit' ,那么数据行存在 canEdit 字段，并且字段为true时则显示编辑，否则不显示
    public $canDelete      = false;   // 是否可以删除，1 表示永远可用，字符串表示数据行的字段为真
    public $selectMode     = 'single'; // 选择模式, null:不可选择，single:单选，multi:多选
    public $query          = []; // 当前查询条件，filter[ [field,operation,value],... ], sort[ [field,asc|desc],... ], limit[start, end]
    public $fields         = [];// 字段定义[ [name:字段名, text:显示名, tooltip:提示符, sort:1(是否可排序), filter:1(是否可以添加过滤条件), edit:1(是否可编辑), width:null, show:1, type:编辑器类型, params:[编辑器的参数，必须、验证器等], icon:图标, class:额外的样式] ]

    public $createApi      = ''; // 创建数据的API
    public $queryApi       = ''; // 获得数据的API
    public $updateApi      = ''; // 编辑数据的API 其中 {id} 会被替换成真实 ID
    public $deleteApi      = ''; // 删除数据的API 其中 {id} 会被替换成真实 ID

    public $verticalLine   = ''; // 垂直线
    public $horizontalLine = ''; // 水平线
    public $primary = 'id'; // 主键
    
    public function __construct(string $name)
    {
        parent::__construct('table');
        $this->name = $name;
    }
    
    public function fields(array $fields){
        $this->fields = $fields;
        return $this;
    }
    
    public function queryApi(string $api){
        $this->queryApi = $api;
        return $this;
    }
    
    public function query(array $query){
        $this->query = $query;
        return $this;
    }
    
 
}