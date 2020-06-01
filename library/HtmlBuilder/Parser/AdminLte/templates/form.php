<form id="<?=$id?>" action="<?=$action?>" method="<?=$method?>" enctype="multipart/form-data">
    <?php foreach($elements as $element) echo $this->parse($element); ?>
</form>
<?php
$this->script(/** @lang JavaScript */ <<<'Out'
// 检查所有的 input、select、file、textarea、checkbox、radio 的 data-validator 属性的定义，然后调用
function HB_form_check(id){
    var ok = true;
    $(
        '#'+id+' input[data-validator],'+
        '#'+id+' select[data-validator],'+
        '#'+id+' file[data-validator],'+
        '#'+id+' textarea[data-validator],'+
        '#'+id+' checkbox[data-validator],'+
        '#'+id+' radio[data-validator]'
    ).each(function(index, element) {
        ok = eval($(element).data('validator'));
        if($(element).attr('data-verified')){
            ok = $(element).attr('data-verified') == '1';
        }
        if(!ok) return false;
    });
    if(!ok){
        showDialogs({
            body:'<h4 class="text-center">请按照要求填写相关字段</h4>',
            delay:3000,
            width:300,
            ok:{text:'好的',click:function(o){o.close()}},
            close:{text:'关闭',click:function(o){o.close()}},
        });        
    }
    return ok;
}
Out
);

$this->script('$("#'.$id.'").submit(function(){ return HB_form_check("'.$id.'")})');


$event_str = '';
foreach ($events as $event){
    $event_str .= '$("#'. $id . ($event['selector'] ? " {$event['selector']}" : '') . '").'.$event['event'].'(eval('.$event['code'].'));';
    //[] = ['event' => $event_name, 'code' => $js_code, 'selector' => $selector];
}
if($event_str) $this->script($event_str);
?>