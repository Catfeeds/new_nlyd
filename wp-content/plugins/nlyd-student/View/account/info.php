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
                    <h1 class="mui-title">个人资料</h1>
                </header>
            </div> 
            <div class="layui-row nl-border nl-content">
                    <form class="nl-page-form layui-form width-margin-pc have-bottom" lay-filter='nicenameForm'>   
                    
                        <div class="nl-form-tips width-padding width-padding-pc">为了保证您考级及比赛的真实有效性，请您确保个人资料准确无误</div>
                        <div class="form-inputs">
                            <div class="form-input-row no_edit">
                                <div class="form-input-label">用户账号</div>
                                <div class="nl-input"><?=$user_info['contact']?></div>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label">账户昵称</div>
                                <input name='meta_val' value="<?=isset($user_info['nickname']) ? $user_info['nickname'] : '';?>" type="text" placeholder="账户昵称" class="nl-input nl-foucs" lay-verify="required">
                                <input  type="hidden" name="action" value="student_saveInfo"/>
                                <input type="hidden" name="_wpnonce" value="<?=wp_create_nonce('student_saveInfo_code_nonce');?>">
                                <input  type="hidden" name="meta_key" value="user_nicename"/>
                            </div>
                            <a class="form-input-row" href="<?=home_url('account/certification');?>" >
                                <div class="form-input-label">实名认证</div>
                                <span class="form-input-right"><i class="iconfont">&#xe727;</i></span>
                                <div class="nl-input"><?=$user_info['real_ID']?></div>
                            </a>
                            <a class="form-input-row a address-row layui-row" href="<?=home_url('/account/address');?>">
                                <div class="form-input-label">收货地址</div>
                                <span class="form-input-right"><i class="iconfont">&#xe727;</i></span>
                                <div  class="nl-input">  
                                    <?php if($user_address){ ?>
                                        <p class="accept-address">
                                            <?=$user_address['fullname']?>&nbsp;&nbsp;&nbsp;&nbsp;<?=$user_address['telephone']?>
                                            <br><?=$user_address['address']?>
                                        </p>
                                    <?php }else{ ?>
                                        暂无地址
                                    <?php }?>
                                        
                                </div>
                            </a>
  
                            <a class="a-btn" id="nicenameFormBtn" lay-filter="nicenameFormBtn" lay-submit="">更新个人资料</a>
                        </div>
                
                    </form>
              
                    <!-- <a class="a-btn nicenameFormBtn" >更新个人资料</a> -->
            </div>
        </div>           
    </div>
</div>
<script>
jQuery(document).ready(function($) {
        sendloginAjax=function(formData){
            //type：确定回调函数
            //url:ajax地址
            //formData:ajax传递的参数
            $.ajax({
                data: formData,
                success: function(data, textStatus, jqXHR){
                    $.alerts(data.data.info)
                    if(data.success){
                        if(data.data.url){
                            window.location.href=data.data.url
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
            form.on('submit(nicenameFormBtn)', function(data){//昵称
                sendloginAjax(data.field)
                return false;
            });
        });
})

</script>