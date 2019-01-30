
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
                <div class="zone-form-tips width-padding width-padding-pc"><i class="iconfont">&#xe65b;</i> <?=__('任职人员需在平台注册并实名认证，否则审核无法通过', 'nlyd-student')?></div>
                <div class="width-padding layui-row width-margin-pc "  style="padding-bottom: 333px;">
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
                            </div>
                            <div class="input_row change_num_row">
                                <input class="radius_input_row change_num nl-foucs" value="<?=$user_mobile?>" type="tel" lay-verify="required" autocomplete="off" placeholder="<?=__('输入任职人员注册手机号查询，未注册无法选择', 'nlyd-student')?>">
                                <a class="coach_add_btn c_blue">确认</a> 
                                <input type="hidden" name="team_director_phone">
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
                        <?php if($is_default != 'y'):?>
                        <div>
                            <a class="c_blue team_clear"><?=__('解散并删除战队', 'nlyd-student')?></a>
                        </div>
                        <?php endif;?>
                        <a class="a-btn a-btn-table" lay-filter="layform" lay-submit=""><div><?=__('确认申请', 'nlyd-student')?></div></a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
jQuery(function($) { 
    $('body').on('change','.change_num',function(){
        var _this=$(this);
        _this.next().next('input').val('')
    })
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
    layui.use(['form','layer'], function(){
        var form = layui.form
        form.render();
        // 自定义验证规则
        form.verify($.validationLayui.allRules);
        // 监听提交
        form.on('submit(layform)', function(data){//提交
            var _this=$(this);
            if(data.field['team_director_phone']==""){
                $.alerts("<?=__('请确战队负责人', 'nlyd-student')?>")
                $('.change_num').focus().addClass('layui-form-danger')
                return false;
            }
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
        $('.team_clear').click(function(){//解散
            var _this=$(this);
            var content="<div class='box-conent-wrapper'>"
                            +"<form class='layui-form ta_l' style='width:75%;margin:auto' id='checkForm'>"
                                +"<input type='radio' name='memberGo' value='1' title='<?=__('直接移出该战队', 'nlyd-student')?>' checked>"
                                +"<input type='radio' name='memberGo' value='2' title='<?=__('转移队员至赛区默认战队', 'nlyd-student')?>'>"
                            +"</form>"
                        +"</div>"
            layer.open({
                type: 1
                ,maxWidth:300
                ,title: "<?=__('选择该战队队员操作', 'nlyd-student')?>" //不显示标题栏
                ,skin:'nl-box-skin'
                ,id: 'certification' //防止重复弹出
                ,content: content
                ,btn: [ "<?=__('按错了', 'nlyd-student')?>","<?=__('确认', 'nlyd-student')?>",]
                ,success: function(layero, index){
                    form.render('radio'); //刷新select选择框渲染
                },
                cancel: function(index, layero){
                    
                    layer.closeAll();
                }
                ,yes: function(index, layero){
                    layer.closeAll();
                }
                ,btn2: function(index, layero){
                    if(!_this.hasClass('disabled')){
                        var memberGo=$('#checkForm').serializeObject().memberGo;
                        console.log(memberGo)
                        var data={
                            action:'team_disband',
                            type:memberGo,
                            team_id:$.Request('team_id')
                        }
                        $.ajax({
                            data: data,
                            beforeSend:function(XMLHttpRequest){
                                _this.addClass('disabled')
                            },
                            success: function(res, textStatus, jqXHR){
                                console.log(res)
                                if(res.success){
                                    if(res.data.url){
                                        window.location.href=res.data.url
                                    }
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
                        $.alerts("<?=__('正在处理您的请求，请稍后再试', 'nlyd-student')?>",1200)
                    }
                    layer.closeAll();
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
