
<div class="layui-fluid">
    <div class="layui-row">

        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>

        <?php if($row){ ?>
            <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback static" onclick="window.location.href = '<?=home_url('account')?>' ">
                    <i class="iconfont">&#xe610;</i>
                </a>
                <h1 class="mui-title">我的比赛</h1>
            </header>
                <div class="layui-row nl-border nl-content">
                    <div class="layui-tab layui-tab-brief width-margin width-margin-pc" lay-filter="tabs">
                        <div class="layui-tab-content" style="padding: 0;">
                            <div class="layui-tab-item layui-show">
                                <ul class="flow-default layui-row layui-col-space20" id="flow-list">

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php }else{ ?>
            <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper layui-bg-white">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback">
                    <i class="iconfont">&#xe610;</i>
                </a>
                <h1 class="mui-title">我的比赛</h1>
            </header>    
                <div class="layui-row nl-border nl-content">
                    <div class="no-info-page">
                        <div class="no-info-img">
                            <img src="<?=student_css_url.'image/noInfo/noMatch1042@2x.png'?>">
                        </div>
                        <p class="no-info-text">您暂未参加任何比赛</p>
                        <a class="a-btn" href="<?=home_url('/account/matchList');?>">看看最近比赛</a>
                    </div>
                </div>
            </div>
        <?php } ?>
       
    </div>
</div>

<script>
jQuery(function($) { 
layui.use(['element','flow'], function(){
    var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
    var flow = layui.flow;//流加载
    flow.load({
        elem: '#flow-list' //流加载容器
        ,scrollElem: '#flow-list' //滚动条所在元素，一般不用填，此处只是演示需要。
        ,isAuto: false
        ,isLazyimg: true
        ,done: function(page, next){ //加载下一页
            var postData={
                action:'get_my_match_list',
                page:page
            }
            var lis = [];
            $.post(window.admin_ajax,postData,function(res,ajaxStatu,xhr){
                        if(res.success){
                            $.each(res.data.info,function(i,v){
                                var isMe='';//标签
                                var match_status='';//比赛中高亮
                                var rightBtn='';   
                                if(v.user_id!=null){//我报名参加的赛事
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
                                                +'<div class="nl-match-header">'
                                                    +'<span class="nl-match-name">'+v.post_title+'</span>'
                                                    +isMe
                                                    +'<p class="long-name">2018脑力世界杯金澳凯文速度计以(温州平阳)战队精英赛</p>'
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
                                                        +'<span class="nl-match-label">报名截止倒计时：</span>'
                                                        +'<span class="nl-match-info getTimes" data-seconds="'+sys_second+'">'
                                                        
                                                        +'报名截止</span>'
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
                                var dom='<div class="no-info">近期比赛无记录</div>'
                                lis.push(dom) 
                            }else{
                                $.alerts('没有更多了')
                            }
                            next(lis.join(''),false)
                        }
                        $('.getTimes').countdown(function(s, d){//倒计时
                            var D=d.day>0 ? d.day+'天' : '';
                            var h=d.hour<10 ? '0'+d.hour : d.hour;
                            var m=d.minute<10 ? '0'+d.minute : d.minute;
                            var s=d.second<10 ? '0'+d.second : d.second;
                            var time=D+h+':'+m+':'+s;
                            var items = $(this).text(time);
                        });
                })   
        }
    });
})

})
</script>