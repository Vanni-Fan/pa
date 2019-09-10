<div class="form-group" id="<?=$id?>" style="display:flex;">
<?php
foreach($choices as $item) {
    $checked = (array_search($item,$choices)===false) ? '' : 'checked';
    $colwidth = 100 / $colCount;
?>
    <label style="flex-basis: <?=$colwidth?>%;"><input name="<?=$name?>" <?=$checked?> value="<?=$item['value']?>" type="<?=$subtype?>"><?=$item['text']?></label>
    <?php }?>
</div>

<?php
$this->css('/dist/plugins/iCheck/all.css');
$this->js('/dist/plugins/iCheck/icheck.min.js');
$this->script("$('#$id input').iCheck({{$subtype}Class:'i{$subtype}_{$flat}-{$style}'});");
?>