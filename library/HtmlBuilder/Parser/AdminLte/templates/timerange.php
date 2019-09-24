<?php
$this->js('/dist/plugins/input-mask/jquery.inputmask.js');
$this->js('/dist/plugins/input-mask/jquery.inputmask.date.extensions.js');
$this->script("$('#$id input').inputmask('hh:mm:ss', {placeholder:'__:__:__',insertMode: false,showMaskOnHover: false,alias: 'datetime',hourFormat: 24});\n");
?>
<div id="<?=$id?>" class="input-group">
    <input type="text" class="form-control">
    <div class="input-group-addon" style="border-left: none;border-right: none;"><i class="fa fa-clock-o"></i></div>
    <input type="text" class="form-control">
</div>

