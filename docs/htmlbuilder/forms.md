# 表单元素
## 按钮 Button 
```php
<?php
$obj = \HtmlBuilder\Forms::button('a button');
# 或者
$obj = new \HtmlBuilder\Forms\Button('a button');
```
### 属性列表
| 参数 | 默认值 | 功能 |
| :--- | :---: | :--- |
| $flat | false | 是否为无圆角的方形 |
| $block | false | 是否单独占一行 |
| $style | 'info' | 按钮的样式：default, primary, success, info, danger, warning |
| $btnBeforeIcon | '' | string 或 Element 按钮前部分的图标、文字或者Element原始 |
| $btnAfterIcon | '' | string 或 Element 按钮后部分的图标、文字或者Element原始 |
| $badgeColor | '' | 按钮尾部角标的背景颜色，可选：maroon, purple, orange, navy, olive |
| $badge | '' | 按钮尾部上方的角标 |
| $action | 'button' | 按钮的点击事件，button:普通按钮,  reset:重置表单， submit:提交表单 |
| $vertical | false | 组合按钮时，是否垂直排列 |
| $subtype | 'default' | 按钮子类型：input（有输入的按钮）, group（组合按钮）, default |

## 选择项（多选或单选） Check
### 属性列表
| 参数 | 默认值 | 功能 |
| :--- | :---: | :--- |
| $style | 'purple' | iCheck的样式，10种之一：black,red,green,blue,aero,grey,orange,yellow,pink,purple |
| $flat | 'flat' | 是扁平(flat)还是圆角(square) |
| $colCount | 3 | 每行几列 |
| **$choices** | \[{}\] | \[Object\] 可以选项目 |
| `$choices\[x\]`.text | [] | 文本 |
| `$choices\[x\]`.value | [] | 值 |
| **$choicesByUrl** | {} | 获得选项的URL |
| `$choicesByUrl`.url |  | 获得选项的URL |
| `$choicesByUrl`.path |  | 返回体中的 xpath |
| `$choicesByUrl`.textName |  | 名称字段 |
| `$choicesByUrl`.valueName |  | 值字段 |
| $other | '' | string 或 Element 是否允许输入其他选项 |
| $selectAll | '' | 允许选择所有 |
| $none | '' | 允许不选 |

## 文件 File
### 属性列表
| 参数 | 默认值 | 功能 |
| :--- | :---: | :--- |
| $subtype | 'single' | 单个文件（single）还是多个文件（multiple） |
| $statistics | false | 是否显示文件的统计信息：比如尺寸，上载进度等 |
| $accept | '\*/\*' | 接受的文件类型，比如 image/* |
| $corpWidth | '' | 裁剪最大宽 |
| $corpHeight | '' | 裁剪最大高 |
| $uploadUrl | '' | 上传图片的URL，Ajax使用，如果有剪切，这个是必须的，否则使用 form 的 action |

## 表单 Form
### 属性列表
| 参数 | 默认值 | 功能 |
| :--- | :---: | :--- |
| $action | '' | 提交地址 |
| $method | 'post' | 提交方式 post or get |

## 输入框 Input
### 属性列表
| 参数 | 默认值 | 功能 |
| :--- | :---: | :--- |
| $inputMask | '' | 输入蒙版 |
| $statistics | false | 显示在右下角，字符长度，单词个数 |
| $inputBeforeIcon | false | 输入框前面的图标 |
| $inputAfterIcon | false | 输入框后面的图标 |

## 下拉选框 Select
### 属性列表
| 参数 | 默认值 | 功能 |
| :--- | :---: | :--- |
| $isTags | false | 是否为Tags，如果是Tags则可以输入新的值，否则只能选择一个值 |
| $multiple | false | 是否可以多选 |
| $rows | 1 | 行高 |
| $subtype | 'select' | 下拉选择的样式，select, select2 |
| **$choices** | \[{}\] | \[Object\] 可以选项目 |
| `$choices\[x\]`.text | [] | 文本 |
| `$choices\[x\]`.value | [] | 值 |
| **$choicesByUrl** | {} | 获得选项的URL |
| `$choicesByUrl`.url |  | 获得选项的URL |
| `$choicesByUrl`.path |  | 返回体中的 xpath |
| `$choicesByUrl`.textName |  | 名称字段 |
| `$choicesByUrl`.valueName |  | 值字段 |
| $other | '' | string 或 Element 是否允许输入其他选项 |

## 文本域 TextArea
### 属性列表
| 参数 | 默认值 | 功能 |
| :--- | :---: | :--- |
| $rows | 1 | 行高 |
| $subtype | 'simple' | 富编辑器样式： simple,  ckeditor, wysihtml5 |
