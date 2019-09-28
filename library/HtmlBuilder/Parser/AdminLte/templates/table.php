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
                        <thead>
                            <tr class="htmlbuild-table-header"></tr>
                        </thead>
                        <tbody class="htmlbuild-table-body">
                            <tr class="htmlbuild-table-columns" style="display:none"></tr>
                        </tbody>
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
.dataTables_paginate .pagination{
    float: right!important;
    margin: auto;
}
OUT
);
?>

<div id="<?=$id?>"></div>

<?php
$this->script(/** @lang JavaScript */ <<<'OUT'
function HtmlBuilder_table_findFieldIndex(field_name, fields){
    for(var i in fields) if(field_name === fields[i].name) return i;
    return -1;
}
function HtmlBuilder_table_fixedColumnWidth(id,options){
    var obj = $('#'+id);
    var refer = obj.find('.htmlbuild-table-header th');
    obj.find('.htmlbuild-table-header').css({
        position: 'absolute',
        'z-index': 12,
        'background-color': '#fff',
    });
    obj.find('.htmlbuild-table-columns').css('display','');
    var tds = '';
    for(var index=0; index<refer.length; index++){
        var width = $(refer[index]).outerWidth();
        tds += '<td width="' + width + 'px"' + (index === 0 ? (' height="' + $(refer[index]).outerHeight() + '"' ) : '') + ' style="min-width:' + width + 'px;">';
    }
    obj.find('.htmlbuild-table-columns').append(tds);
    obj.find('.htmlbuild-table-body').css({
        height: (options.height || 200 ) + 'px',
        display: 'block',
        'overflow-y': 'scroll'
    });
}
function HtmlBuilder_table_init(options,id){
    var obj = $('#htmlbuilder-table-template').clone();
    obj.removeAttr('id').css('display','block');
    obj.find('.box-title').html(options.name);
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
        // {"name":"a","text":"字段A","tooltip":"这个是字段A","sort":1,"filter":1,"edit":1,"delete":"canDelete","width":100,"show":1,"type":"text","params":[],"icon":"fa fa-users","class":""}
        var field = options.fields[index];
        // <i class="fa fa-sort-asc text-info"></i> <i class="fa fa-sort-desc text-info"></i>
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
                filterStatus = '<i class="fa fa-filter text-gray"></i>';
            }else{
                tooltip += '</div>';
                filterStatus = '<i class="fa fa-filter text-info has-filter" data-html="true" data-toggle="tooltip" title="' + tooltip + '"></i>';
            }
        }
        if(field.sort){
            var sort_index = HtmlBuilder_table_findFieldIndex(field.name, options.query.sort);
            if(sort_index === -1){
                sortStatus = '<i class="fa fa-sort text-gray"></i>';
            }else{
                sortStatus = '<i class="fa fa-sort-amount-' + options.query.sort[sort_index].type + ' text-info"></i><span class="sort-type">'+ (parseInt(sort_index)+1) + '</span>';
            }
        }
        var th =
        '<th ' + (field.hasOwnProperty('width') ? ('width="' + field.width + 'px" style="min-width:' + field.width + 'px;"') : '') + ' class="text-center ' + (field.class ? field.class : '') + '">' + (field.icon ? ('<span class="'+field.icon+'"></span> ') : '') +
            field.text +
            sortStatus +
            filterStatus +
        '</th>';
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
    var body = obj.find('.htmlbuild-table-body');
    $.ajax(options.queryApi).done(function(data){
        var html = '';
        for(var row in data.list){
            var tr_class = row % 2 ? 'odd' : 'even';
            var tr = '<tr class="' + tr_class + '">';
            if(options.selectMode){
                tr += '<td class="text-center"><input type="checkbox"></td>';
            }
            for(var field in data.list[row]){
                var def = options.fields[HtmlBuilder_table_findFieldIndex(field, options.fields)];
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
        options.fixedTop && HtmlBuilder_table_fixedColumnWidth(id,options);
        
        // 分页设置
        obj.find('.page-status').text((data.page * data.size) + '-' + (data.size+data.list.length) + ' of ' + data.total);
        obj.find('.dataTables_paginate').twbsPagination({
            totalPages: Math.ceil(data.total/data.size),
            startPage: data.page
            // onPageClick: function (event, page) {
            //     $('#page-content').text('Page ' + page);
            // }
        });
        $('.has-filter').tooltip()
    });
    $('#'+id).append(obj);
}
function HtmlBuilder_table_getPage(obj, total, current, show){
    obj.twbsPagination({
        totalPages: 35,
        visiblePages: 7,
        onPageClick: function (event, page) {
            $('#page-content').text('Page ' + page);
        }
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