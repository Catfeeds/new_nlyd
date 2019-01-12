<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>

        <?php if(!$row){ ?>
            <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback static" href="<?=home_url('/zone/');?>">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('管理课程', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content match_tabs have-bottom">
                <div class="layui-tab layui-tab-brief width-padding width-padding-pc" lay-filter="matchList" style="margin-top:20px">
                    <ul style="margin:0;padding:0" class="layui-tab-title">
                        <li class="layui-this" lay-id="1">
                            <div><?=__('全部课程', 'nlyd-student')?></div>
                        </li>
                        <li lay-id="2">
                        <div><?=__('近期课程', 'nlyd-student')?></div>
                        </li>
                        <li lay-id="3"><div><?=__('往期课程', 'nlyd-student')?></div></li>
                        <div class="nl-transform"><div><?=__('全部课程', 'nlyd-student')?></div></div>
                    </ul>
                    <div class="layui-tab-content">
                        <!-- 全部课程 -->
                        <div class="layui-tab-item layui-show">
                            <ul class="flow-default layui-row" id="1" style="margin:0">
                               
                            </ul>
                        </div>
                        <!-- 近期课程 -->
                        <div class="layui-tab-item">
                            <ul class="flow-default layui-row" id="2" style="margin:0">
                              
                            </ul>
                        </div>
                        <!-- 往期课程 -->
                        <div class="layui-tab-item">
                            <ul class="flow-default layui-row" id="3" style="margin:0">
                                
                            </ul>
                        </div>
                    </div>
                </div>    
            </div>
        </div>  
        <?php }else{ ?>
        <style>
            @media screen and (max-width: 1199px){
                #page {
                    top: 0;
                }
            }

        </style>
            <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper layui-bg-white">
                <div class="layui-row nl-border nl-content">
                    <div class="no-info-page">
                        <div class="no-info-img">
                            <img src="<?=student_css_url.'image/noInfo/noMatch1042@2x.png'?>">
                        </div>
                        <p class="no-info-text"><?=__('无课程信息', 'nlyd-student')?></p>
                    </div>
                </div>
            </div>
        <?php } ?>  
        <a class="a-btn" href="<?=home_url('/zone/courseBuild/');?>"><?=__('发布新的课程', 'nlyd-student')?></a>   
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
        function pagation(id,match_page){
            flow.load({
                elem: '#'+id //流加载容器
                ,isAuto: false
                ,isLazyimg: true
                ,done: function(page, next){ //加载下一页
                    var postData={
                        action:'get_zone_course',
                        _wpnonce:$('#inputMatch').val(),
                        page:match_page,
                        match_type:'',
                    }
                    if(parseInt(id)==1){//全部报名
                        postData['course_type']="all";
                    }else if(parseInt(id)==2){//课程
                        postData['course_type']="matching";
                    }else{//往期
                        postData['course_type']="history";
                    }
                    var lis = [];
                    $.ajax({
                        data: postData,
                        success:function(res,ajaxStatu,xhr){
                            console.log(res,postData)
                            match_page++
                            isClick[id]=true
                            if(res.success){
                                $.each(res.data.info,function(i,v){
                                    // 已结束-3
                                    // 等待开课-2
                                    // 未开始-1
                                    // 报名中1
                                    // 进行中2
                                    var dom='<li class="match_row">'
                                                +'<div class="match_header bold c_black f_16 mt_10">'+v.course_title+'</div>'
                                                +'<div class="match_body">'
                                                    +'<div class="match_body_row">'
                                                        +'<div class="match_body_label"><?=__('开课日期：', 'nlyd-student')?></div>'
                                                        +'<div class="match_body_info c_black">'+v.start_time+' <span class="c_blue ml_10">'+v.status_cn+'</span></div>'
                                                    +'</div>'
                                                    +'<div class="match_body_row">'
                                                        +'<div class="match_body_label"><?=__('授课教练', 'nlyd-student')?></div>'
                                                        +'<div class="match_body_info c_black">'+v.real_name+'</div>'
                                                    +'</div>'
                                                    +'<div class="match_body_row">'
                                                        +'<div class="match_body_label"><?=__('开放名额', 'nlyd-student')?></div>'
                                                        +'<div class="match_body_info c_black">'+v.open_quota+'</div>'
                                                    +'</div>'
                                                    +'<div class="match_body_row">'
                                                        +'<div class="match_body_label"><?=__('已占名额', 'nlyd-student')?></div>'
                                                        +'<div class="match_body_info c_black">'+v.entry_total+' <a href="'+window.home_url+'/zone/courseStudent/id/'+v.id+'" class="c_blue ml_10">查看</a></div>'
                                                    +'</div>'
                                                +'</div>'
                                                +'<div class="match_footer flex-h">'
                                                    +'<div class="edit_match c_black6 flex1 ta_l get_sign_code"></div>'
                                                    +'<div class="edit_match c_black6 flex1 ta_c"></div>'
                                                    +'<a href="'+window.home_url+'/zone/courseBuild/id/'+v.id+'" class="edit_match c_black6">'
                                                        +'<div class="zone_bg bg_edit dis_inlineBlock"></div>'
                                                        +'<span class=" dis_inlineBlock"> <?=__("编辑课程", "nlyd-student")?></span>'
                                                    +'</a>'
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
                        },
                        complete:function(XMLHttpRequest, textStatus){
							if(textStatus=='timeout'){
								$.alerts('<?=__('网络质量差,请重试', 'nlyd-student')?>')
								next(lis.join(''),true)
							}
                        }
                    })       
                }
            });
        }
        var isClick={}
    //    if(location.hash.replace(/^#matchList=/, '').length==0){
    //        //获取hash来切换选项卡，假设当前地址的hash为lay-id对应的值
    //        location.hash = 'matchList='+ <?=$anchor?>;
    //    }
        // var layid = location.hash.replace(/^#matchList=/, '');
     
        // element.tabChange('matchList', layid);
        pagation($('.layui-this').attr('lay-id'),1)
        var lefts=$('.layui-this').position().left;
        $('.nl-transform').css({
            'transform':'translate3d('+lefts+'px, 0, 0px)'
        })
        element.on('tab(matchList)', function(){//matchList
            // location.hash = 'matchList='+ $(this).attr('lay-id');
            var html=$(this).html();
            var left=$(this).position().left+parseInt($(this).css('marginLeft'));
            var id=$(this).attr('lay-id')
            $('.nl-transform').css({
                'transform':'translate3d('+left+'px, 0px, 0px)'
            }).html(html)
            if(!isClick[id]){
                pagation(id,1)
            }
        });
    })
})
</script>