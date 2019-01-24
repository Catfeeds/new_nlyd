
<div class="layui-fluid">
    <div class="layui-row">
        <?php
        require_once leo_student_public_view.'leftMenu.php';

        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('关联账号设置', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="zone-form-tips width-padding width-padding-pc"><i class="iconfont">&#xe65b;</i> <?=__('任职人员需在平台注册并实名认证，否则审核无法通过', 'nlyd-student')?></div>
                <div class="layui-row width-padding width-padding-pc">
                    <form class="layui-form apply_form" lay-filter='layform'>
                        <input type="hidden" name="action" value="set_zone_manager">
                        <input type="hidden" name="type" value="set">
                        <div>
                            <div class="lable_row">
                                <span class="c_black"><?=__('关联账号', 'nlyd-student')?>：</span>
                            </div>
                            <div class="input_row">
                            <input class="radius_input_row nl-foucs" name="user_phone" value="" type="tel" lay-verify="phone" autocomplete="off" placeholder="<?=__('输入任职人员注册手机号查询，未注册无法选择', 'nlyd-student')?>">
                            </div>
                        </div>
                        <a class="a-btn a-btn-table" lay-filter="layform" lay-submit=""><div><?=__('添加关联账号', 'nlyd-student')?></div></a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function($) {
        layui.use(['form'], function(){
            var form = layui.form
            form.render();
            // 自定义验证规则
            form.verify($.validationLayui.allRules);
            // 监听提交
            form.on('submit(layform)', function(data){
                var _this=$(this);
                if(!_this.hasClass('disabled')){
                    $.ajax({
                        data: data.field,
                        beforeSend:function(XMLHttpRequest){
                            _this.addClass('disabled')
                        },
                        success: function(res, textStatus, jqXHR){
                            console.log(res)
                            $.alerts(res.data.info)
                            if(res.data.url){
                                setTimeout(function() {
                                     window.location.href=res.data.url
                                }, 300);
                            }
                        },
                        complete: function(jqXHR, textStatus){
                            if(textStatus=='timeout'){
                                $.alerts("<?=__('网络质量差', 'nlyd-student')?>")
                            }
                            _this.removeClass('disabled');
                        }
                    })
                }else{
                    $.alerts("<?=__('正在执行您的操作...', 'nlyd-student')?>")
                }
                return false;
            });
        });
    })
</script>
