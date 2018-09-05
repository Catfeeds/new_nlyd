

<div class="nl-foot-nav">
    <a class="nl-foot-item" href="<?=home_url('/student/index');?>">
        <div class="nl-foot-icon">
            <div class="footer-home"></div>
        </div>
        <div class="nl-foot-name">首页</div>
    </a>
    <a class="nl-foot-item disabled_a" href="<?=home_url('/account/matchList');?>">
        <div class="nl-foot-icon">
            <div class="footer-train"></div>
        </div>
        <div class="nl-foot-name">训练</div>
    </a>
    <a class="nl-foot-item active" href="<?=home_url('/matchs');?>">
        <div class="nl-foot-icon">
            <div class="footer-match"></div>
        </div>
        <div class="nl-foot-name">比赛</div>
    </a>
    <a class="nl-foot-item disabled_a">
        <div class="nl-foot-icon">
            <div class="footer-kaoji"></div>
        </div>
        <div class="nl-foot-name">考级</div>
    </a>
    <a class="nl-foot-item" href="<?=home_url('account')?>">
        <div class="nl-foot-icon">
            <div class="footer-user"></div>
        </div>
        <div class="nl-foot-name">我的</div>
    </a>
</div>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>

        <?php if($row){ ?>
            <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper have-footer">
                <div class="layui-row nl-border nl-content">
                    <div class="layui-tab layui-tab-brief" lay-filter="tabs" style="margin:0">
                        <ul style="margin-left:0;padding:0" class="mui-bar mui-bar-nav layui-tab-title">
                            <li class="layui-this">近期比赛</li>
                            <li>往期比赛</li>
                            <div class="nl-transform" data-y="-5">近期比赛</div>
                        </ul>
                        <div class="layui-tab-content width-margin width-margin-pc">
                            <!-- 近期比赛 -->
                            <div class="layui-tab-item layui-show">
                                <div class="countdown-time c_blue"><i class="iconfont">&#xe685;</i>&nbsp;&nbsp;最新比赛倒计时
                                    <span class="getTime" id="getTimes">00:00:00</span>        
                                </div>
                                <ul class="flow-default layui-row layui-col-space20" id="flow-match1">
                                    
                                </ul>
                            </div>
                            <!-- 往期比赛 -->
                            <div class="layui-tab-item">
                                <ul class="flow-default layui-row layui-col-space20" id="flow-match2" style="margin:0">

                                </ul>
                            </div>
                        </div>
                    </div>    
                </div>
            </div>  
        <?php }else{ ?>
            <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper layui-bg-white">
                <div class="layui-row nl-border nl-content">
                    <div class="no-info-page">
                        <div class="no-info-img">
                            <img src="<?=student_css_url.'image/noInfo/noMatch1042@2x.png'?>">
                        </div>
                        <p class="no-info-text">无比赛信息</p>
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
    layui.use(['element','flow'], function(){
        var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
        var flow = layui.flow;//流加载
        element.on('tab(tabs)', function(){//tabs
            var left=$(this).position().left;
            $('.nl-transform').css({
                'transform':'translate3d('+left+'px, -5px, 0px)'
            })
        });
        flow.load({
            elem: '#flow-match1' //流加载容器
            ,isAuto: false
            ,isLazyimg: true
            ,done: function(page, next){ //加载下一页
                var postData={
                    action:'get_match_list',
                    _wpnonce:$('#inputMatch').val(),
                    page:page,
                    match_type:'now',
                }
                var lis = [];
                $.post(window.admin_ajax+"?date="+new Date().getTime(),postData,function(res,ajaxStatu,xhr){
                        if(res.success){
                            $.each(res.data.info,function(i,v){
                                var isMe='';//标签
                                var match_status='c_blue';//比赛中高亮
                                var rightBtn='';  
                                if(v.user_id!=null){//我报名参加的赛事
                                    // isMe='<div class="nl-match-metal">我</div>'
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
                                            rightBtn='<div class="nl-match-button last-btn">'
                                                        +'<a href="'+v.right_url+'">'+v.button_title+'</a>'
                                                    +'</div>'
                                        }
                                    }
                                }else{
                                    if(v.right_url.length>0){
                                        rightBtn='<div class="nl-match-button last-btn">'
                                                    +'<a href="'+v.right_url+'">'+v.button_title+'</a>'
                                                +'</div>'
                                        if(v.match_status==1 && v.user_id!=null){//报名中已报名
                                            rightBtn='<div class="nl-match-button last-btn">'
                                                        +'<a class="bg_gradient_grey">已报名参赛</a>'
                                                    +'</div>'
                                            
                                        }
                                    }
                                }
                                var onBtn="" ;
                                if(rightBtn.length==0){
                                    onBtn="onBtn"
                                }
                                var domTime=v.entry_end_time.replace(/-/g,'/');
                                var end_time = new Date(domTime).getTime();//月份是实际月份-1
                                var serverTimes=new Date(xhr.getResponseHeader('Date')).getTime()
                                var sys_second = (end_time-serverTimes)/1000;
                                var sys_second_text=sys_second>0 ? '' :  "报名结束";
                                var dom='<li class="layui-col-lg4 layui-col-md4 layui-col-sm12 layui-col-xs12">'
                                            +'<div class="nl-match">'
                                                +'<div class="nl-match-header">'
                                                    +'<span class="nl-match-name fs_16 c_blue">'+v.post_title+'</span>'
                                                    +isMe
                                                    +'<p class="long-name fs_12 c_black3">'+v.post_content+'</p>'
                                                +'</div>'
                                                +'<div class="nl-match-body">'
                                                    +'<div class="nl-match-detail">'
                                                        +'<span>开赛日期：</span>'
                                                        +'<span class="c_black">'+v.match_start_time+'</span>'
                                                        +'<span class="nl-match-type '+match_status+'">'+v.match_status_cn+'</span>'
                                                    +'</div>'
                                                    +'<div class="nl-match-detail">'
                                                        +'<span>比赛地点：</span>'
                                                        +'<span class="c_black">'+v.match_address+'</span>'
                                                    +'</div>'
                                                    +'<div class="nl-match-detail">'
                                                        +'<span>报名费用：</span>'
                                                        +'<span class="c_black">¥'+v.match_cost+'</span>'
                                                    +'</div>'
                                                    +'<div class="nl-match-detail">'
                                                        +'<span>报名截止：</span>'
                                                        +'<span class="c_black getTimes" data-seconds="'+sys_second+'">'
                                                        
                                                        +sys_second_text+'</span>'
                                                    +'</div>'
                                                    +'<div class="nl-match-detail">'
                                                        +'<span>已报选手：</span>'
                                                        +'<span class="c_black">'+v.entry_total+'人</span>'
                                                    +'</div>'
                                                +'</div>'

                                                +'<div class="nl-match-footer">'
                                                    +'<div class="nl-match-button">'
                                                        +'<a class="'+onBtn+'" href="'+v.left_url+'">查看详情</a>'
                                                    +'</div>'
                                                    +rightBtn
                                                +'</div>'
                                            +'</div>'
                                        +'</li>'
                                lis.push(dom) 
                            })
                            if (res.data.info.length<10) {
                                next(lis.join(''),false) 
                            }else{
                                next(lis.join(''),true) 
                            }
                            
                        }else{
                            // if(page==1){
                            //     var dom='<div class="no-info">近期比赛无记录</div>'
                            //     lis.push(dom) 
                            // }else{
                            //     $.alerts('没有更多了')
                            // }
                            next(lis.join(''),false)
                        }
                        $('.getTimes').countdown(function(S, d){//倒计时
                            if(S>0){
                                var D=d.day>0 ? d.day+'天' : '';
                                var h=d.hour<10 ? '0'+d.hour : d.hour;
                                var m=d.minute<10 ? '0'+d.minute : d.minute;
                                var s=d.second<10 ? '0'+d.second : d.second;
                                var time=D+h+':'+m+':'+s;
                                $(this).text(time);
                            }else{
                                $(this).text("报名结束");
                            }
                        });
                })       
            }
        });
        flow.load({//往期比赛
            elem: '#flow-match2' //流加载容器
            ,isAuto: false
            ,isLazyimg: true
            ,done: function(page, next){ //加载下一页
                var postData={
                    action:'get_match_list',
                    _wpnonce:$('#inputMatch').val(),
                    page:page,
                    match_type:'history',
                }
                var lis = [];
                $.post(window.admin_ajax+"?date="+new Date().getTime(),postData,function(res){
                        if(res.success){
                            $.each(res.data.info,function(i,v){
                                var isMe='';//标签
                                var rightBtn='<div class="nl-match-button last-btn">'
                                                +'<a href="'+v.right_url+'">查看战绩</a>'
                                            +'</div>';   
                                if(v.user_id!=null){//我报名参加的赛事
                                    isMe='<div class="nl-badge"><i class="iconfont">&#xe608;</i></div>'
                                }
                                var onBtn="" ;
                                if(rightBtn.length==0){
                                    onBtn="onBtn"
                                }
                                var dom='<li class="layui-col-lg4 layui-col-md4 layui-col-sm12 layui-col-xs12">'
                                            +'<div class="nl-match">'
                                                +'<div class="nl-match-header">'
                                                    +'<span class="nl-match-name  fs_16 c_blue">'+v.post_title+'</span>'
                                                    +isMe
                                                    +'<p class="long-name fs_12 c_black3">'+v.post_title+'</p>'
                                                +'</div>'
                                                +'<div class="nl-match-body">'
                                                    +'<div class="nl-match-detail">'
                                                        +'<span>开赛日期：</span>'
                                                        +'<span class="c_black">'+v.match_start_time+'</span>'
                                                        +'<span class="nl-match-type c_blue">'+v.match_status_cn+'</span>'
                                                    +'</div>'
                                                    +'<div class="nl-match-detail">'
                                                        +'<span>比赛地点：</span>'
                                                        +'<span class="c_black">'+v.match_address+'</span>'
                                                    +'</div>'
                                                    +'<div class="nl-match-detail">'
                                                        +'<span>报名费用：</span>'
                                                        +'<span class="c_black">¥'+v.match_cost+'</span>'
                                                    +'</div>'
                                                    +'<div class="nl-match-detail">'
                                                        +'<span>已报选手：</span>'
                                                        +'<span class="c_black">'+v.entry_total+'人</span>'
                                                    +'</div>' 
                                                +'</div>'

                                                +'<div class="nl-match-footer">'
                                                    +'<div class="nl-match-button">'
                                                        +'<a class="'+onBtn+'"  href="'+v.left_url+'">查看详情</a>'
                                                    +'</div>'
                                                    +rightBtn
                                                +'</div>'
                                            +'</div>'
                                        +'</li>'
                                lis.push(dom) 
                            })
                            if (res.data.info.length<10) {
                                next(lis.join(''),false) 
                            }else{
                                next(lis.join(''),true) 
                            }
                        }else{
                            // if(page==1){
                            //     var dom='<div class="no-info">往期比赛无记录</div>'
                            //     lis.push(dom) 
                            // }else{
                            //     $.alerts('没有更多了')
                            // }
                            next(lis.join(''),false)
                        }
                }) 
            }
        });
    })
})
</script>