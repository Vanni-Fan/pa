<?php
$this->js('/dist/plugins/twbs-pagination/jquery.twbsPagination.min.js');

$this->html(/** @lang HTML */<<<'OUT'
<div class="box box-primary" id="htmlbuilder-table-template" style="display:none;">
    <div class="box-header with-border">
        <h3 class="box-title"><?=$name?></h3>
    </div>
    <div class="box-body">
        <div class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-bordered table-striped dataTable" style="margin-bottom:0">
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
                    <select class="input-sm page-size">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="page-status"></span>
                </div>
            </div>
            <div class="col-sm-7 dataTables_paginate">
                <div class=" paging_simple_numbers"></div>
            </div>
        </div>
    </div>
</div>
OUT
);
$this->style(/** @lang CSS */ <<<'OUT'
.htmlbuild-table-header i{
    width:15px;
    margin-left:5px;
}
.htmlbuild-table-header i:hover{
    color:green !important;
    cursor: pointer;
}
.htmlbuild-table-header .sort-type{
    background-color: #31708f;
    color: white;
    border-radius: 15px;
    height: 15px;
    width: 15px;
    display: inline-block;
    line-height: 15px;
    position: relative;
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
OUT
);
?>

<div id="<?=$id?>"></div>
    
<div id="htmlbuilder-table-filter-template" class="input-group margin" style="display:none;">
    <div class="input-group-btn">
        <span type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" style="width:110px;"><span class="current-operation">操作符</span><span class="fa fa-caret-down pull-right"></span></span>
        <ul class="dropdown-menu">
            <li class="disabled"><a>选择一个操作符</a></li>
            <li class="divider"></li>
            <li value="==" onclick="HtmlBuilder_table_opetationClick($(this));"><a href="#">等于</a></li>
            <li value=">"  onclick="HtmlBuilder_table_opetationClick($(this));"><a href="#">大于</a></li>
            <li value=">=" onclick="HtmlBuilder_table_opetationClick($(this));"><a href="#">大于或等于</a></li>
            <li value="<"  onclick="HtmlBuilder_table_opetationClick($(this));"><a href="#">小于</a></li>
            <li value="<=" onclick="HtmlBuilder_table_opetationClick($(this));"><a href="#">小于或等于</a></li>
            <li value="!=" onclick="HtmlBuilder_table_opetationClick($(this));"><a href="#">不等于</a></li>
            <li value="%"  onclick="HtmlBuilder_table_opetationClick($(this));"><a href="#">包含</a></li>
        </ul>
    </div>
    <input type="text" class="form-control">
    <span class="input-group-btn">
        <span class="badge bg-yellow filter-close" onclick="HtmlBuilder_table_delFilter($(this));"><span class="fa fa-close"></span></span>
        <span type="button" class="btn btn-info" onclick="HtmlBuilder_table_addFilter($(this));" style="width:45px;"><span class="fa fa-check filter-edit-icon"></span></span>
    </span>
</div>

<!--
<?=$element?>
-->

<?php
$this->script(/** @lang JavaScript 1.5 */ <<<'OUT'
// 初始化表格
function HtmlBuilder_table_init(options,id){
    // 初始化对象
    options.query  = options.query  || {};
    options.fields = options.fields || [];
    options.query.sort    = options.query.sort    || [];
    options.query.limit   = options.query.limit   || {};
    options.query.filters = options.query.filters || [];
    $('#'+id).attr('data-options', JSON.stringify(options).replace(/"/g,'&#34;'));

    var obj = $('#htmlbuilder-table-template').clone();
    obj.removeAttr('id').css('display','block').find('.box-title').html(options.name);
    var header = obj.find('.htmlbuild-table-header');
    
    // 初始化表头
    var html = '';
    if(options.selectMode){    // fa-check-square:全选，   fa-square:全不选，   fa-minus-square:反选
        html += '<th style="padding:0;text-align:center;vertical-align:middle;min-width:80px;" width="80px">' +
        '<div class="btn-group">'+
              '<button type="button" class="btn btn-primary btn-xs"><i class="fa fa-check-square"></i></button>'+
              '<button type="button" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-transfer"></i></button>'+
              (options.canEdit ? '<button type="button" class="btn btn-primary btn-xs"><i class="fa fa-trash-o"></i></button>' : '') +
        '</div></th>';
    }
    
    for(var index in options.fields){
        var field = options.fields[index];
        var sortStatus = '';
        var filterStatus = '';
        if(field.filter) {
            var tooltip = '<div>';
            for(var filter_index in options.query.filters){
                if(field.name === options.query.filters[filter_index].name){
                    tooltip += "<b class='filter-field'>" + field.text + '</b>';
                    tooltip += "<b class='filter-operation'>" + $('<div>').html(options.query.filters[filter_index].operation).html() + '</b>';
                    tooltip += "<b class='filter-value'>" + options.query.filters[filter_index].value + '</b>';
                    tooltip += '<br/>';
                }
            }
            if(tooltip === '<div>'){
                filterStatus = '<i class="fa fa-filter text-gray" onclick="HtmlBuilder_setFilter($(this).parent(\'th\'),\'' + id + '\',\'' + field.name + '\')" ></i>';
            }else{
                tooltip += '</div>';
                filterStatus = '<i onclick="HtmlBuilder_setFilter($(this).parent(\'th\'),\'' + id + '\',\'' + field.name + '\')" class="fa fa-filter text-info has-filter" data-html="true" data-toggle="tooltip" title="' + tooltip + '"></i>';
            }
        }
        if(field.sort){
            var sort_index = options.query.sort.findIndex(function(_v){ return _v.name === field.name; });
            if(sort_index === -1){
                sortStatus = '<i onclick="HtmlBuilder_sort(\'' + id + '\', \'' + field.name + '\')" class="fa fa-sort text-gray"></i>';
            }else{
                sortStatus = '<i onclick="HtmlBuilder_sort(\'' + id + '\', \'' + field.name + '\')" class="fa fa-sort-amount-' + options.query.sort[sort_index].type + ' text-info"></i><span class="sort-type">'+ (parseInt(sort_index)+1) + '</span>';
            }
        }
        var filter_json = ' data-filters="' + JSON.stringify(options.query.filters.filter(function(value){
            return value.name === field.name;
        })).replace(/"/g,'&#34;') + '"';
        var th =
        '<th data-field="' + field.name + '"'  + filter_json + (field.hasOwnProperty('width') ? ('width="' + field.width + 'px" style="min-width:' + field.width + 'px;"') : '') + ' class="text-center ' + (field.class ? field.class : '') + '">' + (field.icon ? ('<span class="'+field.icon+'"></span> ') : '') +
            field.text +
            sortStatus +
            filterStatus +
        '</th>';
        console.log(th);
        html += th;
    }
    if(options.canEdit){
        html += '<th class="text-center" width="90px" style="min-width:90ox">编辑 <i class="btn btn-primary fa fa-plus" style="width:22px;height:22px;line-height: 18px;padding: 1px;margin-top: -2px;"></i></th>';
    }
    header.append(html);
    // header.render();
    
    // 设置页码尺寸
    obj.find('.page-size').val(options.query.limit.size);
    
    // 填充数据
    // var body = obj.find('.htmlbuild-table-body');
    $('#'+id).append(obj);
    HtmlBuilder_table_query(id);
}

// 点击排序时的动作
function HtmlBuilder_sort(id, field){
    var options = JSON.parse($('#'+id).data('options').replace(/&#34;/g,'"'));
    var found = options.query.sort.findIndex(function(_v){ return _v.name === field});
    if(found === -1){
        options.query.sort.push({name:field,type:'asc'});
    }else{
        var type = options.query.sort[found].type;
        if(type === 'asc'){
            options.query.sort[found].type = 'desc';
        }else{
            options.query.sort.splice(found,1);
        }
    }
    $('#'+id).attr('data-options', JSON.stringify(options).replace(/"/g, '&#34;'));
    console.log('需要设置的参数',options.query.sort);
    HtmlBuilder_table_query(id);
}

// 打开设置过滤条件的弹窗
function HtmlBuilder_setFilter(field_th, id, field) {
    var filters = $(field_th).data('filters') || [];
    var body = $('<div data-id="' + id +'" data-field="' + field + '">');
    // 修改项目
    for(var index in filters){
        var item = $('#htmlbuilder-table-filter-template').clone().css('display','table').removeAttr('id').attr('data-index',index);
        item.find('input').val(filters[index].value);
        var operation_name = item.find('li[value="'+filters[index].operation+'"]').addClass('active').text();
        item.find('.current-operation').text(operation_name);
        body.append(item);
    }
    // 添加项目
    var add = $('#htmlbuilder-table-filter-template').clone().css('display','table').removeAttr('id').attr('data-index',parseInt(index)+1);
    add.find('.filter-edit-icon').removeClass('fa-check').addClass('fa-plus');
    add.find('.filter-close').remove();
    body.append(add);
    
    showDialogs({
        title:'编辑窗口',
        body: body,
        // height:300,
        ok:{
            text:'确定',
            click:HtmlBuilder_table_filterConfirm
        },
        close:{
            text:'取消',
            click:function(o){o.close()}
        }
    });
}

// 过滤条件中的操作符更改时的动作
function HtmlBuilder_table_opetationClick(obj){
    obj.parent('ul').find('li').removeClass('active');
    obj.addClass('active');
    obj.parent().parent().find('.current-operation').text(obj.text());
}

// 删除一个过滤条件
function HtmlBuilder_table_delFilter(obj) {
  console.log(obj.parent().parent().data('index'));
}

// 添加一个过滤条件
function HtmlBuilder_table_addFilter(obj) {
  console.log(obj.parent().parent().data('index'));
}

// 过滤条件确认的动作
function HtmlBuilder_table_filterConfirm(obj) {
    var fields = obj.find('.modal-body>div');
    var field = $('#' + fields.data('id') + ' th[data-field="' + fields.data('field') + '"]'); // 原始th field 对象
    console.log(field.data('filters')); // 源对象
    HtmlBuilder_table_query(fields.data('id'));
    obj.close();
}

// 执行AJAX查询
function HtmlBuilder_table_query(id){
    var options = JSON.parse($('#'+id).data('options').replace(/&#34;/g,'"'));
    console.log('提交的参数', options.query);
    $.ajax(options.queryApi,{method:'post',data:options.query}).done(function(data){
        HtmlBuilder_table_setData(data, id);
    });
}

// AJAX请求后的数据设置
function HtmlBuilder_table_setData(data, id) {
    var obj  = $('#'+id);
    var html = '';
    var body = obj.find('.htmlbuild-table-body').html('');
    var options = JSON.parse(obj.data('options').replace(/&#34;/g,'"'));
    console.log(options,data.list);
    
    for(var row in data.list){
        var tr_class = row % 2 ? 'odd' : 'even';
        var tr = '<tr class="' + tr_class + '">';
        if(options.selectMode){
            tr += '<td class="text-center"><input type="checkbox"></td>';
        }
        for(var field in data.list[row]){
            var field_index = options.fields.findIndex(function(_v){ return _v.name === field });
            // if(field_index === -1) continue; // 其实一定是会有的
            var def = options.fields[field_index];
            var cls = (def.class ? (' class="' + def.class + '"') : '');
            tr += '<td' + cls + '>' + data.list[row][field] + '</td>';
        }
        if(options.canEdit){
            tr +=
            '<td class="text-center">' +
                (data.list[row].hasOwnProperty('canEdit') ? (data.list[row].canEdit ? 'Edit ' : '') : '<a href="#"><i class="fa fa-edit" style="font-size:18px"></i></a> ') +
                (data.list[row].hasOwnProperty('canDelete') ? (data.list[row].canDelete ? ' Remove' : '') : '&nbsp; <a href="#"><i class="fa fa-trash-o" style="font-size:18px"></i></a>') +
            '</td>';
        }
        html += tr + '</tr>';
    }
    body.append(html);
    options.fixedTop && HtmlBuilder_table_fixedColumnWidth(id);
    
    // 分页设置
    obj.find('.page-status').text((data.page * data.size) + '-' + (data.size+data.list.length) + ' of ' + data.total);
    obj.find('.dataTables_paginate').twbsPagination({
        totalPages: Math.ceil(data.total/data.size),
        startPage: data.page,
        onPageClick: function (event, page) {
            options.query.limit.page = page;
            console.log('设置分页后的值', options.query);
            obj.attr('data-options', JSON.stringify(options).replace(/"/g, '&#34;'));
            HtmlBuilder_table_query(id);
        }
    });
}

// 设置固定表头的宽度（在固定表头时）
function HtmlBuilder_table_fixedColumnWidth(id){
    var obj = $('#'+id);
    var refer = obj.find('.htmlbuild-table-header th');
    var options = JSON.parse(obj.data('options').replace(/&#34;/g,'"'));

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

$this->script(/** @lang JS */"
$(function(){
    HtmlBuilder_table_init($element, '$id');
});
");


?>