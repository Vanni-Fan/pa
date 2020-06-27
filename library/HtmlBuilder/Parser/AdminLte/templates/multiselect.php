<?php
$this->style(/** @lang CSS */ <<<'OUT'
.htmlbuild-multselect{height: 35px;border-right:none;}
.htmlbuild-multselect:last-child{border-right:solid 1px darkgrey;}
OUT
);
$this->script(/** @lang javascript */<<<OUT
window['HBE'] = window['HBE'] || {};
window['HBE']["$id"] = $element;
$(function(){HB_initMultiselect("$id");});
OUT
);

$this->script(/** @lang JavaScript 1.5 */<<<'OUT'
    function HB_initMultiselect(id){
        let obj = window['HBE'][id];
        let col = Math.floor(12 / obj.selects.length) || 1;
        let len = obj.selects.length;
        for(let i=0; i<len; i++){
            let one = obj.selects[i];
            let select = $('<select id="' + id + '_' + i + '" name="' + one.name + '"' + (obj.style === 'single' ? '' : ' multiple=""') + ' class="htmlbuild-multselect col-sm-' + col + '"></select>');
            $('#' + id + '-selects').append(select);
    
            if(one.itemsApi) setOptions(id, i); // 初始化数据
            
            if(i !== len-1){
                select.change(function(event){
                    setOptions(id, i+1)
                })
            }
        }
    }
    function setOptions(id, index){
        let obj = window['HBE'][id];
        let api = obj.selects[index].itemsApi;
        let dom = $('#' + id + '_' + index);
        let len = obj.selects.length;
        dom.find('option').remove();
        let all = $('#' + id + '-selects select');
        for(let i=0; i<all.length; i++){ // 将 [$x] 替换成对应的值
            api = api.replace('[$'+i+']', all.get(i).value);
        }
        console.log('api:'+api);
        $.ajax(api).done(function(data){
            for(var i in data){
                dom.append('<option value="' + data[i].value + '">' + data[i].text + '</option>');
            }
            if(index !== len-1){
                setOptions(id, index+1)
            }
        });
    }
OUT
);
?>

<div id="<?=$id?>" class="form-group htmlbuild-form <?=$attributes['class']??''?>" style="<?=$visible?'':'display:none;'?>">
    <?php include(__DIR__.'/_label.php'); ?>
    <div id="<?=$id?>-selects" class="<?=$labelWidth?('col-sm-'.(12-$labelWidth)):''?>"></div>
    <?php if($description){?>
    <span id="<?=$id?>-description" class="help-block pull-left"><?=$description?></span>
    <?php }?>
    <?php if($validators){ ?>
    <span id="<?=$id?>-message" class="help-block pull-right"></span>
    <?php } ?>
</div>
