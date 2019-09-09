<?php $this->css('/dist/htmlbuilder.css'); ?>

<?php if($subtype === 'simple' && $label){ ?>
<div id="<?=$id?>-wrap" class="form-group htmlbuild-form <?=$attributes['lableClass']??''?>">

    <label id="<?=$id?>-label" for="<?=$id?>" class="<?=$labelWidth?('col-sm-'.$labelWidth):''?> control-label input-label input-label-<?=$labelPosition?>">
        <?php if($labelIcon) {?><i id="<?=$id?>-labeIcon" class="<?=$labelIcon?>"></i><?php }?>
        <span id="<?=$id?>-label"><?=$label?><?php if($required){ ?><i class="require-star">*</i><?php }?></span>
        <?php if($tooltip){ ?><i id="<?=$id?>-tooltip" data-placement="bottom" class="fa fa-question-circle" data-toggle="tooltip" data-original-title="<?=$tooltip?>"></i><?php }?>
    </label>

<?php }elseif($subtype === 'ckeditor'){
    $this->js('/dist/bower_components/ckeditor/ckeditor.js');
    $this->script("$(function(){ CKEDITOR.replace('$id') });\n");
}elseif($subtype === 'wysihtml5'){
    $this->css('/dist/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css');
    $this->js('/dist/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js');
    $this->script("$(function(){ $('#$id').wysihtml5() });\n");
} ?>

    <div class="<?=$labelWidth?('col-sm-'.(12-$labelWidth)):''?>" style="padding:0">
        <textarea
            id="<?=$id?>"
            name="<?=$name?>"
            class="form-control <?=$attributes['class']??''?>"
            placeholder="<?=$placeHolder?>"
            rows="<?=$rows?>"
            <?=($subtype==='simple')?'':'style="width:100%"'?>
        ><?=$value?></textarea>
    </div>

<?php if($subtype === 'simple' && $label) echo '</div>'; ?>