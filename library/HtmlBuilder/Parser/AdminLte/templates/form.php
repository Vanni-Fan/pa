<form id="<?=$id?>" action="<?=$action?>" method="<?=$method?>">
    <?php foreach($elements as $element) echo $this->parse($element); ?>
</form>