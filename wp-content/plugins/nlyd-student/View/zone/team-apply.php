<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12  detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <a class="mui-pull-left nl-goback static" href="<?=home_url('/zone/team/');?>">
                <div><i class="iconfont">&#xe610;</i></div>
            </a>
            <h1 class="mui-title">
            <div><?=__('战队申请管理', 'nlyd-student')?></div>
            </h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="teamApply_row width-padding width-padding-pc" data-id="1">
                    <div class="teamApply_row_info fs_14">
                        <span class="c_blue"><?=__('詹冬梅', 'nlyd-student')?></span>
                        <span><?=__('申请', 'nlyd-student')?></span>
                        <span class="c_blue"><?=__('加入', 'nlyd-student')?></span>
                        <span><?=__('战队', 'nlyd-student')?></span>
                    </div>
                    <div class="teamApply_row_btns fs_14 pull-right">
                        <a class="_btn c_blue mr_10" data-id="true"><?=__('同意', 'nlyd-student')?></a>
                        <a class="_btn c_black6" data-id="false"><?=__('拒绝', 'nlyd-student')?></a>
                    </div>
                </div>

                <div class="teamApply_row width-padding width-padding-pc" data-id="2">
                    <div class="teamApply_row_info fs_14">
                        <span class="c_blue"><?=__('詹冬梅', 'nlyd-student')?></span>
                        <span><?=__('申请', 'nlyd-student')?></span>
                        <span class="c_blue"><?=__('退出', 'nlyd-student')?></span>
                        <span><?=__('战队', 'nlyd-student')?></span>
                    </div>
                    <div class="teamApply_row_btns fs_14 pull-right">
                        <a class="_btn c_blue mr_10" data-id="true"><?=__('同意', 'nlyd-student')?></a>
                        <a class="_btn c_black6" data-id="false"><?=__('拒绝', 'nlyd-student')?></a>
                    </div>
                </div>
            </div>
        </div>           
    </div>
</div>

<script>
jQuery(function($) {     
    layui.use(['layer'], function(){
        $('._btn').click(function(){
            var _this=$(this);
            var isAgree=_this.attr('data-id');
            var text=_this.parents('.teamApply_row').find('.teamApply_row_info').text();
            var isAgree_text=_this.text();
            // switch  (isAgree) {
            //     case 'true':

            //     break;
            //     case 'false':
            //     break;
            // }
            layer.open({
                type: 1
                ,maxWidth:300
                ,title: "<?=__('提示', 'nlyd-student')?>" //不显示标题栏
                ,skin:'nl-box-skin'
                ,id: 'certification' //防止重复弹出
                ,content: "<div class='box-conent-wrapper'><?=__('是否', 'nlyd-student')?>"+isAgree_text+text+"？</div>"
                ,btn: [ "<?=__('按错了', 'nlyd-student')?>","<?=__('确认', 'nlyd-student')?>",]
                ,success: function(layero, index){
                },
                cancel: function(index, layero){
                    layer.closeAll();
                }
                ,yes: function(index, layero){
                    layer.closeAll();
                }
                ,btn2: function(index, layero){
                    if(!_this.hasClass('disabled')){
                        var data={
                            action:'zone_coach_relieve',
                            coach_id:$.Request('coach_id'),
                        }
                        $.ajax({
                            data: data,
                            beforeSend:function(XMLHttpRequest){
                                _this.addClass('disabled')
                            },
                            success: function(res, textStatus, jqXHR){
                                console.log(res)
                                if(res.success){
                                    if(res.data.info){
                                        $.alerts(res.data.info);
                                        setTimeout(function() {
                                            window.location.href=window.home_url+'/zone/coach/'
                                        }, 1000);
                                    }
                                    if(res.data.list){
                                        $.alerts("<?=__('当前教练下存在学员，请为学员绑定新的教练关系并进行解绑教练操作', 'nlyd-student')?>",3000)
                                        var arr=JSON.parse(res.data.list);
                                        mobileSelect4.updateWheel(0,arr);
                                        mobileSelect4.show();
                                        _this.removeClass('disabled');
                                    }
                                    
                                }else{
                                    $.alerts(res.data.info);
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
                        $.alerts("<?=__('正在删除此轮比赛，请稍后再试', 'nlyd-student')?>",1200)
                    }
                    layer.closeAll();
                }
                ,closeBtn:2
                ,btnAagn: 'c' //按钮居中
                ,shade: 0.3 //遮罩
                ,isOutAnim:true//关闭动画
            });
        })
    }) 
})
</script>