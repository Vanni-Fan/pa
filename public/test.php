<?php

?>
<html>
<head>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="/dist/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/dist/plugins/bootstrap-slider/slider.css">
    <link rel="stylesheet" href="/dist/plugins/cropperjs/cropper.css">
    <link rel="stylesheet" href="/dist/bower_components/font-awesome/css/font-awesome.min.css">
    
    <script src="/dist/bower_components/jquery/dist/jquery.min.js"></script>
    <style>
        .outer{
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
        .outer:after{
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
        .top,.bottom{
            color:white;
            z-index:10000;
            min-height: 50px;
            height:50px;
            background-color:#000000b0;
            width:100%;
            text-align: center;
        }
        .body{
            z-index:10000;
            /*opacity: .05;*/
            /*padding:20px;*/
            flex-grow: 1;
            padding: 0 !important;
        }
        .top{
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-evenly;
        }
        .bottom{
            padding: 15px;
            /*position: absolute;*/
            bottom: 0;
        }
        .slider-handle.custom {
            background: transparent none;
        }
        .slider.slider-horizontal .slider-track-high {
            background-color: #cccccc;
        }
        .slider-selection{
            background-color: #cccccc;
            background-image: none;
        }
        .slider-handle.custom::before {
            line-height: 20px;
            font-size: 40px;
            content: '‖';
            color: #fff;
        }
        .hand{
            cursor: pointer;
        }
    </style>
</head>

<body>
<div class="outer">
    <div class="top">
        <i onclick="doRotate(-90)" class="fa fa-rotate-left hand">Rotate left</i>
        <div onclick="doVertical()" class="hand">
            <i class="fa fa-exchange" style="transform: rotate(90deg);"></i>Flip vertical
        </div>
        <i onclick="doHorizontal()" class="fa fa-exchange hand">Flip horizontal</i>
        <i onclick="doReset()" class="fa fa-refresh hand">Reset</i>
        <i onclick="doCrop()" class="fa fa-crop hand">Crop</i>
        <i onclick="doClose()" class="fa fa-close hand">Close</i>
    </div>
    
    <div class="body">
        <img id="myi" src="/dist/adminlte/img/photo1.png" width="100%">
    </div>
    
    <div class="bottom">
        <input id="mys" type="text" data-slider-handle="custom">
    </div>
</div>

<img id="final">

<script src="/dist/plugins/bootstrap-slider/bootstrap-slider.js"></script>
<script src="/dist/plugins/cropperjs/cropper.js"></script>
<script>
    var myCropper = null;
    var mySlider = null;
    var sliderStatus = {
        rotate:0,
        horizontal:false,
        vertical:false
    };
    $(function () {
        mySlider  = $('#mys').slider({step: 1,min: -45,value:0,max: 45,tooltip:'hide'});
        mySlider.on("slide", function(sliderValue) {
            var newValue = sliderValue.value - sliderStatus.rotate;
            doRotate(newValue);
            sliderStatus.rotate = sliderValue.value;
        });
        mySlider.on('slideStart', function () {
            sliderStatus.rotate = 0;
        })
        mySlider.on('slideStop', function () {
            sliderStatus.rotate = 0;
            mySlider.slider('setValue',0);
        });
        //a.slider('setValue', -20);
        myCropper = new Cropper(document.getElementById('myi'), {
            aspectRatio: 16 / 9,
            crop(event) {
                // console.log(event.detail.x);
                // console.log(event.detail.y);
                // console.log(event.detail.width);
                // console.log(event.detail.height);
                // console.log(event.detail.rotate);
                // console.log(event.detail.scaleX);
                // console.log(event.detail.scaleY);
            },
        });
    });
    function doRotate(i){
        myCropper.rotate(i);
    }
    function doHorizontal(){
        myCropper.scale(sliderStatus.horizontal ? 1 : -1, sliderStatus.vertical ? -1 :1);
        sliderStatus.horizontal = !sliderStatus.horizontal;
    }
    function doVertical(){
        myCropper.scale(sliderStatus.horizontal ? -1 : 1, sliderStatus.vertical ? 1 :-1);
        sliderStatus.vertical = !sliderStatus.vertical;
    }
    function doCrop(){
        // myCropper.crop();
        myCropper.getCroppedCanvas().toBlob(function(blob){
            var uuu = URL.createObjectURL(blob);
            $('#final').attr('src',uuu);
            console.log(uuu);
            doClose();
            return;
            var formData = new FormData();
            // Pass the image file name as the third parameter if necessary.
            formData.append('croppedImage', blob/*, 'example.png' */);
            // Use `jQuery.ajax` method for example
            $.ajax('/path/to/upload', {
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success:function() {console.log('Upload success');},
                error:function() {console.log('Upload error');},
            });
        }/*, 'image/png' */);
    }
    function doReset() {myCropper.reset();}
    function doClose(){$('.outer').hide()}
</script>
</body>
</html>