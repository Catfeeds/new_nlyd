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
            <h1 class="mui-title"><div><?=__('安全设置', 'nlyd-student')?></div></h1>
        </header>  
            <div class="layui-row nl-border nl-content">
                <div class="have-bottom">
                    <div class="nl-page-form width-margin-pc ">
                        <div class="form-inputs">
                            <div class="form-input-row">
                                <div class="form-input-label"><div><?=__('登陆密码', 'nlyd-student')?></div></div>
                                <div class="nl-input"><div>**********</div></div>
                                <a class="form-input-right c_blue" href="<?=home_url('safety/safetySetting/type/pass')?>"><div><?=__('修改', 'nlyd-student')?></div></a>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label"><div><?=__('绑定手机', 'nlyd-student')?></div></div>
                                <?php if(!empty($user_info['user_mobile'])){?>
                                <div class="nl-input"><div><?=hideStar($user_info['user_mobile'])?></div></div>
                                <a class="form-input-right c_blue" href="<?=home_url('safety/safetySetting/type/mobile')?>"><div><?=__('修改', 'nlyd-student')?></div></a>
                                <?php }else{ ?>
                                    <a class="form-input-right c_blue" href="<?=home_url('safety/safetySetting/type/mobile')?>"><div><?=__('去设置', 'nlyd-student')?></div></a>
                                <?php } ?>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label"><div><?=__('绑定邮箱', 'nlyd-student')?></div></div>
                                <?php if(!empty($user_info['user_email'])){?>
                                    <div class="nl-input"><div><?=hideStar($user_info['user_email'])?></div></div>
                                    <a class="form-input-right c_blue" href="<?=home_url('safety/safetySetting/type/email')?>"><div><?=__('修改', 'nlyd-student')?></div></a>
                                <?php }else{ ?>
                                    <a class="form-input-right c_blue" href="<?=home_url('safety/safetySetting/type/email')?>"><div><?=__('去设置', 'nlyd-student')?></div></a>
                                <?php } ?>
                            </div>
                            <?php if(!empty($user_info['weChat_openid'])){ ?>
                            <div class="form-input-row">
                                <div class="form-input-label"><div><?=__('绑定微信', 'nlyd-student')?></div></div>
                                <div class="nl-input"><div>*******</div></div>
                                <div class="form-input-right c_blue clear" data-type="weChat"><div><?=__('解绑', 'nlyd-student')?></div></div>
                            </div>
                            <?php } ?>
                            <?php if(!empty($user_info['qq_union_id'])){ ?>
                            <div class="form-input-row">
                                <div class="form-input-label"><div><?=__('绑定QQ', 'nlyd-student')?></div></div>
                                <div class="nl-input"><div>*******</div></div>
                                <div class="form-input-right c_blue clear" data-type="qq"><div><?=__('解绑', 'nlyd-student')?></div></div>
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
