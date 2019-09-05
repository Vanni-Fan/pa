### 参考
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
      ```php
      Former::xlarge_text('name') # Bootstrap sizing
        ->class('myclass') # arbitrary attribute support
        ->label('Full name')
        ->value('Joseph')
        ->required() # HTML5 validation
        ->help('Please enter your full name');
      ```

### 我们的标准
#### 基本属性
```js
config = {
    type:"xxx",        //类型 
    name: "xxx",  // 表单中的名称
    label:"xxx",  // 表单标签
    labelPosition:"top,bottom,left,right,left-right,right-left",
    labelWidth:"宽度",
    labelMargin:"留空",
    required:true,      // 是否必须
    enabled:false,      // 是否禁用，禁用的组件不会被提交
    visible:false,      // 是否显示，虽然不显示，但可以被提交
    value:"默认值", 
    tooltip:"帮助提示语",// 显示在后面的问号信息，移动上去就显示
    description:"",     // 固定显示到下面的提示语
    attributes:{        // 扩展属性,展示在 html 标签里
        class:"自定义样式",
        "data-aaa":2
    },
    validators:[{       // 验证器
        type:"numeric, text, regex, e-mail, expression",
        text:"错误提示语", // 显示在左下角、显示在表单上方、红边框
        OTHER:"其他具体的验证字段"
    }],
    subtype:"plain,color,date,datetime,datetime-local,email,month,number,password,range,tel,text,time,url,week",
    // elements/components  // 子元素或者组件 todo ?
}
```
#### 验证器
- numeric
    - text, minValue, maxValue
- text
    - text, minLength, maxLength, allowDigits
- answercount
    - text, minCount, maxCount
- regex
    - text, regex
- e-mail
    - text
- expression
    - text, expression

