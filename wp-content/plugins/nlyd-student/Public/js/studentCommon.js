jQuery(document).ready(function($) {
    isIos=function(){
        var u = navigator.userAgent, app = navigator.appVersion;  
        var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Linux') > -1; //android终端或者uc浏览器  
        var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端  
        //alert('是否是Android：'+isAndroid);  
        //alert('是否是iOS：'+isiOS);
         if(isAndroid){
            $("input[type='file']").attr('capture','camera');
         }else{
         }
    }
   
    initHeight=function(){
        if('ontouchstart' in window) {
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
    count_down=function(_count_time,waiting_url){
            _count_time--
            if(_count_time<=120){
                clearTimeout(count_down_timer)
                window.location.href=waiting_url;
            }
            count_down_timer=setTimeout("count_down("+_count_time+",'"+waiting_url+"')",1000);

    } 
    getMatchTime=function(){
        var wait_match=window.wait_match;
        if(wait_match.length>0){
             wait_match=JSON.parse(window.wait_match);

            if(typeof(wait_match)!='undefined' && wait_match!=null){
                var match_start_time=wait_match.match_start_time;//比赛距离开始时间
                var match_url=wait_match.match_url;//倒计时跳转链接
                var match_id=wait_match.match_id;//比赛id，如果url存在相同的match_id则不跳转
                //return false;
                if($.Request('match_id')===null || match_id != $.Request('match_id')){
                    if(match_start_time>120){//倒计时时间大于2分钟,进行倒计时
                        count_down(match_start_time,wait_match.waiting_url)
                    }else{
                        if(match_start_time<=120 && match_start_time>0){//倒计时时间小于于2分钟,大于0，跳转到比赛等待页
                            window.location.href=wait_match.waiting_url;
                        }else{//倒计时时间小于=0，跳转至比赛页
                            window.location.href=match_url;
                        }
                    }
                }
                if($('#getTime').length>0){//最新比赛倒计时
                    $('#getTime').attr('data-seconds',match_start_time).countdown(function(S, d){//倒计时
                        var D=d.day>0 ? d.day+'天' : d.day;
                        var h=d.hour<10 ? '0'+d.hour : d.hour;
                        var m=d.minute<10 ? '0'+d.minute : d.minute;
                        var s=d.second<10 ? '0'+d.second : d.second;
                        var time=D+h+':'+m+':'+s;
                        $(this).attr('data-seconds',S).text(time);
                    })
                }    
            }
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
    function isSarari() {
        var ua = window.navigator.userAgent.toLowerCase();
        if(ua.match(/Safari/i) == 'Safari') {
            return true;
        }else{
            return false;
        }
    }
    $('body').on('click','.share-bg',function(){
        $('.share-bg').css('display','none')
    })
    share=function(){//分享功能

        if('ontouchstart' in window){//移动端
            if(isWeiXin() || isSarari()){
                if(isWeiXin()){//微信浏览器
                    $('body').on('click','.shareContent',function(){
                        $(this).parents('.selectBottom').removeClass('selectBottom-show');
                        if($('.share-bg').length>0){
                            $('.share-bg').css('display','block')
                        }else{
                            var src=window.plugins_url+'/nlyd-student/Public/css/image/weChat-share.png'
                            var dom='<div class="share-bg">'
                                        +'<div class="img-box share-box">'
                                            +'<img src="'+src+'">'
                                        +'</div>'
                                        +'<p class="share-font">点击右上角分享给好友或朋友圈</p>'
                                    +'</div>'
                            $('body').append(dom)
                        }

                    })
                }else if(isSarari()){//Sarari浏览器
                    
                }
            }else{
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
                    var id=_this.attr('data-id');
                    call(id)
                })

            }
    
        }
    }
    //监听屏幕方向
    window.onresize = function(){
        $('.nl-transform').each(function(){
            var _this=$(this);
            var left=_this.parents('.layui-tab-title').find('.layui-this').position().left;
            var y=0;
            if(_this.attr('data-y')){
                y=_this.attr('data-y')
            }
            _this.css({
                'transform':'translate3d('+left+'px, '+y+'px, 0px)',
            })
        })
    } 
    getMatchTime()
    initHeight();//手机端最小高度为屏幕高度
    isIos()
})
