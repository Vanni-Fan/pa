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
            height:50px;
            background-color:#000000b0;
            width:100%;
            text-align: center;
        }
        .body{
            z-index:10000;
            opacity: .05;
            padding:20px;
        }
        .top{
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-evenly;
        }
        .bottom{
            padding: 15px;
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
    </style>
</head>

<body>
<div class="outer">
    <div class="top">
        <i onclick="doRotate(90)" class="fa fa-rotate-right">Rotate 90</i>
        <div onclick="doVertical()">
            <i class="fa fa-exchange" style="transform: rotate(90deg);"></i>Vertical
        </div>
        <i onclick="doReset()" class="fa fa-refresh">Reset</i>
        <i onclick="doHorizontal()" class="fa fa-exchange">Horizontal</i>
        <i onclick="doCrop()" class="fa fa-crop">Crop</i>
        <i onclick="doClose()" class="fa fa-close">Close</i>
    </div>
    
    <div class="body">
        <img id="myi" src="/dist/adminlte/img/photo1.png" width="100%">
    </div>
    
    <div class="bottom">
        <input id="mys" type="text" data-slider-handle="custom">
    </div>
</div>
<script src="/dist/plugins/bootstrap-slider/bootstrap-slider.js"></script>
<script src="/dist/plugins/cropperjs/cropper.js"></script>
<script>
    var myCropper = null;
    var mySlider = null;
    $(function () {
        mySlider  = $('#mys').slider({step: 1,min: -45,value:0,max: 45,tooltip:'hide'});
        mySlider.on("slide", function(sliderValue) {
            console.log(sliderValue);
            doRotate(sliderValue.value);
        });
        mySlider.on('slideStop', function () {
            mySlider.slider('setValue',0);
        });
        //a.slider('setValue', -20);
        myCropper = new Cropper(document.getElementById('myi'), {
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
    });
    function doRotate(i){myCropper.rotate(i);}
    function doHorizontal(){ myCropper.scale(-1, 1);}
    function doVertical(){myCropper.scale(1,-1); }
    function doCrop(){myCropper.crop();}
    function doReset() {myCropper.reset();}
    function doClose(){$('.outer').hide()}
</script>
</body>
</html>