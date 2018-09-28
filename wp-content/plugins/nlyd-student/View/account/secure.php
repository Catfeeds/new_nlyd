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
                                <a class="form-input-right c_blue" href="<?=home_url('safety/safetySetting/type/pass')?>">修改</a>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label">绑定手机</div>
                                <?php if(!empty($user_info['user_mobile'])){?>
                                <div class="nl-input"><?=hideStar($user_info['user_mobile'])?></div>
                                <a class="form-input-right c_blue" href="<?=home_url('safety/safetySetting/type/mobile')?>">修改</a>
                                <?php }else{ ?>
                                    <a class="form-input-right c_blue" href="<?=home_url('safety/safetySetting/type/mobile')?>">去设置</a>
                                <?php } ?>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label">绑定邮箱</div>
                                <?php if(!empty($user_info['user_email'])){?>
                                    <div class="nl-input"><?=hideStar($user_info['user_email'])?></div>
                                    <a class="form-input-right c_blue" href="<?=home_url('safety/safetySetting/type/email')?>">修改</a>
                                <?php }else{ ?>
                                    <a class="form-input-right c_blue" href="<?=home_url('safety/safetySetting/type/email')?>">去设置</a>
                                <?php } ?>
                            </div>
                            <?php if(!empty($user_info['weChat_openid'])){ ?>
                            <div class="form-input-row">
                                <div class="form-input-label">绑定微信</div>
                                <div class="nl-input">*******</div>
                                <span class="form-input-right c_blue clear" data-type="weChat">解绑</span>
                            </div>
                            <?php } ?>
                            <?php if(!empty($user_info['qq_union_id'])){ ?>
                            <div class="form-input-row">
                                <div class="form-input-label">绑定QQ</div>
                                <div class="nl-input">*******</div>
                                <span class="form-input-right c_blue clear" data-type="qq">解绑</span>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>           
    </div>
</div>
<script>
jQuery(document).ready(function($) {
    $('.clear').click(function(){//解绑
        var type=$(this).attr('data-type')
        var postData={
            action:'untie',
            type:type
        }
        $.ajax({
            data: postData,
            success: function(data, textStatus, jqXHR){
                console.log(data)
                $.alerts(data.data.info)
                if(data.success){
                    if(data.data.url){
                        setTimeout(function() {
                            window.location.href=data.data.url
                        }, 1000);
                    }
                }
                return false;
            }
        });
    })
});
</script>
