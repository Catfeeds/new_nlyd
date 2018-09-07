
<!-- 登陆 -->
<div class="wrapper_content">
    <p class="titleLanguage">
        <!-- <span>切换语言</span>
        <span class="c_blue pointer">中文</span> -->
    </p>
    <div class="login-box-top">
        <div class="box-logo ">
            <img src="<?=student_css_url.'image/login-logo.png'?>" class="logoImg">
        </div>
        <!-- <div class="box-logo-name">
            <img src="<?=student_css_url.'image/InternationalIntellectualSports.png'?>" class="logoImg">
        </div> -->
    </div>
    <div class="layui-tab layui-tab-brief" lay-filter="tabs">
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show"> 
                <p class="ta_c">为了您账号的统一性，请您绑定手机号码</p>
                <!-- 手机号码登陆 -->
                <div class="tabs-wraps a1">
                    <form class="layui-form" action="" id='loginFormFast' lay-filter='loginFormFast'>
                        <!-- 使用手机验证码快速登录 -->
                        <div class="layui-form-item">
                            <div class="layui-input-inline">
                                <input type="hidden" name="action" value="wxWebLoginBindMobile">
                                <input type="hidden" name="_wpnonce" value="<?=wp_create_nonce('student_current_wx_web_login_nonce');?>">
                                <div class="input-icon "><i class="iconfont">&#xe61c;</i></div>
                                <input type="tel" name="mobile" lay-verify="phone" autocomplete="off" placeholder="手机号" class="layui-input hasIcon">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-inline">
                                <div class="input-icon "><i class="iconfont">&#xe645;</i></div>
<!--                                <input type="tel" name="send_code" placeholder="输入验证码" autocomplete="off" class="layui-input hasIcon">-->
                                <input type="tel" name="send_code" lay-verify="required" placeholder="输入验证码" autocomplete="off" class="layui-input hasIcon">
                                <a type="button" class="getCodeBtn c_blue getCode" data-sendCodeCase="17">获取验证码</a>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-inline">
                                <input type="hidden" name="user_id" value="<?=$data['uid']?>">
                                <input type="hidden" name="access" value="<?=$data['access']?>">
                                <input type="hidden" name="open" value="<?=$data['open']?>">
                                <a class="layui-btn submitBtn  bg_gradient_blue fs_16" id="bindPhone" lay-filter="bindPhone" lay-submit="">绑定手机号</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div> 
        </div> 
    </div>  
</div>