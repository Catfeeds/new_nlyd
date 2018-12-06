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
                <?php if($rows){?>
                    <div class="layui-tab layui-tab-brief width-margin  width-margin-pc" lay-filter="tabs">

                        <div data-id="1">
                            <ul class="flow-default layui-row layui-col-space20" id="1" style="margin:0">
                               <?php foreach ($rows as $row1){ ?>
                                   <li class="layui-col-lg4 layui-col-md12 layui-col-sm12 layui-col-xs12">
                                       <div class="coach-row mt_10">
                                           <div class="coach-row-top">
                                               <div class="coach-picture img-box">
                                                   <img src="<?=$row1['user_head']?$row1['user_head']:home_url('wp-content/plugins/nlyd-student/Public/css/image/nlyd.png')?>">
                                               </div>
                                               <div class="coach-detail">
                                                   <div class="text_1">
                                                       <span class="fs_16 c_blue"><?=$row1['real_name']?></span>
                                                       <span class="c_black6"><?=$row1['sex']?></span>
                                                       <span class="c_black6">ID <?=$row1['ID']?></span>
                                                   </div>
                                                   <div class="text_3">
                                                       <span class="c_black6"><?=__('国际脑力运动委员会', 'nlyd-student')?>（IISC） 高级教练</span>
                                                   </div>
                                                   <div class="coach-detail-footer flex-h">
                                                       <?php foreach ($row1['category'] as $vv){ ?>
                                                       <div class="coach-type flex1 text_1 c_blue ta_l" data-id="<?=$vv['category_id']?>"><div class="nl-badge bg_gradient_blue"><i class="iconfont">&#xe608;</i></div> <span><?=__($vv['category_name'],'nlyd-student')?></span></div>

                                                       <?php } ?>
                                                     <!-- <div class="coach-type flex1 text_1 c_blue ta_l" data-id="22"><div class="nl-badge bg_gradient_blue"><i class="iconfont">&#xe608;</i></div> <span>速记类</span></div> -->
                                                       <!-- <div class="coach-type flex1 text_1 c_blue ta_l"></div> -->
                                                   </div>
                                               </div>
                                           </div>
                                           <div class="coach-row-footer flex-h">
                                               <div class="left_c flex1">
                                                   <button class="coach-btn detail c_black text_1 bg_white see_detail"  href="<?=home_url('teams/coachDetail/coach_id/'.$row1['coach_id'])?>"><?=__('查看详情', 'nlyd-student')?></button>
                                               </div>
                                               <div class="right_c flex1">
                                                   <button type="button" class="clearCoach coach-btn text_1  detail bg_white c_black" data-coachName="<?=$row1['real_name']?>" data-coachId="<?=$row1['coach_id']?>"><?=__('解除关联', 'nlyd-student')?></button>
                                               </div>
                                           </div>
                                       </div>
                                   </li>
                               <?php } ?>
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
            var coach_type=_this.parents('li').find('.coach-detail-footer .coach-type');
            var coach_id=_this.attr('data-coachId');
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
                    if(coach_type.length>1){
                        $('.layui-form-checked').each(function(){
                            var id=$(this).attr('data-id');
                            category_id.push(id)
                        })
                        category_id=category_id.join(',')
                    }else{
                        var id=coach_type.attr('data-id');
                        category_id+=id;
                    }
                    if(category_id.length<1){
                        
                        layer.closeAll();
                        $.alerts("<?=__('请选择您要解除的教练类别', 'nlyd-student')?>");
                        _this.removeClass('opacity')
                        return false;
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
layui.use(['element','layer'], function(){
    var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
});
})
</script>