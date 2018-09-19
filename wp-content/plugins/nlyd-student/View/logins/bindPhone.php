
<!-- 登陆 -->
<div class="wrapper_content">
    <div class="login_zoo">
        <!-- <p class="titleLanguage">
            <span>切换语言</span>
            <span class="c_blue pointer">中文</span>
        </p> -->
        <div class="login-box-top">
            <div class="box-logo ">
                <img src="<?=student_css_url.'image/login-logo.png'?>" class="logoImg">
            </div>
        </div>
        <div class="layui-tab layui-tab-brief" lay-filter="tabs">
            <ul style="margin-left: 0" class="layui-tab-title  ">
                <li class="layui-this">
                    <div class="login_icon iconLock lock_blue display-hide"></div>
                    <div class="login_icon iconPhone phone_blue"></div>&nbsp;&nbsp;<span class="formName">验证码绑定</span>
                </li>
                <li>
                    <div class="login_icon icon-zhuce user_blue"></div>&nbsp;&nbsp;<span >用户登录</span>
                </li>
                <div class="nl-transform">
                    <div class="login_icon iconLock user_white display-hide"></div>
                    <div class="login_icon iconPhone phone_white"></div>&nbsp;&nbsp;<span class="formName">验证码绑定</span>
                </div>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show"> 
                    <p class="ta_c">首次登录请绑定手机号或邮箱</p>
                    <!-- 手机号码登陆 -->
                    <form class="layui-form" action="" id='loginFormFast' lay-filter='loginFormFast'>
                        <!-- 使用手机验证码快速登录 -->
                        <div class="layui-form-item">
                            <div class="layui-input-inline">
                                <input type="hidden" name="action" value="wxWebLoginBindMobile">
                                <input type="hidden" name="_wpnonce" value="<?=wp_create_nonce('student_current_wx_web_login_nonce');?>">
                                <div class="input-icon "><div class="login_icon phone_grey"></div></div>
                                <input type="text" name="mobile" id="mobile" lay-verify="phoneOrEmail" autocomplete="off" placeholder="手机号/邮箱" class="layui-input hasIcon">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-inline">
                                <div class="input-icon "><div class="login_icon code_grey"></div></div>
                                <input type="tel" name="send_code" lay-verify="required" placeholder="输入验证码" autocomplete="off" class="layui-input hasIcon">
                                <a type="button" class="getCodeBtn c_blue getCode" data-sendCodeCase="17">获取验证码</a>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-inline">
                                <input type="hidden" name="type" value="code">
                                <input type="hidden" name="user_id" value="<?=$data['uid']?>">
                                <input type="hidden" name="access" value="<?=$data['access']?>">
                                <input type="hidden" name="open" value="<?=$data['open']?>">
                                <a class="layui-btn submitBtn  bg_gradient_blue fs_16" id="bindPhone" lay-filter="bindPhone" lay-submit="">确认绑定</a>
                            </div>
                        </div>
                    </form>
                </div> 

                <div class="layui-tab-item">
                    <p class="ta_c">请使用已有账号密码登录</p>
                    <form class="layui-form" action="" id='loginPwdForm' lay-filter='loginPwdForm'>
                        <!-- 使用密码登录 -->
                        <div class="layui-form-item">
                            <div class="layui-input-inline">
                                <input type="hidden" name="action" value="wxWebLoginBindMobile">
                                <input type="hidden" name="_wpnonce" value="<?=wp_create_nonce('student_current_wx_web_login_nonce');?>">
                                <div class="input-icon "><div class="login_icon phone_grey"></div></div>
                                <input type="text" name="mobile" lay-verify="phoneOrEmail" autocomplete="off" placeholder="手机号/邮箱" class="layui-input hasIcon">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-inline">
                                <div class="input-icon "><div class="login_icon code_grey"></div></div>
                                <input type="password" name="password" lay-verify="required" placeholder="输入密码" autocomplete="off" class="layui-input hasIcon">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-inline">
                                <input type="hidden" name="type" value="username">
                                <input type="hidden" name="user_id" value="<?=$data['uid']?>">
                                <input type="hidden" name="access" value="<?=$data['access']?>">
                                <input type="hidden" name="open" value="<?=$data['open']?>">
                                <a class="layui-btn submitBtn  bg_gradient_blue fs_16" id="bindPwd" lay-filter="bindPwd" lay-submit="">登 录</a>
                            </div>
                        </div>
                    </form>
                </div> 
            </div> 
        </div>  
    </div>
</div>