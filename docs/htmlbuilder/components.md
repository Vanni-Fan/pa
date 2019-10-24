# 组件
## 多级菜单 MultiSelect 
> 级联多选下拉列表
```php
<?php
$obj = \HtmlBuilder\Components::multiselect($style);
# 或者
$obj = new \HtmlBuilder\Components\MultiSelect($rootApi, $style);
```
### 属性列表
| 参数 | 默认值 | 功能 |
| :--- | :---: | :--- |
| $selects | [] | 全部子选项 |
| $rootApi | '' | 获取第一个选择项的API接口 |
| $style | 'single' | 下拉选择的样式，两种：single 或 multiSelect |

### 方法列表
#### 添加子选择项：addSelect
> addSelect(string $name, $default=null, int $maxSelect=1, string $subItemsApi=null): self

| 参数 | 功能 |
| :--- | :--- |
| $name | 子选择项名称 |
| $default | 默认值 |
| $maxSelect | 最大能选择几项 |
| $subItemsApi | 当选择发送改变时，获取子选项的API接口 |

## 表格 Table
> 表格对象
### 属性列表
| 参数 | 默认值 | 功能 |
| :--- | :---: | :--- |
| $fixedTop | false | 是否固定表头 |
| $height | false | 最大高度如果定义，那么则固定头部 fixedTop = 1 |
| $fixedLeft | false | `TODO` 固定左边栏  |
| $fixedRight | false | `TODO` 固定右边栏  |
| $canEdit | false | 编辑栏的标题，如果有表示可以编辑 |
| $editColWidth | 90 | 编辑栏宽 |
| $editCallback | ''  | 编辑的回调函数 |
| $canDelete | false | 数据是否可删除 |
| $selectMode | 'single' | 选择模式, null:不可选择，single:单选，multi:多选 |
| **$query** | null | Object，当前查询条件 |
| `$query.filter[x]`.key | null | 查询字段名 |
| `$query.filter[x]`.op | null | 操作符，比如 `< = <= > >= !=` |
| `$query.filter[x]`.val | null | 查询值 |
| `$query.sort`\[x\] | null | 排序规则，比如 `['a desc','b asc','c desc']` |
| `$query.limit`\[x,y\] | null | 限制条件 x=开始 y=结束，比如 `[1, 10]` |
| **$fields** | [] | \[Object\]，表格中的所有列定义 |
| `$fields[x]`.name |  | 字段名, 必须 |
| `$fields[x]`.text |  | 显示名, 必须 |
| `$fields[x]`.tooltip | '' | 提示符, 可选 |
| `$fields[x]`.sort | 1 | 是否可排序, 可选 |
| `$fields[x]`.filter | 1 | 是否可以添加过滤条件, 可选 |
| `$fields[x]`.width | null | 列宽，可选 |
| `$fields[x]`.show | 1 | 是否显示, 可选 |
| `$fields[x]`.render | null | JS的渲染器回调函数，可选 |
| `$fields[x]`.type | text | 编辑器类型, 可选 |
| `$fields[x]`.params | null | 编辑时附加的参数，比如\[编辑器的参数，必须、验证器等\], 可选 |
| `$fields[x]`.icon | '' | 表头图标, 可选 |
| `$fields[x]`.class | '' | 单元格上的自定义 |
| $queryApi | '' | 获得数据的API，必须 |
| $createApi | '' | 创建数据的API |
| $updateApi | '' | 编辑数据的API 其中 {id} 会被替换成真实 ID |
| $deleteApi | '' | 删除数据的API 其中 {id} 会被替换成真实 ID |
| $verticalLine | '' | 垂直线样式 `TODO` |
| $horizontalLine | '' | 水平线样式 `TODO` |
| $primary | 'id' | 主键，用于编辑和删除的替换ID |

## 时间区间 TimeRange
> 时间区间的选择
