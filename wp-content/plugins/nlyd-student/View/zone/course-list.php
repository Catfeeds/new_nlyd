<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>

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
                    <a class="a-btn" href="<?=home_url('/zone/courseBuild/');?>"><?=__('发布新的课程', 'nlyd-student')?></a>
                </div>    
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
        $('body').on('click','.get_sign_code',function(){
            var _this=$(this);
            var course_id=_this.attr('data-id');
            var course_name=_this.attr('data-name');
            if(!_this.hasClass('disabled')){
                var post_data={
                    action:'course_sign_code',
                    course_id:course_id,
                }
                $.ajax({
                    data: post_data,
                    beforeSend:function(XMLHttpRequest){
                        _this.addClass('disabled')
                    },
                    success: function(res, textStatus, jqXHR){
                        if(res.success){
                            if(res.data && res.data.length>0){
                                var url=res.data;
                                var json={
                                    "title": "<?=__('签到二维码', 'nlyd-student')?>", //相册标题
                                    "id": course_id, //相册id
                                    "start": 0, //初始显示的图片序号，默认0
                                    "data": [   //相册包含的图片，数组格式
                                        {
                                        "alt": "<?=__('签到二维码', 'nlyd-student')?>",
                                        "pid": course_id, //图片id
                                        "src": url, //原图地址
                                        "thumb": url //缩略图地址
                                        }
                                    ]
                                    }
                                layer.photos({//图片预览
                                    photos: json,
                                    anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
                                })
                                setTimeout(function(){
                                    var _layerImg=$('.layui-layer-phimg');
                                    var _tips='<div class="ta_c c_white" style="position: absolute;width: 100%;bottom: 40px;"><?=__("长按下载图片", "nlyd-student")?></div>'
                                    $('.layui-layer-shade').append(_tips);
                                    new AlloyFinger(_layerImg[0], {
                                        longTap:function(){
                                            savaFile(url,course_name+"<?=__('签到二维码', 'nlyd-student')?>")
                                        }
                                    })
                                },100)
                            }
                        }
                        _this.removeClass('disabled');
                    },
                    complete: function(jqXHR, textStatus){
                        if(textStatus=='timeout'){
                            $.alerts("<?=__('网络质量差', 'nlyd-student')?>")
                            _this.removeClass('disabled');
                        }
                        
                    }
                })
            }else{
                $.alerts("<?=__('正在处理您的请求..', 'nlyd-student')?>")
            }
        })
        function pagation(id,match_page){
            flow.load({
                elem: '#'+id //流加载容器
                ,isAuto: false
                ,isLazyimg: true
                ,done: function(page, next){ //加载下一页
                    var postData={
                        action:'get_zone_course',
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
                                                        +'<div class="match_body_info c_black"><span class="c_blue">'+v.entry_total+'</span>/'+v.open_quota+ '<a href="'+window.home_url+'/zone/courseStudent/id/'+v.id+'" class="c_blue ml_10">查看</a></div>'
                                                    +'</div>'
                                                +'</div>'
                                                +'<div class="match_footer flex-h">'
                                                    +'<a data-id="'+v.id+'" data-name="'+v.course_title+'" class="edit_match c_black6 flex1 ta_l get_sign_code">'
                                                        +'<div class="zone_bg bg_qr_code dis_inlineBlock"></div>'
                                                        +'<span class=" dis_inlineBlock"> <?=__("查看二维码", "nlyd-student")?></span>'
                                                    +'</a>'
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