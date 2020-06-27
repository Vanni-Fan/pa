<?php

if(empty($style)){
    $style = $visible ? "display:flex;$style;" : 'display:none;';
}else{
    if(strpos($style,'}')){
        echo '<style>'.$style.'</style>';
        $style = '';
    }
}

echo '<',$type,' id="',$id,'" style="',$style,'" class="',($attributes['class']??''),'">',$label;
foreach($elements as $element){
    echo $this->parse($element);
}
echo "</$type>";
