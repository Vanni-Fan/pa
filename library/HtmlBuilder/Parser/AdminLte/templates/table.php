<?php
//$this->js('/dist/plugins/twbs-pagination/jquery.twbsPagination.min.js');
$this->js('/dist/plugins/twbs-pagination/jquery.twbsPagination.js');
$this->js('/dist/plugins/vue/vue.js');
$this->js('/dist/vue.component.js');

// 样式，缓存
$this->style(/** @lang CSS */ <<<'OUT'
.htmlbuild-table-header i{
    width:15px;
    margin-left:5px;
}
.htmlbuild-table-header i:hover{
    color:green !important;
    cursor: pointer;
}
.htmlbuild-table-selected-row{
    background-color: #64a7ce !important;
    color: white;
}
.htmlbuild-table-selected-row a{
    color: white;
}
.htmlbuild-table-header .sort-badge{
    background-color: white;
    color: #3c8dbc;
    border-radius: 15px;
    height: 15px;
    width: 15px;
    display: inline-block;
    line-height: 15px;
    position: relative;
}
.htmlbuild-table-header th .text-info{
    color:white;
}
.filter-field, .filter-operation, .filter-value{
    margin:10px;
    font-size:16px;
}
.filter-field{
    color:yellow;
}
.filter-operation{
    color:white;
}
.filter-value{
    color:#a5ffb7;
}
.filter-close{
    position: absolute;
    z-index: 99;
    right: -5px;
    top: -5px;
    width:20px;
    height:20px;
    padding:0;
    line-height: 20px;
    cursor:pointer;
}
.dataTables_paginate .pagination{
    float: right!important;
    margin: auto;
}
.table-edit-btn{
    cursor:pointer;
}
.table-striped>tbody>tr:nth-of-type(odd) {
    background-color: #d9e9f2;
}
tr.htmlbuild-table-header{
    background-color: #3c8dbc;
    color:white;
}
.filter-set{
    margin-left:20px;
    margin-top: 5px;
    border-left-style: dashed;
    border-left-width:1px;
    border-left-color: #cccccc;
    padding-left: 5px;
    margin-bottom: 10px;
}
.filter-set-before{
    width: 20px;background-color: white;margin-left: -15px;font-size: 20px;display: block;position: absolute;color:#cccccc;
}
.filter-set-condition{
    margin-left:10px;
}
.filter-set-after{
    font-size: 20px;background-color: white;position: relative;margin-left: -15px;height: 10px;color:#cccccc;
}
.dropdown-toggle .fa-caret-down{
    line-height: 20px;
}
.add-filter-template{
    margin-left: 10px;
}
.filters{
    min-height: 10px;
}
.filters-min-height{
    height:35px;
    overflow: hidden;
}
OUT
);

// HTML 模板，缓存
$this->html(/** @lang HTML */<<<'OUT'
<div id="test"></div>
<div class="box box-primary" id="htmlbuilder-table-template" style="display:none;">
    <div class="box-header with-border">
        <h3 class="box-title"><?=$name?></h3>
        <i class="text-gray table-description">数据源可以添加描述字段，这里是描述信息</i>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
        <div class="pull-right table-edit-btn" style="display: inline-block;margin-right:50px;">
            <span onclick="HtmlBuilder_table_selectAll(this.getAttribute('data-id'),event)" class="text-light-blue select-el"><i class="check-all fa fa-square-o"></i> 全选 </span>&nbsp;
            <span onclick="HtmlBuilder_table_inverse(this.getAttribute('data-id'))" class="text-light-blue select-el"><i class="glyphicon glyphicon-transfer"></i> 反选 </span>&nbsp;
            <span onclick="HtmlBuilder_table_delItems(this.getAttribute('data-id'),HtmlBuilder_table_getSelected(this.getAttribute('data-id')))" class="text-red del-el"><i class="fa fa-trash-o"></i> 删除 </span>&nbsp;
            <span onclick="HtmlBuilder_table_setFilter(this.getAttribute('data-id'))" class="text-light-blue filter-el"><i class="fa fa-filter"></i> 筛选 </span>&nbsp;
            <span onclick="HtmlBuilder_table_selectFields(this.getAttribute('data-id'))" class="text-light-blue filter-el"><i class="fa fa-tasks"></i> 字段 </span>&nbsp;
            <span class="text-aqua add-el"><a><i class="fa fa-plus"></i> 添加 </a></span>
        </div>
    </div>
    <div class="box-body">
        <div class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-bordered table-striped dataTable" style="margin-bottom:0" table-layout="fixed">
                        <thead><tr class="htmlbuild-table-header"></tr></thead>
                        <tbody class="htmlbuild-table-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="box-footer clearfix">
        <div class="row">
            <div class="col-sm-5">
                <div class="dataTables_info">
                    Rows per page:
                    <select class="input-sm page-size" onchange="HtmlBuilder_table_changePageSize($(this))">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="40">40</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="page-status"></span>
                </div>
            </div>
            <div class="col-sm-7 dataTables_paginate"></div>
        </div>
    </div>
</div>
<div id="FILTERS-TEMPLATE" style="display: none">
    <filters :value="filters" :fields="fields"></filters>
</div>
OUT
);

