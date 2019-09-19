<?php
$this->css('/dist/plugins/jQuery-File-Upload/css/jquery.fileupload.css');
$this->css('/dist/plugins/bootstrap-slider/slider.css');
$this->css('/dist/plugins/cropperjs/cropper.css');

$this->js('/dist/plugins/jQuery-File-Upload/js/jquery.fileupload.js');
$this->js('/dist/plugins/bootstrap-slider/bootstrap-slider.js');
$this->js('/dist/plugins/cropperjs/cropper.js');


$this->style("
<style>
.htmlbuild-form-file-over{
    border-color:red;
}
#$id{
    border-style: dashed;
    border-width:2px;
}
</style>
");

# 图片裁剪
$this->style("
<style>
        .cropperWarpDiv{
            display: flex;
            position: fixed;
            top:0;
            left:0;
            width:100%;
            height:100%;
            justify-content: space-between;
            align-items: center;
            flex-direction: column;
            z-index: 9998;
        }
        .cropperWarpDiv:after{
            content:'';
            width:100%;
            height:100%;
            opacity: 0.5;
            background-color:#000;
            position: fixed;
            top:0;
            left:0;
            z-index: 9999;
        }
        .cropperWarpDiv>.top,.cropperWarpDiv>.bottom{
            color:white;
            z-index:10000;
            min-height: 50px;
            height:50px;
            background-color:#000000b0;
            width:100%;
            text-align: center;
        }
        .cropperWarpDiv>.body{
            z-index:10000;
            /*opacity: .05;*/
            /*padding:20px;*/
            /*flex-grow: 1;*/
            padding: 0 !important;
            height: calc(100% - 100px);
            width:100%;
        }
        .cropperWarpDiv>.top{
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-evenly;
        }
        .cropperWarpDiv>.bottom{
            padding: 15px;
            /*position: absolute;*/
            bottom: 0;
        }
        .cropperWarpDiv .slider-handle.custom {
            background: transparent none;
        }
        .cropperWarpDiv .slider.slider-horizontal .slider-track-high {
            background-color: #cccccc;
        }
        .cropperWarpDiv .slider-selection{
            background-color: #cccccc;
            background-image: none;
        }
        .cropperWarpDiv .slider-handle.custom::before {
            line-height: 20px;
            font-size: 40px;
            content: '‖';
            color: #fff;
        }
        .cropperWarpDiv .hand{
            cursor: pointer;
        }
    </style>
");

$this->script("
<script>
$('#$id-file').fileupload(
    {
        autoUpload:false
        ,replaceFileInput:false
        ,singleFileUploads:true
        ,dropZone:$('#$id')
        ,acceptFileTypes:/(\.|\/)(gif|jpe?g|png)$/i
    }
).bind('fileuploadadd', function(e, data){
    $('#$id-text').val(data.files[0].name);
    updateFileTypeIcon($('#$id-icon'), data.files[0].type);
    updateFileStatMsg($('#$id-message'),data.files[0]);
    $('#ABCD').attr('src', cropperWarp.getBlobUrl(data.files[0]));
    
    console.log(data);
}).bind('fileuploaddrop', function(e){
    $('#$id-no-file').css('display','none');
    $('#$id-has-file').css('width','100%').css('display','flex');
}).bind('fileuploaddragover',function(e){
    $('#$id').addClass('htmlbuild-form-file-over');
});
$('#$id').on('dragleave',function(e){
    $('#$id').removeClass('htmlbuild-form-file-over');
});

;
$('#$id-folder-btn,#$id-text').click(function(e){ $('#$id-file').click(); });
</script>
");

# 但图片设置
$this->script("
<script>
function updateFileTypeIcon(obj, type){
    var fa = '';
    if(/^image/.test(type))      fa = 'fa-file-image-o';
    else if(/^audio/.test(type)) fa = 'fa-file-audio-o';
    else if(/^video/.test(type)) fa = 'fa-file-video-o';
    else if(/^text/.test(type))  fa = 'fa-file-text-o';
    else if(/^application/.test(type)){
        if(/excel/.test(type))          fa = 'fa-file-excel-o';
        else if(/pdf/.test(type))       fa = 'fa-file-pdf-o';
        else if(/powerpoint/.test(type))fa = 'fa-file-powerpoint-o';
        else if(/word/.test(type))      fa = 'fa-file-word-o';
    }
    else fa = 'fa-file-o';
    console.log(fa);
    obj.removeClass().addClass('fa '+fa);
}
function updateFileStatMsg(obj, file){
    obj.text('Type:' + file.type + ', Size:' + Math.floor(file.size/1024*100)/100 + 'k')
}
</script>
");

# 图片裁剪
$this->script("
<script>
var cropperWarp = {
    cropper: new Cropper(document.getElementById('htmlBuilder_image_source')),
    slider: $('#htmlBuilder_slider_bar').slider({step: 1,min: -45,value:0,max: 45,tooltip:'hide'}),
    sliderStatus: {rotate:0,horizontal:false,vertical:false},
    blob:null,
    doRotate:function doRotate(i){ this.cropper.rotate(i) },
    doHorizontal:function doHorizontal(){
        this.cropper.scale(this.sliderStatus.horizontal ? 1 : -1, this.sliderStatus.vertical ? -1 :1);
        this.sliderStatus.horizontal = !this.sliderStatus.horizontal;
    },
    doVertical:function doVertical(){
        this.cropper.scale(this.sliderStatus.horizontal ? -1 : 1, this.sliderStatus.vertical ? 1 :-1);
        this.sliderStatus.vertical = !this.sliderStatus.vertical;
    },
    doCrop:function doCrop(func){
        var that = this;
        this.cropper.getCroppedCanvas().toBlob(function(blob){
            that.blob = blob;
            func(URL.createObjectURL(blob));
            that.hide();
        }/*, 'image/png' */);
        return this;
    },
    setImage:function setImage(url){ this.cropper.replace(url); return this; }, // 设置图片
    setAspectRatio:function setAspectRatio(aspectRatio){ this.cropper.setAspectRatio(aspectRatio); return this; }, // 设置高宽比
    doReset:function doReset(){ this.cropper.reset(); return this; },
    getBlob:function getBlob(){ return this.blob },
    getBlobUrl:function getBlobUrl(blob){
        return URL.createObjectURL(blob || this.blob);
    },
    show:function show(){ $('.cropperWarpDiv').show(); return this; },
    hide:function hide(){ $('.cropperWarpDiv').hide(); return this; },
    init:function init(){
        var that = this;
        this.slider.on(\"slide\", function(sliderValue) {
            var newValue = sliderValue.value - that.sliderStatus.rotate;
            that.doRotate(newValue);
            that.sliderStatus.rotate = sliderValue.value;
        });
        this.slider.on('slideStart', function () { that.sliderStatus.rotate = 0 });
        this.slider.on('slideStop', function () { that.sliderStatus.rotate = 0; that.slider.slider('setValue',0);});
        return this;
    }
};
cropperWarp.init();
</script>
");
?>

<?php if($subtype === 'single'){ ?>
<div id="<?=$id?>" class="form-group htmlbuild-form <?=$attributes['class']??''?>" style="<?=$visible?'':'display:none;'?>">
    <?php include(__DIR__.'/_label.php'); ?>
    <input accept="<?=$accept?>" id="<?=$id?>-file" name="<?=$name?>" type="file" style="display: none">
    <div class="input-group <?=$labelWidth?('col-sm-'.(12-$labelWidth)):''?>">
        <span class="input-group-addon"><i id="<?=$id?>-icon" class="fa fa-file-o"></i></span>
        <input id="<?=$id?>-text" type="text" class="form-control" placeholder="选择一个文件" readonly="true">
        <span class="input-group-addon" id="<?=$id?>-folder-btn"><i class="fa fa-folder-open"></i></span>
    </div>
    <?php if($description){?>
        <span id="<?=$id?>-description" class="help-block pull-left"><?=$description?></span>
    <?php }?>
    <?php if($validators || $statistics){ ?>
        <span id="<?=$id?>-message" class="help-block pull-right"></span>
    <?php } ?>
</div>
<?php }elseif($subtype === 'multiple') { ?>


    <div class="cropperWarpDiv" style="display: none">
        <div class="top">
            <i onclick="cropperWarp.doRotate(-90)" class="fa fa-rotate-left hand">Rotate left</i>
            <div onclick="cropperWarp.doVertical()" class="hand"><i class="fa fa-exchange" style="transform: rotate(90deg);"></i>Flip vertical</div>
            <i onclick="cropperWarp.doHorizontal()" class="fa fa-exchange hand">Flip horizontal</i>
            <i onclick="cropperWarp.doReset()" class="fa fa-refresh hand">Reset</i>
            <i onclick="cropperWarp.doCrop(function(){})" class="fa fa-crop hand">Crop</i>
            <i onclick="cropperWarp.hide()" class="fa fa-close hand">Close</i>
        </div>

        <div class="body"><img id="htmlBuilder_image_source" width="100%"></div>
        <div class="bottom"><input id="htmlBuilder_slider_bar" type="text" data-slider-handle="custom"></div>
    </div>
    <input accept="<?=$accept?>" id="<?=$id?>-file" name="<?=$name?>" type="file" style="display: none">

    <div id="<?=$id?>" class="<?=$attributes['class']??''?>" style="display:flex;flex-wrap:wrap;">
        <div id="<?=$id?>-no-file" style="border-style: dashed;border-width: 1px;width:100%;display: flex;align-items: center;justify-content: center;transition: all 1s;">
            <i class="fa fa-cloud-upload" style="font-size: 50px;padding: 10px;"></i>
            <span>Drag & Drop a File</span>
        </div>
        <div id="<?=$id?>-has-file" style="border-style: dashed;border-width: 1px;width:100%;transition: width 1s;display:none;flex-wrap: wrap;">
            <div style="padding:10px;display:flex;width:100%;">
                <div style="flex-basis:50px">
                    <img id="ABCD" src="/dist/adminlte/img/avatar.png" style="width:50px; height:50px;float:left;">
                </div>
                <div style="flex-grow: 1">
                    <div style="font-weight: bold;padding-bottom:5px;padding-left:5px;padding-right: 5px;">件名文件名....jpg</div>
                    <div style="padding-left:5px;padding-right: 5px;">10.30m/33m (120kb/s)</div>
                </div>
                <div style="flex-basis: 50px">
                    <button type="button" class="btn btn-block btn-primary btn-xs" onclick="cropperWarp.init().setImage($('#ABCD').attr('src')).show();">Edit</button>
                    <button type="button" class="btn btn-block btn-danger btn-xs">Remove</button>
                </div>
            </div>
        </div>
    </div>





<?php } ?>