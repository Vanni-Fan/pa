/**
 * 显示一个模态框，可选参数：
 *  title: 标题(html)
 *  body: 内容(html)
 *  close.text: 关闭按钮(html)
 *  close.click: 关闭按钮的回调(function)
 *  ok.text: 确认按钮(html)
 *  ok.click: 确认按钮的回调(function)
 *  delay: 延迟多久自动关闭,0表示不会自动关闭(int)
 *  width: 宽，默认auto
 *  height： 高，默认auto
 * @param params
 */
function showDialogs(params, name){
    name = name || 'table_dialog';
    $('#' + name).remove();
    var dom_str =
        '<div class="modal fade" id="'+name+'" tabindex="-1" role="dialog">'
        +'    <div class="modal-dialog modal-dialog-centered" role="document">'
        +'        <div class="modal-content" style="border-radius:5px;">'
        +'           <div class="modal-header"><h5 class="modal-title">Title</h5></div>'
        +'           <div class="modal-body">Body</div>'
        +'           <div class="modal-footer">'
        +'              <button type="button" class="close_btn btn btn-secondary pull-left" data-dismiss="modal">Close</button>'
        +'              <button type="button" class="ok_btn btn btn-primary" data-dismiss="modal">OK</button>'
        +'           </div>'
        +'        </div>'
        +'    </div>'
        +'</div>';
    var obj = $(dom_str);
    $('body').append(obj);
    // 标题
    if(params.title)  obj.find('.modal-title').html(params.title);
    else              obj.find('.modal-header').remove();
    // 内容
    if(params.body)   obj.find('.modal-body').html(params.body);
    else              obj.find('.modal-body').remove();

    // 位置大小
    if(params.width)  obj.find('.modal-dialog').css('width',   params.width);
    if(params.height){
        obj.find('.modal-content').css({
            height: params.height,
            display: 'flex',
            'flex-direction': 'column',
            'overflow-y':'auto',
        });
        obj.find('.modal-body').css('flex-grow',1);
    }

    // 按钮
    if(params.close || params.ok){
        // 取消按钮
        if(params.close){
            obj.find('.close_btn').html(params.close.text);
            if(params.close.click) obj.find('.close_btn').unbind().click(function(e){params.close.click(obj,e)});
        }else obj.find('.close_btn').remove();
        // 确认按钮
        if(params.ok){
            obj.find('.ok_btn').html(params.ok.text);
            if(params.ok.click) obj.find('.ok_btn').unbind().click(function(e){params.ok.click(obj,e)});
        }else obj.find('.ok_btn').remove();
    }else obj.find('.modal-footer').remove();

    // 显示
    // obj.modal('show');
    obj.css({"display":"block"}).addClass("in");

    // 居中
    // obj.css('padding-top', '100px');
    obj.css('transition',  'padding-top 0.5s ease-out');

    // 监控内容变化，动态调整位置
    if(!params.height) {
        var mo = new MutationObserver(function () {
            var top = ($(window).height() - obj.find('.modal-dialog').height() - 60) / 2; // 60 为内容上下30的Padding
            obj.css('padding-top', top + 'px');
            var height = obj.find('.modal-body').height();
            if (height > parseInt(params.height)) obj.find('.modal-content').css('overflow-y', 'scroll');
            // console.log("MO 输出：",l);
        });
        mo.observe(obj[0], {attributes: true, childList: true, subtree: true});
        setTimeout(function () { // 延迟更新，因为一开始获得不到 obj.find('.modal-dialog').height() 的高度
            var top = ($(window).height() - obj.find('.modal-dialog').height() - 60) / 2; // 60 为内容上下30的Padding
            obj.css('padding-top', top + 'px');
            var height = obj.find('.modal-body').height();
            if (height > parseInt(params.height)) obj.find('.modal-content').css('overflow-y', 'scroll');
        }, 10);
    }else{
        var top = ($(window).height() - params.height - 60) / 2; // 60 为内容上下30的Padding
        obj.css('padding-top', top + 'px');
    }

    // 关闭
    obj.close = function(){
        obj.css({"display":"none"}).removeClass("in");
    }

    // 自动关闭
    if(params.delay){
        obj.find('.modal-content').addClass('toasts_content');//,0.5);
        $('.modal-backdrop').addClass('toasts_bg');//css('background-color');
        setTimeout(obj.close,params.delay);
    }else{
        obj.find('.modal-content').removeClass('toasts_content');//'opacity',1);
        $('.modal-backdrop').removeClass('toasts_bg');//'background-color','#000');
    }

    return obj;
}