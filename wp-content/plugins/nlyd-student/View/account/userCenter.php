<style>
@media screen and (max-width: 991px){
    .layui-fluid>.layui-row>.layui-bg-white:first-child,#page{
        background-color:#f6f6f6!important;
    }
    #page{
        top:0;
    }
}
</style>
<?php require_once PLUGINS_PATH.'nlyd-student/View/public/student-footer-menu.php' ;?>
<div class="nl-cropper-bg">
    <div class="img-container">
        <img id="image" src="">
    </div>
    <div class="nl-cropper-footer">
        <button type="button" class="pull-left" id='crop-cancel'>取消</button>
        <button type="button" class="pull-right" id="crop">确认</button>
    </div>

</div>
<div class="layui-fluid">
    <div class="layui-row">
        <div class="nl-left-menu layui-col-sm12 layui-col-xs12 layui-bg-white have-footer">
            <div class="userCenter-info layui-row">
                <?php if(is_user_logged_in()): ?>
                <!-- 消息 -->
                <a href="<?=home_url('account/messages')?>" class="userCenter-message layui-hide-md layui-hide-lg"><i class="iconfont">&#xe60d;</i>&nbsp;&nbsp;消息<?=$message_total > 0 ? '<span class="layui-badge-dot"></span>' : '';?></a>
                <!-- 编辑 -->
                <a href="<?=home_url('account/info')?>" class="userCenter-edit layui-hide-md layui-hide-lg"><i class="iconfont">&#xe600;</i>&nbsp;&nbsp;编辑资料</a>
                <?php endif;?>
                <div class="radius-zoo">
                    <!-- 头像 -->
                    <div class="userCenter-main layui-row">
                        <div class="img-box">
                            <img src="<?=$user_info['user_head'];?>" class="logoImg rounded" id="avatar">
                        </div>
                    </div>
                    <!-- 用户名称 -->
                    <div class="userCenter-name layui-row">
                        <?php if(!is_user_logged_in()){ ?>
                        <a class="userCenter-names c_black" href="<?=home_url('/logins')?>">未登录</a>
                        <?php }else{ ?>
                        <div class="userCenter-names login"><?=$user_info['nickname']?></div>
                        <?=$user_info['user_type'] ? '<div class="userCenter-type fs_12 layui-hide-md layui-hide-lg">'.$user_info['user_type'].'</div>':'';?>
                        <?php } ?>
                    </div>
                    <?php if(is_user_logged_in()): ?>
                    <!-- 用户标签 -->
                    <div class="userCenter-describe layui-row layui-hide-md layui-hide-lg">
                        <span class="userCenter-item">ID<?=isset($user_info['user_ID']) ? ':'.$user_info['user_ID'] : '';?></span>
                        <?php if(in_array($my_team['status'],array(-1,1,2))){ ?>
                            <a class="userCenter-item c_black6" href="<?=home_url('teams/teamDetail/team_id/'.$my_team['ID'])?>">
                                <?=$my_team['my_team']?>
                                <?php if($my_team['status'] != 2):?>
                                <span>(<?=$my_team['status_cn']?>)</span>
                                <?php endif;?>
                            </a>
                        <?php }else{ ?>
                            <a class="userCenter-item c_blue" href="<?=home_url('teams')?>">加入战队</a>
                        <?php }; ?>
                    </div>
                    <?php endif;?>
                </div>
            </div>
            <div class="layui-row menu-wrapper">
                <?php if(is_user_logged_in()){ ?>
                <!-- 级别 -->
                <div class="userCenter-row width-padding layui-row layui-bg-white layui-hide-md layui-hide-lg ta_c text_1">
                    <span class="fs_14 c_black">
                        <?php if(!empty($my_skill['nationality']) && !empty($my_skill['mental_lv']) && !empty($my_skill['mental_type'])):?>
                            <?=$my_skill['nationality']?><span class="c_orange"><?=$my_skill['mental_lv']?></span>级<?=$my_skill['mental_type']?> |
                        <?php endif;?>
                        记忆<span class="c_orange bold"><?=empty($my_skill['memory'])?0:$my_skill['memory']?></span>级 |
                        速读<span class="c_orange bold"><?=empty($my_skill['reading'])?0:$my_skill['reading']?></span>级 |
                        速算<span class="c_orange bold"><?=empty($my_skill['compute'])?0:$my_skill['compute']?></span>级
                    </span>
                </div>
                <?php }else{ ?>
                <div class="userCenter-row width-padding layui-row layui-bg-white layui-hide-md layui-hide-lg ta_c text_1">
                    <a class="c_black6" href="<?=home_url('/logins')?>">登录后可查看认证脑力等级</a>
                </div>
                <?php } ?>
                <!-- 我的钱包 -->
                <!-- <a class="userCenter-row layui-row layui-bg-white layui-hide-md layui-hide-lg" href="<?=home_url('wallet')?>">
                    <span class="pull-left">我的余额：<i class="iconfont">&#xe61e;</i>3200.00</span>
                    <span class="pull-right">我的脑币：<?=$user_info['mycred_default_total'] > 0 ? $user_info['mycred_default_total'] : 0 ;?></span>
                </a> -->
                <div class="userCenter-detail width-padding layui-row layui-bg-white width-margin-pc">
                    <a class="c_black8" href="<?=home_url('/account/recentMatch');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper my-match">
                            </div>
                            
                        </div>
                        <div class="userCenter-detail-foot">我的比赛</div>
                    </a>
                    <a class="c_black8 disabled_a"  href="<?=home_url('/account/matchList');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper my-train">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">我的训练</div>
                    </a>
                    <a class="c_black8 disabled_a"  href="<?=home_url('/account/course');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper my-course">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">我的课程</div>
                    </a>
                    <a class="c_black8" href="<?=home_url('/teams/myCoach');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper my-coach">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">我的教练</div>
                    </a>
                    <a class="c_black8 disabled_a"  href="<?=home_url('orders');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper my-order">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">我的订单</div>
                    </a>
                    <a class="c_black8 disabled_a" >
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper my-kaoji">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">我的考级</div>
                    </a>
                    <a class="c_black8 disabled_a" >
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper my-tuiguang">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">我的推广</div>
                    </a>
                    <a class="c_black8"  href="<?=home_url('/account/secure');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper my-secure">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">安全中心</div>
                    </a>
                    <a class="c_black8" href="<?=home_url('/safety/setting');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper my-setting">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">其他设置</div>
                    </a>
                    <a class="c_black8 disabled_a"  href="<?=home_url('/teams');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper my-wallet">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">我的钱包</div>
                    </a>
                </div>
            </div>
        
            <input style="display:none;" type="file" name="meta_val" id="file" class="file" value="" accept="image/*" multiple />
            <input type="hidden" name="_wpnonce" id="inputImg" value="<?=wp_create_nonce('student_saveInfo_code_nonce');?>">        
        </div>
        <div class="nl-right-content layui-show-lg-block layui-show-md-block layui-hide-sm layui-hide-xs layui-bg-white">
            <header class="mui-bar mui-bar-nav">
            <a class="mui-pull-left nl-goback">
            <i class="iconfont">&#xe610;</i>
            </a>
            <h1 class="mui-title">个人中心</h1>
            </header>
            <div class="layui-row nl-border nl-content">

            </div>
        </div>
    </div>
