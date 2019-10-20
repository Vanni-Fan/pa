// 添加条件的按钮
Vue.component('condition-add-btn',{
    template:'<div class="add-filter-template">\n' +
        '        <div class="btn-group">\n' +
        '            <div class="btn-group">\n' +
        '            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" :aria-expanded="false">\n' +
        '                <i class="fa fa-plus"></i> 添加条件</span><span class="fa fa-caret-down pull-right"></span>\n' +
        '            </button>\n' +
        '            <ul class="dropdown-menu">\n' +
        '                <li value="BASE" @click="add(\'BASE\')"><a>标准条件</a></li>\n' +
        '                <li class="divider"></li>\n' +
        '                <li value="AND"  @click="add(\'AND\')"><a>条件集合（全部匹配）</a></li>\n' +
        '                <li value="OR"   @click="add(\'OR\')"><a>条件集合（部分匹配）</a></li>\n' +
        '            </ul>\n' +
        '            </div>\n' +
        '        </div>\n' +
        '    </div>',
    data:function(){
        return {}
    },
    methods:{
        add:function(type){
            this.$emit('add',type);
        }
    }
});

// 单个条件
Vue.component('condition',{
    template:'<div about="基本条件样式" class="base-filter-template">\n' +
        '        <div class="input-group margin">\n' +
        '            <div class="input-group-btn">\n' +
        '                <span type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" style="width:150px;border-right:none;">\n' +
        '                    <span class="current-operation">{{value.key ? fields[value.key] : "字段"}}</span><span class="fa fa-caret-down pull-right"></span>\n' +
        '                </span>\n' +
        '                <ul class="dropdown-menu">\n' +
        '                    <li class="disabled"><a>选择一个字段</a></li>\n' +
        '                    <li class="divider"></li>\n' +
        '                    <template v-for="(val, key) in fields">' +
        '                        <li :value="val" :class="value.key==key ? \'active\' : null" @click="selectField(key);"><a>{{val}}</a></li>\n' +
        '                    </template>' +
        '                </ul>\n' +
        '            </div>\n' +
        '            <div class="input-group-btn">\n' +
        '                <span type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" style="width:110px;border-radius:0;border-width:1px 0 1px 1px;">' +
        '                <span class="current-operation">{{opText ? opText : "操作符"}}</span><span class="fa fa-caret-down pull-right"></span></span>\n' +
        '                <ul class="dropdown-menu">\n' +
        '                    <li class="disabled"><a>选择一个操作符</a></li>\n' +
        '                    <li class="divider"></li>\n' +
        '                    <template v-for="operation in operations">' +
        '                        <li :value="operation.op" :class="operation.text==opText ? \'active\' : null" @click="selectOp(operation.op);"><a>{{operation.text}}</a></li>\n' +
        '                    </template>' +
        '                </ul>\n' +
        '            </div>\n' +
        '            <input type="text" class="form-control" style="border-color:#00acd6;" placeholder="查询值" v-model="value.val">\n' +
        '            <span class="input-group-btn">\n' +
        '              <button type="button" class="btn btn-info" @click="del"><i class="fa fa-close"></i></button>\n' +
        '            </span>\n' +
        '        </div>\n' +
        '    </div>',
    props:['value','fields'],
    data:function(){
        return {
            operations:[
                {'op':'=', 'text':'等于'},
                {'op':'>', 'text':'大于'},
                {'op':'>=','text':'大于或等于'},
                {'op':'<', 'text':'小于'},
                {'op':'<=','text':'小于或等于'},
                {'op':'!=','text':'不等于'},
                {'op':'%', 'text':'包含'}
            ]
        }
    },
    computed:{
        opText:function () {
            var op = this.value.op || '=';
            var index = this.operations.findIndex(function(v){
                return v.op == op;
            });
            if(index==-1) return '操作符';
            return this.operations[index].text;
        }
    },
    methods:{
        selectField:function(key){
            this.value.key = key;
        },
        selectOp:function(op){
            this.value.op = op;
        },
        del:function () {
            this.$emit('del');
        }
    }
});

// 集合条件
Vue.component('conditions',{
    template:'' +
        '   <div about="条件集合" :class="\'filter-set filter-set-template \' + height" style="cursor:pointer;">\n' +
        '       <div about="前括号">\n' +
        '           <span class="filter-set-before" @click="autoHeight=!autoHeight">{{ autoHeight ? "︹" : "&nbsp;+" }}</span>\n' +
        '           <div class="btn-group filter-set-condition">\n' +
        '             <button type="button" class="btn btn-info" @click="switchType">{{ value.op === "AND" ? "条件集合（全部匹配 AND）" : "条件集合（部分匹配 OR）" }}</button>\n' +
        '             <button type="button" class="btn btn-info" @click="del"><i class="fa fa-close"></i></button>\n' +
        '           </div>\n' +
        '       </div>\n' +
        '       <div about="所有子条件的集合（直接添加：基本条件样式）" class="filters">' +
        '          <template v-for="(cond,index) in value.sub">' + // 循环 sub
        '             <condition v-if="!cond.sub" :value="cond" :fields="fields" @del="delSub(index)"></condition>' +  // 如果子条件没有 sub，直接渲染
        '             <conditions v-if="cond.sub" :value="cond" :fields="fields" @del="delSub(index)"></conditions>' +
        '          </template>' +
        '       </div>\n' +
        '       <condition-add-btn @add="add" />\n' +
        '       <div about="后括号" class="filter-set-after">︺</div>\n' +
        '    </div>' +
        '',
    props:['value','fields','index'],
    data:function(){
        return {
            autoHeight:true
        }
    },
    computed:{
        height:function(){
            if(this.autoHeight) return '';
            else return 'filters-min-height';
        }
    },
    methods:{
        switchType:function(){
            this.value.op = this.value.op === 'AND' ? 'OR' : 'AND';
        },
        del:function(){
            this.$emit('del');
        },
        delSub:function (index) {
            this.value.sub.splice(index,1);
        },
        add:function (type) {
            if(type === 'BASE'){
                this.value.sub.push({
                    key:'',op:'=',val:''
                });
            }else {
                this.value.sub.push({
                    op:type,
                    sub:[]
                })
            }
        }
    }
});

// Filter 封装
Vue.component('filters',{
    template:
        '<div class="filters">\n' +
        '    <template v-for="(filter, index) in value">\n' +
        '    <condition v-if="!filter.sub" :value="filter" :fields="fields" @del="delSub(index)"></condition>\n' +
        '    <conditions v-if="filter.sub" :value="filter" :fields="fields" @del="delSub(index)"></conditions>\n' +
        '    </template>\n' +
        '    <condition-add-btn @add="add"></condition-add-btn>' +
        '</div>',
    props:['value','fields'],
    data:function(){
        return {}
    },
    methods:{
        delSub:function(index){
            this.value.splice(index,1);
        },
        add:function(type){
            if(type === 'BASE'){
                this.value.push({key:'',op:'=',val:''});
            }else{
                this.value.push({op:type,sub:[]});
            }
        }
    }
});
