<?php
if($inputMask){
//    $this->css('xxx.css');
}
?>

<?php $this->style('input', <<<Out
<style>
.input-label {
    padding: 0;
    line-height: 34px;
    margin: 0;
}
.input-label-top {}
.input-label-left{
    float: left;
    padding-right: 10px !important;
}
.input-label-left-right{
    float: left;
    padding-right: 10px;
    text-align: right;
}
.input-label-right-right{
    float: right;
    text-align: right;
    padding-left: 10px;
}
.input-label-right-left{
    float: right;
    text-align: left;
    padding-left: 10px;
}
.input-label-bottom {}

/** 公共 **/
.require-star{
    font-size: 20px;
    color: red !important;
    line-height: 0px;
    display: inline-block;
    vertical-align: middle;
}
.htmlbuild-form{
    clear: both;
    display: inline-block;
    width: 100%;
    margin-bottom: 5px !important;
}
</style>
Out
); ?>

<div id="<?=$id?>" class="form-group htmlbuild-form <?=$attributes['class']??''?>" style="<?=$visible?'':'display:none;'?>">
    <?php if($label){ ?>
    <label for="<?=$id?>-input" class="<?=$labelWidth?('col-sm-'.$labelWidth):''?> control-label input-label input-label-<?=$labelPosition?>">
        <?php if($labelIcon) {?><i id="<?=$id?>-labeIcon" class="<?=$labelIcon?>"></i><?php }?>
        <span id="<?=$id?>-label"><?=$label?><?php if($required){ ?><i class="require-star">*</i><?php }?></span>
        <?php if($tooltip){ ?><i id="<?=$id?>-tooltip" data-placement="bottom" class="fa fa-question-circle" data-toggle="tooltip" data-original-title="<?=$tooltip?>"></i><?php }?>
    </label>
    <?php } ?>
    <div class="<?=($inputAfterIcon||$inputBeforeIcon)?'input-group':''?> <?=$labelWidth?('col-sm-'.(12-$labelWidth)):''?>" style="<?=($inputAfterIcon||$inputBeforeIcon)?'':'padding:0'?>">
        <?php if($inputBeforeIcon) { ?><span class="input-group-addon"><i class="<?=$inputBeforeIcon?>"></i></span><?php } ?>
        <input <?=$inputMask?('data-inputmask="'.$inputMask.'"'):''?> id="<?=$id?>-input" name="<?=$name?>" type="<?=$subtype?>" value="<?=$value?>" class="form-control" placeholder="<?=$placeHolder?>" <?=$enabled?'':'disabled'?>>
        <?php if($inputAfterIcon) { ?><span class="input-group-addon"><i class="<?=$inputAfterIcon?>"></i></span><?php } ?>
    </div>
    <?php if($description){?>
        <span id="<?=$id?>-description" class="help-block pull-left"><?=$description?></span>
    <?php }?>
    <span id="<?=$id?>-message" class="help-block pull-right"></span>
</div>

<?php
$script     = '';
$required   = (int)$required;
$statistics = (int)$statistics;
if($tooltip){ $script .= "$('#$id-tooltip').tooltip();\n"; }
if($statistics || $required){
    $script .= "$('#$id-input').keyup(function(e){var v = $(e.target).val();\n";
    if($statistics) $script .= "$('#$id-message').text('长度:'+v.length+',词汇:'+v.split(/\b/).filter(function(i){ return i.trim() }).length);\n";
    if($required){ // 如果不能为空，添加警报
        $script .= "if(v.trim().length==0){ $('#$id').addClass('has-warning'); $('#$id-message').text('此字段为必填字段！') }\n";
        $script .= "else{ $('#$id').removeClass('has-warning'); }\n";
    }
    $script .= "});\n";
}

if($validators){
    $script .= "$('#$id-input').blur(function(e){var v=$(e.target).val();\n";
    foreach($validators as $v){
        $cond = '';
        switch($v->type){
            case 'number':
                $cond .= 'v>'.$v->rule->minValue.' && v<'.$v->rule->maxValue;
                break;
            case 'text':
                $cond .= 'v.length>'.$v->rule->minLength.' && v.length<'.$v->rule->maxLength;
                break;
            case 'regex':
                $cond .= '/'.$v->rule->regex.'/.test(v)';
                break;
            case 'mail':
                $cond .= '/^[^@ ]+@[^\.]+\.[a-z]{2,}$/i.test(v)';
                break;
        }
        if($cond){
            $script .= "if(!($cond)){ $('#$id').addClass('has-error'); $('#$id-message').text('{$v->text}'); return; }\n";
            $script .= "else{ $('#$id').removeClass('has-error').addClass('has-success'); $('#$id-message').text(''); }\n";
        }
    }
    $script .= "});\n";
}
if($inputMask){
    $this->js('/dist/plugins/input-mask/jquery.inputmask.js');
    $this->js('/dist/plugins/input-mask/jquery.inputmask.date.extensions.js');
    $this->js('/dist/plugins/input-mask/jquery.inputmask.extensions.js');
    $script .= "$('#$id-input').inputmask();\n";
}
?>

<?php if($script){ $this->script('$(function(){'.$script.'});'); }?>