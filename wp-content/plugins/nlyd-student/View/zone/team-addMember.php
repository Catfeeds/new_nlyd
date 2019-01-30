
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
                <div class="zone-form-tips width-padding width-padding-pc"><i class="iconfont">&#xe65b;</i> <?=__('任职人员需在平台注册并实名认证，否则审核无法通过', 'nlyd-student')?></div>
                <div class="width-padding width-padding-pc">
                    <form class="layui-form">
                        <div class="coach_add_row">
                            <div class="change_num_row">
                                <input class="radius_input_row change_num nl-foucs" value="<?=$row['user_phone']?>" type="text" lay-verify="required" autocomplete="off" placeholder="<?=__('输入任职人员注册手机号查询，未注册无法选择', 'nlyd-student')?>">
                                <a class="coach_add_btn c_blue">确认</a> 
                                <input type="hidden" name="user_phone">
                            </div>
                            <input type="hidden" name="action" value="add_team_personnel">
                            <input type="hidden" name="team_id" value="<?=$_GET['team_id']?>">
                        </div>
                        <a class="a-btn a-btn-table" lay-filter='layform' lay-submit="" ><div><?=__('添加战队成员', 'nlyd-student')?></div></a>
                    </form>
                </div>
            </div>
        </div>           
    </div>
</div>
<script>
jQuery(function($) { 
    $('body').on('click','.coach_add_btn',function(){
        var _this=$(this);
        var val=_this.prev('input').val();
        _this.next('input').val('');
        $.ajax({
            data: {
                mobile:val,
                action:'get_mobile_user',
            },
            success: function(res, textStatus, jqXHR){
                if(res.success){
                    _this.next('input').val(res.data.user_id);
                    _this.prev('input').val(res.data.user_name)
                }else{
                    $.alerts(res.data.info)
                }
            },
            complete: function(jqXHR, textStatus){
                if(textStatus=='timeout'){
                    $.alerts("<?=__('网络质量差', 'nlyd-student')?>")
                }
            }
        })
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