
<div class="layui-fluid">
    <div class="layui-row">

        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>

        <?php if($row){ ?>
            <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback static" href="<?=home_url('/account/')?>">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('我的课程', 'nlyd-student')?></div></h1>
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
            <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper layui-bg-white">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback static"  href="<?=home_url('/account/')?>">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('我的课程', 'nlyd-student')?></div></h1>
            </header>    
                <div class="layui-row nl-border nl-content">
                    <div class="no-info-page">
                        <div class="no-info-img">
                            <img src="<?=student_css_url.'image/noInfo/noCourse1043@2x.png'?>">
                        </div>
                        <p class="no-info-text"><?=__('您暂未参加任何课程', 'nlyd-student')?></p>
                        <a class="a-btn a-btn-table" href="<?=home_url('/courses/');?>"><div><?=__('看看最近课程', 'nlyd-student')?></div></a>
                    </div>
                </div>
            </div>
        <?php } ?>
       
    </div>
</div>
<script>
jQuery(function($) { 
    var _recentMatch={
        'stop':"<?=__('报名结束', 'nlyd-student')?>",
        'date':"<?=__('开课日期', 'nlyd-student')?>",
        'address':"<?=__('上课地点', 'nlyd-student')?>",
        'money':"<?=__('报名费用', 'nlyd-student')?>",
        'end':"<?=__('报名截止', 'nlyd-student')?>",
        'player':"<?=__('已报人数', 'nlyd-student')?>",
        'look':"<?=__('查看详情', 'nlyd-student')?>",
        'must':"<?=__('上课须知', 'nlyd-student')?>",
        'people':"<?=__('人', 'nlyd-student')?>",
        'day':"<?=__('天', 'nlyd-student')?>",
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
                                    var match_status='c_blue';//比赛中高亮
                                    var rightBtn='';   
                                    var match_notice_url="";//参赛须知
                                    if(v.match_status==2){//比赛进行中
                                        match_status='c_orange';   
                                    }
                                    if(v.right_url.length>0){
                                        rightBtn='<div class="nl-match-button flex1 last-btn">'
                                            +'<button type="button" href="'+v.right_url+'">'+v.button_title+'</button>'
                                        +'</div>'
                                    }
                                    if(v.match_status==1){//报名中
                                        className='bg_gradient_grey';
                                        rightBtn='<div class="nl-match-button flex1 last-btn">'
                                            +'<button type="button" class="'+className+'">'+v.button_title+'</button>'
                                        +'</div>'
                                    }
                                    if(v.match_notice_url && v.match_notice_url.length>0){//参赛须知
                                        match_notice_url='<a class="c_orange" style="margin-left:10px" href="'+v.match_notice_url+'">'+_recentMatch.must+'</a>'
                                    }
                                    var onBtn="" ;
                                    if(rightBtn.length==0){
                                        onBtn="onBtn"
                                    }
                                    var end_time = new Date(v.entry_end_time).getTime();//月份是实际月份-1
                                    var serverTimes=new Date(xhr.getResponseHeader('Date')).getTime()
                                    var sys_second = (end_time-serverTimes)/1000;
                                    var sys_second_text=sys_second>0 ? '' :  _recentMatch.stop;
                                    var dom='<li class="layui-col-lg4 layui-col-md12 layui-col-sm12 layui-col-xs12">'
                                                +'<div class="nl-match">'
                                                    +'<div class="nl-match-header">'
                                                        +'<span class="nl-match-name  fs_16 c_blue">'+v.post_title+'</span>'
                                                        +isMe
                                                        +'<p class="long-name fs_12 c_black8">'+v.post_content+'</p>'
                                                    +'</div>'
                                                    +'<div class="nl-match-body">'
                                                        +'<div class="nl-match-detail layui-row">'
                                                            +'<div class="nl-match-label"><div>'+_recentMatch.date+':</div></div>'
                                                            +'<div class="nl-match-info">'
                                                                +'<div class="c_black">'+v.match_start_time+'</div>'
                                                                +'<span class="nl-match-type '+match_status+'">'+v.match_status_cn+'</span>'
                                                            +'</div>'
                                                        +'</div>'
                                                        +'<div class="nl-match-detail layui-row">'
                                                            +'<div class="nl-match-label"><div>'+_recentMatch.address+':</div></div>'
                                                            +'<div class="nl-match-info">'
                                                                +'<div class="c_black">'+v.match_address+'</div>'
                                                            +'</div>'
                                                        +'</div>'
                                                        +'<div class="nl-match-detail layui-row">'
                                                            +'<div class="nl-match-label"><div>'+_recentMatch.money+':</div></div>'
                                                            +'<div class="nl-match-info">'
                                                                +'<div class="c_black">¥'+v.match_cost+'</div>'
                                                            +'</div>'
                                                        +'</div>'
                                                        +'<div class="nl-match-detail layui-row">'
                                                            +'<div class="nl-match-label"><div>'+_recentMatch.end+':</div></div>'
                                                            +'<div class="nl-match-info">'
                                                                +'<div class="c_black getTimes" data-seconds="'+sys_second+'">'
                                                                +sys_second_text+'</div>'
                                                            +'</div>'
                                                        +'</div>'
                                                        +'<div class="nl-match-detail layui-row">'
                                                            +'<div class="nl-match-label"><div>'+_recentMatch.player+':</div></div>'
                                                            +'<div class="nl-match-info">'
                                                                +'<div class="c_black">'+v.entry_total+_recentMatch.people+'</div>'
                                                                +match_notice_url
                                                            +'</div>'
                                                        +'</div>'
                                                    +'</div>'

                                                    +'<div class="nl-match-footer flex-h">'
                                                        +'<div class="nl-match-button flex1">'
                                                            +'<button type="button" class="'+onBtn+'"  href="'+v.left_url+'">'+_recentMatch.look+'</button>'
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
                            $('.getTimes').countdown(function(S, d){//倒计时
                                if(S>0){
                                    var D=d.day>0 ? d.day+_recentMatch.day : '';
                                    var h=d.hour<10 ? '0'+d.hour : d.hour;
                                    var m=d.minute<10 ? '0'+d.minute : d.minute;
                                    var s=d.second<10 ? '0'+d.second : d.second;
                                    var time=D+h+':'+m+':'+s;
                                    $(this).text(time);
                                }else{
                                    $(this).text(_recentMatch.stop);
                                }
                            });
                    }
                })  
                     
            }
        });
    })

})
</script>