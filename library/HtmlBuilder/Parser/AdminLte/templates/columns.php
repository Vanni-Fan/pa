<div id="<?=$id?>" class="row <?=$attributes['class']??''?>">
<?php foreach($elements as $column){ ?>
    <div id="<?=$id?>-column" class="col-sm-<?=$column->width?>">
        <?php foreach($column->elements as $element){ ?>
            <?php echo $this->parse($element); ?>
        <?php } ?>
    </div>
<?php } ?>
</div>