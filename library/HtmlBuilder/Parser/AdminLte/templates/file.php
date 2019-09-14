<?php
$this->css('/dist/plugins/jQuery-File-Upload/css/jquery.fileupload.css');
$this->css('/dist/plugins/cropperjs/cropper.css');
$this->js('/dist/plugins/jQuery-File-Upload/js/jquery.fileupload.js');
$this->js('/dist/plugins/jQuery-File-Upload/js/jquery.iframe-transport.js');
$this->js('/dist/plugins/cropperjs/cropper.js');
# 图片缩放
$this->js('/dist/plugins/jQuery-File-Upload/js/jquery.fileupload-process.js');
$this->js('/dist/plugins/jQuery-File-Upload/js/jquery.fileupload-image.js');

$this->js('https://blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js');
$this->js('https://blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js');


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


$this->script("
<script>
$('#$id-file').fileupload(
    {
        autoUpload:false
        ,replaceFileInput:false
        ,singleFileUploads:true
        ,dropZone:$('#$id')
        ,acceptFileTypes:/(\.|\/)(gif|jpe?g|png)$/i
//        ,disableImageResize:false
//        ,imageCrop: true
        
        
//        ,previewMaxWidth: 120
//        ,previewMaxHeight: 120
//        ,previewCrop: false
//
//        ,disableImageResize:false
//        ,imageMaxWidth: 1024
//        ,imageMaxHeight: 1024
//        ,imageCrop: false // Force cropped images
        
        
//        ,imageMaxWidth:800
//        ,imageMaxHeight:800
//        ,disableImageResize: /Android(?!.*Chrome)|Opera/.test(window.navigator && navigator.userAgent),
    }
).bind('fileuploadadd', function(e, data){
    $('#$id-text').val(data.files[0].name);
    updateFileTypeIcon($('#$id-icon'), data.files[0].type);
    updateFileStatMsg($('#$id-message'),data.files[0]);
    
    loadImage(
        data.files[0],
        function(img){
            var t = document.body.appendChild(img);
            const cropper = new Cropper(document.getElementById('A1'), {
              aspectRatio: 16 / 9,
              crop(event) {
                console.log(event.detail.x);
                console.log(event.detail.y);
                console.log(event.detail.width);
                console.log(event.detail.height);
                console.log(event.detail.rotate);
                console.log(event.detail.scaleX);
                console.log(event.detail.scaleY);
              },
            });
        },
        { maxWidth: 600 }
    );
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

    <div id="<?=$id?>" class="<?=$attributes['class']??''?>" style="display:flex;flex-wrap:wrap;">
        <div id="<?=$id?>-no-file" style="border-style: dashed;border-width: 1px;width:100%;display: flex;align-items: center;justify-content: center;transition: all 1s;">
            <i class="fa fa-cloud-upload" style="font-size: 50px;padding: 10px;"></i>
            <span>Drag & Drop a File</span>
        </div>
        <div id="<?=$id?>-has-file" style="border-style: dashed;border-width: 1px;transition: width 1s;display:none;">
            <div style="padding:10px;display:flex;">
                <div style="flex-basis:50px">
                    <img src="/dist/adminlte/img/avatar.png" style="width:50px; height:50px;float:left;">
                </div>
                <div style="flex-grow: 1">
                    <div style="font-weight: bold;padding-bottom:5px;padding-left:5px;padding-right: 5px;">件名文件名....jpg</div>
                    <div style="padding-left:5px;padding-right: 5px;">10.30m/33m (120kb/s)</div>
                    <div class="progress active progress-xxs" style="height: 5px;margin-bottom: 0;margin-right: 5px;margin-left: 5px;">
                        <div class="progress-bar progress-bar-gray progress-bar-striped" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%">
                            <span class="sr-only">60% Complete (warning)</span>
                        </div>
                    </div>
                </div>
                <div style="flex-basis: 50px">
                    <button type="button" class="btn btn-block btn-primary btn-xs">Edit</button>
                    <button type="button" class="btn btn-block btn-danger btn-xs">Remove</button>
                </div>
            </div>
        </div>
    </div>














    <div id="<?=$id?>" class="form-group htmlbuild-form <?=$attributes['class']??''?>" style="<?=$visible?'':'display:none;'?>">
        <?php include(__DIR__.'/_label.php'); ?>
        <input accept="<?=$accept?>" id="<?=$id?>-file" name="<?=$name?>" multiple="multiple" type="file" style="">
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


<?php } ?>