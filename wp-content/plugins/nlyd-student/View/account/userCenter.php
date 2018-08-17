<!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
<!--[if lt IE 9]>
  <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
  <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<style>
@media screen and (max-width: 991px){
    .layui-fluid>.layui-row>.layui-bg-white:first-child,#page{
        background-color:#eee!important;
    }
    #page{
        top:0;
    }
}
</style>
<div class="nl-foot-nav">
    <a class="nl-foot-item" href="<?=home_url();?>">
        <div class="nl-foot-icon"><i class="iconfont">&#xe62d;</i></div>
        <div class="nl-foot-name">首页</div>
    </a>
    <a class="nl-foot-item" href="<?=home_url('/account/matchList');?>">
        <div class="nl-foot-icon"><i class="iconfont">&#xe631;</i></div>
        <div class="nl-foot-name">训练</div>
    </a>
    <a class="nl-foot-item" href="<?=home_url('/matchs');?>">
        <div class="nl-foot-icon"><i class="iconfont">&#xe692;</i></div>
        <div class="nl-foot-name">比赛</div>
    </a>
    <a class="nl-foot-item">
        <div class="nl-foot-icon"><i class="iconfont">&#xe630;</i></div>
        <div class="nl-foot-name">考级</div>
    </a>
    <a class="nl-foot-item active" href="<?=home_url('account')?>">
        <div class="nl-foot-icon"><i class="iconfont">&#xe632;</i></div>
        <div class="nl-foot-name">我的</div>
    </a>
