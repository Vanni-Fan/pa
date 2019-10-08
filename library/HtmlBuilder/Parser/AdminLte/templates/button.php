<?php
$this->css('/dist/htmlbuilder.css');

if($subtype === 'input') { ?>
<div id="<?=$id?>"  class="input-group margin">
    <?php
    if($btnBeforeIcon) {
        if(is_string($btnBeforeIcon)){
            echo '<span class="input-group-btn"><button type="button" class="btn btn-',$style,'"><i class="',$btnBeforeIcon,'"></i></button></span>';
        }else{
            echo '<span class="input-group-btn">',$this->parse($btnBeforeIcon),'</span>';
        }
    } ?>
    <input name="<?=$name?>" type="text" class="form-control">
    <?php
    if($btnAfterIcon) {
        if(is_string($btnAfterIcon)){
            echo '<span class="input-group-btn"><button type="button" class="btn btn-',$style,'"><i class="',$btnAfterIcon,'"></i></button></span>';
        }else{
            echo '<span class="input-group-btn">',$this->parse($btnAfterIcon),'</span>';
        }
    } ?>
</div>
<?php } elseif($subtype === 'default') { ?>
    <button id="<?=$id?>" type="<?=$action?>" class="btn <?=($btnAfterIcon||$btnBeforeIcon)?'btn-social':''?> <?=$flat?'btn-flat':''?> <?=$enabled?'':'disabled'?> <?=$block?'btn-block':''?> btn-<?=$style?> <?=$class??''?>">
        <?php if($btnBeforeIcon) { ?><i class="<?=$btnBeforeIcon?>"></i><?php } ?>
        <?=$label?>
        <?php if($btnAfterIcon) { ?><i class="<?=$btnAfterIcon?>"></i><?php } ?>
        <?php if($badge){?>
        <span class="badge bg-<?=$badgeColor?>"><?=$badge?></span>
        <?php } ?>
    </button>
<?php }elseif($subtype === 'group') { ?>
<div id="<?=$id?>"  class="btn-group<?=$vertical?'-vertical':''?> <?=$flat?'btn-flat':''?> <?=$enabled?'':'disabled'?> <?=$block?'btn-block':''?> <?=$class??''?>">
    <?php foreach($elements as $element){ echo $this->parse($element); }?>
</div>
<?php } ?>
