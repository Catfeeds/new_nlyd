<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>

        <?php if(!$row){ ?>
            <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback static" href="<?=home_url('/zone/')?>">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('比赛管理', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content match_tabs have-bottom">
                <div class="layui-tab layui-tab-brief width-padding width-padding-pc" lay-filter="matchList" style="margin-top:20px">
                    <ul style="margin:0;padding:0" class="layui-tab-title">
                        <li class="layui-this" lay-id="1">
                            <div><?=__('全部比赛', 'nlyd-student')?></div>
                        </li>
                        <li lay-id="2">
                        <div><?=__('近期比赛', 'nlyd-student')?></div>
                        </li>
                        <li lay-id="3"><div><?=__('往期比赛', 'nlyd-student')?></div></li>
                        <div class="nl-transform"><div><?=__('全部比赛', 'nlyd-student')?></div></div>
                    </ul>
                    <div class="layui-tab-content">
                        <!-- 全部比赛 -->
                        <div class="layui-tab-item layui-show">
                            <ul class="flow-default layui-row" id="1" style="margin:0">
                               
                            </ul>
                        </div>
                        <!-- 近期比赛 -->
                        <div class="layui-tab-item">
                            <ul class="flow-default layui-row" id="2" style="margin:0">
                              
                            </ul>
                        </div>
                        <!-- 往期比赛 -->
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
                        <p class="no-info-text"><?=__('无比赛信息', 'nlyd-student')?></p>
                    </div>
                </div>
            </div>
        <?php } ?>  
        <a class="a-btn" href="<?=home_url('/zone/matchBuild/');?>"><?=__('发布新的比赛', 'nlyd-student')?></a>   
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
  function savaFile(data,filename)
    {
        var save_link=document.createElementNS('http://www.w3.org/1999/xhtml', 'a');
        save_link.href=data;
        save_link.download=filename;
        var event=document.createEvent('MouseEvents');
        event.initMouseEvent('click',true,false,window,0,0,0,0,0,false,false,false,false,0,null);
        save_link.dispatchEvent(event);
    };
    layui.use(['element','flow','layer'], function(){
        var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
        var flow = layui.flow;//流加载
        $('body').on('click','.get_sign_code',function(){
            var _this=$(this);
            var match_id=_this.attr('data-id');
            var match_name=_this.attr('data-name');
            if(!_this.hasClass('disabled')){
                var post_data={
                    action:'match_sign_code',
                    match_id:match_id,
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
                                    "id": match_id, //相册id
                                    "start": 0, //初始显示的图片序号，默认0
                                    "data": [   //相册包含的图片，数组格式
                                        {
                                        "alt": "<?=__('签到二维码', 'nlyd-student')?>",
                                        "pid": match_id, //图片id
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
                                            savaFile(url,match_name+"<?=__('签到二维码', 'nlyd-student')?>")
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
                        action:'get_zone_match_list',
                        _wpnonce:$('#inputMatch').val(),
                        page:match_page,
                        match_type:'',
                    }
                    if(parseInt(id)==1){//全部报名
                        postData['match_type']="all";
                    }else if(parseInt(id)==2){//比赛
                        postData['match_type']="matching";
                    }else{//往期
                        postData['match_type']="history";
                    }
                    var lis = [];
                    $.ajax({
                        data: postData,
                        success:function(res,ajaxStatu,xhr){
                            console.log(res)
                            match_page++
                            isClick[id]=true
                            if(res.success){
                                $.each(res.data.info,function(i,v){
                                    // 已结束-3
                                    // 等待开赛-2
                                    // 未开始-1
                                    // 报名中1
                                    // 进行中2
                                    var dom='<li class="match_row">'
                                                +'<div class="match_header bold c_black f_16 mt_10">'+v.post_title+'</div>'
                                                +'<div class="match_body">'
                                                    +'<div class="match_body_row">'
                                                        +'<div class="match_body_label"><?=__("比赛类型：", "nlyd-student")?></div>'
                                                        +'<div class="match_body_info c_black">'+v.role_name+'</div>'
                                                    +'</div>'
                                                    +'<div class="match_body_row">'
                                                        +'<div class="match_body_label"><?=__("报名截止：", "nlyd-student")?></div>'
                                                        +'<div class="match_body_info c_black">'+v.entry_end_time+'</div>'
                                                    +'</div>'
                                                    +'<div class="match_body_row">'
                                                        +'<div class="match_body_label"><?=__("开赛日期：", "nlyd-student")?></div>'
                                                        +'<div class="match_body_info c_black">'+v.match_start_time+' <span class="c_blue ml_10">'+v.match_status_cn+'</span></div>'
                                                    +'</div>'
                                                    +'<div class="match_body_row">'
                                                        +'<div class="match_body_label"><?=__("开赛地点：", "nlyd-student")?></div>'
                                                        +'<div class="match_body_info c_black">'+v.match_address+'</div>'
                                                    +'</div>'
                                                    +'<div class="match_body_row">'
                                                        +'<div class="match_body_label"><?=__("比赛费用：", "nlyd-student")?></div>'
                                                        +'<div class="match_body_info c_black">'+v.match_cost+'</div>'
                                                    +'</div>'
                                                    +'<div class="match_body_row">'
                                                        +'<div class="match_body_label"><?=__("已报人数：", "nlyd-student")?></div>'
                                                        +'<div class="match_body_info c_black">'+v.entry_total+'<a class="c_red fs_12 ml_10" href="'+window.home_url+'/zone/matchSignDetail/match_id/'+v.match_id+'"><?=__("学员报名/签到情况", "nlyd-student")?></a></div>'
                                                    +'</div>'
                                                +'</div>'
                                                +'<div class="match_footer flex-h">'
                                                    +'<a data-id="'+v.match_id+'" data-name="'+v.post_title+'" class="edit_match c_black6 flex1 ta_l get_sign_code">'
                                                        +'<div class="zone_bg bg_qr_code dis_inlineBlock"></div>'
                                                        +'<span class=" dis_inlineBlock"> <?=__("获取签到码", "nlyd-student")?></span>'
                                                    +'</a>'
                                                    +'<a href="'+window.home_url+'/zone/matchTime/match_id/'+v.match_id+'" class="edit_match c_black6 flex1 ta_c">'
                                                        +'<div class="zone_bg bg_time dis_inlineBlock"></div>'
                                                        +'<span class=" dis_inlineBlock"> <?=__("时间表", "nlyd-student")?></span>'
                                                    +'</a>'
                                                    +'<a href="'+window.home_url+'/zone/matchBuild/match_id/'+v.match_id+'" class="edit_match c_black6 flex1 ta_r">'
                                                        +'<div class="zone_bg bg_edit dis_inlineBlock"></div>'
                                                        +'<span class=" dis_inlineBlock"> <?=__("编辑比赛", "nlyd-student")?></span>'
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