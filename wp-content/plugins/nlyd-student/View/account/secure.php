<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <a class="mui-pull-left nl-goback static" onclick='window.location.href = "<?=home_url('account')?>"'>
                <i class="iconfont">&#xe610;</i>
            </a>
            <h1 class="mui-title">安全设置</h1>
        </header>  
            <div class="layui-row nl-border nl-content">
                <div class="have-bottom">
                    <div class="nl-page-form width-margin-pc ">
                        <div class="form-inputs">
                            <div class="form-input-row">
                                <div class="form-input-label">登陆密码</div>
                                <div class="nl-input">**********</div>
                                <a class="form-input-right c_blue" href="<?=home_url('safety/safetySetting')?>">修改</a>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label">绑定手机</div>
                                <div class="nl-input">13982242710</div>
                                <a class="form-input-right c_blue" href="<?=home_url('safety/safetySetting')?>">修改</a>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label">绑定邮箱</div>
                                <div class="nl-input">821831825@qq.com</div>
                                <a class="form-input-right c_blue" href="<?=home_url('safety/safetySetting')?>">修改</a>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label">绑定微信</div>
                                <div class="nl-input">不听不听</div>
                                <a class="form-input-right c_blue" href="<?=home_url('safety/safetySetting')?>">解绑</a>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label">绑定QQ</div>
                                <div class="nl-input">一时发疯，回不了神</div>
                                <a class="form-input-right c_blue" href="<?=home_url('safety/safetySetting')?>">解绑</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>           
    </div>
</div>