#### 单独属性
- input 单行输入（邮件、URL、电话、地址、货币、数字、密码、隐藏元素、时间、日期 ~~自动完成~~）
```js
config = {
  type:"text",        //类型 
  name: "user_name",  // 表单中的名称
  label:"user Name",  // 表单标签
  labelPosition:"top,bottom,left,right,left-right,right-left",
  labelWidth:"宽度",
  labelMargin:"留空",
  enabled:false,      // 是否禁用，禁用的组件不会被提交
  visible:false,      // 是否显示，虽然不显示，但可以被提交
  value:"默认值", 
  required:true,      // 是否必须
  inputMask:"99:99",
  placeHolder:"占位符",
  tooltip:"帮助提示语",// 显示在后面的问号信息，移动上去就显示
  description:"",     // 固定显示到下面的提示语
  newLine:true,       // 是否新行显示，会清除浮动，开启新行
  attributes:{        // 扩展属性,展示在 html 标签里
    AA:1,
    BB:2
  },
  validators:[{       // 验证器
    type:"numeric, text, regex, e-mail, expression",
    text:"错误提示语", // 显示在左下角、显示在表单上方、红边框
    OTHER:"其他具体的验证字段"
  }],
  statistics:true,    // 显示在右下角，字符长度，单词个数
  appendIcon:"",      // 后面的图标
  prependIcon:""      // 前面的图标
  // elements/components  // 子元素或者组件 todo ?
}
```
- checkbox 复选框(多选)
```js
config = {
    type:"checkbox",
    name:"rules[]",
    value:["",""],
    label:"user Name",  // 表单标签
    labelPosition:"top,bottom,left,right,left-right,right-left",
    labelWidth:"宽度",
    labelMargin:"留空",
    enabled:false,      // 是否禁用，禁用的组件不会被提交
    visible:false,      // 是否显示，虽然不显示，但可以被提交
    required:true,      // 是否必须
    tooltip:"帮助提示语",// 显示在后面的问号信息，移动上去就显示
    description:"",     // 固定显示到下面的提示语
    choices:[
        {
            text:"显示",
            value:"值"
        }
    ],
    hasOther:true, // 允许其他值，会有一个输入框输入值
    otherText:"", // 其他文本

    hasSelectAll:true, // 选择它即全选所有
    selectAllText:"", // 选择所有是的文本

    hasNone:true, // 选择它会取消所有
    noneText: "不设置",

    colCount:3, // 每列显示的元素个数
    choicesByUrl:{
        url:"",
        path:"",
        valueName:"",
        titleName:""
    }
}
```
- radio 单选框(开关)
```js
config = {
    type:"radio",
    name:"rules[]",
    value:"",
    label:"user Name",  // 表单标签
    labelPosition:"top,bottom,left,right,left-right,right-left",
    labelWidth:"宽度",
    labelMargin:"留空",
    enabled:false,      // 是否禁用，禁用的组件不会被提交
    visible:false,      // 是否显示，虽然不显示，但可以被提交
    required:true,      // 是否必须
    tooltip:"帮助提示语",// 显示在后面的问号信息，移动上去就显示
    description:"",     // 固定显示到下面的提示语
    choices:[
        {
            text:"显示",
            value:"值"
        }
    ],
    hasOther:true, // 允许其他值，会有一个输入框输入值
    otherText:"", // 其他文本

    colCount:3, // 每列显示的元素个数
    choicesByUrl:{
        url:"",
        path:"",
        valueName:"",
        titleName:""
    }
}
```
- select 下拉列表（~~资源选择器、树选择、图片列表等~~）
```js
config = {
    type:"select",
    name:"rules[]",
    value:"",
    label:"user Name",  // 表单标签
    labelPosition:"top,bottom,left,right,left-right,right-left",
    labelWidth:"宽度",
    labelMargin:"留空",
    enabled:false,      // 是否禁用，禁用的组件不会被提交
    visible:false,      // 是否显示，虽然不显示，但可以被提交
    required:true,      // 是否必须
    tooltip:"帮助提示语",// 显示在后面的问号信息，移动上去就显示
    placeholder: "1111",
    description:"",     // 固定显示到下面的提示语
    choices:[
        {
            text:"显示",
            value:"值"
        }
    ],
    hasOther:true, // 允许其他值，会有一个输入框输入值
    otherText:"", // 其他文本

    colCount:3, // 每列显示的元素个数
    choicesByUrl:{
        url:"",
        path:"",
        valueName:"",
        titleName:""
    }
}
```
- button 按钮（提交、重置、图片、模式弹窗、提交等）
```js
config = {
    type:"button",
    subtype:"reset/submit/button",
    name:"rules[]",
    value:"",
    label:"user Name",  // 表单标签
    labelPosition:"top,bottom,left,right,left-right,right-left",
    labelWidth:"宽度",
    labelMargin:"留空",
    enabled:false,      // 是否禁用，禁用的组件不会被提交
    visible:false,      // 是否显示，虽然不显示，但可以被提交
    tooltip:"帮助提示语", // 显示在后面的问号信息，移动上去就显示
    theme:"success,info,...",
    size:"medium,big,small",
    leftIcon: "fa fa-users",
    rightIcon: "fa fa-info"
}
```
- file 文件（图片、视频、多文件、图片裁剪）
```js
config = {
    type: "file",
    imageHeight:200, // 图片高度
    imageWidth:200, // 图片宽度
    maxSize:200, // 最大尺寸
    storeDataAsText:false,
    showPreview:false,
    filePattern:"*.jpg",
    fileMinSize: "2KB",
    fileMaxSize: "13GB",
    fileTypes:[
        {label:"JPG",value:"jpg"},
        {label:"PNG",value:"png"}
    ]
}
```
- textarea 富文本编辑器（多行文本、HTML内容、Markdown、UEditor、WangEditor）
```js
config = {
    type:"textarea",
    subtype:"quill|markdown|UEditor|WanEditor|ckeditor",
    maxlength:20,
    rows:3
}
```
#### 组件
- 多级联动下拉列表
- 日期+时间
- 日期段
- 时间段
- 标签（tags）
- 取色器
- 地图
- table 表格
```js

```

#### 布局：Layout [参考自<form.io>](https://formio.github.io/formio.js/app/builder)
- columns 列
```js
config = {
    type: "columns",
    label: "Columnszz",
    columns: [
        {
            components: [ /* 可以是组件，可以是独立元素，也可以是form元素 */ ],
            width: 5,   // 列宽 12 分之几
            offset: 0,  // 空白多少
            push: 0,    // 浮动后移多少 
            pull: 0,    // 浮动前移多少
            type: "column",
        }
    ]
 }
```
- box 盒子（面板、字段集、卡片）（有标题，内容，有底部）(可用来设置：标题、段落、图片、视频、地图、验证码、背景等)
```js
config = {
    label: "Field Set",
    type: "fieldset",
    header:{ // 是否有头部
        type:"fieldset|title", // fieldset
        text:"内容", 
        components:[{/*..*/}], // 可以包含一些组件
    },
    body:{ /* 和 header 一致 */ },
    footer:{ /* 和 header 一致 */ },
    badges:"",
}
```
- tabs 标签（tabs、page页）
```js
config = {
    label: "Tabs",
    type: "tabs",
    components: [
        {
            /* ... */
        }
    ],
    attributes: {}
}
```
### 
