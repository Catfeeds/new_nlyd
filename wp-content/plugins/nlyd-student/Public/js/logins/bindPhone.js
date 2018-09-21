jQuery(function($) {
    initHeight=function(){
        var window_height=$(window).height();
        var top=parseInt($('#page').css('top'));
        var height=window_height-top+'px'
        $('.wrapper_content').css('minHeight',height)
    };
    if(parseInt(window.innerWidth)<=1199){
        initHeight();//手机端最小高度为屏幕高度
    }
    var height= $('.login_zoo').height();
    var marginTop=height / 2;
    var top=$('.wrapper_content').height() / 2;
    if(top>marginTop){
        $('.login_zoo').css({
            'margin-top':-marginTop+'px',
            'top':top+'px',
            'width': '100%',
            'position': 'absolute',
            'left': '0',
        })
    }
        sendloginAjax=function(url,formData){
            //type：确定回调函数
            //url:ajax地址
            //formData:ajax传递的参数
            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                dataType:'json',
                timeout:3000,
                success: function(data, textStatus, jqXHR){
                    // console.log(data)
                    $.alerts(data.data.info)
                    if(data.success){
                        if(data.data.url){
                            setTimeout(function(){
                                window.location.href=data.data.url
                            },1600)
                        }                        
                    }
                },
                error:function (XMLHttpRequest, textStatus, errorThrown) {
                    // 通常 textStatus 和 errorThrown 之中
                    // 只有一个会包含信息
                    // 调用本次AJAX请求时传递的options参数
                }
            });
        } 
        layui.use(['element','form'], function(){
            var form = layui.form;
            var element = layui.element;
            form.render();
            // 自定义验证规则
            form.verify($.validationLayui.allRules); 
            // 监听提交
            form.on('submit(bindPhone)', function(data){//快速登录
                sendloginAjax(window.admin_ajax+"?date="+new Date().getTime(),data.field)
                return false;
            });
            form.on('submit(bindPwd)', function(data){//快速登录
                sendloginAjax(window.admin_ajax+"?date="+new Date().getTime(),data.field)
                return false;
            });
            element.on('tab(tabs)', function(){//tabs
                var left=$(this).position().left;
                var html=$(this).html();
                var css=''
                if($(this).index()==0){
                    css='22.5px 0 0 22.5px'
                }else{
                    css='0px 22.5px 22.5px 0'
                }
                $('.nl-transform').css({
                    'transform':'translate3d('+left+'px, 0px, 0px)',
                    'border-radius':css
                }).html(html)
                $('.nl-transform .login_icon.phone_blue').removeClass('phone_blue').addClass('phone_white')
                $('.nl-transform .login_icon.lock_blue').removeClass('lock_blue').addClass('lock_white')
                $('.nl-transform .login_icon.user_blue').removeClass('user_blue').addClass('user_white')
            });
        });

    //-----------------获取验证码-------------------- 
            
    function time(wait,o){//倒计时
        if (wait == 0) {  
            o.removeClass("disabled");            
            o.text("获取验证码")  
            wait = 60;  
        } else {  
            o.addClass("disabled");  
            o.text("重新发送(" + wait + ")")

            wait--;  
            setTimeout(function() {  
                time(wait,o)  
            },  
            1000)  
        }  
    }
    // $('.getCode').click(function(){//获取验证码
$('.getCode').each(function(){
    var _this=$(this);
    new AlloyFinger(_this[0], {
        tap:function(){
            if(!_this.hasClass('disabled')){
                var dom=$("#mobile")
                console.log(dom)
                var value=dom.val()
                var allRules=$.validationLayui.allRules;//全局正则配置
                var phone=allRules['phone'][0];
                var email=allRules['email'][0];
                var layVerify=dom.attr('lay-verify')
                var message=allRules[layVerify][1];
                var template=parseInt(_this.attr('data-sendCodeCase'));

                if(layVerify=='phone'){//手机登录
                    if(phone.test(value)){
                        var formData=_this.parents('form').serializeObject();
                        var getTimestamp=new Date().getTime()
                        var action='get_sms_code'
                        var data={
                            action:action,
                            mobile:formData.mobile,
                            template:template,
                            tamp:getTimestamp,
                        }
                        sendloginAjax(window.admin_ajax+"?date="+new Date().getTime(),data)
                        var wait=60;  
                        time(wait,_this)
                    }else{
                        // $(this).parents('form').find("input[name='user_login']").focus()
                        $.alerts(message)
                        return false
                    }
                }else if(layVerify=='phoneOrEmail'){//手机或邮箱登录
                    message=allRules['phoneOrEmail'];
                    if(phone.test(value) || email.test(value)){
                        var formData=_this.parents('form').serializeObject();
                        var getTimestamp=new Date().getTime()
                        if(phone.test(value)){//手机号码登录
                            action='get_sms_code'
                            var data={
                                action:action,
                                mobile:formData.mobile,
                                template:template,
                                tamp:getTimestamp,
                            }
                        }else if(email.test(value)){//邮箱登录
                            action='get_smtp_code'    
                            var data={
                                action:action,
                                user_login:formData.mobile,
                                template:template,
                                tamp:getTimestamp,
                            }
                        }
                        sendloginAjax(window.admin_ajax+"?date="+new Date().getTime(),data)
                        var wait=60;  
                        time(wait,_this)
                        return false
                    }else{
                        $.alerts(message)
                        return false
                    }
                }
            }
        }
    })
})
})
