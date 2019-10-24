# 布局元素
## 盒子 Box 
```php
<?php
$obj = \HtmlBuilder\Layouts::box('a box');
# 或者
$obj = new \HtmlBuilder\Layouts\Box('a box');
```
### 属性列表
| 参数 | 默认值 | 功能 |
| :--- | :---: | :--- |
| $header | '' | string or Element 盒子的头部 |
| $body | '' | string or Element 盒子的内容 |
| $footer | '' | string or Element 盒子的尾部 |
| $style | 'box-info' | 盒子样式：box-danger,box-primary, box-info, box-success, box-warning, box-danger, bod-gray |
| $canClose | false | 盒子是否有关闭按钮 |
| $canMove | false | 盒子是否可以拖拽移动 `TODO` |
| $canMini | false | 盒子是否可以最小化 |

## 列 Columns
### 属性列表
| 参数 | 默认值 | 功能 |
| :--- | :---: | :--- |
| $width | '' | 列宽多少，范围 `[0,12]` |
| $offset | '' | 偏移多少，范围 `[0,12]` |
| $push | '' | 前推多少，范围 `[0,12]` |
| $pull | '' | 后拉多少，范围 `[0,12]` |
### 方法列表
> column(Element $element,int $width, int $offset=0, int $push=0, int $pull=0):self

| 参数 | 功能 |
| :--- | :--- |
| $element | 列里面的元素 |
| $width | 列宽多少，默认0 |
| $offset | 偏移多少，默认0 |
| $push | 前推多少，默认0 |
| $pull | 后拉多少，默认0 |

## Tabs
### 方法列表
> tab(string $name, Element $element, $visible=false): self

| 参数 | 功能 |
| :--- | :--- |
| $name | 列宽多少，默认0 |
| $element | 列里面的元素 |
| $visible | 是否可视 |
