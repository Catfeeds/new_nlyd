<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <div class="main-header">
                <header class="mui-bar mui-bar-nav main">
                    <a class="mui-pull-left nl-goback static" href="<?=home_url('account/')?>">
                        <i class="iconfont">&#xe610;</i>
                    </a>
                    <h1 class="mui-title"><?=__('个人资料', 'nlyd-student')?></h1>
                </header>
            </div> 
            <div class="layui-row nl-border nl-content">
                <form class="nl-page-form layui-form width-margin-pc have-bottom" lay-filter='nicenameForm'>   
                
                    <div class="nl-form-tips width-padding width-padding-pc"><?=__('为了保证您考级及比赛的真实有效性，请您确保个人资料准确无误', 'nlyd-student')?></div>
                    <div class="form-inputs">
                        <div class="form-input-row no_edit">
                            <div class="form-input-label"><div><?=__('用户账号', 'nlyd-student')?></div></div>
                            <div class="nl-input"><div><?=$user_info['contact']?></div></div>
                        </div>
                        <div class="form-input-row no_edit">
                            <div class="form-input-label"><div>ID</div></div>
                            <div class="nl-input"><div><?=isset($user_info['user_ID']) ? $user_info['user_ID'] : '';?></div></div>
                        </div>
                        <div class="form-input-row">
                            <div class="form-input-label"><div><?=__('账户昵称', 'nlyd-student')?></div></div>
                            <input name='meta_val' value="<?=isset($user_info['nickname']) ? $user_info['nickname'] : '';?>" type="text" placeholder="<?=__('账户昵称', 'nlyd-student')?>" class="nl-input nl-foucs" lay-verify="required">
                            <input  type="hidden" name="action" value="student_saveInfo"/>
                            <input type="hidden" name="_wpnonce" value="<?=wp_create_nonce('student_saveInfo_code_nonce');?>">
                            <input  type="hidden" name="meta_key" value="user_nicename"/>
                            <div class="form-input-right c_blue" id="nicenameFormBtn" lay-filter="nicenameFormBtn" lay-submit=""><?=__('更新', 'nlyd-student')?></div>
                        </div>
                        <a class="form-input-row" href="<?=home_url('account/certification');?>" >
                            <div class="form-input-label"><div><?=__('实名认证', 'nlyd-student')?></div></div>
                            <span class="form-input-right c_blue"><?=__('修改实名认证', 'nlyd-student')?></span>
                            <div class="nl-input"><div><?=$user_info['real_ID']?></div></div>
                        </a>
                        <a class="form-input-row a address-row layui-row" href="<?=home_url('/account/address');?>">
                            <div class="form-input-label"><div><?=__('收件地址', 'nlyd-student')?></div></div>
                            <span class="form-input-right c_blue"><?=__('修改收件地址', 'nlyd-student')?></span>
                            <div  class="nl-input">  
                                <div>
                                <?php if($user_address){ ?>
                                    <?=$user_address['fullname']?>&nbsp;&nbsp;&nbsp;&nbsp;<?=$user_address['telephone']?>
                                    <br><?=$user_address['address']?>
                                <?php }else{ ?>
                                    <?=__('暂无地址', 'nlyd-student')?>
                                <?php }?>
                                </div>
                            </div>
                        </a>
                    </div>
                </form>
            </div>
        </div>           
    </div>
</div>
<input style="display:none;" type="file" name="meta_val" id="img" value="" accept="image/*" multiple/>
<input style="display:none;" type="file" name="meta_val" id="file" class="file" value="" accept="image/*" multiple/>
<input type="hidden" name="_wpnonce" id="inputImg" value="<?=wp_create_nonce('student_saveInfo_code_nonce');?>">
<script>
jQuery(document).ready(function($) {
        sendloginAjax=function(formData){
            $.ajax({
                data: formData,
                success: function(data, textStatus, jqXHR){
                    $.alerts(data.data.info)
                    // if(data.success){
                    //     if(data.data.url){
                    //         window.location.href=data.data.url
                    //     }
                    // }
                    return false;
                }
            });
        }
        layui.use(['form'], function(){
            var form = layui.form
            form.render();
            // 自定义验证规则
            form.verify($.validationLayui.allRules);
            form.on('submit(nicenameFormBtn)', function(data){//昵称
                sendloginAjax(data.field)
                return false;
            });
        });
})

</script>