<div id="<?=$id?>" class="box <?=$style?> <?=$attributes['class']??''?>">
    <?php if($header->text || $header->element){ ?>
    <div id="<?=$id?>-header" class="box-header with-border">
        <?php if($header->element){ echo $this->parse($header->element); } else { ?>
        <h3 class="box-title">
            <?=$labelIcon?"<i class='$labelIcon'></i>":''?>
            <?=$header->text?>
        </h3>
        <?php if($canMini || $canClose){ ?>
            <div class="box-tools pull-right">
                <?php if($canMini) { ?>
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                <?php }if($canClose){ ?>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                <?php }?>
            </div>
        <?php } ?>
    <?php } ?>
    </div>
    <?php } ?>
    
    <div id="<?=$id?>-body" class="box-body">
        <?=$body->text?:$this->parse($body->element)?>
    </div>
    
    <?php if($footer->text || $footer->element){ ?>
    <div id="<?=$id?>-footer" class="box-footer clearfix">
        <?=$footer->text?:$this->parse($footer->element)?>
    </div>
    <?php } ?>
</div>