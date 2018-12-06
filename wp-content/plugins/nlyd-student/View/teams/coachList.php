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
        <div><i class="iconfont">&#xe610;</i></div>
        </a>
        <h1 class="mui-title"><div><?=$action == 'myCoach' ? __('我的教练', 'nlyd-student') :__('教练列表', 'nlyd-student');?></div></h1>
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
                            $set_url = home_url('/teams/coachList/category_id/'.$_GET['category_id']);
                            if(isset($_GET['match_id'])) $url .= '/match_id/'.$_GET['match_id'];
                            if(isset($_GET['match_id'])) $set_url .= '/match_id/'.$_GET['match_id'];
                            if(isset($_GET['directory'])) $url .= '/directory/1';
                            if(isset($_GET['back'])){
                                $url .= '/back/1';
                            }
                        ?>
                        <div class="search-zoo" style="margin-bottom:10px">
                            <i class="iconfont search-Icon">&#xe63b;</i>
                            <div class="search-btn bg_gradient_blue"><span><?=__('搜 索', 'nlyd-student')?></span></div>
                            <input type="text" class="serach-Input nl-foucs" placeholder="<?=__('搜索教练姓名', 'nlyd-student')?>">
                        </div>
                        <ul style="margin-left: 0" class="layui-tab-title">
                            <?php foreach ($category as $k => $val){ ?>
                                <li data-id="<?=$val['ID']?>" class="<?=$val['ID'] == $_GET['category_id'] || (!isset($_GET['category_id']) && $k==0) ? 'layui-this' : '';?>">
                                    <?=$val['post_title']?>
                                </li>
                                <?php if($k==0){ ?>
                                    <div class="nl-transform"><?=$val['post_title']?></div>
                                <?php } ?>
                            <?php } ?>
                           
                        </ul>
                        <?php endif;?>
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
                            <p class="no-info-text"><?=__('您还未设置任何教练', 'nlyd-student')?></p>
                            <?php
                                $url = home_url('/teams/coachList');
                                if(!empty($_GET['match_id']) ) $url .= '/match_id/'.$_GET['match_id'];
                            ?>
                            <a class="a-btn a-btn-table" href="<?=$url;?>"><div><?=__('去设置我的教练', 'nlyd-student')?></div></a>
                        <?php }else{ ?>
                            <p class="no-info-text"><?=__('无教练信息', 'nlyd-student')?></p>
                        <?php } ?>
                    </div>
                <?php } ?>                
            </div>
        </div>
    </div>
