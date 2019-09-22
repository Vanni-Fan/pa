<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <style>
        .box{
            width: 500px;
            height: 200px;
            background-color: #ccc;
            border:1px solid black;
            line-height: 200px;
            position: absolute;
            left: 50%;
            top: 50%;
            margin-top: -100px;
            margin-left: -200px;
            text-align: center;
            /*display:none;*/
        }
    </style>
    <script>
        window.onload = function(){
            var oBox=document.querySelector('.box');
            console.log(oBox);

            document.addEventListener('dragover',function(ev){
                oBox.style.display='block';
                ev.preventDefault()
            });
            oBox.addEventListener('dragenter',function(){
                oBox.innerHTML='请松手';
            });
            oBox.addEventListener('dragleave',function(){
                console.log('bbb')
                oBox.innerHTML='请拖到这里';
            });

            oBox.addEventListener('drop',function(ev){
                console.log(ev.dataTransfer.files)
                oBox.style.display='block';
                console.log(ev);
                ev.preventDefault();
            });
        };
    </script>
</head>
<body>
<div class='box'>
    请把文件拖到这里
</div>
</body>
</html>