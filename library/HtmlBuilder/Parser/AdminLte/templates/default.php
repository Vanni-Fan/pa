<?php
echo '<',$type,' id="',$id,'" class="',($attributes['class']??''),'">',$label;
foreach($elements as $element){
    echo $this->parse($element);
}
echo "</$type>";
