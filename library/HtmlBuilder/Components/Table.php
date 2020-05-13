<?php

namespace HtmlBuilder\Components;

use HtmlBuilder\Element;

/*
 * 填充表格的数据格式为：
 *  [
 *      'list'  => $data,
 *      'total' => 总行数
 *      'page'  => 当前是第几页,
 *      'size'  => 每页多少条记录,
 *  ]
 *
 * $data 为具体的表里面的数据，但有几个特殊字段：
 *     _CAN_SELECT_ : 表示本行是否能被选择
 *     _CAN_UPDATE_ : 表示本行是否能被编辑
 *     _CAN_DELETE_ : 表示本行是否能被删除
 */
class Table extends Element
{
    public $fixedTop       = false;
    public $height         = false; // 最大高度如果定义，那么则固定头部 fixedTop = 1
    
    public $fixedLeft      = true; // todo
    public $fixedRight     = true; // todo

    public $canMin         = true;  // 是否可用最小化
    public $canClose       = false; // 是否可用关闭
    public $canEdit        = false; // 编辑栏的标题，如果有表示可以编辑
    public $editColWidth   = 90;    // 编辑栏宽
    public $editCallback   = '';    // 编辑的回调函数
    
    public $canDelete      = false; // 数据是否可删除
    public $selectMode     = 'single'; // 选择模式, null:不可选择，single:单选，multi:多选

    public $query          = null; // 当前查询条件，filter[ [field,operation,value],... ], sort[ [field,asc|desc],... ], limit[start, end]
    public $fields         = [];

    public $createApi      = ''; // 创建数据的API
    public $queryApi       = ''; // 获得数据的API
    public $updateApi      = ''; // 编辑数据的API 其中 _ID_ 会被替换成真实 ID
    public $deleteApi      = ''; // 删除数据的API 其中 _ID_ 会被替换成真实 ID

    public $verticalLine   = ''; // 垂直线 todo
    public $horizontalLine = ''; // 水平线 todo
    public $primary        = 'id'; // 主键，用于编辑和删除的替换ID
    
    public function __construct(string $name)
    {
        parent::__construct('table');
        $this->name = $name;
    }

    /**
     * 字段定义[ [name:字段名, text:显示名, tooltip:提示符, sort:1(是否可排序), filter:1(是否可以添加过滤条件), edit:1(是否可编辑), width:null, show:1, type:编辑器类型, params:[编辑器的参数，必须、验证器等], icon:图标, class:额外的样式] ]
     * 定义表的每一列的显示情况，每一列可以包含下面字段：
     * [
     *  name:字段名, 必须
     *  text:显示名, 必须
     *  tooltip:提示符, 可选，默认无
     *  sort:是否可排序, 可选，默认0
     *  filter:是否可以添加过滤条件, 可选，默认0
     *  width:列宽，可选，默认无
     *  show:是否显示, 可选，默认1
     *  render:JS的渲染器回调函数，可选，默认无
     *  type:编辑器类型, 可选，默认text
     *  params:编辑时附加的参数，比如[编辑器的参数，必须、验证器等], 可选，默认无
     *  icon:表头图标, 可选，默认无
     *  class:单元格上的自定义，可选，默认无
     * ]
     * @param array $fields
     * @return $this
     */
    public function fields(array $fields){
        $this->fields = $fields;
        return $this;
    }
    
    public function queryApi(string $api){
        $this->queryApi = $api;
        return $this;
    }

    /**
     * 当前的查询条件，用来初始化过滤器
     * [
     *   filter:[
     *     [field,operation,value],...
     *   ],
     *   sort[
     *     [field,asc|desc],...
     *   ],
     *   limit[start, end]
     * ]
     * @param array $query
     * @return $this
     */
    public function query(array $query){
        $this->query = $query;
        return $this;
    }
    
 
}