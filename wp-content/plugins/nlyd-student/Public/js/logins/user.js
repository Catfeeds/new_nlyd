jQuery(function($) {
    initHeight=function(){
        var window_height=$(window).height();
        var top=parseInt($('#page').css('top'));
        var height=window_height-top+'px'
        $('.wrapper_content').css('minHeight',height)
    };
    if(/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
        initHeight();//手机端最小高度为屏幕高度
    }
    setLocal=function(key,value) {
        if(window.localStorage){
            var storage=window.localStorage;
            var v=JSON.stringify(value)
            storage.setItem(key,escape(v));
        }
    }
    getLocal=function(key) {
        if(window.localStorage){
            var storage=window.localStorage;
            var v=unescape(storage.getItem(key))
            return JSON.parse(v);
        }else{
            return null;
        }
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
                        // window.localStorage.removeItem(formData.user_login);//登陆成功删除记录
                        if(data.data.url){
                            window.location.href=data.data.url
                            setTimeout(function(){
                                window.location.href=data.data.url
                            },300)
                        }                        
                    }else{//登陆失败。记录登录时间
                    }


                },
                error:function (XMLHttpRequest, textStatus, errorThrown) {
                    // 通常 textStatus 和 errorThrown 之中
                    // 只有一个会包含信息
                    // 调用本次AJAX请求时传递的options参数
                    console.log(XMLHttpRequest, textStatus, errorThrown)
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
            form.on('submit(loginFormFastBtn)', function(data){//快速登录
                sendloginAjax(window.admin_ajax+"?date="+new Date().getTime(),data.field)
                return false;
            });
            form.on('submit(loginFormForgetBtn)', function(data){//重置密码
                sendloginAjax(window.admin_ajax+"?date="+new Date().getTime(),data.field)
                return false;
            });
            form.on('submit(loginFormPswBtn)', function(data){//账号密码登录
                sendloginAjax(window.admin_ajax+"?date="+new Date().getTime(),data.field)
                return false;
            });
            form.on('submit(registerBtn)', function(data){//注册
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
            });
        });

