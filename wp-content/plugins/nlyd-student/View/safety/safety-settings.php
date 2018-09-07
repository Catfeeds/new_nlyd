<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <a class="mui-pull-left nl-goback static" onclick='window.location.href = "<?=home_url('account')?>"'>
                <i class="iconfont">&#xe610;</i>
            </a>
            <h1 class="mui-title">重置密码/更换手机号/绑定手机号/绑定邮箱</h1>
        </header>  
            <div class="layui-row nl-border nl-content">
                <div class="have-bottom">
                    <form class="nl-page-form width-margin-pc layui-form" lay-filter='safetySetting'>
                        <div class="form-inputs">
                            <!-- 重置密码 -->
                            <div class="form-input-row">
                                <div class="form-input-label">旧密码</div>
                                <div class="nl-input">**********</div>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label">新密码</div>
                                <div class="nl-input">luolan12345</div>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label">再次输入</div>
                                <div class="nl-input">13982242710</div>
                            </div>
                            <!-- 更换手机号 -->


                            <!-- 绑定手机号 -->


                            <!-- 绑定更换邮箱 -->
                            <div class="form-input-row">
                                <div class="form-input-label">登陆密码</div>
                                <div class="nl-input">**********</div>
                                <a class="form-input-right c_blue">修改</a>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label">绑定手机</div>
                                <div class="nl-input">13982242710</div>
                                <a class="form-input-right c_blue">修改</a>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label">绑定邮箱</div>
                                <div class="nl-input">821831825@qq.com</div>
                                <a class="form-input-right c_blue">修改</a>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label">绑定微信</div>
                                <div class="nl-input">不听不听</div>
                                <a class="form-input-right c_blue">解绑</a>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label">绑定QQ</div>
                                <div class="nl-input">一时发疯，回不了神</div>
                                <a class="form-input-right c_blue">解绑</a>
                            </div>
                            <a class="a-btn" style="display:none;" id="safetySetting" lay-filter="safetySetting" lay-submit="">更 新</a>
                        </div>
                    </form>
                </div>
                <a class="a-btn safetySetting">更 新</a>
            </div>
        </div>           
    </div>
</div>
<script>
    jQuery(document).ready(function($) {
        $('.safetySetting').click(function(){
            $('#safetySetting').click()
        })
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
                sendloginAjax(window.admin_ajax+"?date="+new Date().getTime(),data.field)
                return false;
            });
        });
    })
</script>
