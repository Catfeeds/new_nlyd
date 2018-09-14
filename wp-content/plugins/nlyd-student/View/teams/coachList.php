<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <?php
            if(isset($_GET['match_id'])){
                $next_url = home_url('/teams/coachList/category_id/'.$category_id.'/match_id/'.$_GET['match_id'].'/back/1');
                if(isset($_GET['back'])){
                    $back_u = home_url('teams/myCoach/match_id/'.intval($_GET['match_id']));
                }else{
                    $back_u = home_url('matchs/confirm/match_id/'.intval($_GET['match_id']));
                }
            }elseif(isset($_GET['directory'])){
                $next_url = home_url('/teams/coachList/category_id/'.$category_id.'/directory/1');
                $back_u = home_url('directory/');
            }else{
                $next_url = home_url('/teams/coachList/category_id/'.$category_id.'/back/1');
                if(isset($_GET['back'])){
                    $back_u = home_url('teams/myCoach');
                }else{
                    $back_u = home_url('account');
                }
            }
            echo '<a class="mui-pull-left nl-goback static" href="'.$back_u.'">';


           ?>
        <i class="iconfont">&#xe610;</i>
        </a>
        <h1 class="mui-title"><?=$action == 'myCoach' ? '我的教练' :'教练列表';?></h1>
        </header>
            <div class="layui-row nl-border nl-content layui-bg-white">
                <?php if($coachCount > 0){?>
                <div class="swiper-container layui-bg-white">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="swiper-content img-box"><img src="<?=student_css_url.'image/homePage/ad1.png'?>"></div>
                        </div>
                        <div class="swiper-slide">
                            <div class="swiper-content img-box"><img src="<?=student_css_url.'image/homePage/ad2.png'?>"></div>
                        </div>
                        <div class="swiper-slide">
                            <div class="swiper-content img-box"><img src="<?=student_css_url.'image/homePage/ad3.png'?>"></div>
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
                            if(isset($_GET['directory'])) $url .= '/directory/1';
                            if(isset($_GET['back'])){
                                $url .= '/back/1';
                            }
                        ?>
                        <ul style="margin-left: 0" class="layui-tab-title">
                            <?php foreach ($category as $k => $val){ ?>
                                <li data-id="<?=$val['ID']?>" class="<?=$val['ID'] == $_GET['category_id'] || (!isset($_GET['category_id']) && $k==0) ? 'layui-this' : '';?>">
                                    <!-- <a href="<?=$url.'/category_id/'.$val['ID']?>" ><?=$val['post_title']?></a> -->
                                    <?=$val['post_title']?>
                                </li>
                                <?php if($k==0){ ?>
                                    <div class="nl-transform"><?=$val['post_title']?></div>
                                <?php } ?>
                            <?php } ?>
                           
                        </ul>
                        <?php endif;?>
                        <input type="hidden" name="user_id" value="<?=$action=='myCoach'?$user_id:'';?>">
                        <div class="layui-tab-content" style="padding:0">
                            <?php foreach ($category as $k => $val){ ?>
                                <div data-id="<?=$val['ID']?>" class="layui-tab-item <?=$val['ID'] == $_GET['category_id'] || (!isset($_GET['category_id']) && $k==0) ? 'layui-show' : '';?>">
                                    <ul class="flow-default layui-row layui-col-space20" id="<?=$val['ID']?>" style="margin:0">
                                    
                                    </ul>
                                </div>
                            <?php } ?>
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
<!-- 判断是否存在主训 -->
<input type="hidden" name="_wpnonce" id="isMajor" value="<?=wp_create_nonce('student_current_coach_code_nonce');?>">
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
$('body').on('click','.layui-form-checkbox',function(){
    var _this=$(this);
    _this.toggleClass('layui-form-checked')
    _this.prev('input').click()
})
layui.use(['element','flow','layer','form'], function(){
    var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
    var flow = layui.flow;//流加载

    $('body').on('click','.setTeacher',function(){//申请当我教练
        var _this=$(this);
        if(!_this.hasClass('disabled')){
            _this.addClass('disabled')
            var coach_id=_this.attr('data-coachId');
            var category_id=_this.attr('data-categoryId');
            var coach_name=_this.attr('data-coachName')
            var type=$('.layui-this a').text()
            var ajax_data={
                action:'searchCurrentCoach',
                _wpnonce:$('#isMajor').val(),
                category_id:category_id
            }
            $.ajax({
                data:ajax_data,success(resp,ajaxStatu,xhr){  
                    var hasMajor= resp.success ? true : false;
                    var content=""
                    if(hasMajor){
                        content='<div class="box-conent-wrapper">是否确认向“'+coach_name+'”发送教练申请？</div>'
                                +'<div style="text-align:center" class="fs_12 c_orange"><input type="checkbox" class="coachCheckbox" lay-skin="primary"><div class="layui-unselect layui-form-checkbox" lay-skin="primary"><i class="layui-icon layui-icon-ok"></i></div> 同时设为'+type+'主训教练</div>'
                    }else{
                        content='<div class="box-conent-wrapper">是否确认向“'+coach_name+'”发送教练申请？</div>'
                    }
                        layer.open({
                        type: 1
                        ,maxWidth:300
                        ,title: '教练申请' //不显示标题栏
                        ,skin:'nl-box-skin'
                        ,id: 'certification' //防止重复弹出
                        ,content:content
                        ,btn: ['再想想', '确认', ]
                        ,cancel:function(){
                            _this.removeClass('disabled')
                        }
                        ,success: function(layero, index){
                            
                        }
                        ,yes: function(index, layero){
                            layer.closeAll();
                            _this.removeClass('disabled')
                        }
                        ,btn2: function(index, layero){
                            var major=0;
                            if($('.coachCheckbox').attr('checked')){
                                major=1;
                            }
                            var postData={
                                action:'set_coach',
                                _wpnonce:$('#setCoach').val(),
                                category_id:category_id,
                                coach_id:coach_id,
                                major:major,
                            }
                            $.ajax({
                                data:postData,success(res,ajaxStatu,xhr){  
                                    $.alerts(res.data.info)
                                    if(res.success){
                                        _this.removeClass('setTeacher').addClass('bg_gradient_grey').text('教练审核中···');
                                        _this.parents('.coach-row').find('.coach-type').each(function(){
                                            var __this=$(this);
                                            var data_id=__this.attr('data-id')
                                            if(category_id==data_id){
                                                __this.html('<span style="color:#FF2300">审核中···</span>')
                                            }
                                        })
                                    }
                                    _this.removeClass('disabled')
                                }
                            })
                        }
                        ,closeBtn:2
                        ,btnAagn: 'c' //按钮居中
                        ,shade: 0.3 //遮罩
                        ,isOutAnim:true//关闭动画
                    });
                }
            });
        }
        return false
    })
    $('body').on('click','.setCoach',function(){//设为主训
        var _this=$(this);
        if(!_this.hasClass('disabled')){
            _this.addClass('disabled')
            var coach_id=_this.attr('data-coachId');
            var category_id=_this.attr('data-categoryId');
            var coach_name=_this.attr('data-coachName')
            var type=$('.layui-this a').text()
                layer.open({
                type: 1
                ,maxWidth:300
                ,title: '设置主训教练' //不显示标题栏
                ,skin:'nl-box-skin'
                ,closeBtn:0
                ,id: 'certification' //防止重复弹出
                ,content: '<div class="box-conent-wrapper">是否确认设置“'+coach_name+'”为'+type+'主训教练？</div>'
                ,btn: ['再想想', '确认', ]
                ,cancel:function(){
                    _this.removeClass('disabled')
                }
                ,success: function(layero, index){
                }
                ,yes: function(index, layero){
                    layer.closeAll();
                    _this.removeClass('disabled')
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
                    $.ajax({
                        data:postData,success(res,ajaxStatu,xhr){  
                            if(res.success){
                                $.alerts(res.data.info)
                                if(res.data.url&&res.data.url.length>0){
                                    setTimeout(function() {
                                        window.location.href=res.data.url
                                    }, 1600);
                                    
                                }else{
                                    _this.parents('.coach-row-footer').find('.left_c .clearCoach').text('解除主训关系').removeClass('clearCoach').addClass('clearMain')
                                    _this.parents('.coach-row').find('.coach-type').each(function(){
                                        var __this=$(this);
                                        var data_id=__this.attr('data-id')
                                        if(category_id==data_id){
                                            __this.removeClass('c_blue').addClass('c_orange')
                                            __this.children('.nl-badge').removeClass('bg_gradient_blue').addClass('bg_gradient_orange')
                                        }
                                    })
                                    _this.parents('.right_c').remove()
                                }
                                _this.removeClass('disabled')
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
                                        ,cancel:function(){
                                            _this.removeClass('disabled')
                                        }
                                        ,success: function(layero, index){
                                            
                                        }
                                        ,yes: function(index, layero){
                                            layer.closeAll();
                                            _this.removeClass('disabled')
                                        }
                                        ,btn2: function(index, layero){
                                            var replaceData={
                                                action:'replaceMajorCoach',
                                                _wpnonce:$('#replaceMain').val(),
                                                coach_id:coach_id,
                                                category_id:category_id,
                                            }
                                            $.ajax({
                                                data:replaceData,success(response,ajaxStatu,xhr){  
                                                    $.alerts(response.data.info)
                                                    if(response.success){
                                                        var majorDom=$('.coach-type.c_orange')
                                                        if(majorDom.length>0){//列表中存在主训教练
                                                            majorDom.each(function(){
                                                                var __this=$(this);
                                                                var data_id=__this.attr('data-id')
                                                                if(category_id==data_id){
                                                                    var this_coach_name=__this.parents('.coach-row').find('.left_c .ta_l').attr('data-coachName')
                                                                    var this_coach_id=__this.parents('.coach-row').find('.left_c .ta_l').attr('data-coachId')
                                                                    var this_category_id=__this.parents('.coach-row').find('.left_c .ta_l').attr('data-categoryId')
                                                                    var coach_btn='<div class="right_c"><a class="coach-btn text_1  bg_gradient_orange setCoach c_blue" data-coachName="'+this_coach_name+'" data-coachId="'+this_coach_id+'" data-categoryId="'+this_category_id+'">设为主训教练</a></div>';
                                                                    __this.removeClass('c_orange').addClass('c_blue')
                                                                    __this.children('.nl-badge').removeClass('bg_gradient_orange').addClass('bg_gradient_blue')
                                                                    __this.parents('.coach-row').find('.left_c .ta_l').text('解除教学关系').removeClass('clearMain').addClass('clearCoach')
                                                                    __this.parents('.coach-row').find('.coach-row-footer').append(coach_btn)     
                                                                }
                                                            })

                                                        }
                                                        _this.parents('.coach-row-footer').find('.left_c .ta_l').text('解除主训关系').removeClass('clearCoach').addClass('clearMain')
                                                        _this.parents('.coach-row').find('.coach-type').each(function(){
                                                            var __this=$(this)
                                                            var data_id=__this.attr('data-id')
                                                            if(category_id==data_id){
                                                                __this.removeClass('c_blue').addClass('c_orange')
                                                                __this.children('.nl-badge').removeClass('bg_gradient_blue').addClass('bg_gradient_orange')
                                                            }
                                                        })
                                                        _this.parents('.right_c').remove()
                                                    }
                                                    _this.removeClass('disabled')
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
                        }
                    })
                }
                ,closeBtn:2
                ,btnAagn: 'c' //按钮居中
                ,shade: 0.3 //遮罩
                ,isOutAnim:true//关闭动画
            });
        }
            return false
    })

    $('body').on('click','.clearMain',function(){//解除主训
        var _this=$(this);
        if(!_this.hasClass('disabled')){
            _this.addClass('disabled')
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
                ,cancel:function(){
                    _this.removeClass('disabled')
                }
                ,success: function(layero, index){
                    
                }
                ,yes: function(index, layero){
                    layer.closeAll();
                    _this.removeClass('disabled')
                }
                ,btn2: function(index, layero){
                    var postData={
                        action:'set_major_coach',
                        _wpnonce:$('#setMain').val(),
                        coach_id:coach_id,
                        category_id:category_id,
                    }
                    $.ajax({
                        data:postData,success(res,ajaxStatu,xhr){  
                            $.alerts(res.data.info)
                            if(res.success){
                                if(res.data.url.length>0){
                                    window.location.href=res.data.url
                                }else{
                                    var coach_btn='<div class="right_c"><a class="coach-btn text_1  bg_gradient_orange setCoach c_white" data-coachName="'+coach_name+'" data-coachId="'+coach_id+'" data-categoryId="'+category_id+'">设为主训教练</a></div>';
                                    _this.text('解除教学关系').removeClass('clearMain').addClass('clearCoach')
                                    _this.parents('.coach-row').find('.coach-row-footer').append(coach_btn)
                                    _this.parents('.coach-row').find('.coach-type').each(function(){
                                        var __this=$(this);
                                        var data_id=__this.attr('data-id')
                                        if(category_id==data_id){
                                            __this.removeClass('c_orange').addClass('c_blue')
                                            __this.children('.nl-badge').removeClass('bg_gradient_orange').addClass('bg_gradient_blue')
                                        }
                                    })
                                }
                            }
                            _this.removeClass('disabled')
                        }
                    })
                }
                ,closeBtn:2
                ,btnAagn: 'c' //按钮居中
                ,shade: 0.3 //遮罩
                ,isOutAnim:true//关闭动画
            });
            return false
        }
    })
    $('body').on('click','.clearCoach',function(){//解除教学关系
        var _this=$(this);
        if(!_this.hasClass('disabled')){
            _this.addClass('disabled')
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
                ,cancel:function(){
                    _this.removeClass('disabled')
                }
                ,success: function(layero, index){
                    
                }
                ,yes: function(index, layero){
                    layer.closeAll();
                    _this.removeClass('disabled')
                }
                ,btn2: function(index, layero){
                    var postData={
                        action:'relieveMyCoach',
                        _wpnonce:$('#clearCoach').val(),
                        coach_id:coach_id,
                        category_id:category_id,
                    }
                    $.post(window.admin_ajax+"?date="+new Date().getTime(),postData,function(res){
                        $.alerts(res.data.info)
                        if(res.success){
                            _this.parents('.coach-row').find('.nl-badge.bg_gradient_blue').remove()
                            _this.parents('.coach-row').find('.coach-btn').removeClass('bg_gradient_orange').removeClass('setCoach').addClass('bg_gradient_blue').addClass('setTeacher').text('请TA当教练')
                            _this.remove()
                            _this.parents('.coach-row').find('.coach-type').each(function(){
                                var __this=$(this);
                                var data_id=__this.attr('data-id')
                                if(category_id==data_id){
                                    __this.children('.nl-badge').remove()
                                }
                            })
                        }
                        _this.removeClass('disabled')
                    })
                }
                ,closeBtn:2
                ,btnAagn: 'c' //按钮居中
                ,shade: 0.3 //遮罩
                ,isOutAnim:true//关闭动画
            });
            return false
        }
    })
    function pagation(category_id){
 //--------------------分页--------------------------
        flow.load({
            elem: '#'+category_id //流加载容器
            ,isAuto: false
            ,isLazyimg: true
            ,done: function(page, next){//加载下一页
                //模拟插入
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
                        isClick[category_id]=true
                            if(res.success){
                                $.each(res.data.info,function(i,v){
                                    var detailFooter="";
                                    var coach_btn="";
                                    var clear_btn="";
                                    var isLeft='ta_r';
                                    var post_title="";
                                    $.each(v.category,function(index,value){
                                        var is_current="";//当前教练橘色或蓝色类型判断
                                        var metal="";//我的教练主训教练展示的标签
                                        if(value.is_current=="true"){//教练属于当前类型教练
                                        
                                            is_current="c_blue"
                                            if(value.is_my_major=="true"){//当前教练是主训教练
                                                is_current='c_orange'
                                                metal='<div class="nl-badge bg_gradient_orange"><i class="iconfont">&#xe608;</i></div>';
                                            }else{
                                                if(value.is_my_coach=="true"){//当前教练是我的教练
                                                    metal='<div class="nl-badge bg_gradient_blue"><i class="iconfont">&#xe608;</i></div>';
                                                }
                                            }
                                        }
                                        post_title=value.post_title
                                        if(v.apply_status==1){//1，申请中
                                            if(value.category_id==category_id){//当前类目教练
                                                post_title='<span style="color:#FF2300">审核中···</span>'
                                            }   
                                        }
                                        var categoryBtnDom='<div data-id="'+value.category_id+'" class="coach-type text_1 '+is_current+'">'+metal+' '+post_title+'</div>'
                                        detailFooter+=categoryBtnDom
                                    }) 
                                    if(v.apply_status!=null){//-1,拒绝1，申请中，2我的教练，3,取消
                                        if(v.apply_status==1){//1，申请中，2我的教练
                                            coach_btn='<div class="right_c"><div class="coach-btn bg_gradient_grey text_1 ">教练审核中···</div></div>';
                                            isLeft="ta_l"
                                        }else if(v.apply_status==2){//1，申请中，2我的教练
                                            if(v.my_major_coach=='y'){//主训教练
                                                clear_btn='<a class="clearMain text_1 ta_l c_black6"  data-coachName="'+v.display_name+'" data-coachId="'+v.coach_id+'" data-categoryId="'+v.category_id+'">解除主训关系</a>'
                                            }else{
                                                coach_btn='<div class="right_c"><a class="coach-btn text_1  bg_gradient_orange setCoach c_white" data-coachName="'+v.display_name+'" data-coachId="'+v.coach_id+'" data-categoryId="'+v.category_id+'">设为主训教练</a></div>';
                                                clear_btn='<a class="clearCoach text_1 ta_l c_black6" data-coachName="'+v.display_name+'"  data-coachId="'+v.coach_id+'" data-categoryId="'+v.category_id+'">解除教学关系</a>'
                                            }
                                        }else{
                                            coach_btn='<div class="right_c"><a class="coach-btn bg_gradient_blue text_1 setTeacher c_white" data-coachName="'+v.display_name+'" data-coachId="'+v.coach_id+'" data-categoryId="'+v.category_id+'">请TA当教练</a></div>';//不是我的教练
                                            isLeft="ta_l"
                                        }
                                    }else{
                                        coach_btn='<div class="right_c"><a class="coach-btn bg_gradient_blue text_1 setTeacher c_white" data-coachName="'+v.display_name+'" data-coachId="'+v.coach_id+'" data-categoryId="'+v.category_id+'">请TA当教练</a></div>';//不是我的教练
                                        isLeft="ta_l"
                                    }
                                    var dom='<li class="layui-col-lg4 layui-col-md12 layui-col-sm12 layui-col-xs12">'
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
                                                            +'<div class="text_3">'
                                                                +'<span class="c_black6">国际脑力运动委员会（IISC） '+v.user_coach_level+'</span>'
                                                            +'</div>'
                                                            +'<div class="coach-detail-footer">'
                                                                +detailFooter
                                                            +'</div>'
                                                        +'</div>'
                                                    +'</div>'
                                                    +'<div class="coach-row-footer">'
                                                        +'<div class="left_c">'
                                                            +clear_btn
                                                            +'<a class="c_black6 text_1 '+isLeft+'"  href="'+v.coach_url+'/category_id/<?=$_GET['category_id']?>">查看详情</a>'
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
                                    if(flag.length>0){
                                        var text=$('.layui-this').text();
                                        var dom='<a class="a-btn" href="<?=$next_url?>">设置我的'+text+'教练</a>'
                                    }
                                    lis.push(dom) 
                                }
                                next(lis.join(''),false)
                            }
                })       
            }
        });
    }
    var isClick={}
    pagation($('.layui-this').attr('data-id'))
    element.on('tab(tabs)', function(){//tabs
        var left=$(this).position().left+parseInt($(this).css('marginLeft'));
        var html=$(this).html();
        var id=$(this).attr('data-id')
        $('.nl-transform').css({
            'transform':'translate3d('+left+'px, 0px, 0px)'
        }).html(html)
        if(!isClick[id]){
            pagation(id)
        }
    })
});

 //--------------------分页-------------------------- 
})
</script>