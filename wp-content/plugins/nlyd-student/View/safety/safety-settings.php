<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <a class="mui-pull-left nl-goback">
                <i class="iconfont">&#xe610;</i>
            </a>
            <h1 class="mui-title"><?=$title?></h1>
        </header>  
            <div class="layui-row nl-border nl-content">
                <div class="have-bottom">
                    <form class="nl-page-form width-margin-pc layui-form" lay-filter='safetySetting'>
                        <div class="form-inputs">
                            <input type="hidden" name="action" value="secure_save">
                            <?php if($_GET['type'] == 'pass'){ ?>
                            <!-- 重置密码 -->
                            <input type="hidden" name="save_type" value="pass">
                            <div class="form-input-row">
                                <div class="form-input-label">旧密码</div>
                                <input name='old_pass' value="" type="password" placeholder="旧密码" class="nl-input nl-foucs" lay-verify="required">
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label">新密码</div>
                                <input name='new_pass' value="" type="text" placeholder="新密码" class="nl-input nl-foucs" lay-verify="required">
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label">再次输入</div>
                                <input name='confirm_pass' value="" type="text" placeholder="再次输入" class="nl-input nl-foucs" lay-verify="required">
                            </div>
                            <?php } ?>
                            <?php if($_GET['type'] == 'mobile'){ ?>
                             <?php if(!isset($_GET['confirm'])){ ?>
                            <!-- 更换手机号 -->
                            <input type="hidden" name="save_type" value="mobile">
                            <input type="hidden" name="step" value="one">
                            <p class="c_blue" style="margin-bottom:0">更换后可使用新手机号登陆，当前手机号13982242710</p>
                            <div class="form-input-row">
                                <div class="form-input-label">手机号码</div>
<<<<<<< HEAD
                                <div class="nl-input" lay-verify="phone"><?=$user_info['contact']?></div>
                                <input type="hidden" name="user_mobile" value="<?=$user_info['user_mobile']?>" />
=======
                                <div class="nl-input"><?=$user_info['contact']?></div>
                                <input type="hidden" lay-verify="phone" name="user_mobile" value="<?=$user_info['user_mobile']?>" />
>>>>>>> 5230e5dd8e65f2feac4acc6636f361d51234ed7f
                                <a class="form-input-right getCode c_blue" data-sendCodeCase="21">发送验证码</a>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label">验证码</div>
                                <input name='verify_code' value="" type="tel" placeholder="验证码" class="nl-input nl-foucs" lay-verify="required">
                            </div>
                            <?php }?>
                            <?php if(!empty($user_info['user_mobile']) && $_GET['confirm'] == 1){ ?>
                            <!-- 绑定手机号 -->
                            <input type="hidden" name="save_type" value="mobile">
                            <p class="c_blue" style="margin-bottom:0">绑定后可使用手机号登陆</p>
                            <div class="form-input-row">
                                <div class="form-input-label">手机号码</div>
                                <input name='user_mobile' value="" type="tel" placeholder="手机号码" class="nl-input nl-foucs" lay-verify="phone">
                                <a class="form-input-right getCode c_blue" data-sendCodeCase="16">发送验证码</a>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label">验证码</div>
                                <input name='verify_code' value="" type="tel" placeholder="验证码" class="nl-input nl-foucs" lay-verify="required">
                            </div>
                            <?php } ?>
                            <?php } ?>
                            <?php if($_GET['type'] == 'email'){ ?>
                            <!-- 绑定更换邮箱 -->
                            <input type="hidden" name="save_type" value="email">
                            <div class="form-input-row">
                                <div class="form-input-label">邮箱地址</div>
                                <input name='user_email' value="<?=$user_info['user_email']?>" type="text" placeholder="邮箱地址" class="nl-input nl-foucs" lay-verify="email">
                                <a class="form-input-right getCode c_blue" data-sendCodeCase="16">发送验证码</a>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label">验证码</div>
                                <input name='verify_code' value="" type="tel" placeholder="验证码" class="nl-input nl-foucs" lay-verify="required">
                            </div>
                            <?php }?>
                            <?php if($_GET['type']=='mobile' && !isset($_GET['confirm']) ){ ?>
                            <a class="a-btn" id="safetySetting" lay-filter="safetySetting" lay-submit="">下一步</a>
                            <?php }else{ ?>
                            <a class="a-btn" id="safetySetting" lay-filter="safetySetting" lay-submit="">更 新</a>
                            <?php } ?>
                        </div>
                    </form>
                </div>
                <!-- <a class="a-btn safetySetting">更 新</a> -->
            </div>
        </div>           
    </div>
</div>
<script>
    jQuery(document).ready(function($) {
        // $('.safetySetting').click(function(){
        //     $('#safetySetting').click()
        // })
        sendloginAjax=function(formData){
            //type：确定回调函数
            //url:ajax地址
            //formData:ajax传递的参数
            $.ajax({
                data: formData,
                success: function(data, textStatus, jqXHR){
                    console.log(data)
                    $.alerts(data.data.info)
                    if(data.success){
                        if(data.data.url){
                            setTimeout(() => {
                                window.location.href=data.data.url
                            }, 1600);
                        }
                    }
                    return false;
                }
            });
        }
        layui.use(['form'], function(){
            var form = layui.form
            form.render();
            // 自定义验证规则
            form.verify($.validationLayui.allRules);
            form.on('submit(safetySetting)', function(data){
                sendloginAjax(data.field)
                return false;
            });
        });

            //-----------------获取验证码-------------------- 
            
    function time(wait,o){//倒计时
        if (wait == 0) {  
            o.removeClass("disabled");            
            o.text("获取短信验证码")  
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
    $('.getCode').click(function(){//获取验证码
        if(!$(this).hasClass('disabled')){
            var dom=$(this).parents('.form-input-row').find("input")
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
                        mobile:value,
                        template:template,
                        tamp:getTimestamp,
                    }
                    sendloginAjax(data)
                    var wait=60;  
                    time(wait,$(this))
                }else{
                    // $(this).parents('form').find("input[name='user_login']").focus()
                    $.alerts(message)
                    return false
                }
            }else if(layVerify=='email'){//手机或邮箱登录
                if( email.test(value)){
                    var formData=$(this).parents('form').serializeObject();
                    var getTimestamp=new Date().getTime()
                    var action='get_smtp_code'   
                    
                    var data={
                        action:action,
                        user_login:value,
                        template:template,
                        tamp:getTimestamp,
                    }
                    sendloginAjax(data)
                    var wait=60;  
                    time(wait,$(this))
                    return false
                }else{
                    $.alerts(message)
                    return false
                }
            }
        }
    })
    })
</script>