//-----------------获取验证码-------------------- 
        
        function time(wait,o){//倒计时
            if (wait == 0) {  
                o.removeAttr("disabled");            
                o.text("获取短信验证码")  
                wait = 60;  
            } else {  
                o.attr("disabled", true);  
                o.text("重新发送(" + wait + ")")

                wait--;  
                setTimeout(function() {  
                    time(wait,o)  
                },  
                1000)  
            }  
        }
        $('.getCode').click(function(){//获取验证码
            // var value=$("#loginForm input[name='user_login']").val()
            var dom=$(this).parents('form').find("input[name='user_login']")
            var value=dom.val()
            var allRules=$.validationLayui.allRules;//全局正则配置
            var phone=allRules['phone'][0];
            var email=allRules['email'][0];
            var layVerify=dom.attr('lay-verify')
            var message=allRules[layVerify][1];
            var template=parseInt($(this).attr('data-sendCodeCase'));

            if(layVerify=='phone'){//手机登录
                if(phone.test(value)){
                    var formData=$(this).parents('form').serializeObject();
                    var getTimestamp=new Date().getTime()
                    var action='get_sms_code'
                    var data={
                        action:action,
                        mobile:formData.user_login,
                        template:template,
                        tamp:getTimestamp,
                    }
                    sendloginAjax(window.admin_ajax+"?date="+new Date().getTime(),data)
                    var wait=60;  
                    time(wait,$(this))
                }else{
                    // $(this).parents('form').find("input[name='user_login']").focus()
                    $.alerts(message)
                    return false
                }
            }else if(layVerify=='phoneOrEmail'){//手机或邮箱登录
                message=allRules['phoneOrEmail'];
                if(phone.test(value) || email.test(value)){
                    var formData=$(this).parents('form').serializeObject();
                    var getTimestamp=new Date().getTime()
                    var action='get_sms_code'
                    if(phone.test(value)){//手机号码登录
                        action='get_sms_code'
                    }else if(email.test(value)){//邮箱登录
                        action='get_smtp_code'    
                    }
                    var data={
                        action:action,
                        user_login:formData.user_login,
                        template:template,
                        tamp:getTimestamp,
                    }
                    sendloginAjax(window.admin_ajax+"?date="+new Date().getTime(),data)
                    var wait=60;  
                    time(wait,$(this))
                    return false
                }else{
                    // $(this).parents('form').find("input[name='user_login']").focus()
                    $.alerts(message)
                    return false
                }
            }
        })
        $('.login-by-code').click(function(){//快速登录
            $('#loginFormFast')[0].reset();//重置表单
            $('#loginFormPsw')[0].reset();//重置表单
            $('.iconLock').removeClass('display-block').addClass('display-hide');//icon
            $('.iconPhone').removeClass('display-hide').addClass('display-block');//icon
            $('.formName').text('手机快速登录')
            $('.tabs-wraps').removeClass('display-block').addClass('display-hide');
            $('.'+$(this).attr('data-show')).removeClass('display-hide').addClass('display-block');
            // $('.login-by').removeClass('display-block').addClass('display-hide');
            // $('.login-by-psw').removeClass('display-hide').addClass('display-block');
        })
        $('.login-by-psw').click(function(){//密码登录
            $('#loginFormFast')[0].reset();//重置表单
            $('#loginFormPsw')[0].reset();//重置表单
            $('.tabs-wraps').removeClass('display-block').addClass('display-hide');
            $('.'+$(this).attr('data-show')).removeClass('display-hide').addClass('display-block');
            $('.iconLock').removeClass('display-block').addClass('display-hide');//icon
            $('.iconPhone').removeClass('display-hide').addClass('display-block');//icon
            $('.formName').text('手机快速登录')
        })
        
        $('.login-by-reset').click(function(){//忘记密码
            $('#loginFormFast')[0].reset();//重置表单
            $('#loginFormPsw')[0].reset();//重置表单
            $('.iconLock').removeClass('display-block').addClass('display-hide');//icon
            $('.iconPhone').removeClass('display-hide').addClass('display-block');//icon
            $('.formName').text('重置密码')
            $('.tabs-wraps').removeClass('display-block').addClass('display-hide');
            $('.'+$(this).attr('data-show')).removeClass('display-hide').addClass('display-block');
            // $('.login-by').removeClass('display-block').addClass('display-hide');
            // $('.login-by-psw').removeClass('display-hide').addClass('display-block');
        })
        
        $('.login-fast').click(function(){//注册tab页返回快速登录
            $('#loginFormFast')[0].reset();//重置表单
            $('#loginFormPsw')[0].reset();//重置表单
            
            $('.iconLock').removeClass('display-block').addClass('display-hide');//icon
            $('.iconPhone').removeClass('display-hide').addClass('display-block');//icon
            $('.formName').text('手机快速登录')
            $('.tabs-wraps').removeClass('display-block').addClass('display-hide');
            $('.'+$(this).attr('data-show')).removeClass('display-hide').addClass('display-block');

            //tabs切换
            $('.layui-tab-title li').eq(0).click()
            // $('.login-by').removeClass('display-block').addClass('display-hide');
            // $('.login-by-psw').removeClass('display-hide').addClass('display-block');
        })
        $('.nl-agreement .pointer').click(function(){
                var html=$('.userAgreement').html(); 
                layer.open({
                    type: 1
                    ,title: false //不显示标题栏
                    ,closeBtn: false
                    ,area: '300px;'
                    ,shade: 0.8
                    ,id: 'LAY_layuipro' //设定一个id，防止重复弹出
                    ,btn: ['知道了']
                    ,btnAlign: 'c'
                    ,moveType: 1 //拖拽模式，0或者1
                    ,content: '<div class="width-margin width-margin-pc userAgreement-content">'+html+'</div>'
                    ,success: function(layero){
                        
                    },
                    cancel: function(index, layero){
                    layer.closeAll();
                    }
                    ,yes: function(index, layero){
                        layer.closeAll();
                    }
                });
            })
})