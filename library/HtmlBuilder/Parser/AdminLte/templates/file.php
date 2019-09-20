<?php
$this->css('/dist/plugins/jQuery-File-Upload/css/jquery.fileupload.css');
$this->js('/dist/plugins/jQuery-File-Upload/js/jquery.fileupload.js');

# 全局样式
$this->style(/** @lang CSS */"
/** 拖拽 **/
.htmlbuild-form-file-over{
    border-color:red;
}
/** 虚拟边框 **/
#$id{
    border-style: dashed;
    border-width:2px;
}
");

$isSingle = $subtype==='single';
$canCorp  = (bool)($corpWidth || $corpHeight);

# 通用函数
$this->script(/** @lang JavaScript */ "
function getFileIcon(file){
    var fa = 'fa ';
    var type = file.type;
    if(/^image/.test(type))      fa += 'fa-file-image-o';
    else if(/^audio/.test(type)) fa += 'fa-file-audio-o';
    else if(/^video/.test(type)) fa += 'fa-file-video-o';
    else if(/^text/.test(type))  fa += 'fa-file-text-o';
    else if(/^application/.test(type)){
        if(/excel|sheet/.test(type))    fa += 'fa-file-excel-o';
        else if(/pdf/.test(type))       fa += 'fa-file-pdf-o';
        else if(/powerpoint/.test(type))fa += 'fa-file-powerpoint-o';
        else if(/word/.test(type))      fa += 'fa-file-word-o';
        else                            fa += 'fa-file-o';
    }
    else fa += 'fa-file-o';
    return fa;
}
function getFileSize(file){
    var size = file.size;
    var label='0B';
    var base = [
        {label:'GB',pow:1073741824},
        {label:'MB',pow:1048576},
        {label:'KB',pow:1024},
        {label:'B',pow:1}
    ];
    for(var i in base){
        if(size > base[i].pow){
            label = (Math.floor(size/base[i].pow * 100)/100) + base[i].label;
            break;
        }
    }
    return label;
}
");

# 如果有限制最大宽高，那么就有裁剪
if($canCorp) {
    $corpWidth   = $corpWidth??$corpHeight??100;
    $corpHeight  = $corpHeight??$corpWidth??100;
    $aspectRatio = $corpWidth/$corpHeight;
    $this->css('/dist/plugins/bootstrap-slider/slider.css');
    $this->css('/dist/plugins/cropperjs/cropper.css');
    $this->js('/dist/plugins/bootstrap-slider/bootstrap-slider.js');
    $this->js('/dist/plugins/cropperjs/cropper.js');
    
    # 图片裁剪 CSS
    $this->style(/** @lang CSS */ "
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
        .cropperWarpDiv i{
            margin-right:5px;
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
        }"
    );

    # 图片裁剪 JS
    $this->script(/** @lang JavaScript */ "
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
            doCrop:function doCrop(func,options){
                var that = this;
                this.cropper.getCroppedCanvas(options || {}).toBlob(function(blob){
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
        cropperWarp.init();"
    );

    # 裁剪器
    $this->html(/** @lang HTML */'
        <div class="cropperWarpDiv" style="display: none">
            <div class="top">
                <i onclick="cropperWarp.doRotate(-90)" class="fa fa-rotate-left hand">左转</i>
                <div onclick="cropperWarp.doVertical()" class="hand"><i class="fa fa-exchange" style="transform: rotate(90deg);"></i>垂直翻转</div>
                <i onclick="cropperWarp.doHorizontal()" class="fa fa-exchange hand">水平翻转</i>
                <i onclick="cropperWarp.doReset()" class="fa fa-refresh hand">重置</i>
                <i onclick="cropperWarp.doCrop(function(){})" class="fa fa-crop hand">裁剪</i>
                <i onclick="cropperWarp.hide()" class="fa fa-close hand">取消</i>
            </div>
            <div class="body"><img id="htmlBuilder_image_source" width="100%"></div>
            <div class="bottom"><input id="htmlBuilder_slider_bar" type="text" data-slider-handle="custom"></div>
        </div>'
    );
}else $aspectRatio = '';
# 初始化JS
$this->script(/** @lang JavaScript */ "
$(function() {
    $('#$id-file').fileupload(
        { autoUpload:false, replaceFileInput:false, singleFileUploads:'$isSingle'?true:false, dropZone:$('#$id') }
    ).bind('fileuploadadd', function(e, data){
        if('$isSingle'){
            var file = data.files[0];
            $('#$id-text').val(file.name);
            $('#$id-message').text('类型:'+file.type+',大小:'+getFileSize(file));
            if('$canCorp'){
                var imageBlobUrl = cropperWarp.getBlobUrl(file);
                $('#$id-icon').parent().css({
                    padding: 0,
                    width: '35px',
                    backgroundImage: 'url(' + imageBlobUrl + ')',
                    backgroundSize: 'cover'
                }).click(function(){
                    cropperWarp.init().setImage(imageBlobUrl).show();
                });
            }else{
                $('#$id-icon').removeClass().addClass(getFileIcon(file));
            }
        }else{
            $('#$id-no-file').css('display','none');
            $('#$id-files').css('width','100%').css('display','flex');
            for(var i in data.files){
                var file = data.files[i];
                var tmp = $('#$id-file-template').clone();
                if('$canCorp'){
                    tmp.find('img').attr('src', cropperWarp.getBlobUrl(file));
                    tmp.find('i').remove();
                }else{
                    tmp.find('img').remove();
                    tmp.find('i').addClass(getFileIcon(file)).css('font-size','40px');
                }
                tmp.find('.filename').text(file.name);
                tmp.css('display','flex');
                tmp.find('.fileinfo').text('类型:'+file.type+', 大小:' +getFileSize(file));
                $('#$id-files').append(tmp);
            }
        }
    }).bind('fileuploaddrop', function(e){
        $('#$id-no-file').css('display','none');
        $('#$id-files').css('width','100%').css('display','flex');
    }).bind('fileuploaddragover',function(e){
        $('#$id').addClass('htmlbuild-form-file-over');
    }).bind('fileuploaddone',function(e){
        console.log('完成');
    }).bind('fileuploadstop',function(e){
        console.log('完成stop');
    });
    $('#$id').on('dragleave',function(e){
        $('#$id').removeClass('htmlbuild-form-file-over');
    });
    
    $('#$id-folder-btn,#$id-text,#$id-no-file').click(function(e){ $('#$id-file').click(); });
    
    if('$canCorp') cropperWarp.setAspectRatio($aspectRatio);
});

");

?>

<?php if($subtype === 'single'){ ?>
<div id="<?=$id?>" class="form-group htmlbuild-form <?=$attributes['class']??''?>" style="<?=$visible?'':'display:none;'?>">
    <?php include(__DIR__.'/_label.php'); ?>
    <input accept="<?=$accept?>" id="<?=$id?>-file" name="<?=$name?>" type="file" style="display: none">
    <div class="input-group <?=$labelWidth?('col-sm-'.(12-$labelWidth)):''?>">
        <span class="input-group-addon"><i id="<?=$id?>-icon" class="fa fa-file-o"></i></span>
        <input id="<?=$id?>-text" type="text" class="form-control" placeholder="<?=$placeHolder?>" readonly="true">
        <span class="input-group-addon" id="<?=$id?>-folder-btn"><i class="fa fa-folder-open"></i></span>
    </div>
    <?php if($description){?><span id="<?=$id?>-description" class="help-block pull-left"><?=$description?></span><?php }?>
    <?php if($validators || $statistics){ ?><span id="<?=$id?>-message" class="help-block pull-right"></span><?php } ?>
</div>
<?php }elseif($subtype === 'multiple'){ ?>
<div id="<?=$id?>" class="<?=$attributes['class']??''?>" style="display:flex;flex-wrap:wrap;">
    <input accept="<?=$accept?>" id="<?=$id?>-file" name="<?=$name?>" multiple="multiple" type="file" style="display: none">
    <div id="<?=$id?>-no-file" style="border-style: dashed;border-width: 1px;width:100%;display: flex;align-items: center;justify-content: center;transition: all 1s;">
        <i class="fa fa-cloud-upload" style="font-size: 50px;padding: 10px;"></i>
        <span><?=$label?:'Drag & Drop a File'?></span>
    </div>
    <div id="<?=$id?>-files" style="border-style: dashed;border-width: 1px;width:100%;transition: width 1s;display:none;flex-wrap: wrap;">
        <div id="<?=$id?>-file-template" style="padding:10px;display:none;width:100%;">
            <div style="flex-basis:50px">
                <img src="" style="width:50px; height:50px;float:left;">
                <i></i>
            </div>
            <div style="flex-grow: 1">
                <div class="filename" style="font-weight: bold;padding-bottom:5px;padding-left:5px;padding-right: 5px;">件名文件名.jpg</div>
                <div class="fileinfo" style="padding-left:5px;padding-right: 5px;">Type:image/jpeg, Size:192.1k</div>
            </div>
            <div style="flex-basis: 50px">
                <button type="button" class="btn btn-block btn-primary btn-xs" onclick="cropperWarp.init().setImage($('#<?=$id?>-file-template img').attr('src')).show();">Edit</button>
                <button type="button" class="btn btn-block btn-danger btn-xs">Remove</button>
            </div>
        </div>
    </div>
</div>
<?php } ?>