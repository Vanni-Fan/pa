# 解析器
## AdminLte 解析器
### 方法列表
#### parse(Element ...$elements):string
> 分析元素

| 参数 | 功能 |
| :--- | :--- |
| $element | 需要分析的元素 |

#### setResources(adminBaseController $controller):void
> 设置相关的HTML，JS，CSS到Phalcon的View对象中

| 参数 | 功能 |
| :--- | :--- |
| adminBaseController $controller | 当前的Controller对象 |

#### css(string $file):void
> 添加CSS文件

| 参数 | 功能 |
| :--- | :--- |
| $file | CSS文件 |

#### style(string $content):void
> 添加样式

| 参数 | 功能 |
| :--- | :--- |
| $content | 样式 |

#### script(string $content):void
> 添加脚本

| 参数 | 功能 |
| :--- | :--- |
| $content | 脚本 |

#### js(string $file):void
> 添加JS文件

| 参数 | 功能 |
| :--- | :--- |
| $file | JS文件 |

#### html(string $html):void
> 添加HTML内容

| 参数 | 功能 |
| :--- | :--- |
| $html | HTML内容 |