</div>
<script>
<?php if(empty($user_info['user_real_name']) && get_time() < $_SESSION['login_time']){ ?>
layui.use('layer', function(){ //独立版的layer无需执行这一句
  layer.open({
        type: 1
        ,maxWidth:300
        ,title: '提示' //不显示标题栏
        ,skin:'nl-box-skin'
        ,id: 'certification' //防止重复弹出
        ,content: '<div class="box-conent-wrapper">是否立即进行实名认证？</div>'
        ,btn: ['稍后认证', '立即认证', ]
        ,success: function(layero, index){
            
        }
        ,yes: function(index, layero){
            layer.closeAll();
        }
        ,btn2: function(index, layero){
            //按钮【按钮二】的回调
            
            //return false 开启该代码可禁止点击该按钮关闭
            window.location.href="<?=home_url('/account/info');?>"
        }
        ,closeBtn:2
        ,btnAagn: 'c' //按钮居中
        ,shade: 0.3 //遮罩
        ,isOutAnim:true//关闭动画
      });
});
<?php } ?>
jQuery(document).ready(function($) {
    $('.userCenter-main').click(function(){
        $("#file").click()
    })
    var avatar = $('#avatar');
    var image = $('#image');
    var input = $('#file');
    var bg=$('.nl-cropper-bg');
    var cropper;
    input.change(function (e) {
        var files = e.target.files;
        var done = function (url) {
            input.val('');
            image.attr('src',url);
            bg.addClass('bg-show')
            cropper = new Cropper(image[0], {
                aspectRatio: 1,
            });
        };
        var reader;
        var file;
        var url;

        if (files && files.length > 0) {
            file = files[0];
            reader = new FileReader();
            reader.onload = function (ev) {
                done(reader.result);
            };
            reader.readAsDataURL(file);
        }
    });

    $('body').on('click','#crop-cancel',function(){
            bg.removeClass('bg-show')
            cropper.destroy();
            cropper = null;
    })
    document.getElementById('crop').addEventListener('click', function () {
        var canvas;
        if (cropper) {
        canvas = cropper.getCroppedCanvas({
            width: 160,
            height: 160,
        });
        avatar.src = canvas.toDataURL();
        if (!HTMLCanvasElement.prototype.toBlob) {//针对ios不兼容toBlob（）
            Object.defineProperty(HTMLCanvasElement.prototype, 'toBlob', {
            value: function (callback, type, quality) {

                var binStr = atob( this.toDataURL(type, quality).split(',')[1] ),
                    len = binStr.length,
                    arr = new Uint8Array(len);

                for (var i=0; i<len; i++ ) {
                arr[i] = binStr.charCodeAt(i);
                }

                callback( new Blob( [arr], {type: type || 'image/png'} ) );
            }
            });
        }
        canvas.toBlob(function (blob) {
            var formData = new FormData();
            formData.append('action','student_saveInfo');
            formData.append('_wpnonce',$("#inputImg").val());
            formData.append('meta_key','user_head');
            formData.append('meta_val',blob);
            $.ajax({
                    data: formData,
                    contentType : false,
                    processData : false,
                    success: function(data, textStatus, jqXHR){
                        $.alerts(data.data.info)
                        if(data.data.head_url){
                            $('.logoImg').attr('src',data.data.head_url)
                        }
                        bg.removeClass('bg-show')
                        cropper.destroy();
                        cropper = null;
                    }
                })
            }); 
        }
    });
});
</script>