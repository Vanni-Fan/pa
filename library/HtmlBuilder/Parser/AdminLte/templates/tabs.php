<div id="<?=$id?>" class="nav-tabs-custom <?=$attributes['class']??''?>">
    <ul id="<?=$id?>-tabs" class="nav nav-tabs">
<?php
    $body = '';
    foreach($elements as $i=>$tab){
        $contents = '';
        foreach($tab->elements as $element){
            $contents .= $this->parse($element);
        }
        $active = $tab->visible?'active':'';
        $body .= '<div class="tab-pane '.$active.'" id="'.$id.'-content-'.$i.'">'.$contents.'</div>';
?>
        <li id="<?=$id?>-tabs-<?=$i?>" class="<?=$active?>"><a href="#<?=$id?>-content-<?=$i?>" data-toggle="tab" aria-expanded="<?=$tab->visible?'true':'false'?>"><?=$tab->name?></a></li>
    <?php }?>
    </ul>

    <div id="<?=$id?>-content" class="tab-content">
        <?=$body?>
    </div>
</div>