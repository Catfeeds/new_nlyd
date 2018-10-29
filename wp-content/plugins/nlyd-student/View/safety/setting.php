<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <a class="mui-pull-left nl-goback static" onclick='window.location.href = "<?=home_url('account')?>"'>
                <div><i class="iconfont">&#xe610;</i></div>
            </a>
            <h1 class="mui-title"><div><?=__('设置', 'nlyd-student')?></div></h1>
        </header>  
            <div class="layui-row nl-border nl-content">
                <div class="have-bottom">
                    <div class="nl-page-form width-margin-pc ">
                        <div class="form-inputs">
                            <a class="form-input-row a" href="<?=home_url('/safety/userAgreement');?>">
                                <div class="form-input-label"><div><?=__('用户协议', 'nlyd-student')?></div></div>
                                <span class="form-input-right"><i class="iconfont">&#xe727;</i></span>
                            </a>
                            <a class="form-input-row a" href="<?=home_url('/safety/privacyAgreement');?>">
                                <div class="form-input-label"><div><?=__('隐私协议', 'nlyd-student')?></div></div>
                                <span class="form-input-right"><i class="iconfont">&#xe727;</i></span>
                            </a>
                            <a class="form-input-row a" href="<?=home_url('/safety/suggest');?>">
                                <div class="form-input-label"><div><?=__('意见反馈', 'nlyd-student')?></div></div>
                                <span class="form-input-right"><i class="iconfont">&#xe727;</i></span>
                            </a>
                            <div class="form-input-row">
                                <div class="form-input-label"><div><?=__('版本号', 'nlyd-student')?></div></div>
                                <span class="form-input-right"><?=leo_student_version?></span>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label"><div><?=__('清除缓存', 'nlyd-student')?></div></div>
                            </div>
                            <a class="a-btn a-btn-table" id="loginOut"><div><?=__('退出登录', 'nlyd-student')?></div></a>
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
            //type:确定回调函数
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
                            setTimeout(function(){
                                window.location.href=data.data.url
                            }, 1600);
                        }
                    }
                    return false;
                }
            });
        }
        $('#loginOut').click(function(){//登出
            sendloginAjax(window.admin_ajax+"?date="+new Date().getTime(),{action:'user_logout'})
        })
    })
</script>
