
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
                <h1 class="mui-title"><div><?=__('战队管理', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="width-padding layui-row width-margin-pc  have-bottom">
                    <form class="layui-form apply_form" lay-filter='layform'>
                        <input type="hidden" name="action" value="team_apply" />
                        <input type="hidden" name="team_id" value="<?=$_GET['team_id']?>" />
                        <input type="hidden" name="type" value="<?=$_GET['type']?>" />
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('战队名称', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <input class="radius_input_row nl-foucs" type="text" lay-verify="required" name="post_title" value="<?=$post_title?>" placeholder="<?=__('填写战队名称', 'nlyd-student')?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row">
                                <span class="c_black"><?=__('战队负责人', 'nlyd-student')?>：</span>
                                <span class="c_red fs_12"><?=__('任职人员需在平台注册并实名认证，否则审核无法通过', 'nlyd-student')?></span>
                            </div>
                            <div class="input_row">
                                <input class="radius_input_row change_num" name="team_director_phone" value="<?=$row['chairman_phone']?>" type="tel" lay-verify="phone" autocomplete="off" placeholder="<?=__('输入用户注册手机号码查询，未注册无法选择', 'nlyd-student')?>">
                                <!-- <select class="js-data-select-ajax" name="team_director" style="width: 100%" data-action="get_manage_user" data-placeholder="<?=__('输入用户注册手机号码查询，未注册无法选择', 'nlyd-student')?>" >
                                    <option value="<?=$team_director?>" selected><?=$real_name?></option>
                                </select> -->
                            </div>
                        </div>
                 
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('战队口号', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="team_slogan" lay-verify="required" autocomplete="off" placeholder="<?=__('填写战队口号', 'nlyd-student')?>" value="<?=$team_slogan?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('战队简介', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <textarea class="radius_input_row nl-foucs" type="text" name="team_brief" placeholder="<?=__('填写战队简介', 'nlyd-student')?>"><?=$team_brief?></textarea>
                            </div>
                        </div>
                        <a class="a-btn a-btn-table" lay-filter="layform" lay-submit=""><div><?=__('确认申请', 'nlyd-student')?></div></a>
                    </form>
                </div>
            </div>
        </div>            
    </div>
</div>
<script>
jQuery(function($) { 
    // $('.js-data-select-ajax').each(function () {
    //         var _this=$(this);
    //         var _placeholder = _this.attr('data-placeholder');
    //         _this.select2({
    //             placeholder : _placeholder,
    //             allowClear:true,
    //             ajax: {
    //                 url: admin_ajax +'?action=get_manage_user'  ,
    //                 dataType: 'json',
    //                 delay: 600, //wait 250 milliseconds before triggering the request
    //                 processResults: function (res) {
    //                     return {
    //                         results: res.data
    //                     };
    //                 }
    //             }
    //         });
    //     })
    layui.use(['form'], function(){
        var form = layui.form
        form.render();
        // 自定义验证规则
        form.verify($.validationLayui.allRules);
        // 监听提交
        form.on('submit(layform)', function(data){//提交
            var _this=$(this);
            if(!_this.hasClass('disabled')){
                $.ajax({
                    data: data.field,
                    beforeSend:function(XMLHttpRequest){
                        _this.addClass('disabled')
                    },
                    success: function(res, textStatus, jqXHR){
                        $.alerts(res.data.info)
                        if(res.success){
                            setTimeout(function() {
                                window.location.href=window.home_url+'/zone/team/'
                            }, 1200);
                        }else{
                            _this.removeClass('disabled');
                        }
                    },
                    complete: function(jqXHR, textStatus){
                        if(textStatus=='timeout'){
                            $.alerts("<?=__('网络质量差', 'nlyd-student')?>")
                            _this.removeClass('disabled');
                　　　　}
                    }
                })
            }else{
                $.alerts("<?=__('正在处理您的请求..', 'nlyd-student')?>")
            }
            return false;
        });
      
    });

})
</script>
