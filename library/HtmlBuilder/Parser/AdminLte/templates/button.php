<?php $this->css('/dist/htmlbuilder.css'); ?>


<!--

// 输入框组 input-group-btn
   [标签:label] [头部:btnBeforeIcon|下拉:btnBeforeMenu]，输入框:input，[尾部:btnAfterIcon|下拉:btnAfterMenu]

// 按钮组，每一个按钮会平均分布 btn-group/ btn-group-vertical
   [按钮:emements.0] [按钮:emements.1] ...

// 按钮图标 default
   [头部图标:btnBeforeIcon] [文本:label] [尾部图标:btnAfterIcon] [角标:badge,badgeStyle]
-->
<?php if($subtype === 'default') { ?>
    <a class="btn <?=($btnAfterIcon||$btnBeforeIcon)?'btn-social':''?> <?=$flat?'btn-flat':''?> <?=$enabled?'':'disabled'?> <?=$block?'btn-block':''?> btn-<?=$style?>">
        <?php if($btnBeforeIcon) { ?><i class="<?=$btnBeforeIcon?>"></i><?php } ?>
        <?=$label?>
        <?php if($btnAfterIcon) { ?><i class="<?=$btnAfterIcon?>"></i><?php } ?>
        <?php if($badge){?>
        <span class="badge bg-<?=$badgeColor?>"><?=$badge?></span>
        <?php } ?>
    </a>
<?php } ?>

<?php if($subtype === 'group') { ?>

<div class="btn-group">
    <button type="button" class="btn btn-info">Action</button>
    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <li><a href="#">Action</a></li>
        <li><a href="#">Another action</a></li>
        <li><a href="#">Something else here</a></li>
        <li class="divider"></li>
        <li><a href="#">Separated link</a></li>
    </ul>
</div>
<?php } ?>

<div class="btn-group">
    <button type="button" class="btn btn-success"><i class="fa fa-align-left"></i></button>
    <button type="button" class="btn btn-success"><i class="fa fa-align-center"></i></button>
    <button type="button" class="btn btn-success"><i class="fa fa-align-right"></i></button>
</div>

<div class="btn-group">
    <button type="button" class="btn btn-info btn-flat"><i class="fa fa-align-left"></i></button>
    <button type="button" class="btn btn-info btn-flat"><i class="fa fa-align-center"></i></button>
    <button type="button" class="btn btn-info btn-flat"><i class="fa fa-align-right"></i></button>
</div>


<div class="btn-group">
    <button type="button" class="btn btn-danger">1</button>
    <button type="button" class="btn btn-danger">2</button>

    <div class="btn-group">
        <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li><a href="#">Dropdown link</a></li>
            <li><a href="#">Dropdown link</a></li>
        </ul>
    </div>
</div>


<div class="btn-group-vertical">
    <button type="button" class="btn btn-info"><i class="fa fa-align-left"></i></button>
    <button type="button" class="btn btn-info"><i class="fa fa-align-center"></i></button>
    <button type="button" class="btn btn-info"><i class="fa fa-align-right"></i></button>
</div>