<?php
if($subtype === 'color'){
    $this->css('/dist/bower_components/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css');
    $this->js('/dist/bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js');
    $this->script("$('#$id-input').colorpicker();");
} elseif( $subtype === 'time'){
    $this->css('/dist/plugins/timepicker/bootstrap-timepicker.min.css');
    $this->js('/dist/plugins/timepicker/bootstrap-timepicker.min.js');
    $this->script("$('#$id-input').timepicker({showInputs:false});");
} elseif($subtype === 'date'){
    $this->css('/dist/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css');
    $this->js('/dist/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js');
    $this->script("$('#$id-input').datepicker({autoclose:true});");
} elseif($subtype === 'daterange' || $subtype === 'datetimerange' || $subtype ==='datetime'){
    $this->js('/dist/bower_components/moment/moment.js');
    $this->css('/dist/bower_components/bootstrap-daterangepicker/daterangepicker.css');
    $this->js('/dist/bower_components/bootstrap-daterangepicker/daterangepicker.js');
    if($subtype === 'datetime'){
        $this->script("$('#$id-input').daterangepicker({singleDatePicker:true,timePicker:true,timePicker24Hour:true,timePickerSeconds:true,locale:{format:'YYYY-MM-DD HH:mm:ss'}});");
    }else{
        $this->script("$('#$id-input').daterangepicker(".($subtype==='datetimerange'?'{timePicker:true}':'').");");
    }
}
?>
<div id="<?=$id?>" class="form-group htmlbuild-form <?=$attributes['class']??''?>" style="<?=$visible?'':'display:none;'?>">
    <?php include(__DIR__.'/_label.php'); ?>
    <div class="<?=($inputAfterIcon||$inputBeforeIcon)?'input-group':''?> <?=$labelWidth?('col-sm-'.(12-$labelWidth)):''?>" style="<?=($inputAfterIcon||$inputBeforeIcon)?'':'padding:0'?>">
        <?php if($inputBeforeIcon) { ?><span class="input-group-addon"><i class="<?=$inputBeforeIcon?>"></i></span><?php } ?>
        <input data-validator="HB_input_verify('<?=$id?>',$('#<?=$id?>-input'))" <?=$inputMask?('data-inputmask="'.$inputMask.'"'):''?> id="<?=$id?>-input" name="<?=$name?>" type="<?=$subtype?>" value="<?=$value?>" class="form-control" placeholder="<?=$placeHolder?>" <?=$enabled?'':'disabled'?>>
        <?php if($inputAfterIcon) { ?><span class="input-group-addon"><i class="<?=$inputAfterIcon?>"></i></span><?php } ?>
    </div>
    <?php if($description){?>
        <span id="<?=$id?>-description" class="help-block pull-left"><?=$description?></span>
    <?php }?>
    <?php if($validators || $statistics){ ?>
    <span id="<?=$id?>-message" class="help-block pull-right"></span>
    <?php } ?>
</div>

<?php
$this->script('window["'.$id.'"] = '.$element.';');

$this->script(/** @lang JavaScript */<<<'Out'
function HB_input_check(id,obj){
    $('#'+id+'-input').blur(function(e){ // 失去焦点判断
        HB_input_verify(id, $(e.currentTarget));    
    }).keyup(function (e){
        HB_input_verify(id, $(e.currentTarget));
    });
}
function HB_input_ok(id){
    $('#'+id).removeClass('has-error').addClass('has-success'); 
    $('#'+id+'-message').text('');
    $('#'+id+'-input').attr('data-verified', 1);
}
function HB_input_error(id,msg){
    $('#'+id).addClass('has-error'); 
    $('#'+id+'-message').text(msg);
    $('#'+id+'-input').attr('data-verified', 0);
}
function HB_input_verify(id,obj){
    var value = obj.val().trim();
    var valid = true;
    if(!window[id].required && !value) return true; // 如果不是必须，并且值为空，则不检查
    if(window[id].validators.length>0){ // 验证器判断
        for(var i in window[id].validators){
            var validator = window[id].validators[i];
            switch(validator.type){
                case 'number':
                    if(value < validator.rule.minValue || value > validator.rule.maxValue){
                        valid = false;
                    }
                    break;
                case 'text':
                    if(value.length < validator.rule.minLength || value.length > validator.rule.maxLength ){
                        valid = false;
                    }
                    break;
                case 'regex':
                    if(!(new RegExp(validator.rule.regex)).test(value)){
                        valid = false;
                    }
                    break;
                case 'mail':
                    if(!/^[^@ ]+@[^\.]+\.[a-z]{2,}$/i.test(value)){
                        valid = false;
                    }
                    break;
                case 'expression':
                    valid = eval(validator.rule.expression);
                    break;
                case 'callback':
                    valid = eval(validator.rule.callback)();
                    break;
            }
            if(valid)HB_input_ok(id);
            else{
                HB_input_error(id, validator.text);
                break;
            }
        }
    }
    return valid;
}
Out
);

$this->script('HB_input_check("'.$id.'")');

if($inputMask){
    $this->js('/dist/plugins/input-mask/jquery.inputmask.js');
    $this->js('/dist/plugins/input-mask/jquery.inputmask.date.extensions.js');
    $this->js('/dist/plugins/input-mask/jquery.inputmask.extensions.js');
    $this->script("$('#$id-input').inputmask();\n");
}
?>