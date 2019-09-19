<html>
<head>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="/dist/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/dist/plugins/bootstrap-slider/slider.css">
    <link rel="stylesheet" href="/dist/plugins/cropperjs/cropper.css">
    <link rel="stylesheet" href="/dist/bower_components/font-awesome/css/font-awesome.min.css">
    
    <script src="/dist/bower_components/jquery/dist/jquery.min.js"></script>
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
</head>

<body>
<div style="width:100px;height: 200px;">
<div class="cropperWarpDiv">
    <div class="top">
        <i onclick="cropperWarp.doRotate(-90)" class="fa fa-rotate-left hand">Rotate left</i>
        <div onclick="cropperWarp.doVertical()" class="hand"><i class="fa fa-exchange" style="transform: rotate(90deg);"></i>Flip vertical</div>
        <i onclick="cropperWarp.doHorizontal()" class="fa fa-exchange hand">Flip horizontal</i>
        <i onclick="cropperWarp.doReset()" class="fa fa-refresh hand">Reset</i>
        <i onclick="cropperWarp.doCrop(function(){})" class="fa fa-crop hand">Crop</i>
        <i onclick="cropperWarp.hide()" class="fa fa-close hand">Close</i>
    </div>
    
    <div class="body"><img id="htmlBuilder_image_source" src="/dist/adminlte/img/photo1.png" width="100%"></div>
    <div class="bottom"><input id="htmlBuilder_slider_bar" type="text" data-slider-handle="custom"></div>
</div>
</div>
<script src="/dist/plugins/bootstrap-slider/bootstrap-slider.js"></script>
<script src="/dist/plugins/cropperjs/cropper.js"></script>
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
    },
    setImage:function setImage(url){ this.cropper.replace(url) }, // 设置图片
    setAspectRatio:function setAspectRatio(aspectRatio){ this.cropper.setAspectRatio(aspectRatio) }, // 设置高宽比
    doReset:function doReset(){ this.cropper.reset() },
    getBlob:function getBlob(){ return this.blob },
    show:function show(){ $('.cropperWarpDiv').show() },
    hide:function hide(){ $('.cropperWarpDiv').hide() },
    init:function init(){
        var that = this;
        this.slider.on("slide", function(sliderValue) {
            var newValue = sliderValue.value - that.sliderStatus.rotate;
            that.doRotate(newValue);
            that.sliderStatus.rotate = sliderValue.value;
        });
        this.slider.on('slideStart', function () { that.sliderStatus.rotate = 0 });
        this.slider.on('slideStop', function () { that.sliderStatus.rotate = 0; that.slider.slider('setValue',0);});
    }
};
cropperWarp.init();
</script>
</body>
</html>