</div>
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
                <div class='bubble bubble1 layui-hide-md layui-hide-lg'></div>
                <div class='bubble bubble2 layui-hide-md layui-hide-lg'></div>
                <div class='bubble bubble3 layui-hide-md layui-hide-lg'></div>
                <div class='bubble bubble4 layui-hide-md layui-hide-lg'></div>
                <div class='bubble bubble5 layui-hide-md layui-hide-lg'></div>
                <!-- 消息 -->
                <a href="<?=home_url('account/messages')?>" class="userCenter-message layui-hide-md layui-hide-lg"><i class="iconfont">&#xe60d;</i>&nbsp;&nbsp;消息<?=$message_total > 0 ? '<span class="layui-badge-dot"></span>' : '';?></a>
                <!-- 编辑 -->
                <a href="<?=home_url('account/info')?>" class="userCenter-edit layui-hide-md layui-hide-lg"><i class="iconfont">&#xe600;</i>&nbsp;&nbsp;编辑资料</a>
                <!-- 级别 -->
                <div class="userCenter-level width-left layui-row layui-bg-white layui-hide-md layui-hide-lg">
                    <div class="userCenter-level-item">
                        <p><?=empty($my_skill['reading'])?0:$my_skill['reading']?>级</p>
                        <p>速读级别</p>
                    </div>
                    <div class="userCenter-level-item">
                        <p><?=empty($my_skill['memory'])?0:$my_skill['memory']?>级</p>
                        <p>速记级别</p>
                    </div>
                    <div class="userCenter-level-item">
                        <p><?=empty($my_skill['compute'])?0:$my_skill['compute']?>级</p>
                        <p>速算级别</p>
                        <!-- <span class="userCenter-level-info bold"><?=$user_info['mycred_default_total']?></span> -->
                    </div>
                </div>
                <!-- 头像 -->
                <div class="userCenter-main isMobile layui-row img-box">
                    <img src="<?=$user_info['user_head'];?>" class="logoImg rounded" id="avatar">
                </div>
                <!-- 用户名称 -->
                <div class="userCenter-name layui-row"><div class="userCenter-names"><?=$user_info['nickname']?></div><?=$user_info['user_type'] ? '<div class="userCenter-type layui-hide-md layui-hide-lg">'.$user_info['user_type'].'</div>':'';?></div>
                <!-- 用户标签 -->
                <div class="userCenter-describe layui-row layui-hide-md layui-hide-lg">
                    <span class="userCenter-item">ID<?=isset($user_info['user_ID']) ? ':'.$user_info['user_ID'] : '';?></span>
                    <span class="userCenter-item"><?= !empty($user_info['user_gender']) ? $user_info['user_gender'] : '性别';?></span>
                    <span class="userCenter-item"><?= !empty($user_info['user_real_name']) ? $user_info['user_real_name']['real_age'] : '年龄';?></span>
                    <span class="userCenter-item"><?= !empty($user_info['user_address']) ? $user_info['user_address']['city'].$user_info['user_address']['area'] : '所在地';?></span>
                </div>
                <!-- 战队信息 -->
                <div class="userCenter-operations layui-hide-md layui-hide-lg">
                    <?php if(!empty($my_team['mental'])): ?>
                        <i class="iconfont">&#xe64a;</i> <?=$my_team['mental']?>
                    <?php endif;?>
                </div>
            </div>
            <div class="width-margin layui-row menu-wrapper">
                <?php if(!empty($my_team['my_team'])){ ?>
                <!-- 我的战队 -->
                    <a class="userCenter-row layui-row layui-bg-white layui-hide-md layui-hide-lg" href="<?=home_url('teams/teamDetail/team_id/'.$my_team['ID'])?>"><span class="pull-left"><?=$my_team['my_team']?></span><span class="pull-right">查看</span></a>
                <?php }else{ ?>
                    <a class="userCenter-row layui-row layui-bg-white layui-hide-md layui-hide-lg" href="<?=home_url('/teams')?>"><span class="pull-left grey-color">暂无战队</span><span class="pull-right">加入战队</span></a>
                <?php }; ?>
                <!-- 我的钱包 -->
                <a class="userCenter-row layui-row layui-bg-white layui-hide-md layui-hide-lg" href="<?=home_url('account/wallet')?>">
                    <span class="pull-left">我的余额：<i class="iconfont">&#xe61e;</i>3200.00</span>
                    <span class="pull-right">我的脑币：<?=$user_info['mycred_default_total'] > 0 ? $user_info['mycred_default_total'] : 0 ;?></span>
                </a>
                <div class="userCenter-detail layui-row layui-bg-white width-margin-pc">
                    <a href="<?=home_url('/account/info');?>" class="layui-show-lg-block layui-show-md-block layui-hide-sm layui-hide-xs">
                        <div class="userCenter-detail-head">
                            <!-- <i class="iconfont">&#xe60f;</i> -->
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/match-big.png'?>" class="menuImg">
                            </div>
                            
                        </div>
                        <div class="userCenter-detail-foot">个人资料</div>
                    </a>
                    <a href="<?=home_url('/account/messages');?>" class="layui-show-lg-block layui-show-md-block layui-hide-sm layui-hide-xs">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/match-big.png'?>" class="menuImg">
                            </div>
                            
                        </div>
                        <div class="userCenter-detail-foot">消 息</div>
                    </a>
                    <a href="<?=home_url('/account/recentMatch');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/match-big.png'?>" class="menuImg">
                            </div>
                            
                        </div>
                        <div class="userCenter-detail-foot">比 赛</div>
                    </a>
                    <a href="<?=home_url('/account/train');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/train-big.png'?>" class="menuImg">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">训 练</div>
                    </a>
                    <a href="<?=home_url('/account/course');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/course-big.png'?>" class="menuImg">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">课 程</div>
                    </a>
                    <a href="<?=home_url('/teams/myCoach');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/coach-big.png'?>" class="menuImg">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">教 练</div>
                    </a>
                    <a href="<?=home_url('/account/order');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/order-big.png'?>" class="menuImg">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">订 单</div>
                    </a>
                    <a>
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/kaoji-big.png'?>" class="menuImg">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">考 级</div>
                    </a>
                    <a>
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/tuiguang-big.png'?>" class="menuImg">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">推 广</div>
                    </a>
                    <a>
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/zice-big.png'?>" class="menuImg">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">自 测</div>
                    </a>
                    <a href="<?=home_url('/safety/setting');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/setting-big.png'?>" class="menuImg">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">设 置</div>
                    </a>
                    <a href="<?=home_url('/teams');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/setting-big.png'?>" class="menuImg">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">战 队</div>
                    </a>
                    <a href="<?=home_url('/account/secure');?>">
                        <div class="userCenter-detail-head">
                            <div class="menuImg-wrapper">
                                <img src="<?=student_css_url.'image/icons/secure-big.png'?>" class="menuImg">
                            </div>
                        </div>
                        <div class="userCenter-detail-foot">安全中心</div>
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
<?php if(empty($user_info['user_real_name']) && time() < $_SESSION['login_time']){ ?>
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

    $('.isMobile').click(function(){
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
        var initialAvatarURL;
        var canvas;
        if (cropper) {
        canvas = cropper.getCroppedCanvas({
            width: 160,
            height: 160,
        });
        initialAvatarURL = avatar.src;
        avatar.src = canvas.toDataURL();
        canvas.toBlob(function (blob) {
            var formData = new FormData();
            formData.append('action','student_saveInfo');
            formData.append('_wpnonce',$("#inputImg").val());
            formData.append('meta_key','user_head');
            formData.append('meta_val',blob);
            $.ajax({
                type: "POST",
                    url: window.admin_ajax,
                    data: formData,
                    dataType:'json',
                    timeout:3000,
                    contentType : false,
                    processData : false,
                    cache : false,
                    success: function(data, textStatus, jqXHR){
                        console.log(data)
                        $.alerts(data.data.info)
                        if(data.data.head_url){
                            $('.logoImg').attr('src',data.data.head_url)
                        }
                        bg.removeClass('bg-show')
                        cropper.destroy();
                        cropper = null;
                    },
                    error: function (data) {
                        console.log(data)
                    },
                })
            }); 
        }
    });
});
</script>