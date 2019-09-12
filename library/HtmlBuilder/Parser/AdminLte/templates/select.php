<?php
if($subtype === 'select2') {
    $this->css('/dist/htmlbuilder.css');
    $this->css('/dist/bower_components/select2/dist/css/select2.min.css');
    $this->js('/dist/bower_components/select2/dist/js/select2.full.min.js');
    $script = "$('#$id-select').select2(" . ($isTags ? '{tags:true}' : '') . ");\n";
    $this->script($script);
}
?>
<div id="<?=$id?>" class="form-group htmlbuild-form <?=$attributes['class']??''?>" style="<?=$visible?'':'display:none;'?>">
    <?php include(__DIR__.'/_label.php'); ?>
    <div class="<?=$labelWidth?('col-sm-'.(12-$labelWidth)):''?>" id="<?=$id?>">
        <select
                id="<?=$id?>-select"
                name="<?=$name?>"
                class="form-control <?=$subtype==='select2'?'select2':''?> <?=$isTags?'tags':''?>" <?=$multiple?'multiple="multiple"':''?>
                data-placeholder="<?=$placeHolder?>"
                style="width:100%;height: auto;"
        >
        <?php foreach($choices as $item){ $selected = (array_search($item['value'], $value)===false) ? '' : ' selected'; ?>
            <option <?=$selected?> value="<?=$item['value']?>"><?=$item['text']?></option>
        <?php } ?>
        </select>
    </div>
</div>