### 分页 （Pagination.php）

### 工具 （Utils.php）
- **字段绑定**
    - 参考来源： [JMESPath](http://jmespath.org/tutorial.html)
    - 点号：[ **.** ] 指对象成员
    - 星号：[ * ] 指数组
    - 大括号：[ **\{,\}** ] ，仅用于对象提取的时候，键名的定义
    - 问号：[ **?** ]，仅用于对象提取的时候，当提取不成功时，继续往后找，如果始终没有找到，则返回最后一个值当默认值
    - 注意：**上面的特殊字符不支持转义，所以请使用简单参数**
    - 如果一个定义中有数组（星号）定义，那么一定返回数组，有多个星号则返回多维数组
    - 如果没有则返回具体的值
    - 比如下面的例子：
    ```text
    // 数据定义举例（可用于设置和提取）：
    a.b.c.d   // 对象
    a.b.*     // b是一个数组
    a.b.*.d   // b是一个数组，并且数组的元素是一个对象，且有一个d成员
    *.a.*     // 根元素为一个数组，里面每个元素都是一个对象，且对象有一个a成员，这个成员也是一个数组
    0.1.a.2.b // 根元素为数组，数组0也是数组，数组1下标里面是一个对象a，a为数组，数组的2下标为对象，对象下面有b成员
    
    $out = [];
    Utils::path2arr('a.b.c.d', '123', $out); // 设置 $out 的 a.b.c.d 为 123
    Utils::path2arr('a.c.*.d', '123', $out); // 设置 $out 的 a.c.0.d 为 123，*号在进行设置时是不能预计有多少记录的，所以只添加0下班，相当于

    $out['a']['b']['c']['d'] = '123';
    $out['a']['c'][0]['d'] = '123';

    // 只用于提取
    *.{user:user_id, tag:tags.0} // [ {user_id:1, tags:[1,2]}, {user_id:2, tags:[3,2,4]} ] 输出 [{user:1, tag:1}, ..]
    a.b.c.d ? a.d.e ? 123 // 如果 a.b.c.d 不存在就用 a.d.e，如果 a.d.e 也不存在就用 123
    a.b.c.d ? 123 // 如果 a.b.c.d 不存在就用 123
    
    // 对象提取举例：
    $source_data = {
        a:[
            {user_id:11, user_name:'aa', sex:1, age:20, tags:[1,2],     parents:[{name:A,user_id:1}]},
            {user_id:22, user_name:'bb', sex:0, age:10, tags:[4,2,3,6], parents:[{name:B,user_id:2},{name:b,user_id:33}]},
            {user_id:33, user_name:'cc', sex:1, age:30, tags:[3],       parents:[{name:C,user_id:3}]}
        ],
        b:123
    };
    $a = Utils::vpath('a.*.{user_id,name:user_name,tag:tags.0,parents:parents.*.name}', $source_data); 
    //返回 [{user_id:11,name:'aa',tag:1,parents:['A']},{user_id:22,name:'bb', tags:4, parents:['B','b']},...]
    $b = Utils::vpath('a.*.user_id', $source_data);   // 返回 [11,22,...]
    $c = Utils::vpath('a.*.user_name', $source_data); // 返回 ['aa','bb',...]
    $d = Utils::vpath('a.0.user_id', $source_data);   // 返回 11
    $e = Utils::vpath('a.1.user_name', $source_data); // 返回 bb
    
    // 查找不存在时的几种用法
    $f = Utils::vpath('a.1.not_exists_field?default_value', $source_data);    // 如果 a.1.not_exists_field字段不存在，则返回 default_value
    $g = Utils::vpath('a.1.not_exists_field', $source_data, 'default_value'); // 同上，区别在于上面的方法只能指定字符串的默认值，而此方法可以指定任意值

    $h = Utils::vpath('a.1.field_a?a.1.field_b?a.1.field_c?unkonw', $source_data); // 相当于
    $h = $source_data->a[1]['field_a'] ?? $source_data->a[1]['field_b'] ?? $source_data->a[1]['field_c'] ?? 'unkonw';
    $h = Utils::vpath('a.1.field_a?a.1.field_b?a.1.field_c', $source_data,'unkonw'); // 同上
    ```
- **函数调用**
    - 竖线：[ **|** ] 表示管道符，将前面的值作为后面函数调用的参数
    - 等号：[ **\=** ] 表示参数定义，如果函数需要有额外的参数，用它定义
    - 逗号：[ **,** ] 用来分割参数
    - 下划线：[ **\_** ] 第一个参数的占位符，默认情况下，前面的值会作为第一个参数传给调用函数，可以使用它来指定位置
    - 注意：**上面的特殊字符不支持转义，所以请使用简单参数**
    - 支持 **默认值** 和 **字段绑定**
    - 比如下面的例子：
    ```text
    // 假设 a.b.c 的值为 11
    a.b.c | func_a | func_b           // 函数调用为 func_b(func_a(11))
    a.b.c | func_a | func_b | func_c  // 函数调用为 func_c(func_b(func_a(11)))
    a.b.c | func_a=22,33              // 函数调用为 func_a(11,22,33)
    a.b.c | func_a=22,_,33            // 函数调用为 func_a(22,11,33)
    444 | func_a=333,stra,_           // 函数调用为 func_a(333,"stra",444)
    ```
- **模板替换**
    - 被中括号包裹的字段：  **[字段]** 
    - 支持 **字段绑定** 和 **函数调用** 
    ```js
    xxx[yyy]zzz
    xxx[a.b.c|func_a]zzz
    xxx[0.a.c|Static_class::static_func]zzz   // 中括号里面的内容会被替换成   字段绑定+函数调用   后的值
    xxx[1.2.c]zzz
    ```
- **条件判断**
    - JSON的数组形式
    - 至少3个元素： \[ **元素1**, **元素2**, **元素3** \]
        - **元素1** 和 **元素3** 位置可以互换，且支持 **字段绑定** 和 **函数调用** 
        - **元素2** 为比较操作符，只能是下面的值：
            - = 或者 == ：表示 **元素1** 等于 **元素3**
            - === ：表示 **元素1** 等于 **元素3**
            - \> ：表示 **元素1** 大于 **元素3**
            - \<  ：表示 **元素1** 小于 **元素3**
            - \>= ：表示 **元素1** 大于或等于 **元素3**
            - <= ：表示 **元素1** 小于或等于 **元素3**
            - != ：表示 **元素1** 不等于 **元素3**
            - and ：表示 **元素1** 和 **元素3** 的值必须都为True
            - or ：表示 **元素1** 和 **元素3** 的值只有有一个为True即可
    -  **元素2** 不是比较操作符时，那么格式为： \[**函数**, **参数1**, **参数2**, ...\]
        - **函数**，支持 **函数调用** 的语法
    - 支持无线嵌套
    - 举例
    ```php
    // 简单的判断
    [1, '=', 2]          // 比较 1 是否等于 2
    [true, 'and', false] // 简单的 and
    [false, 'or', true]  // 简单的 or
    ['a.b', '>', 'c.d']  // 比较字段绑定后的值
    ['a.b|func', '>', 5] // 比较字段绑定后并调用了函数后的值是否大于5
    
    // 函数调用
    [ ['array_sum=_', 'a.num', 'b.num'], '>', 'c.num'] // 比较 a.num 和 b.num 的值之和是否大于 c.num
    [ 'PHP_VERSION|constant', '=', '7.3.4' ]           // 比较 php 版本是否等于 7.3.4
    
    // 子条件
    [
        [
            ['PHP_VERSION|constant', '=', '7.3.4']
            'or',
            ['PHP_VERSION|constant', '=', '7.2.1']
        ],
        'and'
        ['PHP_OS|constant' , '=', 'linux']
    ] // 判断是否是 linux系统并且PHP为7.3.4或者7.2.1
    
    [['DbHelper::uniq_number','0.a','1.a','2.a'], '>=', 5] // 使用数据源 0.a+1.a+2.a 在数据库里面做唯一查询出的记录时，是否 >= 5 
    ```

### HTML构造器
```php
<?php

use HtmlBuilder\Parser\AdminLte\Parser;
use HtmlBuilder\Forms;

# ...

class YourController extends AdminBaseController{
    function YourAction(){
        # 基本用法
        $element = Forms::form($this->url('update'),'post')->add(
            Forms::input('user_name','用户名'),
            Forms::input('user_pass','密码')->subtype('password'),
            Forms::button('提交')->subtype('submit')
        );
        
        # 元素可以通过 add 方法嵌套另一个元素
        $element = \HtmlBuilder\Layouts::columns()->column(
             Forms::form('update','post')->add(
                  Forms::input('user','用户'),
                  Forms::input('passowrd','密码'),
                  Forms::button('登录')->subtype('submit')
             ),6
        )->column(
             HtmlBuilder\Layouts\Box::create()->body(
                 Forms::input('query','搜索'),
             ),6
        );

        $parser = new Parser(); // 初始化分析器，如果需要 HTML 输出，就用 AdminLte 的分析器，如果是前端，就用VUE的分析器
        $this->view->content = $parser->parse($element);
        
        # AdminLte\Parser分析器输出内容，CSS&JS会本记录在对象中
        # 将额外的样式和脚本应用到 View 上
        $this->addStyle($parser->getStyles());
        $this->addScript($parser->getScripts());
        foreach($parser->getJs() as $js)   $this->addJs($js);
        foreach($parser->getCss() as $css) $this->addCss($css);

        # ...
    }
}
```
