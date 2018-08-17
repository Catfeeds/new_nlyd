<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_left_path.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <a class="mui-pull-left nl-goback">
                <i class="iconfont">&#xe610;</i>
            </a>
            <h1 class="mui-title">设置</h1>
        </header>  
            <div class="layui-row nl-border nl-content">
                <div class="main-page">
                    <div class="nl-page-form width-margin-pc">
                        <div class="form-inputs">
                            <a class="form-input-row a" href="<?=home_url('/account/setting/?action=userAgreement');?>">
                                <div class="form-input-label">用户协议</div>
                                <span class="form-input-right"><i class="iconfont">&#xe727;</i></span>
                            </a>
                            <a class="form-input-row a" href="<?=home_url('/account/setting/?action=privacyAgreement');?>">
                                <div class="form-input-label">隐私协议</div>
                                <span class="form-input-right"><i class="iconfont">&#xe727;</i></span>
                            </a>
                            <a class="form-input-row a" href="<?=home_url('/account/setting/?action=suggest');?>">
                                <div class="form-input-label">意见反馈</div>
                                <span class="form-input-right"><i class="iconfont">&#xe727;</i></span>
                            </a>
                            <div class="form-input-row">
                                <div class="form-input-label">版本号</div>
                                <span class="form-input-right">V2.01</span>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label">清除缓存</div>
                            </div>
                            <div class="a-btn" id="loginOut">退出登录</div>  
                        </div>
                    </div>
                </div>
            </div>
        </div>           
    </div>
</div>
<script>
    jQuery(document).ready(function($) {
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
                       window.location.reload()
                    }
                    return false;
                }
            });
        }
        $('#loginOut').click(function(){//登出
            sendloginAjax(window.admin_ajax,{action:'user_logout'})
        })
    })
</script>