</div>
<!-- 申请当我的教练 -->
<input type="hidden" name="_wpnonce" id="setCoach" value="<?=wp_create_nonce('student_set_coach_code_nonce');?>">
<!-- 解除教练关系 -->
<input type="hidden" name="_wpnonce" id="clearCoach" value="<?=wp_create_nonce('student_relieve_coach_code_nonce');?>">
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
    var searchValue="";
    $('body').on('click','.setTeacher',function(){//申请当我教练
        var _this=$(this);
        if(!_this.hasClass('opacity')){
            _this.addClass('opacity')
            var coach_id=_this.attr('data-coachId');
            var category_id=_this.attr('data-categoryId');
            var coach_name=_this.attr('data-coachName')
            var type=$('.layui-this a').text()
                    var content='<div class="box-conent-wrapper"><?=__('您是否确认与', 'nlyd-student')?>“'+coach_name+'”<?=__('建立教学关系', 'nlyd-student')?>？</div>'

                        layer.open({
                        type: 1
                        ,maxWidth:300
                        ,title: '<?=__('教练申请', 'nlyd-student')?>' //不显示标题栏
                        ,skin:'nl-box-skin'
                        ,id: 'certification' //防止重复弹出
                        ,content:content
                        ,btn: ['<?=__('再想想', 'nlyd-student')?>', '<?=__('确认', 'nlyd-student')?>', ]
                        ,cancel:function(){
                            _this.removeClass('opacity')
                        }
                        ,success: function(layero, index){
                            
                        }
                        ,yes: function(index, layero){
                            layer.closeAll();
                            _this.removeClass('opacity')
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
                                data:postData,
                                success:function(res,ajaxStatu,xhr){  
                                    $.alerts(res.data.info)
                                    if(res.success){
                                        _this.removeClass('setTeacher').removeClass('bg_gradient_blue').removeClass('detail').addClass('bg_gradient_grey').text('<?=__('关联审核中', 'nlyd-student')?>···');
                                       
                                    }
                                    _this.removeClass('opacity')
                                },
                                complete:function(XMLHttpRequest, textStatus){
                                    if(textStatus=='timeout'){
                                        _this.removeClass('opacity')
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
    $('body').on('click','.see_detail',function(){
        var href=$(this).attr('href');
        if(href){
            window.location.href=href;
        }
    })
    if($('.search-btn').length>0){//搜索
        new AlloyFinger($('.search-btn')[0], {
            touchStart: function () {
                $('.search-btn').addClass("opacity");
            },
            touchMove: function () {
                $('.search-btn').removeClass("opacity");
            },
            touchEnd: function () {
                $('.search-btn').removeClass("opacity");
            },
            touchCancel: function () {
                $('.search-btn').removeClass("opacity");
            },
            tap: function () {
                var _this=$('.search-btn');
                var value=$('.serach-Input').val()
                searchValue=value;
                var id=$('.layui-this').attr('data-id');
                $('#'+id).empty()
                pagation(id)
            }
        });
    }
    $('body').on('click','.clearCoach',function(){//解除教学关系
        var _this=$(this);
        if(!_this.hasClass('opacity')){
            _this.addClass('opacity')
            var coach_id=_this.attr('data-coachId');
            var category_id=_this.attr('data-categoryId');
            var coach_name=_this.attr('data-coachName')
            var type=$('.layui-this a').text()
                layer.open({
                type: 1
                ,maxWidth:300
                ,title: '<?=__('解除教学关系', 'nlyd-student')?>' //不显示标题栏
                ,skin:'nl-box-skin'
                ,id: 'certification' //防止重复弹出
                ,content: '<div class="box-conent-wrapper"><?=__('您是否确认解除与', 'nlyd-student')?>“'+coach_name+'”<?=__('的教练关系', 'nlyd-student')?>？</div>'
                ,btn: ['<?=__('再想想', 'nlyd-student')?>', '<?=__('确认', 'nlyd-student')?>', ]
                ,cancel:function(){
                    _this.removeClass('opacity')
                }
                ,success: function(layero, index){
                    
                }
                ,yes: function(index, layero){
                    layer.closeAll();
                    _this.removeClass('opacity')
                }
                ,btn2: function(index, layero){
                    var postData={
                        action:'relieveMyCoach',
                        _wpnonce:$('#clearCoach').val(),
                        coach_id:coach_id,
                        category_id:category_id,
                    }
                    $.ajax({
                        data:postData,
                        success:function(res,ajaxStatu,xhr){
                            $.alerts(res.data.info)
                            if(res.success){
                                _this.parents('.coach-row').find('.nl-badge.bg_gradient_blue').removeClass('bg_gradient_blue').addClass('bg_gradient_grey');
                                _this.parents('.coach-row').find('.coach-detail-footer .coach-type').removeClass('c_blue').addClass('c_grey');
                                _this.addClass('bg_gradient_blue').removeClass('clearCoach').addClass('setTeacher').removeClass('detail').removeClass('bg_white').text('<?=__('请TA当教练', 'nlyd-student')?>')
                            }
                            _this.removeClass('opacity')
                        },
                        complete:function(XMLHttpRequest, textStatus){
                            if(textStatus=='timeout'){
                                _this.removeClass('opacity')
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
                    var coach_name=$('.layui-this').text()
                    var s=searchValue;
                    if(!isClick[category_id]){
                        s=''
                    }
                    var postData={
                        action:'get_coach_lists',
                        category_id:category_id,
                        page:page,
                        s:s,
                    }
                    var lis = [];
                    $.ajax({
                        data:postData,
                        success:function(res,ajaxStatu,xhr){
                            isClick[category_id]=true
                            console.log(res)
                            if(res.success){
                                $.each(res.data.info,function(i,v){
                                    var detailFooter="";
                                    var coach_btn="";
                                    var isLeft='ta_r';
                                    var is_current=v.my_coach=='y'?"c_blue":"";
                                    var bg=v.my_coach=='y'?"bg_gradient_blue":"bg_gradient_grey";
                                    detailFooter='<div data-id="'+v.category_id+'" class="coach-type flex1 text_1 ta_l '+is_current+'"><div class="nl-badge '+bg+'"><i class="iconfont">&#xe608;</i></div> '+coach_name+'</div>'
                                    if(v.apply_status!=null){//-1,拒绝1，申请中，2我的教练，3,取消
                                        if(v.apply_status==1){//1，申请中，2我的教练
                                            coach_btn='<div class="right_c flex1"><button type="button" class="coach-btn bg_gradient_grey text_1 "><?=__('关联审核中', 'nlyd-student')?>···</button></div>';
                                            // isLeft="ta_l"
                                        }else if(v.apply_status==2){//1，申请中，2我的教练
                                            coach_btn='<div class="right_c flex1"><button type="button" class="clearCoach coach-btn text_1  detail bg_white c_black" data-coachName="'+v.display_name+'" data-coachId="'+v.coach_id+'" data-categoryId="'+v.category_id+'"><?=__('解除关联', 'nlyd-student')?></button></div>';
                                             
                                        }else{
                                            coach_btn='<div class="right_c flex1"><button type="button" class="coach-btn bg_gradient_blue text_1 setTeacher c_white" data-coachName="'+v.display_name+'" data-coachId="'+v.coach_id+'" data-categoryId="'+v.category_id+'"><?=__('请TA当教练', 'nlyd-student')?></button></div>';//不是我的教练
                                            // isLeft="ta_l"
                                        }
                                    }else{
                                        coach_btn='<div class="right_c flex1"><button type="button" class="coach-btn bg_gradient_blue text_1 setTeacher c_white" data-coachName="'+v.display_name+'" data-coachId="'+v.coach_id+'" data-categoryId="'+v.category_id+'"><?=__('请TA当教练', 'nlyd-student')?></button></div>';//不是我的教练
                                        // isLeft="ta_l"
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
                                                                +'<span class="c_black6"><?=__('国际脑力运动委员会', 'nlyd-student')?>（IISC） '+v.user_coach_level+'</span>'
                                                            +'</div>'
                                                            +'<div class="coach-detail-footer flex-h">'
                                                                +detailFooter
                                                            +'</div>'
                                                        +'</div>'
                                                    +'</div>'
                                                    +'<div class="coach-row-footer flex-h">'
                                                        +'<div class="left_c flex1">'
                                                            // +clear_btn
                                                            
                                                            +'<button class="coach-btn detail c_black text_1 bg_white see_detail"  href="'+v.coach_url+'/category_id/<?=$_GET['category_id']?>"><?=__('查看详情', 'nlyd-student')?></button>'
                                                        +'</div>'
                                                        +coach_btn
                                                    +'</div>'
                                                +'</div>'
                                            +'</li>'
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
                        }
                })       
            }
        });
    }
    function transform() {
        var _this=$('.layui-this')
        var left=_this.position().left+parseInt(_this.css('marginLeft'));
        var html=_this.html();
        var id=_this.attr('data-id')
        $('.nl-transform').css({
            'transform':'translate3d('+left+'px, 0px, 0px)'
        }).html(html)
    }
    transform()
    var isClick={}
    pagation($('.layui-this').attr('data-id'))
    element.on('tab(tabs)', function(){//tabs
        var left=$(this).position().left+parseInt($(this).css('marginLeft'));
        var html=$(this).html();
        var id=$(this).attr('data-id')
        $('.nl-transform').css({
            'transform':'translate3d('+left+'px, 0px, 0px)'
        }).html(html)
        // var value=$('.serach-Input').val();
        if(!isClick[id]){
            // searchValue=value
            pagation(id)
        }
    })
});

 //--------------------分页-------------------------- 
})
</script>