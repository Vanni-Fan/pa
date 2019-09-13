<?php $this->css('/dist/htmlbuilder.css'); ?>
<?php if($label){ ?>
    <label for="<?=$id?>-input" class="<?=$labelWidth?('col-sm-'.$labelWidth):''?> control-label htmlbuild-input-label htmlbuild-input-label-<?=$labelPosition?>">
        <?php if($labelIcon) {?><i id="<?=$id?>-labeIcon" class="<?=$labelIcon?>"></i><?php }?>
        <span id="<?=$id?>-label"><?=$label?><?php if($required){ ?><i class="require-star">*</i><?php }?></span>
        <?php if($tooltip){ ?><i id="<?=$id?>-tooltip" data-placement="bottom" class="fa fa-question-circle" data-toggle="tooltip" data-original-title="<?=$tooltip?>"></i><?php }?>
    </label>
<?php } ?>