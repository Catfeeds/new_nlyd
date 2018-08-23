<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <?php echo (isset($_GET['match_id']) ? '<a class="mui-pull-left nl-goback static" href="'.home_url('matchs/confirm/match_id/'.intval($_GET['match_id'])).'">' : '<a class="mui-pull-left nl-goback static" href="'.home_url('account').'">'); ?>
        <i class="iconfont">&#xe610;</i>
        </a>
        <h1 class="mui-title"><?=$action == 'myCoach' ? '我的教练' :'教练列表';?></h1>
        </header>
            <div class="layui-row nl-border nl-content layui-bg-white">
                <?php if($coachCount > 0){?>
                <div class="swiper-container layui-bg-white">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="swiper-content img-box"><img src="<?=$user_info['user_head'];?>"></div>
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
                    <div class="layui-tab layui-tab-brief width-margin  width-margin-pc" lay-filter="tabs">
                        <?php if(!empty($category)):
                            $url = home_url('/teams/'.$action);
                            //$action = isset($_GET['action'])?$_GET['action']:'index';
                            $set_url = home_url('/teams/coachList/category_id/'.$_GET['category_id']);
                            if(isset($_GET['match_id'])) $url .= '/match_id/'.$_GET['match_id'];
                            if(isset($_GET['match_id'])) $set_url .= '/match_id/'.$_GET['match_id'];
                        ?>
                        <ul style="margin-left: 0" class="layui-tab-title">
                            <?php foreach ($category as $k => $val){ ?>
                                <li data-id="<?=$val['ID']?>" class="<?=$val['ID'] == $_GET['category_id'] || (!isset($_GET['category_id']) && $k==0) ? 'layui-this' : '';?>">
                                    <a href="<?=$url.'/category_id/'.$val['ID']?>" ><?=$val['post_title']?></a>
                                </li>
                            <?php } ?>
                        </ul>
                        <?php endif;?>
                        <input type="hidden" name="user_id" value="<?=$action=='myCoach'?$user_id:'';?>">
                        <div class="layui-tab-content" style="padding:0">
                            <div class="layui-tab-item layui-show layui-row layui-col-space20 flow-default" id="flow-zoo">
                                
                            </div>
                        </div>
                    </div>
                <?php }else{ ?>
                    <div class="no-info-page">
                        <div class="no-info-img">
                            <img src="<?=student_css_url.'image/noInfo/noCoach1044@3x.png'?>">
                        </div>
                        <?php if($action == 'myCoach'){?>
                            <p class="no-info-text">您还未设置任何教练</p>
                            <?php
                                $url = home_url('/teams/coachList');
                                if(!empty($_GET['match_id']) ) $url .= '/match_id/'.$_GET['match_id'];
                            ?>
                            <a class="a-btn" href="<?=$url;?>">去设置我的教练</a>
                        <?php }else{ ?>
                            <p class="no-info-text">无教练信息</p>
                        <?php } ?>
                    </div>
                <?php } ?>                
            </div>
        </div>
    </div>
