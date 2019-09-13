<form id="<?=$id?>" action="<?=$action?>" method="<?=$method?>" enctype="multipart/form-data">
    <?php foreach($elements as $element) echo $this->parse($element); ?>
</form>