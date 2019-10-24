# 工具类 library/Utils.php

## 对象或数组的快捷操作
- 参考来源： [JMESPath](http://jmespath.org/tutorial.html)
```php
<?php
// 比如有如下数据
$t = new stdclass;
$t -> a = 'obj_a';
$t -> b = 'obj_b';

$a = [
    'a' => [
        'b' => [100,200,300],
        'c' => ['user_id'=>11,'user_name'=>'Vanni','sub'=>[$t, $t]],
    ],
    'd' => [
        ['user_id'=>22,'user_name'=>'You'],
        ['user_id'=>33,'user_name'=>'Other'],
    ],
    'e' => $t,
    'f' => [$t, $t],
    'g' => ['obj_A'=>$t, 'obj_B'=>$t],
];
```
### 功能一：数据的提取
> 1、通过点号（对象的成员），提取对象或数组中的值
```php
<?php
Utils::vpath('a.b.0', $a);     // 返回 100
Utils::vpath('a.b.2', $a);     // 返回 300
Utils::vpath('f.1.a', $a);     // 返回 obj_a
Utils::vpath('g.obj_A.a', $a); // 返回 obj_a
Utils::vpath('d.1',$a);        // 返回 ['user_id'=>33,'user_name'=>'Other']
```

> 2、通过星号（匹配所有），提取数组
```php
<?php
Utils::vpath('d.*.user_id',$a);    // 返回 [22,33]
Utils::vpath('f.*.a', $a);         // 返回 ["obj_a","obj_a"]
Utils::vpath('a.*.c.sub.*.b', $a); // 返回 ["obj_b","obj_b"]
```

> 3、通过大括号（对象的定义），提取多个下标
> 语法： =={键名1, 键名2, 别名:键名3, ...}==，键名也可以是一个子查询
```php
<?php
// 返回 [{user_id:11,Alias_Name:Vanni, MySub:[obj_b]}]
Utils::vpath('a.c.*.{user_id, Alias_Name:user_name, MySub:sub.*.b}', $a) 
```

> 4、通过问号设置默认值，当提取的下标不存在时，使用默认值
```php
<?php
# 使用 ? 号只能定义字符串的默认值，或者对应vpath的值，判断依据是 ? 号后面是否有点.
Utils::vpath('not_exists.my_key.your_key?no_value', $a); // 返回 'no_value'
Utils::vpath('f.*.not_exists?abc', $a);                  // 返回 ["abc","abc"]
Utils::vpath('f.*.not_exists?a.b.0', $a);                // 返回 [100,100]
# 使用第三个参数可以定义其他类型的默认值
Utils::vpath('f.*.not_exists', $a, 123);                 // 返回 [123,123]
```
### 功能二：数组结构的生成
- 相当 vpath 函数的反向
```php
<?php
$out = [];
Utils::path2arr('a.b.c.d', '123', $out); // 设置 $out 的 a.b.c.d 为 123
Utils::path2arr('a.c.*.d', '123', $out); // 设置 $out 的 a.c.0.d 为 123，*会替换成0
# 上面的代码相当于:
$out['a']['b']['c']['d'] = '123';
$out['a']['c'][0]['d']   = '123';
```

### 功能三：函数的管道形式调用
> 类似Linux下面的管道命令，通过竖线将前面函数的返回值作为后面函数的输入值
> 1、 竖线，分割函数
```php
<?php
Utils::pipe('strtotime|strval|intval', '2019-11-12');
# 相当于
intval(strval(strtotime('2019-11-12')));
```
> 2、 等号，设置函数默认值，前面函数的返回，默认在第一个位置
```php
<?php
Utils::pipe('func_a=22,33|func_b', 'value');
#相当于
func_b(func_a('value',22,33));
```
> 3、下划线，设置函数参数的占位符
```php
<?php
Utils::pipe('func_a=22,_,33|func_b', 'value');
#相当于
func_b(func_a(22,'value',33));
```

## 其他功能
> todo
### 获得IP：Utils::ip() 
### 使用vpath绑定数据
> 基于 vpath 的功能，函数有 Utils::parseBind()， Utils::replaceBindFields(), Utils::replaceTemplateVal()
### 无限条件判断: Utils::conditions()
### 发送邮件: Utils::sendMail()
### 设置或获取缓存： Utils::cache()
### 加密: Utils::encrypt()
### 解密： Utils::decrypt()
### 组包： Utils::pack()
### 解包： Utils::unpack()

