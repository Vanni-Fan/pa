<?php
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
                    <select name="example1_length" class="input-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    &nbsp;1-5 of 10
                </div>
            </div>
            <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example1_paginate">
                    <ul class="pagination pagination-sm no-margin pull-right">
                        <li><a href="#">«</a></li>
                        <li><a href="#">1</a></li>
                        <li><a href="#">2</a></li>
                        <li><a href="#">3</a></li>
                        <li><a href="#">»</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
OUT
);
$this->style(/** @lang CSS */ <<<'OUT'
.htmlbuild-table-header i{
    width:15px;
}
.htmlbuild-table-header i:hover{
    color:green !important;
    cursor: pointer;
}
OUT
);
?>

<div id="<?=$id?>"></div>

<textarea class="" style="width:100%; height:200px;"><?=$element?></textarea>
<?php
$this->script(/** @lang JavaScript */ <<<'OUT'
function HtmlBuilder_table_findFieldIndex(field_name, fields){
    for(var i in fields) if(field_name === fields[i].name) return i;
    return -1;
}
function HtmlBuilder_table_fixedColumnWidth(id){
    var obj = $('#'+id);
    var refer = obj.find('.htmlbuild-table-header th');
    obj.find('.htmlbuild-table-header').css({
        position: 'absolute',
        'z-index': 12,
        'background-color': '#fff',
    });
    console.log(refer);
    obj.find('.htmlbuild-table-columns').css('display','');
    var tds = '';
    for(var index=0; index<refer.length; index++){
        tds += '<td width="' + $(refer[index]).outerWidth() + '" height="' + $(refer[index]).outerHeight() + '">';
    }
    obj.find('.htmlbuild-table-columns').append(tds);
    obj.find('.htmlbuild-table-body').css({
        height:'200px',
        display: 'block',
        'overflow-y': 'scroll'
    });
}
function HtmlBuilder_table_Init(options,id){
    var obj = $('#htmlbuilder-table-template').clone();
    obj.removeAttr('id').css('display','block');
    obj.find('.box-title').html(options.name);
    var header = obj.find('.htmlbuild-table-header');
    // 初始化表头
    var html = '';
    // if(options.selectMode){ // fa-check-square-o:全选， fa-square-o:全不选， fa-minus-square-o:反选
    if(options.selectMode){    // fa-check-square:全选，   fa-square:全不选，   fa-minus-square:反选
        html += '<th style="padding:0;text-align:center;vertical-align:middle;" width="60px">' +
        '<div class="btn-group">'+
              '<button type="button" class="btn btn-primary btn-xs"><i class="fa fa-check-square"></i></button>'+
              '<button type="button" class="btn btn-primary btn-xs"><i class="fa fa-minus-square"></i></button>'+
        '</div></th>';
    }
    for(var index in options.fields){
        // {"name":"a","text":"字段A","tooltip":"这个是字段A","sort":1,"filter":1,"edit":1,"delete":"canDelete","width":100,"show":1,"type":"text","params":[],"icon":"fa fa-users","class":""}
        var field = options.fields[index];
        // <i class="fa fa-sort-asc text-info"></i> <i class="fa fa-sort-desc text-info"></i>
        var th =
        '<th ' + (field.hasOwnProperty('width') ? ('width="' + field.width + '"') : '') + ' class="text-center ' + (field.class ? field.class : '') + '">' + (field.icon ? ('<span class="'+field.icon+'"></span> ') : '') +
            field.text +
            (field.sort ? '<i class="fa fa-sort text-gray"></i>' : '' ) +
            (field.filter ? '<i class="fa fa-filter text-gray"></i>' : '' ) +
        '</th>';
        html += th;
    }
    if(options.canEdit){
        html += '<th class="text-center">编辑 <button type="button" class="btn btn-primary btn-xs">新建</button></th>';
    }
    header.append(html);
    // header.render();
    
    // 填充数据
    var body = obj.find('.htmlbuild-table-body');
    $.ajax(options.queryApi).done(function(data){
        var html = '';
        for(var row in data){
            var tr_class = row % 2 ? 'odd' : 'even';
            var tr = '<tr class="' + tr_class + '">';
            if(options.selectMode){
                tr += '<td class="text-center"><input type="checkbox"></td>';
            }
            for(var field in data[row]){
                var def = options.fields[HtmlBuilder_table_findFieldIndex(field, options.fields)];
                var cls = (def.class ? (' class="' + def.class + '"') : '');
                tr += '<td' + cls + '>' + data[row][field] + '</td>';
            }
            if(options.canEdit){
                tr +=
                '<td class="text-center">' +
                    (data[row].hasOwnProperty('canEdit') ? (data[row].canEdit ? 'Edit ' : '') : 'Edit ') +
                    (data[row].hasOwnProperty('canDelete') ? (data[row].canDelete ? ' Remove' : '') : ' Remove') +
                '</td>';
            }
            html += tr + '</tr>';
        }
        body.append(html);
        options.fixedTop && HtmlBuilder_table_fixedColumnWidth(id);
    });
    $('#'+id).append(obj);
}

OUT
);

$this->script(/** @lang JS */"
    $(function(){HtmlBuilder_table_Init($element, '$id');});
");


?>