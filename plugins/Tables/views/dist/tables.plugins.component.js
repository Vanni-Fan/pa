// 添加条件的按钮
Vue.component('condition-add-btn',{
    template:'<div class="add-filter-template">\n' +
        '        <div class="btn-group">\n' +
        '            <div class="btn-group">\n' +
        '            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" :aria-expanded="false">\n' +
        '                <i class="fa fa-plus"></i> 添加条件</span><span class="fa fa-caret-down pull-right"></span>\n' +
        '            </button>\n' +
        '            <ul class="dropdown-menu">\n' +
        '                <li value="BASE" @click="$emit(\'add\',\'BASE\')"><a>标准条件</a></li>\n' +
        '                <li class="divider"></li>\n' +
        '                <li value="AND"  @click="$emit(\'add\',\'AND\')"><a>条件集合（全部匹配）</a></li>\n' +
        '                <li value="OR"   @click="$emit(\'add\',\'OR\')"><a>条件集合（部分匹配）</a></li>\n' +
        '            </ul>\n' +
        '            </div>\n' +
        '        </div>\n' +
        '    </div>',
    data:function(){
        return {}
    },
});

// 单个条件
Vue.component('condition',{
    template:'<div about="基本条件样式" class="base-filter-template">\n' +
        '        <div class="input-group margin">\n' +
        '            <div class="input-group-btn">\n' +
        '                <template v-if="!value.key || fields.hasOwnProperty(value.key)">' +
        '                <span type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" style="width:150px;border-right:none;">\n' +
        '                    <span class="current-operation">{{value.key ? fields[value.key].text : "字段"}}</span><span class="fa fa-caret-down pull-right"></span>\n' +
        '                </span>\n' +
        '                <ul class="dropdown-menu">\n' +
        '                    <li class="disabled"><a>选择一个字段</a></li>\n' +
        '                    <li class="divider"></li>' +
        '                        <template v-for="(val, key) in fields">' +
        '                            <li :value="val.text" :class="value.key==key ? \'active\' : null" @click="value.key=key"><a>{{val.text}}</a></li>\n' +
        '                        </template>' +
        '                </ul>\n' +
        '                </template><template v-else>' +
        '                    <div class="btn btn-info disabled" style="border-right:none;min-width: 150px;">{{value.key}}</div>' +
        '                </template>' +
        '            </div>\n' +
        '            <div class="input-group-btn">\n' +
        '                <template v-if="!value.key || fields.hasOwnProperty(value.key)">' +
        '                <span type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" style="width:110px;border-radius:0;border-width:1px 0 1px 1px;">' +
        '                <span class="current-operation">{{opText ? opText : "操作符"}}</span><span class="fa fa-caret-down pull-right"></span></span>\n' +
        '                <ul class="dropdown-menu">\n' +
        '                    <li class="disabled"><a>选择一个操作符</a></li>\n' +
        '                    <li class="divider"></li>\n' +
        '                    <template v-for="operation in operations">' +
        '                        <li :value="operation.op" :class="operation.text==opText ? \'active\' : null" @click="value.op=operation.op"><a>{{operation.text}}</a></li>\n' +
        '                    </template>' +
        '                </ul>\n' +
        '                </template><template v-else>' +
        '                    <div class="btn btn-info disabled" style="border-radius:0;min-width: 111px;">{{value.op}}</div>' +
        '                </template>' +
        '            </div>\n' +
        '            <template v-if="!value.key || fields.hasOwnProperty(value.key)">' +
        '            <input :type="value.key ? fields[value.key].type : \'text\'" class="form-control" style="border-color:#00acd6;border-right:none;" placeholder="查询值" v-model="value.val">\n' +
        '            </template><template v-else>' +
        '            <input type="text" disabled="disabled" style="border-left:0;" class="form-control" :value="value.val">\n' +
        '            </template>' +
        '            <span class="input-group-btn">\n' +
        '              <button type="button" :disabled="value.key!==\'\' && !fields.hasOwnProperty(value.key)" class="btn btn-info" @click="$emit(\'del\')"><i class="fa fa-close"></i></button>\n' +
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
            ],
        }
    },
    computed:{
        opText:function () {
            let op = this.value.op || '=';
            let index = this.operations.findIndex(function(v){
                return v.op == op;
            });
            if(index==-1) return '操作符';
            return this.operations[index].text;
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
        '             <button type="button" class="btn btn-info" @click="value.op = value.op === \'AND\' ? \'OR\' : \'AND\'">{{ value.op === "AND" ? "条件集合（全部匹配 AND）" : "条件集合（部分匹配 OR）" }}</button>\n' +
        '             <button type="button" class="btn btn-info" @click="$emit(\'del\')"><i class="fa fa-close"></i></button>\n' +
        '           </div>\n' +
        '       </div>\n' +
        '       <div about="所有子条件的集合（直接添加：基本条件样式）" class="filters">' +
        '          <template v-for="(cond,index) in value.sub">' + // 循环 sub
        '             <condition v-if="!cond.sub" :value="cond" :fields="fields" @del="value.sub.splice(index,1)"></condition>' +  // 如果子条件没有 sub，直接渲染
        '             <conditions v-if="cond.sub" :value="cond" :fields="fields" @del="value.sub.splice(index,1)"></conditions>' +
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
        '    <condition v-if="!filter.sub" :value="filter" :fields="fields" @del="value.splice(index,1)"></condition>\n' +
        '    <conditions v-if="filter.sub" :value="filter" :fields="fields" @del="value.splice(index,1)"></conditions>\n' +
        '    </template>\n' +
        '    <condition-add-btn @add="add"></condition-add-btn>' +
        '</div>',
    props:['value','fields'],
    data:function(){
        return {}
    },
    methods:{
        // 内部方法
        add:function(type){
            if(type === 'BASE'){
                this.value.push({key:'',op:'=',val:''});
            }else{
                this.value.push({op:type,sub:[]});
            }
        },

        // 对外提供的方法
        getFilters:function(){
            return this.value;
        },
        checkFilters:function(fs){
            let rs = true;
            for(let i=0; i<fs.length; i++){
                if(fs[i].sub){
                    rs = this.checkFilters(fs[i].sub);
                    if(!rs) break;
                } else {
                    if(!fs[i].key || !fs[i].val){
                        rs = false;
                        break;
                    }
                }
            }
            return rs;
        }
    }
});
