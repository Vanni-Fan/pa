<?php $this->css('/dist/htmlbuilder.css'); ?>
<?php

$this->style(/** @lang CSS */'
.label-auto-width ~ *{
    flex-grow:1;
}
');


?>
<?php
if($label){
    if($labelWidth){
        if($labelWidth === 'auto') {
            $class = 'label-auto-width';
            $next_element_class = '';
        }else{
            $class = 'col-sm-'.$labelWidth;
            $next_element_class = 'col-sm-'.(12-$labelWidth);
        }
    }

?>
    <label for="<?=$id?>-input" class="<?=$class?> control-label htmlbuild-input-label htmlbuild-input-label-<?=$labelPosition?>">
        <?php if($labelIcon) {?><i id="<?=$id?>-labeIcon" class="<?=$labelIcon?>"></i><?php }?>
        <span id="<?=$id?>-label"><?=$label?><?php if($required){ ?><i class="require-star">*</i><?php }?></span>
        <?php if($tooltip){ ?><i id="<?=$id?>-tooltip" data-placement="bottom" class="fa fa-question-circle" data-toggle="tooltip" data-original-title="<?=$tooltip?>"></i><?php }?>
    </label>
<?php }

if($tooltip){
    $this->script("$(function(){ $('#$id-tooltip').tooltip(); });\n");
}
?>