</div>
<!-- 申请当我的教练 -->
<input type="hidden" name="_wpnonce" id="setCoach" value="<?=wp_create_nonce('student_set_coach_code_nonce');?>">
<!-- 设为主训教练 -->
<input type="hidden" name="_wpnonce" id="setMain" value="<?=wp_create_nonce('student_set_major_code_nonce');?>">
<!-- 解除教练关系 -->
<input type="hidden" name="_wpnonce" id="clearCoach" value="<?=wp_create_nonce('student_relieve_coach_code_nonce');?>">
<!-- 更换主训教练 -->
<input type="hidden" name="_wpnonce" id="replaceMain" value="<?=wp_create_nonce('student_replace_major_code_nonce');?>">
<script>
jQuery(function($) { 
    var mySwiper = new Swiper('.swiper-container', {
        loop : true,
        autoplay:{
            disableOnInteraction:false
        },//可选选项，自动滑动
        autoplayDisableOnInteraction : false,    /* 注意此参数，默认为true */ 
        initialSlide :0,//初始展示页
        pagination: {
            el: '.swiper-pagination',
            dynamicBullets: true,
            dynamicMainBullets: 2,
            clickable :true,
        },
    }); 
    var arr = <?=json_encode($category)?>; 

layui.use(['element','flow','layer'], function(){
    var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
    var flow = layui.flow;//流加载
    $('body').on('click','.setTeacher',function(){//申请当我教练
        var _this=$(this);
        var coach_id=_this.attr('data-coachId');
        var category_id=_this.attr('data-categoryId');
        var coach_name=_this.attr('data-coachName')
            layer.open({
            type: 1
            ,maxWidth:300
            ,title: '教练申请' //不显示标题栏
            ,skin:'nl-box-skin'
            ,id: 'certification' //防止重复弹出
            ,content: '<div class="box-conent-wrapper">是否确认向“'+coach_name+'”发送教练申请？</div>'
            ,btn: ['再想想', '确认', ]
            ,success: function(layero, index){
                
            }
            ,yes: function(index, layero){
                layer.closeAll();
            }
            ,btn2: function(index, layero){
                var postData={
                    action:'set_coach',
                    _wpnonce:$('#setCoach').val(),
                    category_id:category_id,
                    coach_id:coach_id,
                }
                $.post(window.admin_ajax+"?date="+new Date().getTime(),postData,function(res){
                    $.alerts(res.data.info)
                    if(res.success){
                        _this.removeClass('setTeacher').addClass('bg_gradient_grey').text('教练审核中···')
                    }
                    
                })
            }
            ,closeBtn:2
            ,btnAagn: 'c' //按钮居中
            ,shade: 0.3 //遮罩
            ,isOutAnim:true//关闭动画
        });
        return false
    })
    $('body').on('click','.setCoach',function(){//设为主训
        var _this=$(this);
        var coach_id=_this.attr('data-coachId');
        var category_id=_this.attr('data-categoryId');
        var coach_name=_this.attr('data-coachName')
        var type=$('.layui-this a').text()
            layer.open({
            type: 1
            ,maxWidth:300
            ,title: '设置主训教练' //不显示标题栏
            ,skin:'nl-box-skin'
            ,id: 'certification' //防止重复弹出
            ,content: '<div class="box-conent-wrapper">是否确认设置“'+coach_name+'”为'+type+'主训教练？</div>'
            ,btn: ['再想想', '确认', ]
            ,success: function(layero, index){
                
            }
            ,yes: function(index, layero){
                layer.closeAll();
            }
            ,btn2: function(index, layero){
                var match_id = <?=!empty($_GET['match_id']) ? $_GET['match_id'] : "''"?>;
                var postData={
                    action:'set_major_coach',
                    _wpnonce:$('#setMain').val(),
                    coach_id:coach_id,
                    match_id:match_id,
                    category_id:category_id,
                }
                $.post(window.admin_ajax+"?date="+new Date().getTime(),postData,function(res){
                    if(res.success){
                        $.alerts(res.data.info)
                        if(res.data.url&&res.data.url.length>0){
                            setTimeout(() => {
                                window.location.href=res.data.url
                            }, 1600);
                            
                        }else{
                            _this.parents('.coach-row').find('.nl-badge').removeClass('bg_gradient_blue').addClass('bg_gradient_orange')
                            _this.parents('.coach-row').find('.nl-badge').parents('.coach-type').removeClass('c_blue').addClass('c_orange')
                            _this.parents('.coach-row-footer').find('.left_c .clearCoach').text('解除主训关系').removeClass('clearCoach').addClass('clearMain')
                            _this.parents('.right_c').remove()
                        }
                    }else{//存在主训教练，更改主训
                        var type=$('.layui-this a').text()
                        if(res.data.info==100){
                            layer.open({
                                 type: 1
                                ,maxWidth:300
                                ,title: '更换主训教练' //不显示标题栏
                                ,skin:'nl-box-skin'
                                ,id: 'certification' //防止重复弹出
                                ,content: '<div class="box-conent-wrapper">你已设置“'+type+'”主训教练是否确认更换？</div>'
                                ,btn: ['再想想', '确认', ]
                                ,success: function(layero, index){
                                    
                                }
                                ,yes: function(index, layero){
                                    layer.closeAll();
                                }
                                ,btn2: function(index, layero){
                                    var replaceData={
                                        action:'replaceMajorCoach',
                                        _wpnonce:$('#replaceMain').val(),
                                        coach_id:coach_id,
                                        category_id:category_id,
                                    }
                                    $.post(window.admin_ajax+"?date="+new Date().getTime(),replaceData,function(response){
                                        
                                        $.alerts(response.data.info)
                                        if(response.success){
                                            var majorDom=$('.nl-badge.bg_gradient_orange')
                                            // console.log(majorDom.length)
                                            if(majorDom.length>0){//列表中存在主训教练
                                                var this_coach_name=majorDom.parents('.coach-row').find('.left_c .ta_l').attr('data-coachName')
                                                var this_coach_id=majorDom.parents('.coach-row').find('.left_c .ta_l').attr('data-coachId')
                                                var this_category_id=majorDom.parents('.coach-row').find('.left_c .ta_l').attr('data-categoryId')
                                                var coach_btn='<div class="right_c"><div class="coach-btn text_1  bg_gradient_orange setCoach" data-coachName="'+this_coach_name+'" data-coachId="'+this_coach_id+'" data-categoryId="'+this_category_id+'">设为主训教练</div></div>';
                                                majorDom.parents('.coach-type').removeClass('c_orange').addClass('c_blue')
                                                majorDom.removeClass('bg_gradient_orange').addClass('bg_gradient_blue')
                                                majorDom.parents('.coach-row').find('.left_c .ta_l').text('解除教学关系').removeClass('clearMain').addClass('clearCoach')
                                                majorDom.parents('.coach-row').find('.coach-row-footer').append(coach_btn)
                                            }
                                            _this.parents('.coach-row').find('.nl-badge').removeClass('bg_gradient_blue').addClass('bg_gradient_orange')
                                            _this.parents('.coach-row').find('.nl-badge').parents('.coach-type').removeClass('c_blue').addClass('c_orange')
                                            _this.parents('.coach-row-footer').find('.left_c .ta_l').text('解除主训关系').removeClass('clearCoach').addClass('clearMain')
                                            _this.parents('.right_c').remove()
                                            // setTimeout(() => {
                                            //     window.location.reload()
                                            // }, 1600);
                                        }
                                    })
                                }
                                ,closeBtn:2
                                ,btnAagn: 'c' //按钮居中
                                ,shade: 0.3 //遮罩
                                ,isOutAnim:true//关闭动画
                            });
                        }else{
                            $.alerts(res.data.info)
                        }
                    }
                })
            }
            ,closeBtn:2
            ,btnAagn: 'c' //按钮居中
            ,shade: 0.3 //遮罩
            ,isOutAnim:true//关闭动画
        });
        return false
    })

    $('body').on('click','.clearMain',function(){//解除主训
        var _this=$(this);
        var coach_id=_this.attr('data-coachId');
        var category_id=_this.attr('data-categoryId');
        var coach_name=_this.attr('data-coachName')
        var type=$('.layui-this a').text()
            layer.open({
            type: 1
            ,maxWidth:300
            ,title: '解除主训教学关系' //不显示标题栏
            ,skin:'nl-box-skin'
            ,id: 'certification' //防止重复弹出
            ,content: '<div class="box-conent-wrapper">您是否确认解除与“'+coach_name+'”的主训关系？</div>'
            ,btn: ['再想想', '确认', ]
            ,success: function(layero, index){
                
            }
            ,yes: function(index, layero){
                layer.closeAll();
            }
            ,btn2: function(index, layero){
                var postData={
                    action:'set_major_coach',
                    _wpnonce:$('#setMain').val(),
                    coach_id:coach_id,
                    category_id:category_id,
                }
                $.post(window.admin_ajax+"?date="+new Date().getTime(),postData,function(res){
                    console.log(res)
                    if(res.success){
                        if(res.data.url.length>0){
                            window.location.href=res.data.url
                        }else{
                            var coach_btn='<div class="right_c"><div class="coach-btn text_1  bg_gradient_orange setCoach" data-coachName="'+coach_name+'" data-coachId="'+coach_id+'" data-categoryId="'+category_id+'">设为主训教练</div></div>';
                            _this.parents('.coach-row').find('.nl-badge.bg_gradient_orange').parents('.coach-type').removeClass('c_orange').addClass('c_blue')
                            _this.parents('.coach-row').find('.nl-badge.bg_gradient_orange').removeClass('bg_gradient_orange').addClass('bg_gradient_blue')
                            _this.text('解除教学关系').removeClass('clearMain').addClass('clearCoach')
                            _this.parents('.coach-row').find('.coach-row-footer').append(coach_btn)
                            
                        }

                    }else{
                        $.alerts(res.data.info)
                    }

                })
            }
            ,closeBtn:2
            ,btnAagn: 'c' //按钮居中
            ,shade: 0.3 //遮罩
            ,isOutAnim:true//关闭动画
        });
        return false
    })
    $('body').on('click','.clearCoach',function(){//解除教学关系
        var _this=$(this);
        var coach_id=_this.attr('data-coachId');
        var category_id=_this.attr('data-categoryId');
        var coach_name=_this.attr('data-coachName')
        var type=$('.layui-this a').text()
            layer.open({
            type: 1
            ,maxWidth:300
            ,title: '解除教学关系' //不显示标题栏
            ,skin:'nl-box-skin'
            ,id: 'certification' //防止重复弹出
            ,content: '<div class="box-conent-wrapper">您是否确认解除与“'+coach_name+'”的教学关系？</div>'
            ,btn: ['再想想', '确认', ]
            ,success: function(layero, index){
                
            }
            ,yes: function(index, layero){
                layer.closeAll();
            }
            ,btn2: function(index, layero){
                var postData={
                    action:'relieveMyCoach',
                    _wpnonce:$('#clearCoach').val(),
                    coach_id:coach_id,
                    category_id:category_id,
                }
                $.post(window.admin_ajax+"?date="+new Date().getTime(),postData,function(res){
                    if(res.success){
                        _this.parents('.coach-row').find('.nl-badge.bg_gradient_blue').remove()
                        _this.parents('.coach-row').find('.coach-btn').removeClass('bg_gradient_orange').removeClass('setCoach').addClass('bg_gradient_blue').addClass('setTeacher')
                        _this.remove()
                    }else{
                        $.alerts(res.data.info)
                    }

                })
            }
            ,closeBtn:2
            ,btnAagn: 'c' //按钮居中
            ,shade: 0.3 //遮罩
            ,isOutAnim:true//关闭动画
        });
        return false
    })
 //--------------------分页--------------------------
    flow.load({
        elem: '#flow-zoo' //流加载容器
        ,scrollElem: '#flow-zoo' //滚动条所在元素，一般不用填，此处只是演示需要。
        ,isAuto: false
        ,isLazyimg: true
        ,done: function(page, next){//加载下一页
            //模拟插入
            
                var category_id=<?=!empty($_GET['category_id']) ? $_GET['category_id'] : "''"?>;
                /*if($.Request('category_id')!=null){
                    category_id=$.Request('category_id');
                }*/
                var user_id="";
                if($('input[name="user_id"]').val().length>0){
                    user_id=$('input[name="user_id"]').val()
                }
                var postData={
                    action:'get_coach_lists',
                    category_id:category_id,
                    page:page,
                    user_id:user_id,
                }
                var lis = [];
                $.post(window.admin_ajax+"?date="+new Date().getTime(),postData,function(res){
                    console.log(res)
                        if(res.success){
                            $.each(res.data.info,function(i,v){
                                var major_coach='';//主训教练，我的教练
                                var isMyCoach='';//不是我的教练
                                var memoryClass='',computeClass='',readClass='';
                                var memory_major_coach="",compute_major_coach="",read_major_coach="";
                                var coach_btn="";
                                var text=$('.layui-this').text();
                                var clear_btn="";
                                var isLeft="ta_r";
                                if(v.compute!=null || v.read!=null || v.memory!=null){
                                    if(v.compute!=null){
                                        computeClass='c_blue'
                                    }
                                    if(v.memory!=null){
                                        memoryClass='c_blue'
                                    }
                                    if(v.read!=null){
                                        readClass='c_blue'
                                    } 

                                }
                                if(v.apply_status!=null){//-1,拒绝1，申请中，2我的教练，3,取消
                                    if(v.apply_status==1){//1，申请中，2我的教练
                                        coach_btn='<div class="right_c"><div class="coach-btn bg_gradient_grey text_1 ">教练审核中···</div></div>';
                                        isLeft="ta_l"
                                    }else if(v.apply_status==2){//1，申请中，2我的教练
                                        
                                        if(v.my_major_coach=='y'){//主训教练
                                            
                                            major_coach='<div class="nl-badge bg_gradient_orange"><i class="iconfont">&#xe608;</i></div>';
                                            clear_btn='<span class="clearMain text_1 ta_l"  data-coachName="'+v.display_name+'" data-coachId="'+v.coach_id+'" data-categoryId="'+v.category_id+'">解除主训关系</span>'
                                        }else{
                                            major_coach='<div class="nl-badge bg_gradient_blue"><i class="iconfont">&#xe608;</i></div>';
                                            coach_btn='<div class="right_c"><div class="coach-btn text_1  bg_gradient_orange setCoach" data-coachName="'+v.display_name+'" data-coachId="'+v.coach_id+'" data-categoryId="'+v.category_id+'">设为主训教练</div></div>';
                                            clear_btn='<span class="clearCoach text_1 ta_l" data-coachName="'+v.display_name+'"  data-coachId="'+v.coach_id+'" data-categoryId="'+v.category_id+'">解除教学关系</span>'
                                        }
                                        $.each(arr,function(index,value){//橘色高亮
                                            if(value.ID==$('.layui-this').attr('data-id')){

                                                if(value.post_title=='速算类'){
                                                    compute_major_coach=major_coach;
                                                    if(v.my_major_coach=='y'){
                                                        computeClass='c_orange';
                                                    }
                                                }else if(value.post_title=='速记类'){
                                                    // memoryClass='c_orange';
                                                    memory_major_coach=major_coach;
                                                    if(v.my_major_coach=='y'){
                                                        memoryClass='c_orange';
                                                    }
                                                }else if(value.post_title=='速读类'){
                                                    // readClass='c_orange';
                                                    read_major_coach=major_coach;
                                                    if(v.my_major_coach=='y'){
                                                        readClass='c_orange';
                                                    }
                                                }
                                            }
                                        })

                                    }else{
                                        coach_btn='<div class="right_c"><div class="coach-btn bg_gradient_blue text_1 setTeacher" data-coachName="'+v.display_name+'" data-coachId="'+v.coach_id+'" data-categoryId="'+v.category_id+'">请TA当教练</div></div>';//不是我的教练
                                    }

                                    isMyCoach='<div class="coach-type text_1 '+readClass+'">'+read_major_coach+' 速读类</div>'
                                              +'<div class="coach-type text_1 '+memoryClass+'">'+memory_major_coach+' 速记类</div>'
                                              +'<div class="coach-type text_1 '+computeClass+'">'+compute_major_coach+' 速算类</div>'
                                }else{
                                    isMyCoach='<div class="coach-type text_1 '+readClass+'">速读类</div>'
                                              +'<div class="coach-type text_1 '+memoryClass+'">记忆类</div>'
                                              +'<div class="coach-type text_1 '+computeClass+'">速算类</div>'
                                    coach_btn='<div class="right_c"><div class="coach-btn bg_gradient_blue text_1 setTeacher" data-coachName="'+v.display_name+'" data-coachId="'+v.coach_id+'" data-categoryId="'+v.category_id+'">请TA当教练</div></div>';//不是我的教练
                                    isLeft="ta_l"
                                }
                                var dom='<li class="layui-col-lg4 layui-col-md4 layui-col-sm12 layui-col-xs12">'
                                            +'<div class="coach-row">'
                                                +'<div class="coach-row-top">'
                                                    +'<div class="coach-picture img-box">'
                                                        +'<img src="'+v.user_head+'">'
                                                    +'</div>'
                                                    +'<div class="coach-detail">'
                                                        +'<div class="text_1">'
                                                            +'<span class="fs_16 c_blue">'+v.display_name+'</span>'
                                                            +'<span class="c_black6">'+v.user_gender+'</span>'
                                                            +'<span class="c_black6">ID '+v.user_ID+'</span>'
                                                        +'</div>'
                                                        +'<div class="text_1">'
                                                            +'<span class="c_black6">国际脑力运动委员会（IISC）</span>'
                                                        +'</div>'
                                                        +'<div class="text_1">'
                                                            +'<span class="c_black6">'+v.user_coach_level+'</span>'
                                                        +'</div>'
                                                        +'<div class="coach-detail-footer">'
                                                            +isMyCoach
                                                        +'</div>'
                                                    +'</div>'
                                                +'</div>'
                                                +'<div class="coach-row-footer">'
                                                    +'<div class="left_c">'
                                                        +clear_btn
                                                        +'<a class="c_black6 text_1 '+isLeft+'"  href="'+v.coach_url+'">查看详情</a>'
                                                    +'</div>'
                                                    +coach_btn
                                                +'</div>'
                                            +'</div>'
                                        +'</li>'
                                lis.push(dom) 
                            })
                            if (res.data.info.length<10) {
                                next(lis.join(''),false) 
                            }else{
                                next(lis.join(''),true) 
                            }
                        }else{
                            if(page==1){
                                var flag='<?=$action ?>';
                                // var dom='<div class="no-info">无教练信息</div>'
                                if(flag.length>0){
                                    var text=$('.layui-this').text();
                                    var dom='<a class="a-btn" href="<?=home_url('/teams/coachList/category_id/'.$_GET['category_id']);?>">设置我的'+text+'教练</a>'
                                }
                                lis.push(dom) 
                            }
                            // else{
                            //     $.alerts('没有更多了')
                            // }
                            next(lis.join(''),false)
                        }
            })       
        }
    });
});
 //--------------------分页-------------------------- 
})
</script>