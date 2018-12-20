
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
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('战队名称', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <input class="radius_input_row nl-foucs" type="text" lay-verify="required" value="战队名称" placeholder="<?=__('填写战队名称', 'nlyd-student')?>">
                            </div>
                        </div>
                        <div>
                            <div class="lable_row">
                                <span class="c_black"><?=__('战队负责人', 'nlyd-student')?>：</span>
                            </div>
                            <div class="input_row">
                                <input class="radius_input_row nl-foucs" type="text" disabled>
                            </div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('战队口号', 'nlyd-student')?>：</span></div>
                            <div class="input_row"><input class="radius_input_row nl-foucs" type="text" name="zone_address" lay-verify="required" autocomplete="off" placeholder="<?=__('填写战队口号', 'nlyd-student')?>" value="<?=!empty($row) ? $row['zone_address'] :''?>"></div>
                        </div>
                        <div>
                            <div class="lable_row"><span class="c_black"><?=__('战队简介', 'nlyd-student')?>：</span></div>
                            <div class="input_row">
                                <textarea class="radius_input_row nl-foucs" type="text" placeholder="<?=__('填写战队简介', 'nlyd-student')?>"></textarea>
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
    layui.use(['form'], function(){
        var form = layui.form
        form.render();
        // 自定义验证规则
        form.verify($.validationLayui.allRules);
        // 监听提交
        form.on('submit(layform)', function(data){//提交
            var _this=$(this);
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

                    }
                },
                complete: function(jqXHR, textStatus){
                    if(textStatus=='timeout'){
                        $.alerts("<?=__('网络质量差', 'nlyd-student')?>")
            　　　　}
                    _this.removeClass('disabled');
                }
            })
            return false;
        });
      
    });

})
</script>
