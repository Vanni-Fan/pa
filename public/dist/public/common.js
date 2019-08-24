/**
 * 获得随机整数，当只有一个参数时，返回0到此数字之间的值，两个参数时，返回两个值的区间值
 * @param minNum 最小值，或最大值（只有一个参数时）
 * @param maxNum 最大值
 * @returns {number}
 */
function randomNum(minNum,maxNum){ 
    switch(arguments.length){ 
        case 1: return parseInt(Math.random()*minNum+1,10);
        case 2: return parseInt(Math.random()*(maxNum-minNum+1)+minNum,10);
        default:return 0;
    }
}

/**
 * 显示一个模态框，可选参数：
 *  title: 标题(html)
 *  body: 内容(html)
 *  btns.*.text: 按钮(html)
 *  btns.*.click: 按钮的点击事件(function(this))
 *  delay: 延迟多久自动关闭,0表示不会自动关闭(int)
 *  width: 宽，默认auto
 *  height： 高，默认auto
 * @param params
 */
function showModal (params, name) {
    params = params || {};
    name = name || 'table_modal';
    var $dom = $('#' + name);
    if ($dom.length === 0) {
        $dom = $('<div class="modal fade" tabindex="-1" role="dialog"></div>').attr('id', name);
        $dom.html('<div class="modal-dialog modal-dialog-centered" role="document">' +
            '   <div class="modal-content">' +
            '       <div class="modal-header"><h5 class="modal-title"></h5></div>' +
            '       <div class="modal-body"></div>' +
            '       <div class="modal-footer"></div>' +
            '   </div>' +
            '</div>');
        $('body').append($dom);
    }
    // 标题
    if(params.title)  $dom.find('.modal-title').html(params.title).show();
    else              $dom.find('.modal-header').empty().hide();
    // 内容
    if(params.body)   $dom.find('.modal-body').html(params.body).show();
    else              $dom.find('.modal-body').empty().hide();

    // 位置大小
    if(params.width)  $dom.find('.modal-dialog').css('width',   params.width);
    if(params.height) $dom.find('.modal-content').css('height', params.height);

    if (params.btns && params.btns.length) {
        $dom.find('.modal-footer').empty();
        for (var i = 0; i < params.btns.length; i++) {
            var btn =$('<button type="button" class="btn" data-dismiss="modal"></button>')
                .text(params.btns[i].text || "OK")
                .addClass('btn-' + (params.btns[i].type || 'secondary'))
                .on('click', (function(d, f){ return function(){f(d)}})($dom, params.btns[i].click));//不要动这行
            $dom.find('.modal-footer').append(btn);
        }
    } else $dom.find('.modal-footer').empty().hide();

    // 显示
    $dom.css({"display":"block"}).addClass("in");

    // 居中
    $dom.css('padding-top', '100px');
    $dom.css('transition',  'padding-top 0.5s ease-out');

    // 关闭
    $dom.close = function(){
        $dom.css({"display":"none"}).removeClass("in");
    };

    // 自动关闭
    if(params.delay){
        $dom.find('.modal-content').addClass('toasts_content');//,0.5);
        $('.modal-backdrop').addClass('toasts_bg');//css('background-color');
        setTimeout($dom.close,params.delay);
    }else{
        $dom.find('.modal-content').removeClass('toasts_content');//'opacity',1);
        $('.modal-backdrop').removeClass('toasts_bg');//'background-color','#000');
    }

    return $dom;
}

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
        +'        <div class="modal-content">'
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
    if(params.height) obj.find('.modal-content').css('height', params.height);

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
    obj.css('padding-top', '100px');
    obj.css('transition',  'padding-top 0.5s ease-out');

    // 监控内容变化，动态调整位置
    var mo = new MutationObserver(function(){
        var top = ($(window).height() - obj.find('.modal-dialog').height() - 60)/2; // 60 为内容上下30的Padding
        obj.css('padding-top', top + 'px');
        var height = obj.find('.modal-body').height();
        if(height > parseInt(params.height)) obj.find('.modal-content').css('overflow-y','scroll');
        // console.log("MO 输出：",l);
    });
    mo.observe(obj[0], {attributes:true,childList:true,subtree: true});
    setTimeout(function(){ // 延迟更新，因为一开始获得不到 obj.find('.modal-dialog').height() 的高度
        var top = ($(window).height() - obj.find('.modal-dialog').height() - 60)/2; // 60 为内容上下30的Padding
        obj.css('padding-top', top + 'px');
        var height = obj.find('.modal-body').height();
        if(height > parseInt(params.height)) obj.find('.modal-content').css('overflow-y','scroll');
    },200);

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