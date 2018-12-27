<?php require_once PLUGINS_PATH.'nlyd-student/View/public/student-footer-menu.php' ;?>

<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>

        <?php if($row){ ?>
            <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper have-footer">
                <div class="layui-row nl-border nl-content">
                    <div class="layui-tab layui-tab-brief" lay-filter="matchList" style="margin:0">
                        <ul style="margin-left:0;padding:0" class="mui-bar mui-bar-nav layui-tab-title">
                            <li class="layui-this" lay-id="1">
                                <?=__('报名中', 'nlyd-student')?>
                                <?php
                                if($entry_is_true > 0){
                                    echo '('.$entry_is_true.')';
                                }
                                ?>
                            </li>
                            <li lay-id="2">
                                <?=__('比赛中', 'nlyd-student')?>
                                <?php
                                if($match_is_true > 0){
                                    echo '('.$match_is_true.')';
                                }
                                ?>
                            </li>
                            <li lay-id="3"><?=__('往期比赛', 'nlyd-student')?></li>
                            <div class="nl-transform" data-y="-5"><?=__('近期比赛', 'nlyd-student')?></div>
                        </ul>
                        <div class="layui-tab-content width-margin width-margin-pc">
                            <!-- 报名中 -->
                            <div class="layui-tab-item layui-show">
                                <ul class="flow-default layui-row layui-col-space20" id="1" style="margin:0">
                                    
                                </ul>
                            </div>
                            <!-- 比赛中 -->
                            <div class="layui-tab-item">
                                <div class="countdown-time c_blue"><i class="iconfont">&#xe685;</i>&nbsp;&nbsp;<?=__('最新比赛倒计时', 'nlyd-student')?>
                                    <span class="getTime" id="getTimes">00:00:00</span>        
                                </div>
                                <ul class="flow-default layui-row layui-col-space20" id="2" style="margin:0">

                                </ul>
                            </div>
                            <!-- 往期比赛 -->
                            <div class="layui-tab-item">
                                <ul class="flow-default layui-row layui-col-space20" id="3" style="margin:0">

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
    </div>
</div>
<!-- 获取比赛列表 -->
<input type="hidden" name="_wpnonce" id="inputMatch" value="<?=wp_create_nonce('student_get_match_code_nonce');?>">
<!-- 获取最新比赛倒计时 -->
<input type="hidden" name="_wpnonce" id="inputNewMatch" value="<?=wp_create_nonce('student_get_count_down_code_nonce');?>">
<script>
    
jQuery(function($) { 
    if(window.wait_match == ''){
        $('.countdown-time').hide();
    }
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
                        action:'get_match_list',
                        _wpnonce:$('#inputMatch').val(),
                        page:match_page,
                        match_type:'',
                    }
                    if(parseInt(id)==1){//报名
                        postData['match_type']="signUp";
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
                                    var isMe='';//标签
                                    var match_status='c_blue';//比赛中高亮
                                    var rightBtn='';  
                                    var endTime="";//报名截止
                                    
                                    if(v.entry_end_time===null){
                                        var sys_second_text="-"
                                    }else{
                                        var domTime=v.entry_end_time.replace(/-/g,'/');
                                        var end_time = new Date(domTime).getTime();//月份是实际月份-1
                                        var serverTimes=new Date(xhr.getResponseHeader('Date')).getTime()
                                        var sys_second = (end_time-serverTimes)/1000;
                                        var sys_second_text=sys_second>0 ? '' :  "<?=__('报名结束', 'nlyd-student')?>";
                                    }
                                    var match_notice_url='';//参赛须知
                                    if(v.user_id!=null){//我报名参加的赛事
                                        isMe='<div class="nl-badge"><i class="iconfont">&#xe608;</i></div>'
                                    }
                                    if(v.match_status==2 || v.match_status==-2){//比赛进行中或等待开赛
                                        if(v.match_status==2){
                                            match_status='c_orange';  
                                        }
                                        if(v.user_id==null){//未报名（未登录）
                                            rightBtn=""
                                        }else{
                                            if(v.right_url.length>0){
                                                rightBtn='<div class="nl-match-button flex1 last-btn">'
                                                            +'<button type="button" href="'+v.right_url+'">'+v.button_title+'</button>'
                                                        +'</div>'
                                            }
                                        }
                                        endTime='<div class="nl-match-detail layui-row">'
                                                    +'<div class="nl-match-label"><div><?=__('报名截止', 'nlyd-student')?>:</div></div>'
                                                    +'<div class="nl-match-info">'
                                                        +'<span class="c_black getTimes'+id+'" data-seconds="'+sys_second+'">'
                                                        +sys_second_text+'</span>'
                                                    +'</div>'
                                                +'</div>'
                                    }else if(v.match_status==-3){//已结束
                                        rightBtn='<div class="nl-match-button flex1 last-btn">'
                                                    +'<button type="button" href="'+v.right_url+'"><?=__('查看战绩', 'nlyd-student')?></button>'
                                                +'</div>';   
                                    }else{
                                        if(v.right_url.length>0){
                                            rightBtn='<div class="nl-match-button flex1 last-btn">'
                                                        +'<button type="button" class="not_sign" data-id="'+v.ID+'" href="'+v.right_url+'">'+v.button_title+'</button>'
                                                    +'</div>'
                                            if(v.match_status==1 && v.user_id!=null){//报名中已报名
                                                rightBtn='<div class="nl-match-button flex1 last-btn">'
                                                            +'<button type="button" class="bg_gradient_grey"><?=__('已报名参赛', 'nlyd-student')?></button>'
                                                        +'</div>'
                                                
                                            }
                                        }
                                        endTime='<div class="nl-match-detail layui-row">'
                                                    +'<div class="nl-match-label"><div><?=__('报名截止', 'nlyd-student')?>:</div></div>'
                                                    +'<div class="nl-match-info">'
                                                        +'<span class="c_black getTimes'+id+'" data-seconds="'+sys_second+'">'
                                                        +sys_second_text+'</span>'
                                                    +'</div>'
                                                +'</div>'
                                    }
                                    if(v.match_notice_url && v.match_notice_url.length>0){//参赛须知
                                        match_notice_url='<a class="c_orange" style="margin-left:10px" href="'+v.match_notice_url+'"><?=__('参赛须知', 'nlyd-student')?></a>'
                                    }
                                    var onBtn="" ;
                                    if(rightBtn.length==0){
                                        onBtn="onBtn"
                                    }
                                    var dom='<li class="layui-col-lg4 layui-col-sm12 layui-col-xs12 layui-col-md12">'
                                                +'<div class="nl-match">'
                                                    +'<div class="nl-match-header">'
                                                        +'<span class="nl-match-name fs_16 c_blue">'+v.post_title+'</span>'
                                                        +isMe
                                                        +'<p class="long-name fs_12 c_black8">'+v.post_content+'</p>'
                                                    +'</div>'
                                                    +'<div class="nl-match-body">'
                                                        +'<div class="nl-match-detail layui-row">'
                                                            +'<div class="nl-match-label"><div><?=__('比赛类型', 'nlyd-student')?>:</div></div>'
                                                            +'<div class="nl-match-info">'
                                                            +'<span class="c_black">'+v.role_name+'</span>'
                                                            +'</div>'
                                                        +'</div>'
                                                        +'<div class="nl-match-detail layui-row">'
                                                            +'<div class="nl-match-label"><div><?=__('开赛日期', 'nlyd-student')?>:</div></div>'
                                                            +'<div class="nl-match-info">'
                                                                +'<span class="c_black">'+v.match_start_time+'</span>'
                                                                +'<span class="nl-match-type '+match_status+'">'+v.match_status_cn+'</span>'
                                                            +'</div>'
                                                        +'</div>'
                                                        +'<div class="nl-match-detail layui-row">'
                                                            +'<div class="nl-match-label"><div><?=__('比赛地点', 'nlyd-student')?>:</div></div>'
                                                            +'<div class="nl-match-info">'
                                                                +'<span class="c_black">'+v.match_address+'</span>'
                                                            +'</div>'
                                                        +'</div>'
                                                        +'<div class="nl-match-detail layui-row">'
                                                            +'<div class="nl-match-label"><div><?=__('报名费用', 'nlyd-student')?>:</div></div>'
                                                            +'<div class="nl-match-info">'
                                                                +'<span class="c_black">¥'+v.match_cost+'</span>'
                                                            +'</div>'
                                                        +'</div>'
                                                        +endTime
                                                        +'<div class="nl-match-detail layui-row">'
                                                            +'<div class="nl-match-label"><div><?=__('已报选手', 'nlyd-student')?>:</div></div>'
                                                            +'<div class="nl-match-info">'
                                                                +'<span class="c_black">'+v.entry_total+'<?=__('人', 'nlyd-student')?></span>'
                                                                +match_notice_url
                                                            +'</div>'
                                                        +'</div>'
                                                    +'</div>'
                                                    +'<div class="nl-match-footer flex-h">'
                                                        +'<div class="nl-match-button flex1">'
                                                            +'<button type="button" class="'+onBtn+'" href="'+v.left_url+'"><?=__('查看详情', 'nlyd-student')?></button>'
                                                        +'</div>'
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
                            }
                        
                            $('.getTimes'+id).countdown(function(S, d){//倒计
                                    var D=d.day>0 ? d.day+'<?=__('天', 'nlyd-student')?>' : '';
                                    var h=d.hour<10 ? '0'+d.hour : d.hour;
                                    var m=d.minute<10 ? '0'+d.minute : d.minute;
                                    var s=d.second<10 ? '0'+d.second : d.second;
                                    var time=D+h+':'+m+':'+s;
                                    $(this).text(time);
                                if(S==0){
                                    $(this).text("<?=__('报名结束', 'nlyd-student')?>");
                                    setTimeout(function() {
                                        window.location.reload()  
                                    }, 1000);
                                }
                            });
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
       if(location.hash.replace(/^#matchList=/, '').length==0){
           //获取hash来切换选项卡，假设当前地址的hash为lay-id对应的值
           location.hash = 'matchList='+ <?=$anchor?>;
       }
        var layid = location.hash.replace(/^#matchList=/, '');
     
        element.tabChange('matchList', layid);
        pagation($('.layui-this').attr('lay-id'),1)
        var lefts=$('.layui-this').position().left;
        $('.nl-transform').css({
            'transform':'translate3d('+lefts+'px, -5px, 0px)'
        })
        element.on('tab(matchList)', function(){//matchList
            location.hash = 'matchList='+ $(this).attr('lay-id');
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