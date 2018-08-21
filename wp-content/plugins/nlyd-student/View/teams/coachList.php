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
                        <div class="layui-tab-content">
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
    $('body').on('click','.coach-btn',function(){//申请当我教练
        var _this=$(this);
        var coach_id=_this.attr('data-coachId');
        var category_id=_this.attr('data-categoryId');
            layer.open({
            type: 1
            ,maxWidth:300
            ,title: '提示' //不显示标题栏
            ,skin:'nl-box-skin'
            ,id: 'certification' //防止重复弹出
            ,content: '<div class="box-conent-wrapper">是否请TA当教练？</div>'
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
                       _this.css('display','none');
                       _this.parents('.coach-detail-footer').find('.coach-type').css('display','inline-block')
                      var setMainCoach='<span class="fs_12 c_black6 c_orange">等待教练审核</span>'
                      _this.parents('.coach-detail-footer').prev('.coach-detail-row').html(setMainCoach)                
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
            layer.open({
            type: 1
            ,maxWidth:300
            ,title: '提示' //不显示标题栏
            ,skin:'nl-box-skin'
            ,id: 'certification' //防止重复弹出
            ,content: '<div class="box-conent-wrapper">是否立即更换主训教练？</div>'
            ,btn: ['再想想', '确认', ]
            ,success: function(layero, index){
                
            }
            ,yes: function(index, layero){
                layer.closeAll();
            }
            ,btn2: function(index, layero){
                var match_id = <?=!empty($_GET['match_id']) ? $_GET['match_id'] : "''"?>;
                /*var match_id=''
                if($.Request('match_id')!=null){
                    match_id=$.Request('match_id')
                }*/
                var postData={
                    action:'set_major_coach',
                    _wpnonce:$('#setMain').val(),
                    coach_id:coach_id,
                    match_id:match_id,
                    category_id:category_id,
                }
                $.post(window.admin_ajax+"?date="+new Date().getTime(),postData,function(res){
                    if(res.success){
                        if(res.data.url.length>0){
                            window.location.href=res.data.url
                        }
                        $('.setCoach').css('display','inline');
                        $('.coach-row .nl-badge').html('<i class="iconfont">&#xe608;</i>')
                        _this.css('display','none').parents('.coach-row').find('.nl-badge').html('主');
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
                        if(res.success){
                            $.each(res.data.info,function(i,v){
                                var major_coach='';//主训教练，我的教练
                                var isMyCoach='';//不是我的教练
                                var memoryClass='';
                                var computeClass='';
                                var readClass='';
                                var setMainCoach='';//设为主训教练
                                if(v.compute!=null || v.read!=null || v.memory!=null){
                                    if(v.compute!=null){
                                        computeClass='blue'
                                    }
                                    if(v.memory!=null){
                                        memoryClass='blue'
                                    }
                                    if(v.read!=null){
                                        readClass='blue'
                                    } 

                                }
                                if(v.apply_status!=null){//1，申请中，2我的教练
                                    if(v.apply_status==1){//1，申请中，2我的教练
                                        setMainCoach='<span class="fs_12 c_black6 c_orange">等待教练审核</span>'
                                    }else if(v.apply_status==2){//1，申请中，2我的教练
                                        $.each(arr,function(index,value){//橘色高亮
                                            if(value.ID==$('.layui-this').attr('data-id')){
                                                if(value.post_title=='速算类'){
                                                    computeClass='orange';
                                                }else if(value.post_title=='速记类'){
                                                    memoryClass='orange';
                                                }else if(value.post_title=='速读类'){
                                                    readClass='orange';
                                                }
                                            }
                                        })
                                        if(v.my_major_coach=='y'){//主训教练
                                            major_coach='<div class="nl-badge">主</div>';
                                            setMainCoach='<span class="fs_12 c_black6 c_blue setCoach" data-coachId="'+v.coach_id+'" data-categoryId="'+v.category_id+'" style="display:none;">设为主训教练</span>';
                                        }else{
                                            major_coach='<div class="nl-badge"><i class="iconfont">&#xe608;</i></div>';
                                            setMainCoach='<span class="fs_12 c_black6 c_blue setCoach" data-coachId="'+v.coach_id+'" data-categoryId="'+v.category_id+'">设为主训教练</span>'
                                        }
                                    }

                                    isMyCoach='<div class="coach-type '+readClass+'">速读类</div>'
                                              +'<div class="coach-type '+memoryClass+'">速记类</div>'
                                              +'<div class="coach-type '+computeClass+'">速算类</div>'
                                }else{
                                    isMyCoach='<div class="coach-type '+readClass+'" style="display:none;">速读类</div>'
                                              +'<div class="coach-type '+memoryClass+'" style="display:none;">记忆类</div>'
                                              +'<div class="coach-type '+computeClass+'" style="display:none;">速算类</div>'
                                              +'<div class="coach-btn" data-coachId="'+v.coach_id+'" data-categoryId="'+v.category_id+'">请TA当教练</div>';//不是我的教练
                                }
                                var dom='<li class="layui-col-lg4 layui-col-md4 layui-col-sm12 layui-col-xs12">'
                                            +'<a class="coach-row" href="'+v.coach_url+'">'
                                                +'<div class="coach-picture img-box">'
                                                    +'<img src="'+v.user_head+'">'
                                                +'</div>'
                                                +'<div class="coach-detail">'
                                                    +'<div class="coach-detail-row">'
                                                        +'<span class="fs_18 c_blue">'+v.display_name+'</span>'
                                                        +'<span class="fs_12 c_black6">'+v.user_gender+'</span>'
                                                        +'<span class="fs_12 c_black6">ID '+v.user_ID+'</span>'
                                                        +major_coach
                                                    +'</div>'
                                                    +'<div class="coach-detail-row">'
                                                        +'<span class="fs_12 c_black6">国际脑力运动委员会（IISC）</span>'
                                                    +'</div>'
                                                    +'<div class="coach-detail-row">'
                                                        +'<span class="fs_12 c_black6">'+v.user_coach_level+'</span>'
                                                    +'</div>'
                                                    +'<div class="coach-detail-row">'
                                                        +setMainCoach
                                                    +'</div>'
                                                    +'<div class="coach-detail-footer">'
                                                        +isMyCoach
                                                    +'</div>'
                                                +'</div>'
                                            +'</a>'
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
                                var dom='<div class="no-info">无教练信息</div>'
                                if(flag.length>0){
                                    var text=$('.layui-this').text();
                                    dom+='<a class="a-btn" href="<?=home_url('/teams/coachList/category_id/'.$_GET['category_id']);?>">设置我的'+text+'教练</a>'
                                }
                                lis.push(dom) 
                            }else{
                                $.alerts('没有更多了')
                            }
                            next(lis.join(''),false)
                        }
            })       
        }
    });
});
 //--------------------分页-------------------------- 
})
</script>