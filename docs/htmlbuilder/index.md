# HtmlBuilder介绍
- 它是由两部分组成：定义类 + 解析器
- 定义类为 PHP 类，类对象toString后为 JSON 对象
- 解析器的作用是对定义进行解析，产生对应的结果，目前的解析器有2个
  - AdminLte 解析器，将定义对象解析成对应的 HTML 内容（在后端解析完成，输出到前端）
  - Vuetify 解析器，将定义对象解析成对应的 Vue 代码（在前端解析，输出对应的HTML） `TODO`

## 基本组件对象（基类）
```json5
{
  "id": "",               // 组件ID
  "type": "div",          // 对应的HTML标签
  "name": "",             // 对应的FORM名称
  "subtype": "",          // 子类型

  // 标签的定义
  "label": "",            // 标签字符串
  "labelPosition": "top", // 标签与组件的位置关系，有：top,bottom,left,right,left-right,right-left
  "labelWidth": 0,        // 标签的宽度
  "labelIcon": "",        // 标签前面是的图标

  // 可视化的定义
  "enabled": true,        // 组件是否可用
  "visible": true,        // 组件是否可视
  "required": false,      // 组件是否必须

  // 帮助相关
  "placeHolder": "",      // 组件的占位符
  "tooltip": "",          // 组件的提示符，位于标签的旁边，多出一个 ? 的icon
  "description": "",      // 组件的详细描述，位于组件下方
  "badgeIcon": "",        // 组件右上方角标的内容

  "value": "",            // 组件值
  "attributes": [],       // 组件的其他属性，用于 html 的 data-xxx
  "validators": [],       // 组件的验证器，目前有：numeric, text, regex, e-mail, expression
  "events": [],           // 组件的事件
  "elements": []          // 组件的子元素
}
``` 

## 验证器
```json5
{
  "type": "",      // 验证器类型： number, text, regex, mail, expression
  "text": "",      // 验证不通过时的提示文本
  "rule": {        // 具体规则
     "minValue":0,   // 类型为 number 时的最小值范围
     "maxValue":0,   // 类型为 number 时的最大值范围
     "minLength":0,  // 类型为 text 时的最小字符长度
     "maxLength":0,  // 类型为 text 时的最大字符长度
     "regex":"",     // 类型为 regex,mail,url 等其他类型时的正则表达式
  }
}
```

## 方法列表
### 设置属性
> 通过魔术方法设置，比如： $element_obj->属性名(属性值)->属性名(属性值)...;
```php
<?php
$htmlbuild = new \HtmlBuilder\Element('b');
$htmlbuild->id('B_123')
          ->value('一个加粗标记')
          ->attr('a',1)
          ->on('click','()=>console.log("OK")');
// <b id="B_123" data-a="1" onclick='()=>console.log("OK")'>一个加粗标记</b>
```
### 添加子元素
> 使用 $element_obj->add( 元素1, 元素2, ...) 添加
```php
<?php
$htmlbuild->add(
    (new \HtmlBuilder\Element('span'))->value('Span标签')
);
```

##### 参考来源

- JSON 参考
    - [kevinchappell/formBuilder](https://formbuilder.online) 1.6k 
      - JQuery、React、Angular
      - 可用来参考 json 的输出
    - [formio/formio.js](https://formio.github.io/formio.js/app/builder) 472
      - 可以用来做页面、PDF
      - 可用来参考 json
    - [surveyjs](https://surveyjs.io/create-survey) 收费
      - 可用来参考 json
- UI、VUE参考
    - [mrabit/vue-formbuilder](https://github.com/mrabit/vue-formbuilder) 243 
      - vue form 创建器
      - 参考UI
    - [xaboy/form-create](https://github.com/xaboy/form-create) 906
      - vue form 创建器（文档全面）
      - 可用来参考 vue 的配置
- PHP 参考
    - [formers/former](https://github.com/formers/former) 1.2k
      - 用来PHP的方法调用参考