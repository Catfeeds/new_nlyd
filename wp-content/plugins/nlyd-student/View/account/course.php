
<div class="layui-fluid">
    <div class="layui-row">

        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>

      
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback static" href="<?=home_url('/account/')?>">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('我的课程', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content have-bottom">
                <div class="layui-tab layui-tab-brief width-margin width-margin-pc" lay-filter="tabs">
                    <div class="layui-tab-content" style="padding: 0;">
                        <div class="layui-tab-item layui-show">
                            <ul class="flow-default layui-row layui-col-space20" id="flow-list">

                            </ul>
                        </div>
                    </div>
                </div>
                <a class="a-btn a-btn-table" href="<?=home_url('/courses/');?>"><div><?=__('看看最近课程', 'nlyd-student')?></div></a>
                <!-- <a href="<?=home_url('/courses/')?>" class="a-btn a-btn-table"><div></div><?=__('前往课程中心', 'nlyd-student')?></a> -->
            </div>
        </div>
    </div>
</div>
<script>
jQuery(function($) { 
    $('body').on('click','.nl-match-button button',function(){
        var _this=$(this);
        var href=_this.attr('href');
        if(href){
            _this.addClass('opacity')
            setTimeout(function(){
                _this.removeClass('opacity')
            }, 100);
            window.location.href=href;
        }
    })
    layui.use(['element','flow'], function(){
        var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
        var flow = layui.flow;//流加载
        flow.load({
            elem: '#flow-list' //流加载容器
            ,isAuto: true
            ,isLazyimg: true
            ,done: function(page, next){ //加载下一页
                var postData={
                    action:'get_my_course',
                    page:page,
                };
                var lis = [];
                $.ajax({
                    data:postData,
                    success:function(res,ajaxStatu,xhr){ 
                        console.log(res)
                            if(res.success){
                                $.each(res.data.info,function(i,v){
                                    var isMe='<div class="nl-badge"><i class="iconfont">&#xe608;</i></div>';//标签
                                    var is_enable='';
                                    var rightBtn=rightBtn='<button type="button" class="bg_gradient_grey"><?=__("您已抢占名额", "nlyd-student")?></button>';
                                    if(v.is_enable==1){//报名中
                                        is_enable="<?=__('报名中', 'nlyd-student')?>"
                                    }else if(v.is_enable==2){//进行
                                        is_enable="<?=__('中', 'nlyd-student')?>"
                                    }else if(v.is_enable==-3){//结课
                                        is_enable="<?=__('已结课', 'nlyd-student')?>"
                                        rightBtn='<button type="button" class="bg_gradient_blue" href="'+window.home_url+'/courses/courseEnd/center_id/'+v.zone_id+'/id/'+v.course_id+'"><?=__("结课成绩", "nlyd-student")?></button>'
                                    }
                                    var dom='<li class="layui-col-lg4 layui-col-md12 layui-col-sm12 layui-col-xs12">'
                                                +'<div class="nl-match">'
                                                    +'<div class="nl-match-header">'
                                                        +'<span class="nl-match-name  fs_16 c_blue">'+(v.course_title || '-')+'</span>'
                                                        +isMe
                                                        +'<p class="long-name fs_12 c_black8">'+(v.type_name || '-')+'</p>'
                                                    +'</div>'
                                                    +'<div class="nl-match-body">'
                                                        +'<div class="nl-match-detail layui-row">'
                                                            +'<div class="nl-match-label"><div><?=__('开课日期', 'nlyd-student')?>:</div></div>'
                                                            +'<div class="nl-match-info">'
                                                                +'<div class="c_black">'+(v.start_time || '-')+'</div>'
                                                                +'<span class="nl-match-type c_blue">'+is_enable+'</span>'
                                                            +'</div>'
                                                        +'</div>'
                                                        +'<div class="nl-match-detail layui-row">'
                                                            +'<div class="nl-match-label"><div><?=__('授课教练', 'nlyd-student')?>:</div></div>'
                                                            +'<div class="nl-match-info">'
                                                                +'<div class="c_black">'+(v.real_name || '-')+'</div>'
                                                            +'</div>'
                                                        +'</div>'
                                                        +'<div class="nl-match-detail layui-row">'
                                                            +'<div class="nl-match-label"><div><?=__('报名费用', 'nlyd-student')?>:</div></div>'
                                                            +'<div class="nl-match-info">'
                                                                +'<div class="c_black">¥'+(v.const || '-')+'</div>'
                                                            +'</div>'
                                                        +'</div>'
                                                        +'<div class="nl-match-detail layui-row">'
                                                            +'<div class="nl-match-label"><div><?=__('报名截止', 'nlyd-student')?>:</div></div>'
                                                            +'<div class="nl-match-info">'
                                                                +'<div class="c_black getTimes">'
                                                                +(v.end_time || '-')+'</div>'
                                                            +'</div>'
                                                        +'</div>'
                                                        +'<div class="nl-match-detail layui-row">'
                                                            +'<div class="nl-match-label"><div><?=__('课程名额', 'nlyd-student')?>:</div></div>'
                                                            +'<div class="nl-match-info">'
                                                                +'<div class="c_black">'+(v.open_quota || '-')+'</div>'
                                                            +'</div>'
                                                        +'</div>'
                                                    +'</div>'
                                                    +'<div class="nl-match-footer flex-h">'
                                                        +'<div class="nl-match-button flex1">'
                                                            +'<button type="button"   href="'+window.home_url+'/courses/courseDetail/center_id/'+v.zone_id+'/id/'+v.course_id+'"><?=__("查看详情", "nlyd-student")?></button>'
                                                        +'</div>'
                                                        +'<div class="nl-match-button flex1 last-btn">'
                                                        +rightBtn
                                                        +'</div>'
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
                                if(page==1){
                                    var dom='<div class="no-info-page">'
                                            +'<div class="no-info-img">'
                                                +'<img src="<?=student_css_url.'image/noInfo/noCourse1043@2x.png'?>">'
                                            +'</div>'
                                            +'<p class="no-info-text"><?=__('您暂未参加任何课程', 'nlyd-student')?></p>'
                                            // +'<a class="a-btn a-btn-table" href="<?=home_url('/courses/');?>"><div><?=__('看看最近课程', 'nlyd-student')?></div></a>'
                                        +'</div>'
                                        $('#flow-list').empty().append(dom);
                                }
                                
                            }
                    }
                })  
                     
            }
        });
    })

})
</script>