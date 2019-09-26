<?php
$this->style(/** @lang CSS */ <<<'OUT'
    .htmlbuild-multselect{
        height: 35px;
        border-right:none;
    }
    .htmlbuild-multselect:last-child{
        border-right:solid 1px darkgrey;;
    }
OUT
);
?>

<div id="<?=$id?>" class="form-group htmlbuild-form <?=$attributes['class']??''?>" style="<?=$visible?'':'display:none;'?>">
    <?php include(__DIR__.'/_label.php'); ?>
    <div id="<?=$id?>-selects" class="<?=$labelWidth?('col-sm-'.(12-$labelWidth)):''?>"></div>
    <?php if($description){?>
    <span id="<?=$id?>-description" class="help-block pull-left"><?=$description?></span>
    <?php }?>
    <?php if($validators){ ?>
    <span id="<?=$id?>-message" class="help-block pull-right"></span>
    <?php } ?>
</div>

<script>
var <?=$id?>_Obj = <?=$element??'null'?>;
$(function () {
    var colwidth = Math.floor(12 / <?=$id?>_Obj.selects.length) || 1;
    for(var index in <?=$id?>_Obj.selects){
        var select = $('<select name="' + <?=$id?>_Obj.selects[index].name + '"' + (<?=$id?>_Obj.style == 'single' ? '' : ' multiple=""') + ' class="htmlbuild-multselect col-sm-' + colwidth + '"></select>');
        var next_id = parseInt(index)+1;
        select.attr('id','<?=$id?>_'+index);
        select.attr('data-sid','<?=$id?>_' + next_id);
        $('#<?=$id?>-selects').append(select);
        
        setOptions(select, <?=$id?>_Obj.rootApi);
        if(<?=$id?>_Obj.selects[index].subItemsApi){
            select.attr('data-api', <?=$id?>_Obj.selects[index].subItemsApi);
            select.change(function(event){ // 设置子菜单
                var obj = $(event.target);
                setOptions($('#'+obj.data('sid')), obj.data('api') + obj.val())
            });
        }
    }
});

function setOptions(selectObj, api){
    selectObj.find('option').remove();
    $.ajax(api).done(function(data){
        for(var i in data){
            selectObj.append('<option value="' + data[i].value + '">' + data[i].text + '</option>');
        }
        var api = selectObj.data('api');
        if(api) setOptions($('#'+selectObj.data('sid')), selectObj.data('api'));
    });
}
</script>
