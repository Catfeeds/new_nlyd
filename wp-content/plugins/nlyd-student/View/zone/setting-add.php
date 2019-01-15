
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
                <div class="layui-row width-padding width-padding-pc">
                    <form class="layui-form apply_form" lay-filter='layform'>
                        <!-- 不存在关联账号 -->
                        <div>
                            <div class="lable_row">
                                <span class="c_black"><?=__('关联账号', 'nlyd-student')?>：</span>
                                <span class="c_red fs_12"><?=__('任职人员需在平台注册并实名认证，否则审核无法通过', 'nlyd-student')?></span>
                            </div>
                            <div class="input_row">
                            <input class="radius_input_row nl-foucs" name="user_phone" value="<?=$row['user_phone']?>" type="tel" lay-verify="phone" autocomplete="off" placeholder="<?=__('输入用户注册手机号码查询，未注册无法选择', 'nlyd-student')?>">
                                <!-- <select class="js-data-select-ajax" name="chairman_id" style="width: 100%" data-action="get_manage_user" data-placeholder="<?=__('输入用户注册手机号码查询，未注册无法选择', 'nlyd-student')?>" >
                                    <option value="<?=$row['chairman_id']?>" selected><?=$row['chairman_name']?></option>
                                </select> -->
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
        // $('.js-data-select-ajax').each(function () {
        //     var _this=$(this);
        //     var _placeholder = _this.attr('data-placeholder');
        //     _this.select2({
        //         placeholder : _placeholder,
        //         allowClear:true,
        //         ajax: {
        //             url: admin_ajax +'?action=get_manage_user'  ,
        //             dataType: 'json',
        //             delay: 600, //wait 250 milliseconds before triggering the request
        //             processResults: function (res) {
        //                 return {
        //                     results: res.data
        //                 };
        //             }
        //         }
        //     });
        // })
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
                        data: data,
                        beforeSend:function(XMLHttpRequest){
                            _this.addClass('disabled')
                        },
                        success: function(res, textStatus, jqXHR){
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
                }
                return false;
            });
        });
    })
</script>
