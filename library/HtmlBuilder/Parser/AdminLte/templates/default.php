<?php
echo '<',$type,' id="',$id,'" style="',($style??''),'" class="',($attributes['class']??''),'">',$label;
foreach($elements as $element){
    echo $this->parse($element);
}
echo "</$type>";
