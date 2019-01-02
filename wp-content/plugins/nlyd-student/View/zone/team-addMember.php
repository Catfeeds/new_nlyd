
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback nl-goback">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('添加战队成员', 'nlyd-student')?></div></h1>
            </header>    
            <div class="layui-row nl-border nl-content">
                <div class="width-padding width-padding-pc">
                    <form class="layui-form">
                        <div class="coach_add_row">
                            <div class="coach_add_input">
                                <select class="js-data-select-ajax" name="user_id" style="width: 100%" data-action="get_manage_user" data-placeholder="输入战队成员注册手机号/邮箱/姓名" >
                                </select>
                            </div>
                            <input type="hidden" name="action" value="add_team_personnel">
                            <input type="hidden" name="team_id" value="<?=$id?>">
                            <div class="coach_add_btn c_blue" lay-filter='layform' lay-submit="" ><?=__('确 定', 'nlyd-student')?></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>           
    </div>
</div>
<script>
jQuery(function($) { 
    $('.js-data-select-ajax').each(function () {
        var _this=$(this);
        var _placeholder = _this.attr('data-placeholder');
        _this.select2({
            placeholder : _placeholder,
            ajax: {
                url: admin_ajax +'?action=get_manage_user',
                dataType: 'json',
                delay: 600, //wait 250 milliseconds before triggering the request
                processResults: function (res) {
                    return {
                        results: res.data
                    };
                }
            }
        });
    })
    layui.use(['form'], function(){
        var form = layui.form
        form.render();
        // 自定义验证规则
        form.verify($.validationLayui.allRules);
        // 监听提交
        form.on('submit(layform)', function(data){//实名认证提交
            var _this=$(this);
            if(!_this.hasClass('disabled')){
                $.ajax({
                    data: data.field,
                    beforeSend:function(XMLHttpRequest){
                        _this.addClass('disabled')
                    },
                    success: function(res, textStatus, jqXHR){
                        $.alerts(res.data.info)
                        if(res.data.url){
                            setTimeout(function() {
                                window.location.href=res.data.url
                            }, 300);
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