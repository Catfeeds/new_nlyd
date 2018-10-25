jQuery(document).ready(function($) {
    if('ontouchstart' in window){
        var dom='<meta name="viewport"  content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">'
        $('body').append(dom)
        document.documentElement.addEventListener('touchstart', function (event) {//禁止缩放
            if (event.touches.length > 1) {
              event.preventDefault();
            }
          }, false);
        var lastTouchEnd = 0;
        document.documentElement.addEventListener('touchend', function (event) {
        var now = Date.now();
        if (now - lastTouchEnd <= 300) {
            event.preventDefault();
        }
        lastTouchEnd = now;
        }, false);
        $("body").on("touchstart",'.layui-layer-phimg', function(e) {
            // 判断默认行为是否可以被禁用
            if (e.cancelable) {
                // 判断默认行为是否已经被禁用
                if (!e.defaultPrevented) {
                    e.preventDefault();
                }
            }   
            startX = e.originalEvent.changedTouches[0].pageX,
            startY = e.originalEvent.changedTouches[0].pageY;
        });
        $("body").on("touchend",'.layui-layer-phimg', function(e) {//图片滑动     
            // 判断默认行为是否可以被禁用
            if (e.cancelable) {
                // 判断默认行为是否已经被禁用
                if (!e.defaultPrevented) {
                    e.preventDefault();
                }
            }               
            moveEndX = e.originalEvent.changedTouches[0].pageX,
            moveEndY = e.originalEvent.changedTouches[0].pageY,
            X = moveEndX - startX,
            Y = moveEndY - startY;
            //左滑
            if ( X > 0 ) {
                $('.layui-layer-imgnext').click()              
            }
            //右滑
            else if ( X < 0 ) {
                $('.layui-layer-imgprev').click()  
            }
        });
    }
    addcamera=function(){
        var u = navigator.userAgent;
        var isAndroid = u.indexOf('Android') > -1; //android终端或者uc浏览器  
        var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端  
         if(navigator.userAgent.indexOf('UCBrowser') > -1) { 
            // if(isAndroid){//
            //     // alert('isAndroid UC')
            // }
            // if(isiOS){
            //     // alert('isiOS UC')
            // }
         }else{
            if(isAndroid){
                if(!mqqbrowser() && !isQQInstalled() && !isChrome()){//非QQ浏览器，QQ内置浏览器
                    $("input[type='file']").attr('capture','camera');
                }
                
                // if(isWeiXin()){
                //     $("input[type='file']").attr('capture','camera');
                // }
                
            }
            // if(isiOS){
            //     // alert('isiOS')
            // }
         }
         
    }
   
    function initHeight(){
        if(parseInt(window.innerWidth)<=1199) {
            var window_height=$(window).height();
            var top=parseInt($('#page').css('top'));
            var padding=parseInt($('.detail-content-wrapper').css("paddingBottom"))
            var height=window_height-top-padding+'px'
            $('.detail-content-wrapper').css('minHeight',height)
        }else{
           
            var window_height=$(window).height();
            var height=window_height-parseInt($('html').css('marginTop'))-$('#header').height()-$('.page-title').height()-2*parseInt($('#main').css('paddingTop'))-$('#footer').height()-1+'px'
            $('.nl-content').css('minHeight',height)
            $('.wrapper_content').css('minHeight',parseInt(height)-8+'px')
        }
    };
    $('body').on('focusin','.nl-foucs',function(){
        $('body').find('.a-btn').addClass('focus_none')
        $('body').find('.nl-foot-nav').addClass('focus_none')
    })
    // $('body').on('focus','input[readonly]',function(){
    //     $(this).trigger('blur');
    //   });
    $('body').on('focusout','.nl-foucs',function(){
        $('body').find('.a-btn').removeClass('focus_none')
        $('body').find('.nl-foot-nav').removeClass('focus_none')
    })
    $('body').on('click','.nl-goback',function(){//返回上一页s
        if(!$(this).hasClass('static')){
            window.history.go(-1);
            return false;
        }

    })
    $('body').on('click','.disabled_a',function(){//当前功能暂未开放，敬请期待
        $.alerts(common.disabled)
        return false;
        
    })
    count_down= function(_count_time,waiting_url){
        if(_count_time<=120){
            clearTimeout(count_down_timer)
            window.location.href=waiting_url;
            return false;
        }else{
            _count_time--
            count_down_timer=setTimeout("count_down("+_count_time+",'"+waiting_url+"')",1000);
        }
    } 
    function getMatchTime(){
        var wait_match=window.wait_match;
        if(wait_match.length>0){
             wait_match=JSON.parse(window.wait_match);
            if(typeof(wait_match)!='undefined' && wait_match!=null){
                var match_start_time=wait_match.match_start_time;//比赛距离开始时间
                var match_url=wait_match.match_url;//倒计时跳转链接
                var match_id=wait_match.match_id;//比赛id，如果url存在相同的match_id则不跳转
                if($.Request('match_id')===null || match_id != $.Request('match_id')){
                    if(match_start_time>120){//倒计时时间大于2分钟,进行倒计时
                        count_down(match_start_time,wait_match.waiting_url)
                    }else{
                        if(match_start_time<=120 && match_start_time>=0){//倒计时时间小于于2分钟,大于0，跳转到比赛等待页
                            window.location.href=wait_match.waiting_url;
                        }
                        // else{//倒计时时间小于=0，跳转至比赛页
                        //     window.location.href=wait_match.waiting_url;
                        // }
                    }
                }
                if($('#getTimes').length>0){//最新比赛倒计时
                    $('#getTimes').attr('data-seconds',match_start_time).countdown(function(S, d){//倒计时
                        var D=d.day>0 ? d.day+'天' : '';
                        var h=d.hour<10 ? '0'+d.hour : d.hour;
                        var m=d.minute<10 ? '0'+d.minute : d.minute;
                        var s=d.second<10 ? '0'+d.second : d.second;
                        var time=D+h+':'+m+':'+s;
                        //console.log(d)
                        $(this).attr('data-seconds',S).text(time);
                    })
                }    
            }
        }
    }
    function mqqbrowser(){
        var ua = window.navigator.userAgent.toLowerCase();
        if(ua.indexOf('mqqbrowser')> -1 && ua.indexOf(" qq")<0){
             //qq浏览器
            return true;
        }else{
            return false;
        }
    }
    function isQQInstalled(){
        var ua = window.navigator.userAgent.toLowerCase();
        if(ua.indexOf(' qq')>-1 && ua.indexOf('mqqbrowser') <0){
            //qq内置浏览器
            return true;
        }else{
            return false;
        }
    }
    function isWeiXin(){
        var ua = window.navigator.userAgent.toLowerCase();
        if(ua.match(/MicroMessenger/i) == 'micromessenger'){
            return true;
        }else{
            return false;
        }
    }
    function isSafari() {
        var u=navigator.userAgent
        //Safari                      Chrome            傲游              
        if (/Safari/.test(u) && !/Chrome/.test(u) && !/MXIOS/.test(u)) {
            return true;
        }else{
            return false;
        }
    }
    function isChrome() {
        var u=navigator.userAgent
        //Chrome
        if (/Chrome/.test(u)) {
            return true;
        }else{
            return false;
        }
    }
    share=function(){//分享功能

        if('ontouchstart' in window){//移动端
            var metaDesc = document.getElementsByName('description')[0];
            var firstImg = document.getElementsByTagName('img')[0];
            var nativeShare = new NativeShare()
            var shareData = {
                title: document.title,
                desc: metaDesc && metaDesc.content || '',
                // 如果是微信该link的域名必须要在微信后台配置的安全域名之内的。
                link: window.location.href,
                icon: firstImg && firstImg.src || '',
            }
            nativeShare.setShareData(shareData)
            function call(command) {
                try {
                    nativeShare.call(command)
                } catch (err) {
                    // 如果不支持，你可以在这里做降级处理
                    $.alerts(err.message)
                }
            }
            $('body').on('click','.shareContent',function(){
                var _this=$(this);
                _this.parents('.selectBottom').removeClass('selectBottom-show');
                var u = navigator.userAgent;
               
                if(isSafari()){//Safari
                    // var dom='点击正下方<i class="iconfont" style="font-size:0.20rem">&#xe68d;</i>按钮分享给好友或朋友圈'
                    var dom=common.share
                    $.alerts(dom)
                }else if(isWeiXin()){
                    $(this).parents('.selectBottom').removeClass('selectBottom-show');
                    if($('.share-bg').length>0){
                        $('.share-bg').css('display','block')
                    }else{
                        var src=window.plugins_url+'/nlyd-student/Public/css/image/weChat-share.png'
                        var dom='<div class="share-bg">'
                                    +'<div class="img-box share-box">'
                                        +'<img src="'+src+'">'
                                    +'</div>'
                                    +'<p class="share-font">'+common.click+'</p>'
                                +'</div>'
                        $('body').append(dom)
                    }
                }else{
                    var id=_this.attr('data-id');
                    call(id)
                }
                // alert(u)
            })
            $('body').on('click','.share-bg',function(){
                $('.share-bg').css('display','none')
            })
        }
    }
    //屏幕改变时tabs标签页动画的初始位置初始化
    window.onresize = function(){
        $('.nl-transform').each(function(){
            var _this=$(this);
            var m_left=_this.parents('.layui-tab-title').find('.layui-this').css('marginLeft');
            var p_left=_this.parents('.layui-tab-title').find('.layui-this').position().left;
            var left=p_left+parseInt(m_left);
            var y=0;
            if(_this.attr('data-y')){
                y=_this.attr('data-y')
            }
            
            _this.css({
                'transform':'translate3d('+left+'px, '+y+'px, 0px)',
            })
        })
    } 

    //设置AJAX的全局默认选项
    $.ajaxSetup( {
        url: window.admin_ajax+"?date="+new Date().getTime(), // 默认URL
        aysnc: true ,
        type: "POST" , // 默认使用POST方式
        dataType:'json',
        timeout:20,
        error: function(jqXHR, textStatus, errorMsg){ // 出错时默认的处理函数
            // jqXHR 是经过jQuery封装的XMLHttpRequest对象
            // textStatus 可能为： null、"timeout"、"error"、"abort"或"parsererror"
            // errorMsg 可能为： "Not Found"、"Internal Server Error"等
            if(errorMsg=='timeout'){
                $.alerts(common.slow);
            }      
        }
    } );
    // var visibilityChange; 
    // if (typeof document.hidden !== "undefined") {
    //     visibilityChange = "visibilitychange";
    // } else if (typeof document.mozHidden !== "undefined") {
    //     visibilityChange = "mozvisibilitychange";
    // } else if (typeof document.msHidden !== "undefined") {
    //     visibilityChange = "msvisibilitychange";
    // } else if (typeof document.webkitHidden !== "undefined") {
    //     visibilityChange = "webkitvisibilitychange";
    // }

    // document.addEventListener(visibilityChange, function() {
    //     var isHidden = document.hidden;
    //     if (isHidden) {

    //     // alert('当焦点不在当前窗口时的网页标题')
    //     } else {
            
    //     }
    // });
    // jQuery(window).on("blur",function(){
    //     var leavePage = jQuery.GetSession('leavePage','1');
    //     if(leavePage){
    //         leavePage['leavePage']+=1;
    //     }else{
    //         var sessionData={
    //             leavePage:1,
    //         }
    //         leavePage= sessionData
    //     }
    //     jQuery.SetSession('leavePage',leavePage)
    // })  
    // jQuery(window).on("focus", function(e) {
    //     var leavePage= jQuery.GetSession('leavePage','1');
    //     if(leavePage){
    //         var leveTimes=parseInt(leavePage['leavePage'])
    //         jQuery.SetSession('leavePage',leavePage)
    //         console.log(leveTimes)
    //         if(leveTimes>0 && leveTimes<1){
    //             jQuery.alerts('第'+leveTimes+'次离开考试页面,到达1次自动提交答题')
    //         }
    //         if(leveTimes>=1){
    //             jQuery.alerts(_leavePage.submit)
    //         }
    //     }else{
    //         jQuery.DelSession('leavePage')
    //     }
    // });
// 初始化
    getMatchTime()
    initHeight();//手机端最小高度为屏幕高度
    addcamera()
})
