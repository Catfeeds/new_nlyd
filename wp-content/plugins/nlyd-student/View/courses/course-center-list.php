
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
                    <h1 class="mui-title"><div><?=__('脑博瑞国际脑力训练中心', 'nlyd-student')?></div></h1>
                </header>  
                <div class="layui-row nl-border nl-content course">
                    <div class="layui-tab layui-tab-brief" lay-filter="matchList">
                        <ul style="margin-left:0;padding:0" class="layui-tab-title">
                            <li class="layui-this" lay-id="1">
                                <?=__('课程报名中', 'nlyd-student')?>
                            </li>
                            <li lay-id="2">
                                <?=__('课程进行中', 'nlyd-student')?>
                            </li>
                            <li lay-id="3"><?=__('已结课', 'nlyd-student')?></li>
                            <div class="nl-transform" data-y="-5"><?=__('已结课', 'nlyd-student')?></div>
                        </ul>
                        <div class="layui-tab-content width-margin width-margin-pc">
                            <!-- 课程报名中 -->
                            <div class="layui-tab-item layui-show">
                                <ul class="flow-default layui-row layui-col-space20" id="1" style="margin:0">
                                    <!-- <div class="no-info-page" style="top:50px">
                                        <div class="no-info-img">
                                            <img src="<?=student_css_url.'image/noInfo/noCourse1043@2x.png'?>">
                                        </div>
                                        <p class="no-info-text"><?=__('该中心近期暂无可报名课程', 'nlyd-student')?></p>
                                        <a class="a-btn a-btn-table" href="<?=home_url('/courses/');?>"><div><?=__('查看其它中心课程', 'nlyd-student')?></div></a>
                                    </div> -->
                                    <li class="match_row">
                                        <div class="match_header bold c_black f_16 mt_10">高效记忆术·G预报班·成都郫县</div>
                                        <div class="match_body">
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('开课日期：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_black">2018-04-21 15:00 <span class="c_blue ml_10">课程报名中</span></div>
                                            </div>
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('授课教练：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_blue">成 炜</div>
                                            </div>
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('抢占名额：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_black"><span class="c_blue">21</span>/21</div>
                                            </div>
                                        </div>
                                        <div class="nl-match-footer flex-h">
                                            <div class="nl-match-button flex1">
                                                <a href="<?=home_url('/courses/courseDetail');?>" class="dis_table c_black"><span class="dis_cell"><?=__('查看详情', 'nlyd-student')?></span></a>
                                            </div>
                                            <div class="nl-match-button flex1">
                                                <a href="" class="dis_table c_white bg_gradient_grey"><span class="dis_cell"><?=__('您已抢占名额', 'nlyd-student')?></span></a>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="match_row">
                                        <div class="match_header bold c_black f_16 mt_10">高效记忆术·G预报班·成都郫县</div>
                                        <div class="match_body">
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('开课日期：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_black">2018-04-21 15:00 <span class="c_blue ml_10">课程报名中</span></div>
                                            </div>
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('授课教练：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_blue">成 炜</div>
                                            </div>
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('抢占名额：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_black"><span class="c_blue">21</span>/21</div>
                                            </div>
                                        </div>
                                        <div class="nl-match-footer flex-h">
                                            <div class="nl-match-button flex1">
                                                <a href="<?=home_url('/courses/courseDetail');?>" class="dis_table c_black"><span class="dis_cell"><?=__('查看详情', 'nlyd-student')?></span></a>
                                            </div>
                                            <div class="nl-match-button flex1">
                                                <a href="" class="dis_table c_white bg_gradient_grey"><span class="dis_cell"><?=__('您已抢占名额', 'nlyd-student')?></span></a>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="match_row">
                                        <div class="match_header bold c_black f_16 mt_10">高效记忆术·G预报班·成都郫县</div>
                                        <div class="match_body">
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('开课日期：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_black">2018-04-21 15:00 <span class="c_blue ml_10">课程报名中</span></div>
                                            </div>
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('授课教练：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_blue">成 炜</div>
                                            </div>
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('抢占名额：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_black"><span class="c_blue">21</span>/21</div>
                                            </div>
                                        </div>
                                        <div class="nl-match-footer flex-h">
                                            <div class="nl-match-button flex1">
                                                <a href="<?=home_url('/courses/courseDetail');?>" class="dis_table c_black"><span class="dis_cell"><?=__('查看详情', 'nlyd-student')?></span></a>
                                            </div>
                                            <div class="nl-match-button flex1">
                                                <a href="" class="dis_table c_white bg_gradient_grey"><span class="dis_cell"><?=__('您已抢占名额', 'nlyd-student')?></span></a>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="match_row">
                                        <div class="match_header bold c_black f_16 mt_10">高效记忆术·G预报班·成都郫县</div>
                                        <div class="match_body">
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('开课日期：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_black">2018-04-21 15:00 <span class="c_blue ml_10">课程报名中</span></div>
                                            </div>
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('授课教练：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_blue">成 炜</div>
                                            </div>
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('抢占名额：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_black"><span class="c_blue">21</span>/21</div>
                                            </div>
                                        </div>
                                        <div class="nl-match-footer flex-h">
                                            <div class="nl-match-button flex1">
                                                <a href="<?=home_url('/courses/courseDetail');?>" class="dis_table c_black"><span class="dis_cell"><?=__('查看详情', 'nlyd-student')?></span></a>
                                            </div>
                                            <div class="nl-match-button flex1">
                                                <a href="" class="dis_table c_white bg_gradient_grey"><span class="dis_cell"><?=__('您已抢占名额', 'nlyd-student')?></span></a>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="match_row">
                                        <div class="match_header bold c_black f_16 mt_10">高效记忆术·G预报班·成都郫县</div>
                                        <div class="match_body">
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('开课日期：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_black">待定</div>
                                            </div>
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('授课教练：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_blue">成 炜</div>
                                            </div>
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('抢占名额：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_black"><span class="c_blue">21</span>/21</div>
                                            </div>
                                        </div>
                                        <div class="nl-match-footer flex-h">
                                            <div class="nl-match-button flex1">
                                                <a href="<?=home_url('/courses/courseDetail');?>" class="dis_table c_black"><span class="dis_cell"><?=__('查看详情', 'nlyd-student')?></span></a>
                                            </div>
                                            <div class="nl-match-button flex1">
                                                <a href="" class="dis_table c_white bg_gradient_blue"><span class="dis_cell"><?=__('抢占名额', 'nlyd-student')?></span></a>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <!-- 课程进行中 -->
                            <div class="layui-tab-item">
                                <ul class="flow-default layui-row layui-col-space20" id="2" style="margin:0">
                                    <li class="match_row">
                                        <div class="match_header bold c_black f_16 mt_10">高效记忆术·G预报班·成都郫县</div>
                                        <div class="match_body">
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('开课日期：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_black">2018-04-21 15:00 <span class="c_blue ml_10">课程进行中</span></div>
                                            </div>
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('授课教练：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_blue">成 炜</div>
                                            </div>
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('抢占名额：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_black"><span class="c_blue">21</span>/21</div>
                                            </div>
                                        </div>
                                        <div class="nl-match-footer flex-h">
                                            <div class="nl-match-button flex1">
                                                <a href="<?=home_url('/courses/courseDetail');?>" class="dis_table c_black"><span class="dis_cell"><?=__('查看详情', 'nlyd-student')?></span></a>
                                            </div>
                                            <div class="nl-match-button flex1">
                                                <a href="" class="dis_table c_white bg_gradient_grey"><span class="dis_cell"><?=__('您已抢占名额', 'nlyd-student')?></span></a>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="match_row">
                                        <div class="match_header bold c_black f_16 mt_10">高效记忆术·G预报班·成都郫县</div>
                                        <div class="match_body">
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('开课日期：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_black">待定</div>
                                            </div>
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('授课教练：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_blue">成 炜</div>
                                            </div>
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('抢占名额：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_black"><span class="c_blue">21</span>/21</div>
                                            </div>
                                        </div>
                                        <div class="nl-match-footer flex-h">
                                            <div class="nl-match-button flex1">
                                                <a href="<?=home_url('/courses/courseDetail');?>" class="dis_table c_black"><span class="dis_cell"><?=__('查看详情', 'nlyd-student')?></span></a>
                                            </div>
                                            <div class="nl-match-button flex1">
                                                <a href="" class="dis_table c_white bg_gradient_blue"><span class="dis_cell"><?=__('抢占名额', 'nlyd-student')?></span></a>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <!-- 已结课 -->
                            <div class="layui-tab-item">
                                <ul class="flow-default layui-row layui-col-space20" id="3" style="margin:0">
                                    <li class="match_row">
                                        <div class="match_header bold c_black f_16 mt_10">高效记忆术·G预报班·成都郫县</div>
                                        <div class="match_body">
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('开课日期：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_black">2018-04-21 15:00 <span class="c_blue ml_10">已结课</span></div>
                                            </div>
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('授课教练：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_blue">成 炜</div>
                                            </div>
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('抢占名额：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_black"><span class="c_blue">21</span>/21</div>
                                            </div>
                                        </div>
                                        <div class="nl-match-footer flex-h">
                                            <div class="nl-match-button flex1">
                                                <a href="<?=home_url('/courses/courseDetail');?>" class="dis_table c_black"><span class="dis_cell"><?=__('查看详情', 'nlyd-student')?></span></a>
                                            </div>
                                            <div class="nl-match-button flex1">
                                                <a href="<?=home_url('/courses/courseEnd');?>" class="dis_table c_white bg_gradient_blue"><span class="dis_cell"><?=__('结课成绩', 'nlyd-student')?></span></a>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="match_row">
                                        <div class="match_header bold c_black f_16 mt_10">高效记忆术·G预报班·成都郫县</div>
                                        <div class="match_body">
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('开课日期：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_black">待定</div>
                                            </div>
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('授课教练：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_blue">成 炜</div>
                                            </div>
                                            <div class="match_body_row">
                                                <div class="match_body_label"><?=__('抢占名额：', 'nlyd-student')?></div>
                                                <div class="match_body_info c_black"><span class="c_blue">21</span>/21</div>
                                            </div>
                                        </div>
                                        <div class="nl-match-footer flex-h">
                                            <div class="nl-match-button flex1">
                                                <a href="<?=home_url('/courses/courseDetail');?>" class="dis_table c_black"><span class="dis_cell"><?=__('查看详情', 'nlyd-student')?></span></a>
                                            </div>
                                            <div class="nl-match-button flex1">
                                                <a href="<?=home_url('/courses/courseEnd');?>" class="dis_table c_white bg_gradient_blue"><span class="dis_cell"><?=__('结课成绩', 'nlyd-student')?></span></a>
                                            </div>
                                        </div>
                                    </li>
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
                    }
                    if(parseInt(id)==1){//报名
                        postData['course_type']="1";
                    }else if(parseInt(id)==2){//比赛
                        postData['course_type']="2";
                    }else{//往期
                        postData['course_type']="-3";
                    }
                    var lis = [];
                    $.ajax({
                        data: postData,
                        success:function(res,ajaxStatu,xhr){
                            console.log(res)
                            
                            isClick[id]=true
                            if(res.success){
                                $.each(res.data.info,function(i,v){
                                    var dom='<li class="match_row">'
                                                +'<div class="match_header bold c_black f_16 mt_10">'+v.post_title+'</div>'
                                                +'<div class="match_body">'
                                                    +'<div class="match_body_row">'
                                                        +'<div class="match_body_label"><?=__('开课日期：', 'nlyd-student')?></div>'
                                                        +'<div class="match_body_info c_black">待定</div>'
                                                    +'</div>'
                                                    +'<div class="match_body_row">'
                                                        +'<div class="match_body_label"><?=__('授课教练：', 'nlyd-student')?></div>'
                                                        +'<div class="match_body_info c_blue">成 炜</div>'
                                                    +'</div>'
                                                    +'<div class="match_body_row">'
                                                        +'<div class="match_body_label"><?=__('抢占名额：', 'nlyd-student')?></div>'
                                                        +'<div class="match_body_info c_black"><span class="c_blue">21</span>/21</div>'
                                                    +'</div>'
                                                +'</div>'
                                                +'<div class="nl-match-footer flex-h">'
                                                    +'<div class="nl-match-button flex1">'
                                                        +'<a href="'+window.home_url+'/courses/courseDetail/id/'+v.id+'" class="dis_table c_black"><span class="dis_cell"><?=__('查看详情', 'nlyd-student')?></span></a>'
                                                    +'</div>'
                                                    +'<div class="nl-match-button flex1">'
                                                        +'<a class="dis_table c_white bg_gradient_blue"><span class="dis_cell"><?=__('抢占名额', 'nlyd-student')?></span></a>'
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
                                        case '3':
                                            text='<?=__("该中心近期暂无已结课课程", "nlyd-student")?>'
                                            break;
                                        default:
                                            break;
                                    }
                                    var dom='<div class="no-info-page" style="top:50px">'
                                                +'<div class="no-info-img">'
                                                    +'<img src="'+window.plugins_url+'/nlyd-student/Public/css/image//noInfo/noCourse1043@2x.png">'
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
        pagation($('.layui-this').attr('lay-id'),1)
        
        element.on('tab(matchList)', function(){//matchList
            // location.hash = 'matchList='+ $(this).attr('lay-id');
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