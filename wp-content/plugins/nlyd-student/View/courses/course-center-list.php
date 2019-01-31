
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>

            <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
                <header class="mui-bar mui-bar-nav">
                    <a class="mui-pull-left nl-goback nl-goback static" href="<?=home_url('/courses/');?>">
                        <div><i class="iconfont">&#xe610;</i></div>
                    </a>
                    <h1 class="mui-title"><div><?=__($zone_name.'国际脑力训练中心', 'nlyd-student')?></div></h1>
                </header>  
                <div class="layui-row nl-border nl-content course">
                    <div class="layui-tab layui-tab-brief fixed_tab" lay-filter="courseList">
                        <ul style="margin-left:0;padding:0" class="layui-tab-title">
                            <li class="layui-this" lay-id="1">
                                <?=__('课程报名中', 'nlyd-student')?>
                                <?php
                                if($entry_is_true > 0){
                                    echo '('.$entry_is_true.')';
                                }
                                ?>
                            </li>
                            <li lay-id="2">
                                <?=__('课程进行中', 'nlyd-student')?>
                                <?php
                                if($match_is_true > 0){
                                    echo '('.$match_is_true.')';
                                }
                                ?>
                            </li>
                            <li lay-id="-3"><?=__('已结课', 'nlyd-student')?></li>
                            <div class="nl-transform" data-y="-5"><?=__('已结课', 'nlyd-student')?></div>
                        </ul>
                        <div class="layui-tab-content width-margin width-margin-pc">
                            <!-- 课程报名中 -->
                            <div class="layui-tab-item layui-show">
                                <ul class="flow-default grid" id="1" style="margin:0">
                                    
                                </ul>
                            </div>
                            <!-- 课程进行中 -->
                            <div class="layui-tab-item">
                                <ul class="flow-default grid" id="2" style="margin:0">
                                    
                                </ul>
                            </div>
                            <!-- 已结课 -->
                            <div class="layui-tab-item">
                                <ul class="flow-default grid" id="-3" style="margin:0">
                                </ul>
                            </div>
                        </div>
                    </div>    
                </div>
            </div>  
    </div>
</div>
<script>
    