echo '<div id="', $id , '"></div>';

// Table 使用的脚本，缓存
$this->script(/** @lang JavaScript 1.5 */ <<<'OUT'

// 打开或关闭字段显示
function HtmlBuilder_table_openCloseField(obj){
    var dom = $(obj);
    var id = dom.data('id');
    var field = dom.data('field');
    if(obj.checked){
        $('#'+id+' th[data-field='+field+'],#'+id+' td[data-field='+field+']').removeClass('hidden');
    }else{
        $('#'+id+' th[data-field='+field+'],#'+id+' td[data-field='+field+']').addClass('hidden');
    }
    var index = window[id].fields.findIndex(function(v){ return v.name == field });
    window[id].fields[index].show = obj.checked;
}


// 选择字段
function HtmlBuilder_table_selectFields(id) {
    var dom = '<div style="display:table;">';
    for(var i=0; i<window[id].fields.length; i++){
        var field = window[id].fields[i];
        var checked = field.show ? ' checked ' : '';
        dom+='<label class="col-sm-4"><input data-id="'+id+'" data-field="'+field.name+'" onchange="HtmlBuilder_table_openCloseField(this)"' + checked + ' type="checkbox">' + field.text + '</label>';
    }
    
    showDialogs({
        title:'选择需要展示的列！',
        body: dom+'</div>',
        close:{
            text:'取消',
            click:function(o){o.close()}
        }
    });
}

// 选择所有
function HtmlBuilder_table_selectAll(id,event){
    var obj = $(event.currentTarget).find('i');
    var status = true;
    if(obj.hasClass('fa-square-o')){
        status = true;
        obj.removeClass('fa-square-o').addClass('fa-check-square-o');
        $('#' + id + ' .htmlbuild-table-body tr[onclick]').addClass('htmlbuild-table-selected-row');
    }else{
        status = false;
        obj.removeClass('fa-check-square-o').addClass('fa-square-o');
        $('#' + id + ' .htmlbuild-table-body tr[onclick]').removeClass('htmlbuild-table-selected-row');
    }
}

// 反选
function HtmlBuilder_table_inverse(id) {
    $('#' + id + ' .htmlbuild-table-body tr[onclick]').each(function(index,dom){
        $(dom).toggleClass('htmlbuild-table-selected-row');
    });
}

// 提交删除选择的项
function HtmlBuilder_table_delItems(id, items){
    if(items.length===0){
        showDialogs({
            body:'当前没有选择的数据！',
            delay:3000
        });
        return;
    }
    showDialogs({
        title:'确认删除？',
        body: '这些记录将被删除：<b>' + items + '</b>',
        // height:300,
        ok:{
            text:'确定',
            click:function(o){
                var deleteApi = window[id].deleteApi.replace('_ID_', items);
                $.ajax(deleteApi, {data:window[id].query, method:'POST'}).done(function(data){
                    HtmlBuilder_table_setData(data,id);
                    $('#'+id+' i.check-all').removeClass('fa-check-square-o').addClass('fa-square-o');
                });
                o.close();
            }
        },
        close:{
            text:'取消',
            click:function(o){o.close()}
        }
    });
}

// 获得当前选择的项目
function HtmlBuilder_table_getSelected(id){
    var all = $('#' + id + ' .htmlbuild-table-body .htmlbuild-table-selected-row');
    var ids = [];
    for(var index=0; index<all.length; index++){
        ids.push(all[index].getAttribute('data-id'));
    }
    return ids;
}

// 初始化表格
function HtmlBuilder_table_init(id){
    // 初始化对象
    var options = window[id];
    options.query  = options.query  || {};
    options.fields = options.fields || [];
    options.query.sort    = options.query.sort    || [];
    options.query.limit   = options.query.limit   || {};
    options.query.filters = options.query.filters || [];
    window[id] = options;

    var obj = $('#htmlbuilder-table-template').clone();
    obj.removeAttr('id').css('display','block').find('.box-title').html(options.name);
    var header = obj.find('.htmlbuild-table-header');
    
    // 初始化表头
    var html = '';
    obj.find('.table-edit-btn>*').attr('data-id', id);
    if(options.canEdit){ // 去掉编辑按钮
        obj.find('.add-el a').attr('href', options.createApi);
    }else{
        obj.find('.del-el,.add-el').remove();
    }
    if(!options.canAppend){
        obj.find('.add-el').remove();
    }
    if(!options.selectMode){ // 去掉选择模块
        obj.find('.select-el').remove();
        obj.find('.del-el').remove();
    }
    if(options.description){ // 去掉表头描述
        obj.find('.table-description').text(options.description);
    }else{
        obj.find('.table-description').remove();
    }
    if(!options.canMin){ // 去掉最小化
        obj.find('.box-tools button[data-widget="collapse"]').remove();
    }
    if(!options.canClose){ // 去掉关闭按钮
        obj.find('.box-tools button[data-widget="remove"]').remove();
    }
    
    for(var index in options.fields){
        var field = options.fields[index];
        field.show = field.hasOwnProperty('show') ? field.show : 1;
        // if(!field.show) continue;
        var sortStatus = '';
        var filterStatus = '';
        if(field.sort){
            var sort_index = options.query.sort.findIndex(function(_v){ return _v.name === field.name; });
            if(sort_index === -1){
                sortStatus = '<i onclick="HtmlBuilder_table_sort(\'' + id + '\', \'' + field.name + '\')" class="sort-status fa fa-sort text-gray"></i><span class="sort-badge hidden"></span>';
            }else{
                sortStatus = '<i onclick="HtmlBuilder_table_sort(\'' + id + '\', \'' + field.name + '\')" class="sort-status fa fa-sort-amount-' + options.query.sort[sort_index].type + ' text-info"></i><span class="sort-badge">'+ (parseInt(sort_index)+1) + '</span>';
            }
        }
        var _class = 'text-center ' + (field.show ? '' : ' hidden') + (field.class ? field.class : '');
        var th =
        '<th data-field="' + field.name + '"' +  (field.hasOwnProperty('width') ? ('width="' + field.width + 'px" style="min-width:' + field.width + 'px;"') : '') + ' class="' + _class + '">' + (field.icon ? ('<span class="'+field.icon+'"></span> ') : '') +
            field.text +
            sortStatus +
            filterStatus +
        '</th>';
        html += th;
    }
    if(options.canEdit){
        var cw = options.editColWidth;
        html += '<th class="text-center" width="' + cw + 'px" style="min-width:' + cw + 'px">' + (typeof(options.canEdit) != 'boolean' ? options.canEdit : '编辑') + '</th>';
    }
    header.append(html);
    // header.render();
    
    // 设置页码尺寸
    obj.find('.page-size').val(options.query.limit.size).attr('data-id', id);
    
    // 填充数据
    // var body = obj.find('.htmlbuild-table-body');
    $('#'+id).append(obj);
    HtmlBuilder_table_query(id);
}

// 改变分页数
function HtmlBuilder_table_changePageSize(obj){
    var id = obj.data('id');
    window[id].query.limit.size = obj.val();
    HtmlBuilder_table_query(id);
}

// 点击排序时的动作
function HtmlBuilder_table_sort(id, field){
    var options = window[id];
    var found = options.query.sort.findIndex(function(_v){ return _v.name === field});
    var old_class = [], new_class = [];
    if(found === -1){
        old_class = ['fa-sort','text-gray'];
        new_class = ['fa-sort-amount-asc','text-info'];
        options.query.sort.push({name:field,type:'asc'});
    }else{
        var type = options.query.sort[found].type;
        if(type === 'asc'){
            old_class = ['fa-sort-amount-asc'];
            new_class = ['fa-sort-amount-desc'];
            options.query.sort[found].type = 'desc';
        }else{
            old_class = ['fa-sort-amount-desc','text-info'];
            new_class = ['fa-sort','text-gray'];
            options.query.sort.splice(found,1);
        }
    }
    $('#' + id + ' th[data-field="' + field + '"] .sort-status').removeClass(old_class).addClass(new_class);
    
    // 设置排序角标
    $('#' + id + ' th .sort-badge').addClass('hidden').text('');
    for(var index in options.query.sort){
        $('#' + id + ' th[data-field="' + options.query.sort[index].name + '"] .sort-badge').text(parseInt(index)+1).removeClass('hidden');
    }
    
    window[id] = options;
    HtmlBuilder_table_query(id);
}


// 打开设置过滤条件的弹窗
function HtmlBuilder_table_setFilter(id, field) {
    var dom = $('#FILTERS-TEMPLATE').clone().attr('id','FILTERS_'+id).css('display','block');
    showDialogs({
        title:'编辑筛选条件！',
        body: dom,
        ok:{
            text:'确定',
            click:function(o){ //HtmlBuilder_table_filterConfirm
                var f = window['FILTERS_'+id].getFilters();
                // console.log(id,f);
                if(!window['FILTERS_'+id].checkFilters(f)){
                    showDialogs({
                        body:'有些条件不完整，请修复',
                        width:'200px',
                        delay:2000
                    },'sub');
                    return;
                }
                window[id].query.filters = f.length>0 ? {op:'AND',sub:f} : [];
                // console.log('当前条件',window[id].query.filters);
                HtmlBuilder_table_query(id);
                o.close();
            }
        },
        close:{
            text:'取消',
            click:function(o){o.close()}
        }
    });
    
    var fields = {};
    for(var i=0;i<window[id].fields.length;i++){
        if(window[id].fields[i].hasOwnProperty('filter') && !window[id].fields[i].filter) continue;
        fields[window[id].fields[i].name] = window[id].fields[i].text;
    }
    var filters = $.isEmptyObject(window[id].query.filters) ? [] : window[id].query.filters.sub;
    // console.log('当前的过滤条件', window[id].query.filters, filters);
    // 固定的添加项目，用于新增
    // 找到 当前的 filters
    window['FILTERS_'+id] = new Vue({
        data:function(){
            return {
                fields:fields,
                filters:filters
            };
        },
        el:'#FILTERS_'+id,
        methods:{
            getFilters:function(){
                return this.filters;
            },
            checkFilters:function(fs){
                var rs = true;
                for(var i=0; i<fs.length; i++){
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
    

}

// 执行AJAX查询
function HtmlBuilder_table_query(id){
   $.ajax(window[id].queryApi,{method:'post',data:window[id].query}).done(function(data){
        HtmlBuilder_table_setData(data, id);
    });
}

function HtmlBuilder_table_selectRow(obj){
    if(obj.hasClass('htmlbuild-table-selected-row')){
        obj.removeClass('htmlbuild-table-selected-row');
    }else{
        obj.addClass('htmlbuild-table-selected-row');
    }
}

// AJAX请求后的数据设置
function HtmlBuilder_table_setData(data, id) {
    var obj  = $('#'+id);
    var html = '';
    var body = obj.find('.htmlbuild-table-body').html('');
    var options = window[id];
    options.query.limit = {page:data.page, size:data.size}; // 缓存当前页面参数
    for(var row in data.list){
        var tr_class = row % 2 ? 'odd' : 'even';
        var primary = data.list[row][options.primary || 'id'];
        var canSelect = (options.selectMode && (!data.list[row].hasOwnProperty('_CAN_SELECT_') || data.list[row]._CAN_SELECT_))
                        ? 'onclick="HtmlBuilder_table_selectRow($(this))"' : '';
        var tr = '<tr data-id="' + primary + '" ' + canSelect + ' class="' + tr_class + '">';
        
        for(var i in options.fields){
            var def = options.fields[i]; // defined
            var val = data.list[row][def.name] || ''; // 当前字段值，不存在则为空
            val = def.render ? eval(def.render)(val, data.list[row], def) : val; // 使用 render 函数处理内容
            var cls = def.hasOwnProperty('class') ? def.class : '';
            cls += (def.hasOwnProperty('show') && def.show == 0) ? ' hidden' : '';
            tr += '<td data-field="' + def.name + '" class="' + cls + '">' + val + '</td>';
        }
        if(options.canEdit){
            var updateApi = options.updateApi.replace('_ID_', primary);
            var edit_str = '&nbsp;';
            if(!data.list[row].hasOwnProperty('_CAN_UPDATE_') || data.list[row]._CAN_UPDATE_){
                edit_str += '<a title="编辑" href="' + updateApi + '"><i class="fa fa-edit" style="font-size:18px"></i></a>&nbsp; ';
            }
            if(!data.list[row].hasOwnProperty('_CAN_DELETE_') || data.list[row]._CAN_DELETE_){
                edit_str += '<a title="删除" href="#" onclick="HtmlBuilder_table_delItems(\'' + id + '\',\'' + primary + '\');return false;"><i class="fa fa-trash-o" style="font-size:18px"></i></a>';
            }
            if(options.editCallback){
                edit_str = eval(options.editCallback)(data.list[row], edit_str);
            }
            tr += '<td class="text-center">' + edit_str + '</td>';
        }
        html += tr + '</tr>';
    }
    body.append(html);
    options.fixedTop && HtmlBuilder_table_fixedColumnWidth(id);
    
    // 分页设置
    data.page = parseInt(data.page);
    data.size = parseInt(data.size);
    data.total = parseInt(data.total);
    var offset = (data.page - 1 ) * data.size + 1;
    var end = offset + parseInt((data.list.length < data.size ? data.list.length : data.size)) - 1;
    obj.find('.page-status').text(offset + '-' + end + ' of ' + data.total);
    obj.find('.input-sm.page-size').val(data.size);
    var po = obj.find('.dataTables_paginate');
    var totalPages = Math.ceil(data.total/data.size)||1;
    var startPage = data.page > totalPages ? totalPages : data.page;
    po.twbsPagination('destroy');
    po.twbsPagination({
        totalPages: totalPages,
        startPage: startPage,
        onPageClick: function (event, page) {
            var tmp = Object.assign({}, window[id].query.limit);
            if($.isEmptyObject(tmp) || parseInt(tmp.page) === page) return;
            options.query.limit.page = page;
            window[id] = options;
            HtmlBuilder_table_query(id);
        }
    });
}

// 设置固定表头的宽度（在固定表头时）
function HtmlBuilder_table_fixedColumnWidth(id){
    var obj = $('#'+id);
    var refer = obj.find('.htmlbuild-table-header th');
    var options = window[id];

    obj.find('.htmlbuild-table-header').css({
        position: 'absolute',
        'z-index': 12,
        'background-color': '#fff',
    });
    var tds = '';
    for(var index=0; index<refer.length; index++){
        var width = $(refer[index]).outerWidth();
        tds += '<td width="' + width + 'px"' + (index === 0 ? (' height="' + $(refer[index]).outerHeight() + '"' ) : '') + ' style="min-width:' + width + 'px;">';
    }
    obj.find('.htmlbuild-table-body').prepend($('<tr>').append(tds)).css({
        height: (options.height || 200 ) + 'px',
        display: 'block',
        'overflow-y': 'scroll'
    });
}

OUT
);





// 初始化脚本，不缓存
$this->script(/** @lang JS */"
window['$id'] = $element;
$(function(){ HtmlBuilder_table_init('$id'); });
");

