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
            <div class="layui-row nl-border nl-content flow-default" id="teamApply_flow">
               
            </div>
        </div>           
    </div>
</div>

<script>
jQuery(function($) {     
    var _map=$.Request('map');
    layui.use(['layer','element','flow'], function(){
        var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
        var flow = layui.flow;//流加载
        $('body').on('click','._btn',function(){//同意，拒绝
            var _this=$(this);
            var _that=_this.parent('.teamApply_row_btns')
            var _id=_this.attr('data-id');
            var text=_this.parents('.teamApply_row').find('.teamApply_row_info').html();
            var isAgree_text=_this.text();
            var map=_this.hasClass('yes') ? 'y' : 'n';
            var status=_this.attr('data-status');
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
                    if(!_that.hasClass('disabled')){
                        var data={
                            action:'team_personnel_operation',
                            // map:map,
                            id:_id,
                            status:status,
                        }
                        $.ajax({
                            data: data,
                            beforeSend:function(XMLHttpRequest){
                                _that.addClass('disabled')
                            },
                            success: function(res, textStatus, jqXHR){
                                console.log(res)
                                // _this.parents('.teamApply_row').remove()
                                _that.removeClass('disabled');
                            },
                            complete: function(jqXHR, textStatus){
                                if(textStatus=='timeout'){
                                    $.alerts("<?=__('网络质量差', 'nlyd-student')?>")
                                    _that.removeClass('disabled');
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
//---------------------------------分页------------------------------------------
        function pagation(id,team_page){
            flow.load({
                elem: '#'+id //流加载容器
                ,isAuto: false
                ,isLazyimg: true
                ,done: function(page, next){ //加载下一页
                    var postData={
                        action:'get_team_personnel',
                        page:team_page,
                        map:_map,
                    }
                    var lis = [];
                    $.ajax({
                        data: postData,
                        success:function(res,ajaxStatu,xhr){
                            console.log(res)
                            team_page++
                            if(res.success){
                                $.each(res.data.info,function(i,v){
                                    var dom=  '<div class="teamApply_row width-padding width-padding-pc">'+
                                                '<div class="teamApply_row_info fs_14">'+
                                                    '<span class="c_blue">'+v.real_name+'</span><span><?=__("申请", "nlyd-student")?></span><span class="c_blue">'+v.status_cn+'</span><span><?=__("战队", "nlyd-student")?></span>'+
                                                '</div>'+
                                                '<div class="teamApply_row_btns fs_14 pull-right">'+
                                                    '<a class="_btn yes c_blue mr_10" data-id="'+v.id+'" data-status="'+v.status+'"><?=__("同意", "nlyd-student")?></a>'+
                                                    '<a class="_btn no c_black6" data-id="'+v.id+'" data-status="'+v.status+'"><?=__("拒绝", "nlyd-student")?></a>'+
                                                '</div>'+
                                            '</div>'
                                    lis.push(dom) 
                                })
                                if (res.data.info.length<50) {
                                    next(lis.join(''),false) 
                                }else{
                                    next(lis.join(''),true) 
                                }
                                
                            }else{
                                next(lis.join(''),false)
                            }
                        },
                        complete:function(XMLHttpRequest, textStatus){
							if(textStatus=='timeout'){
								$.alerts("<?=__('网络质量差,请重试', 'nlyd-student')?>")
								next(lis.join(''),true)
							}
                        }
                    })       
                }
            });
        }
        pagation('teamApply_flow',1)
    }) 
})
</script>