jQuery(function($) { 
    var _id=$.Request('id');
    layui.use(['element','flow'], function(){
        var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
        var flow = layui.flow;//流加载
        function pagation(id,match_page){
            flow.load({
                elem: '#'+id //流加载容器
                ,isAuto: false
                ,isLazyimg: true
                ,done: function(page, next){ //加载下一页
                    var postData={
                        action:'get_zone_course',
                        page:match_page,
                        id:_id,
                        course_type:id
                    }
                    var lis = [];
                    $.ajax({
                        data: postData,
                        success:function(res,ajaxStatu,xhr){
                            console.log(res)
                            isClick[id]=true
                            if(res.success){
                                $.each(res.data.info,function(i,v){
                                    var rightBtn='<a href="'+window.home_url+'/courses/courseSign/center_id/'+$.Request('id')+'/id/'+v.id+'" class="dis_table c_white bg_gradient_blue"><span class="dis_cell"><?=__("抢占名额", "nlyd-student")?></span></a>'
                                    if(id=="-3"){//课程报名中
                                        rightBtn='<a href="'+window.home_url+'/courses/courseEnd/center_id/'+$.Request('id')+'/id/'+v.id+'" class="dis_table c_white bg_gradient_blue"><span class="dis_cell"><?=__("结课成绩", "nlyd-student")?></span></a>'
                                    }else{
                                        if(id=="2"){//进行中
                                            rightBtn='<a class="dis_table c_white bg_gradient_grey"><span class="dis_cell"><?=__("课程进行中...", "nlyd-student")?></span></a>'
                                        }
                                        if (v.order_id) {//已报名
                                            rightBtn='<a class="dis_table c_white bg_gradient_grey"><span class="dis_cell"><?=__("您已报名", "nlyd-student")?></span></a>'
                                        }else{
                                            if(v.surplus<=0){
                                                rightBtn='<a class="dis_table c_white bg_gradient_grey"><span class="dis_cell"><?=__("名额已抢光", "nlyd-student")?></span></a>'
                                            }
                                        }
                                    }
                                    var category_title=v.category_title ? '（'+v.category_title+'）' : '';
                                    // var join=parseInt(v.open_quota) - parseInt(v.entry_total);
                                    // var canJoin=join>=0 ? join : '0';
                                    var dom='<li class="match_row">'
                                                +'<div class="match_header mt_10"><span class="bold c_black f_16">'+v.course_title+category_title+'</span><br><span class="c_black8 f_12">'+v.type_name+'</span></div>'
                                                +'<div class="match_body">'
                                                    +'<div class="match_body_row">'
                                                        +'<div class="match_body_label"><?=__("开课日期：", "nlyd-student")?></div>'
                                                        +'<div class="match_body_info c_black">'+v.start_time+' <span class="c_blue ml_10">'+v.status_cn+'</span></div>'
                                                    +'</div>'
                                                    +'<div class="match_body_row">'
                                                        +'<div class="match_body_label"><?=__("授课教练：", "nlyd-student")?></div>'
                                                        +'<a class="match_body_info c_blue" href="'+window.home_url+'/teams/coachDetail/coach_id/'+v.coach_id+'/category_id/">'+v.real_name+'</a>'
                                                    +'</div>'
                                                    +'<div class="match_body_row">'
                                                        +'<div class="match_body_label"><?=__("课程费用", "nlyd-student")?></div>'
                                                        +'<div class="match_body_info c_black">￥'+v.const+'</div>'
                                                    +'</div>'
                                                    +'<div class="match_body_row">'
                                                        +'<div class="match_body_label"><?=__("剩余名额：", "nlyd-student")?></div>'
                                                        +'<div class="match_body_info c_black"><span class="c_blue">'+v.surplus+'</span>'
                                                        // +'<a href="'+window.home_url+'/courses/courseStudent/id/'+v.id+'/center_id/'+$.Request('id')+'" class="c_blue ml_10">查看</a>'
                                                        +'</div>'
                                                    +'</div>'
                                                +'</div>'
                                                +'<div class="nl-match-footer flex-h">'
                                                    +'<div class="nl-match-button flex1">'
                                                        +'<a href="'+window.home_url+'/courses/courseDetail/center_id/'+$.Request('id')+'/id/'+v.id+'" class="dis_table c_black"><span class="dis_cell"><?=__("查看详情", "nlyd-student")?></span></a>'
                                                    +'</div>'
                                                    +'<div class="nl-match-button flex1">'
                                                        +rightBtn
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
                                if(match_page==1){
                                    var text='';
                                    switch (id) {
                                        case '1':
                                            text='<?=__("该中心近期暂无可报名课程", "nlyd-student")?>'
                                            break;
                                        case '2':
                                            text='<?=__("该中心近期暂无进行中课程", "nlyd-student")?>'
                                            break;
                                        case '-3':
                                            text='<?=__("该中心近期暂无已结课课程", "nlyd-student")?>'
                                            break;
                                        default:
                                            break;
                                    }
                                    var dom='<div class="no-info-page" style="top:50px">'
                                                +'<div class="no-info-img">'
                                                    +'<img src="'+window.plugins_url+'/nlyd-student/Public/css/image/noInfo/noCourse1043@2x.png">'
                                                +'</div>'
                                                +'<p class="no-info-text">'+text+'</p>'
                                                +'<a class="a-btn a-btn-table" href="'+window.home_url+'/courses/"><div><?=__("查看其它中心课程", "nlyd-student")?></div></a>'
                                            +'</div>'
                                    $('#'+id).empty().append(dom);
                                }
                            }
                            match_page++
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
        var isClick={}
        if(location.hash.replace(/^#courseList=/, '').length==0){
           //获取hash来切换选项卡，假设当前地址的hash为lay-id对应的值
           location.hash = 'courseList='+<?=$anchor?>;
        }
        var layid = location.hash.replace(/^#courseList=/, '');
     
        element.tabChange('courseList', layid);
        var lefts=$('.layui-this').position().left;
        $('.nl-transform').css({
            'transform':'translate3d('+lefts+'px, -5px, 0px)'
        })
        pagation($('.layui-this').attr('lay-id'),1)
        
        element.on('tab(courseList)', function(){//courseList
            location.hash = 'courseList='+ $(this).attr('lay-id');
            var left=$(this).position().left;
            var id=$(this).attr('lay-id')
            $('.nl-transform').css({
                'transform':'translate3d('+left+'px, -5px, 0px)'
            })
            if(!isClick[id]){
                pagation(id,1)
            }
        });
    })
})
</script>