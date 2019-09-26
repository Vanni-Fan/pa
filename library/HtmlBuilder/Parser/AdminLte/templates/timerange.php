<?php
$this->js('/dist/plugins/input-mask/jquery.inputmask.js');
$this->js('/dist/plugins/input-mask/jquery.inputmask.date.extensions.js');
$this->script("$('#$id input').inputmask('hh:mm:ss', {placeholder:'__:__:__',insertMode: false,showMaskOnHover: false,alias: 'datetime',hourFormat: 24});\n");
?>
<div id="<?=$id?>" class="form-group htmlbuild-form <?=$attributes['class']??''?>" style="<?=$visible?'':'display:none;'?>">
    <?php include(__DIR__.'/_label.php'); ?>
    <div class="input-group <?=$labelWidth?('col-sm-'.(12-$labelWidth)):''?>">
        <input name="<?=$name?>[0]" type="text" class="form-control" value="<?=$value[0]??''?>">
        <div class="input-group-addon" style="border-left: none;border-right: none;"><i class="fa fa-clock-o"></i></div>
        <input name="<?=$name?>[1]" type="text" class="form-control" value="<?=$value[1]??''?>">
    </div>
    <?php if($description){?>
        <span id="<?=$id?>-description" class="help-block pull-left"><?=$description?></span>
    <?php }?>
    <?php if($validators){ ?>
        <span id="<?=$id?>-message" class="help-block pull-right"></span>
    <?php } ?>
</div>

