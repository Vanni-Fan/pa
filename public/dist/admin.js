var total_field_value = 0;
function getFieldFilterItem(post_name_preifx, field_name, operation, value, must) {
    let total = total_field_value++;
    field_name = field_name || '';
    operation  = operation || '';
    value = value || '';
    let options = '';
    let all_opt = ['>','<','=','>=','<=','!=','in','like'];
    for(let i in all_opt){
        options += '<option value="' + all_opt[i] + '"' + (operation==all_opt[i] ? ' selected' : '') + '>' + all_opt[i] + '</option>';
    }
    let str = '<div class="field_value">';
    str += '<div class="input-group">';
    str += '<span class="input-group-addon">' +
        '<input name="' + post_name_preifx +'[must][' + total + ']" type="checkbox" value="yes" class="minimal"' + (must=='yes'?' checked' : '') + '>' +
        '</span>';
    str += '<input name="' + post_name_preifx +'[field][' + total + ']" value="' + field_name + '" type="text" class="form-control" placeholder="ip,version,etc..">';
    str += '</div>';
    str += '<div class="input-group select_addon">';
    str += '<span class="input-group-addon">';
    str += '<select name="' + post_name_preifx +'[operation][' + total + ']" value="' + operation + '" class="form-control">';
    str += options;
    str += '</select>';
    str += '</span>';
    str += '<input name="' + post_name_preifx +'[value][' + total + ']" value="' + value + '" type="text" class="form-control" placeholder="192.168.1.%,1.0,etc..">';
    str += '<span onclick="$(event.currentTarget).parent().parent().remove();" class="input-group-addon"><i class="fa fa-remove"></i></span>';
    str += '</div>';
    str += '</div>';
    return str;
}















