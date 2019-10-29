<?php
if($subtype === 'select2') {
    $this->css('/dist/bower_components/select2/dist/css/select2.min.css');
    $this->js('/dist/bower_components/select2/dist/js/select2.full.min.js');
    $script = "$('#$id-select').select2(" . ($isTags ? '{tags:true}' : '') . ");\n";
    $this->script($script);
}
$this->style(/** @lang CSS */'

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 32px;
}
.select2-container--default .select2-selection--single {
    border: 1px solid #d2d6de;
    border-radius: 0;
    height: 34px;
}

'
);
if(empty($style)){
    $style = $visible ? 'display:flex;' : 'display:none;';
    foreach($styles as $k=>$v){
        $style .= $k.':'.$v.';';
    };
}

?>
<div id="<?=$id?>" class="<?=$attributes['class']??'form-group htmlbuild-form'?>" style="<?=$style?>">
    <?php include(__DIR__.'/_label.php'); ?>
    <div class="<?=$next_element_class??''?>" id="<?=$id?>" style="<?=($labelWidth)?'padding:0':''?>">
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
    <?php if($description){?>
        <span id="<?=$id?>-description" class="help-block pull-left"><?=$description?></span>
    <?php }?>

</div>