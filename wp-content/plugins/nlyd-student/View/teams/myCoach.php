<?php require_once PLUGINS_PATH.'nlyd-student/View/public/student-footer-menu.php' ;?>
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
            <h1 class="mui-title"><div><?=__('我的教练', 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content layui-bg-white">
                <?php if(!$coachCount > 0){?>
                    <div class="layui-tab layui-tab-brief width-margin  width-margin-pc" lay-filter="tabs">
                   
                        <input type="hidden" name="user_id" value="<?=$action=='myCoach'?$user_id:'';?>">
                        <div data-id="1">
                            <ul class="flow-default layui-row layui-col-space20" id="1" style="margin:0">
                                <li class="layui-col-lg4 layui-col-md12 layui-col-sm12 layui-col-xs12">
                                    <div class="coach-row mt_10">
                                        <div class="coach-row-top">
                                            <div class="coach-picture img-box">
                                                <img src="'+v.user_head+'">
                                            </div>
                                            <div class="coach-detail">
                                                <div class="text_1">
                                                    <span class="fs_16 c_blue">名字</span>
                                                    <span class="c_black6">性别</span>
                                                    <span class="c_black6">ID 1231312312</span>
                                                </div>
                                                <div class="text_3">
                                                    <span class="c_black6"><?=__('国际脑力运动委员会', 'nlyd-student')?>（IISC） 高级教练</span>
                                                </div>
                                                <div class="coach-detail-footer flex-h">
                                                    <div class="coach-type flex1 text_1 c_blue ta_l" data-id="11"><div class="nl-badge bg_gradient_blue"><i class="iconfont">&#xe608;</i></div> <span>速算类</span></div>
                                                    <!-- <div class="coach-type flex1 text_1 c_blue ta_l" data-id="22"><div class="nl-badge bg_gradient_blue"><i class="iconfont">&#xe608;</i></div> <span>速记类</span></div> -->
                                                    <!-- <div class="coach-type flex1 text_1 c_blue ta_l"></div> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="coach-row-footer flex-h">
                                            <div class="left_c flex1">
                                                <button class="coach-btn detail c_black text_1 bg_white see_detail"  href=""><?=__('查看详情', 'nlyd-student')?></button>
                                            </div>
                                            <div class="right_c flex1">
                                                <button type="button" class="clearCoach coach-btn text_1  detail bg_white c_black" data-coachName="陈卫东" data-coachId="1" data-categoryId="2"><?=__('解除关联', 'nlyd-student')?></button>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php }else{ ?>
                    <div class="no-info-page">
                        <div class="no-info-img">
                            <img src="<?=student_css_url.'image/noInfo/noCoach1044@3x.png'?>">
                        </div>
                        <p class="no-info-text"><?=__('您还未设置任何教练', 'nlyd-student')?></p>
                        <?php
                            $url = home_url('/teams/coachList');
                            if(!empty($_GET['match_id']) ) $url .= '/match_id/'.$_GET['match_id'];
                        ?>
                        <a class="a-btn a-btn-table" href="<?=$url;?>"><div><?=__('去设置我的教练', 'nlyd-student')?></div></a>
                        
                    </div>
                <?php } ?>                
            </div>
        </div>
    </div>
</div>
<!-- 解除教练关系 -->
<input type="hidden" name="_wpnonce" id="clearCoach" value="<?=wp_create_nonce('student_relieve_coach_code_nonce');?>">
<script>
jQuery(function($) { 
    var arr = <?=json_encode($category)?>; 
    $('body').on('click','.layui-form-checkbox',function(){
        var _this=$(this);
        _this.toggleClass('layui-form-checked')
    })
    $('body').on('click','.see_detail',function(){
        var href=$(this).attr('href');
        if(href){
            window.location.href=href;
        }
    })
    $('body').on('click','.clearCoach',function(){//解除教学关系
        var _this=$(this);
        if(!_this.hasClass('opacity')){
            _this.addClass('opacity')
            var coach_type=$('.coach-detail-footer .coach-type');
            var coach_id=_this.attr('data-coachId');
            // var category_id=_this.attr('data-categoryId');
            var coach_name=_this.attr('data-coachName')
            var content='<div class="box-conent-wrapper"><?=__('您是否确认解除与', 'nlyd-student')?>“'+coach_name+'”<?=__('的教练关系', 'nlyd-student')?>？</div>';
            if(coach_type.length>1){
                coach_type.each(function(){
                    var _this=$(this);
                    var id=_this.attr('data-id');
                    if(id){
                        var type=_this.find('span').text()
                        content+='<div style="text-align:center" class="fs_12 c_blue"><div class="layui-unselect layui-form-checkbox" data-id="'+id+'" lay-skin="primary"><i class="layui-icon layui-icon-ok"></i></div> '+type+'<?=__('教练', 'nlyd-student')?></div>'
                    }
                })
            }

                layer.open({
                type: 1
                ,maxWidth:300
                ,title: '<?=__('解除教学关系', 'nlyd-student')?>' //不显示标题栏
                ,skin:'nl-box-skin'
                ,id: 'certification' //防止重复弹出
                ,content: content
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
                    var category_id=[];
                    
                    if(coach_type.lenth>1){
                        $('.layui-form-checked').each(function(){
                            var id=$(this).attr('data-id');
                            category_id.push(id)
                        })
                    }else{
                        var id=coach_type.attr('data-id');
                        category_id=id;
                    }
                    
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
                                window.location.reload()
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
layui.use(['element','flow','layer','form'], function(){
    var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
    var flow = layui.flow;//流加载
    // function pagation(category_id){
    //     flow.load({
    //         elem: '#'+category_id //流加载容器
    //         ,isAuto: false
    //         ,isLazyimg: true
    //         ,done: function(page, next){//加载下一页
    //             //模拟插入
    //                 var user_id="";
    //                 if($('input[name="user_id"]').val().length>0){
    //                     user_id=$('input[name="user_id"]').val()
    //                 }
    //                 var postData={
    //                     action:'get_coach_lists',
    //                     category_id:category_id,
    //                     page:page,
    //                     user_id:user_id,
    //                 }
    //                 var lis = [];
    //                 $.ajax({
    //                     data:postData,
    //                     success:function(res,ajaxStatu,xhr){
    //                         console.log(res)
    //                         if(res.success){
    //                             $.each(res.data.info,function(i,v){
    //                                 var detailFooter="";
    //                                 var coach_btn="";
    //                                 // var clear_btn="";
    //                                 var isLeft='ta_r';
    //                                 // var post_title="";
    //                                 $.each(v.category,function(index,value){
    //                                     var is_current="c_blue";
    //                                     var metal='<div class="nl-badge bg_gradient_blue"><i class="iconfont">&#xe608;</i></div>';
    //                                     if(value.is_current=="true"){//教练属于当前类型教练
    //                                         is_current="c_blue";
    //                                         var categoryBtnDom='<div data-id="'+value.category_id+'" class="coach-type flex1 text_1 '+is_current+'">'+metal+' '+value.post_title+'</div>';
    //                                         detailFooter+=categoryBtnDom;
    //                                     }  
    //                                 }) 
    //                                 coach_btn='<div class="right_c flex1"><button type="button" class="clearCoach coach-btn text_1  detail bg_white c_black" data-coachName="'+v.display_name+'" data-coachId="'+v.coach_id+'" data-categoryId="'+v.category_id+'"><?=__('解除关联', 'nlyd-student')?></button></div>';
    //                                 var dom='<li class="layui-col-lg4 layui-col-md12 layui-col-sm12 layui-col-xs12">'
    //                                             +'<div class="coach-row">'
    //                                                 +'<div class="coach-row-top">'
    //                                                     +'<div class="coach-picture img-box">'
    //                                                         +'<img src="'+v.user_head+'">'
    //                                                     +'</div>'
    //                                                     +'<div class="coach-detail">'
    //                                                         +'<div class="text_1">'
    //                                                             +'<span class="fs_16 c_blue">'+v.display_name+'</span>'
    //                                                             +'<span class="c_black6">'+v.user_gender+'</span>'
    //                                                             +'<span class="c_black6">ID '+v.user_ID+'</span>'
    //                                                         +'</div>'
    //                                                         +'<div class="text_3">'
    //                                                             +'<span class="c_black6"><?=__('国际脑力运动委员会', 'nlyd-student')?>（IISC） '+v.user_coach_level+'</span>'
    //                                                         +'</div>'
    //                                                         +'<div class="coach-detail-footer flex-h">'
    //                                                             +detailFooter
    //                                                         +'</div>'
    //                                                     +'</div>'
    //                                                 +'</div>'
    //                                                 +'<div class="coach-row-footer flex-h">'
    //                                                     +'<div class="left_c flex1">'
    //                                                         // +clear_btn
                                                            
    //                                                         +'<button class="coach-btn detail c_black text_1 bg_white see_detail"  href="'+v.coach_url+'/category_id/<?=$_GET['category_id']?>"><?=__('查看详情', 'nlyd-student')?></button>'
    //                                                     +'</div>'
    //                                                     +coach_btn
    //                                                 +'</div>'
    //                                             +'</div>'
    //                                         +'</li>'
    //                                 lis.push(dom) 
    //                             })
    //                             if (res.data.info.length<50) {
    //                                 next(lis.join(''),false) 
    //                             }else{
    //                                 next(lis.join(''),true) 
    //                             }
    //                         }else{
    //                             if(page==1){
    //                                 var flag='<?=$action ?>';
    //                                 if(flag.length>0){
    //                                     var text=$('.layui-this').text();
    //                                     var dom='<a class="a-btn a-btn-table" href="<?=$next_url?>"><div><?=__('设置我的', 'nlyd-student')?>'+text+'<?=__('教练', 'nlyd-student')?></div></a>'
    //                                 }
    //                                 lis.push(dom) 
    //                             }
    //                             next(lis.join(''),false)
    //                         }
    //                     }
    //             })       
    //         }
    //     });
    // }
    // pagation(1)
});

 //--------------------分页-------------------------- 
})
</script>