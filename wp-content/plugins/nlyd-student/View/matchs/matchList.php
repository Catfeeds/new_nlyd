

<div class="nl-foot-nav">
    <a class="nl-foot-item" href="<?=home_url();?>">
        <div class="nl-foot-icon"><i class="iconfont">&#xe62d;</i></div>
        <div class="nl-foot-name">首页</div>
    </a>
    <a class="nl-foot-item" href="<?=home_url('/account/matchList');?>">
        <div class="nl-foot-icon"><i class="iconfont">&#xe631;</i></div>
        <div class="nl-foot-name">训练</div>
    </a>
    <a class="nl-foot-item active" href="<?=home_url('/account/matchList');?>">
        <div class="nl-foot-icon"><i class="iconfont">&#xe692;</i></div>
        <div class="nl-foot-name">比赛</div>
    </a>
    <a class="nl-foot-item">
        <div class="nl-foot-icon"><i class="iconfont">&#xe630;</i></div>
        <div class="nl-foot-name">考级</div>
    </a>
    <a class="nl-foot-item" href="<?=home_url('student/account')?>">
        <div class="nl-foot-icon"><i class="iconfont">&#xe632;</i></div>
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
            <!-- <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback">
                    <i class="iconfont">&#xe610;</i>
                </a>
                <h1 class="mui-title">比赛列表</h1>
            </header> -->
                <div class="layui-row nl-border nl-content">
                    <div class="layui-tab layui-tab-brief" lay-filter="tabs" style="margin:0">
                        <ul style="margin-left: 0" class="mui-bar mui-bar-nav layui-tab-title">
                            <li class="layui-this">近期比赛</li>
                            <li>往期比赛</li>
                            <div class="nl-transform" data-y="-5">近期比赛</div>
                        </ul>
                        <div class="layui-tab-content width-margin width-margin-pc">
                            <!-- 近期比赛 -->
                            <div class="layui-tab-item layui-show">
                                <div class="countdown-time"><i class="iconfont">&#xe685;</i>&nbsp;&nbsp;最新比赛倒计时
                                    <span class="getTime" id="getTime">00:00:00</span>        
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
            ,scrollElem: '#flow-match1' //滚动条所在元素，一般不用填，此处只是演示需要。
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
                $.post(window.admin_ajax,postData,function(res,ajaxStatu,xhr){
                        if(res.success){
                            $.each(res.data.info,function(i,v){
                                var isMe='';//标签
                                var match_status='';//比赛中高亮
                                var rightBtn='';   
                                if(v.user_id!=null){//我报名参加的赛事
                                    // isMe='<div class="nl-match-metal">我</div>'
                                    isMe='<div class="nl-badge"><i class="iconfont">&#xe608;</i></div>'
                                }
                                if(v.match_status==2){//比赛进行中
                                    match_status='orange';   
                                }
                                if(v.right_url.length>0 && (v.match_status==2 || v.user_id==null)){
                                    rightBtn='<div class="nl-match-button last-btn">'
                                                +'<a href="'+v.right_url+'">'+v.button_title+'</a>'
                                            +'</div>'
                                }
                                var end_time = new Date(v.entry_end_time).getTime();//月份是实际月份-1
                                var serverTimes=new Date(xhr.getResponseHeader('Date')).getTime()
                                var sys_second = (end_time-serverTimes)/1000;
                                var dom='<li class="layui-col-lg4 layui-col-md4 layui-col-sm12 layui-col-xs12">'
                                            +'<div class="nl-match">'
                                                // +isMe
                                                // +'<span class="nl-match-people">'+v.entry_total+'报名</span>'
                                                +'<div class="nl-match-header">'
                                                    +'<span class="nl-match-name">'+v.post_title+'</span>'
                                                    // +'<span class="nl-match-type '+match_status+'">'+v.match_status_cn+'</span>'
                                                    +isMe
                                                    +'<p class="long-name">'+v.post_content+'</p>'
                                                +'</div>'
                                                +'<div class="nl-match-body">'
                                                    +'<div class="nl-match-detail">'
                                                        +'<span class="nl-match-label">开赛日期：</span>'
                                                        +'<span class="nl-match-info">'+v.match_start_time+'</span>'
                                                        +'<span class="nl-match-type '+match_status+'">'+v.match_status_cn+'</span>'
                                                    +'</div>'
                                                    +'<div class="nl-match-detail">'
                                                        +'<span class="nl-match-label">比赛地点：</span>'
                                                        +'<span class="nl-match-info">'+v.match_address+'</span>'
                                                    +'</div>'
                                                    +'<div class="nl-match-detail">'
                                                        +'<span class="nl-match-label">报名费用：</span>'
                                                        +'<span class="nl-match-info">¥'+v.match_cost+'</span>'
                                                    +'</div>'
                                                    +'<div class="nl-match-detail">'
                                                        +'<span class="nl-match-label">报名截止：</span>'
                                                        +'<span class="nl-match-info getTimes" data-seconds="'+sys_second+'">'
                                                        
                                                        +'报名结束</span>'
                                                    +'</div>'
                                                    +'<div class="nl-match-detail">'
                                                        +'<span class="nl-match-label">已报选手：</span>'
                                                        +'<span class="nl-match-info">'+v.entry_total+'人</span>'
                                                    +'</div>'
                                                +'</div>'

                                                +'<div class="nl-match-footer">'
                                                    // +'<a href="'+v.left_url+'" class="nl-match-button">查看详情</a>'
                                                    +'<div class="nl-match-button">'
                                                        +'<a href="'+v.left_url+'">查看详情</a>'
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
                            if(page==1){
                                var dom='<div class="no-info">近期比赛无记录</div>'
                                lis.push(dom) 
                            }else{
                                $.alerts('没有更多了')
                            }
                            next(lis.join(''),false)
                        }
                        $('.getTimes').countdown(function(s, d){//倒计时
                            var D=d.day>0 ? d.day : '';
                            var h=d.hour<10 ? '0'+d.hour : d.hour;
                            var m=d.minute<10 ? '0'+d.minute : d.minute;
                            var s=d.second<10 ? '0'+d.second : d.second;
                            var time=D+h+':'+m+':'+s;
                            $(this).text(time);
                        });
                })       
            }
        });
        flow.load({//往期比赛
            elem: '#flow-match2' //流加载容器
            ,scrollElem: '#flow-match2' //滚动条所在元素，一般不用填，此处只是演示需要。
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
                $.post(window.admin_ajax,postData,function(res){
                        if(res.success){
                            $.each(res.data.info,function(i,v){
                                var isMe='';//标签
                                var rightBtn='<div class="nl-match-button last-btn">'
                                                +'<a href="'+v.right_url+'">查看战绩</a>'
                                            +'</div>';   
                                if(v.user_id!=null){//我报名参加的赛事
                                    isMe='<div class="nl-badge"><i class="iconfont">&#xe608;</i></div>'
                                }
                                var dom='<li class="layui-col-lg4 layui-col-md4 layui-col-sm12 layui-col-xs12">'
                                            +'<div class="nl-match">'
                                                // +'<span class="nl-match-people">'+v.entry_total+'报名</span>'
                                                +'<div class="nl-match-header">'
                                                    +'<span class="nl-match-name">'+v.post_title+'</span>'
                                                    // +'<span class="nl-match-type '+match_status+'">'+v.match_status_cn+'</span>'
                                                    +isMe
                                                    +'<p class="long-name">'+v.post_title+'</p>'
                                                +'</div>'
                                                +'<div class="nl-match-body">'
                                                    +'<div class="nl-match-detail">'
                                                        +'<span class="nl-match-label">开赛日期：</span>'
                                                        +'<span class="nl-match-info">'+v.match_start_time+'</span>'
                                                        +'<span class="nl-match-type">'+v.match_status_cn+'</span>'
                                                    +'</div>'
                                                    +'<div class="nl-match-detail">'
                                                        +'<span class="nl-match-label">比赛地点：</span>'
                                                        +'<span class="nl-match-info">'+v.match_address+'</span>'
                                                    +'</div>'
                                                    +'<div class="nl-match-detail">'
                                                        +'<span class="nl-match-label">报名费用：</span>'
                                                        +'<span class="nl-match-info">¥'+v.match_cost+'</span>'
                                                    +'</div>'
                                                    +'<div class="nl-match-detail">'
                                                        +'<span class="nl-match-label">已报选手：</span>'
                                                        +'<span class="nl-match-info">'+v.entry_total+'人</span>'
                                                    +'</div>' 
                                                +'</div>'

                                                +'<div class="nl-match-footer">'
                                                    +'<div class="nl-match-button">'
                                                        +'<a href="'+v.left_url+'">查看详情</a>'
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
                            if(page==1){
                                var dom='<div class="no-info">往期比赛无记录</div>'
                                lis.push(dom) 
                            }else{
                                $.alerts('没有更多了')
                            }
                            next(lis.join(''),false)
                        }
                }) 
            }
        });
    })
})
</script>