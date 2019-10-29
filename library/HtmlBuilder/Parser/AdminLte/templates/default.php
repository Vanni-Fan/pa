<?php

if(empty($style)){
    $style = $visible ? 'display:flex;' : 'display:none;';
    foreach($styles as $k=>$v){
        $style .= $k.':'.$v.';';
    };
}

echo '<',$type,' id="',$id,'" style="',$style,'" class="',($attributes['class']??''),'">',$label;
foreach($elements as $element){
    echo $this->parse($element);
}
echo "</$type>";
