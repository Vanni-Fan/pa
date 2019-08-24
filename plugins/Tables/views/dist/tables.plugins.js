$(function () {
    $('#dataTable thead th').click(function(e){
        var obj = $(e.target);
        var method = obj.hasClass('sorting_asc') ? 'sorting_desc' : 'sorting_asc';
        // $('#dataTable thead th').removeClass().addClass("sorting"); // 将其他的全部初始化
        // obj.addClass(method);
        $('#sort_field').val(obj.data('field'));
        $('#sort_method').val(method.substr(8));
        $('#filter_form').submit();
    });


    $('#filter_form').submit( function(e) {
        var data = $( this ).serializeArray();
        if(data.length == 5 && !data[4].value){
            $('#field_template').remove();
            return true;
        }
        for(var i=2; i<data.length; i+=3){
            console.log("field:",data[i]," opt:",data[i+1],' val:',data[i+2]);
            if(!data[i].value || !data[i+1].value || !data[i+2].value) {
                showDialogs({body:"Please enter the complete filtering rules", delay:2000});
                return false;
            }
        }
        return true;
    });
})


function deleteItem(e){
    var url = $(e.target).attr('href');
    showDialogs({
        title:"The record will be deleted.",
        body:"Are you sure?",
        close:{text:"Cancel", click:function(_){_.close()}},
        ok:{text:"Sure!", click:function(_){
            console.log(url);
            $.ajax(url, {
                method:"DELETE",
                success:function(e){
                    location.reload()
                }
            });
        }}
    });
    return false;
}

function addField(self, field, operations, value){
    var newobj = self ? $('#field_template') : $('#field_template').clone().removeAttr('id');

    newobj.find('.operation_filter').text(self ? '+' : 'x');
    if(field){
        newobj.find('.filter_field').text(field);
        newobj.find('.field_form').val(field);
    }
    if(operations){
        newobj.find('.filter_operations').text(operations);
        newobj.find('.operations_form').val(operations);
    }
    value && newobj.find('.filter_input').val(value);

    if(!self) $('#all_field').prepend(newobj);
}

function selectField(e){
    var obj = $(e.target);
    var txt = obj.data('field');
    var parent = $(obj.parent().parent().parent());
    $(parent.find('button')[0]).text(txt);
    parent.find('.field_form').val(txt);
    return false;
    // console.log(e.target.parentElement.parentElement.parentElement);
    // console.log(e.target.parent.parent.parent);
}

function selectOperations(e) {
    console.log(e);
    var obj = $(e.target);
    var txt = obj.text();
    var parent = $(obj.parent().parent().parent());
    $(parent.find('button')[0]).text(txt);
    parent.find('.operations_form').val(txt);
    return false;
}

function addOrDelField(e) {
    var obj = $(e.target);
    if(obj.text() == '+'){
        addField();
    }else{
        obj.parent().parent().remove();
    }
    console.log(e);
}