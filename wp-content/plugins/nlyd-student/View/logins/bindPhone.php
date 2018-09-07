
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
                                <input type="hidden" name="action" value="student_login">
                                <input type="hidden" name="login_type" value="mobile">
                                <input type="hidden" name="_wpnonce" value="<?=wp_create_nonce('student_login_code_nonce');?>">
                                <input type="tel" name="user_login" lay-verify="phone" autocomplete="off" placeholder="手机号" class="layui-input ">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-inline">
                                <input type="tel" name="password" lay-verify="required" placeholder="输入验证码" autocomplete="off" class="layui-input ">
                                <a type="button" class="getCodeBtn c_blue getCode" data-sendCodeCase="19">获取验证码</a>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-inline">
                                <a class="layui-btn submitBtn  bg_gradient_blue fs_16" id="loginFormFastBtn" lay-filter="loginFormFastBtn" lay-submit="">绑定手机号</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div> 
        </div> 
    </div>  
</div>