<div id="<?=$id?>" class="form-group htmlbuild-form <?=$attributes['class']??''?>" style="<?=$visible?'':'display:none;'?>">
    <?php include(__DIR__.'/_label.php'); ?>
    <div class="form-group" style="display:flex;flex-wrap: wrap;">
    <?php
    foreach($choices as $item) {
        $checked = (array_search($item['value'], $value)===false) ? '' : 'checked';
        $colwidth = 100 / $colCount;
    ?>
        <label style="flex-basis: <?=$colwidth?>%;display:flex;align-items:center;"><input name="<?=$name?>" <?=$checked?> value="<?=$item['value']?>" type="<?=$subtype?>"><?=$item['text']?></label>
    <?php }?>

    <?php
    if($other){ ?>
        <div style="flex-basis:<?=$colwidth?>%;display:flex;align-items:center;">
            <label style="white-space:nowrap;">
                <input id="<?=$id?>-other-check" name="<?=$name?>" value="<?=$item['value']?>" type="<?=$subtype?>">
                <?=is_string($other)?$other:''?>
            </label>
            <?php
        if($other instanceof \HtmlBuilder\Forms\Input){ ?>
            <div id="<?=$id?>-other-value" style="display: none">
            <?=$this->parse($other);?>
            </div>
    <?php }else{ ?>
            <input id="<?=$id?>-other-value" type="<?=$name?>-other" disabled="true" class="form-control" placeholder="<?=$placeHolder?>" style="flex-grow:1;display:none">
    <?php
        }
        echo '</div>';
    }
    ?>
    </div>
</div>

<?php
$this->css('/dist/plugins/iCheck/all.css');
$this->js('/dist/plugins/iCheck/icheck.min.js');
$this->script("$('#$id input').iCheck({{$subtype}Class:'i{$subtype}_{$flat}-{$iCheckStyle}'});");

if($other) {
    $this->script("$('#$id-other-check').on('ifChecked',function(e){
        $('#$id-other-value').attr('disabled',false).show();
    }).on('ifUnchecked', function(e){
        $('#$id-other-value').attr('disabled',true).hide();
    });");
}
?>