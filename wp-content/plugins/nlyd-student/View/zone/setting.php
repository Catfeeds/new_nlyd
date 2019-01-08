
<div class="layui-fluid">
    <div class="layui-row">
        <?php
        require_once leo_student_public_view.'leftMenu.php';

        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback static" href="<?=home_url('/zone/');?>">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('账号设置', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="layui-row">
                    <div class="layui-form nl-page-form " lay-filter='layform'>
                        <div class="form-inputs">
                            <!-- 存在关联账号 -->
                            <div class="form-input-row">
                                <div class="form-input-label"><div><?=__('登录账号', 'nlyd-student')?></div></div>
                                <input type="text" name="" readonly value="15695222@gjnlyd.com" placeholder="<?=__('登录账号', 'nlyd-student')?>" class="nl-input nl-foucs">
                                <a class="form-input-right c_blue" href="<?=home_url('/zone/settingPsw/');?>"><div><?=__('修改密码', 'nlyd-student')?></div></a>
                            </div>
                            <div class="form-input-row">
                                <div class="form-input-label"><div><?=__('管理员', 'nlyd-student')?></div></div>
                                <input type="text" name="" readonly value="13982242710" class="nl-input nl-foucs">
                                <a class="form-input-right c_blue clear"><div><?=__('解除关联', 'nlyd-student')?></div></a>
                            </div>
                            <div class="fs_12 ta_c mt_10">
                                <?=__('*管理员必须是使用手机号在本平台注册并已完成实名认证的用户', 'nlyd-student')?>
                            </div>
                        </div>
               
                        <a class="a-btn a-btn-table"  href="<?=home_url('/zone/settingAdd/');?>"><div><?=__('添加关联账号', 'nlyd-student')?></div></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function($) {
        layui.use(['layer'], function(){
            $('.clear').click(function(){
                var _this=$(this);
                var admin=_this.prev('input').val()
                layer.open({
                    type: 1
                    ,maxWidth:300
                    ,title: "<?=__('提示', 'nlyd-student')?>" //不显示标题栏
                    ,skin:'nl-box-skin'
                    ,id: 'certification' //防止重复弹出
                    ,content: '<div class="box-conent-wrapper"><?=__("是否确认解除与“", "nlyd-student")?>'+admin+'<?=__("” 的管理员关系?", "nlyd-student")?>？</div>'
                    ,btn: ["<?=__('按错了', 'nlyd-student')?>","<?=__('确 认', 'nlyd-student')?>" ]
                    ,success: function(layero, index){
                    }
                    ,yes: function(index, layero){
                        layer.closeAll();
                    }
                    ,btn2: function(index, layero){
                        //按钮【按钮二】的回调
                        layer.closeAll();
                        submit(time,1)
                    }
                    ,closeBtn:2
                    ,btnAagn: 'c' //按钮居中
                    ,shade: 0.3 //遮罩
                    ,isOutAnim:true//关闭动画
                });
            })

        });

    })
</script>
