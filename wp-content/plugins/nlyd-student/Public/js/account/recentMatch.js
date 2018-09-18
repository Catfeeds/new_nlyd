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
        flow.load({
            elem: '#flow-list' //流加载容器
            ,scrollElem: '#flow-list' //滚动条所在元素，一般不用填，此处只是演示需要。
            ,isAuto: false
            ,isLazyimg: true
            ,done: function(page, next){ //加载下一页
                var postData={
                    action:'get_my_match_list',
                    page:page,
                };
                var lis = [];
                $.ajax({
                    data:postData,
                    success:function(res,ajaxStatu,xhr){ 
                            if(res.success){
                                $.each(res.data.info,function(i,v){
                                    var isMe='<div class="nl-badge"><i class="iconfont">&#xe608;</i></div>';//标签
                                    var match_status='c_blue';//比赛中高亮
                                    var rightBtn='';   
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
                                    var onBtn="" ;
                                    if(rightBtn.length==0){
                                        onBtn="onBtn"
                                    }
                                    var end_time = new Date(v.entry_end_time).getTime();//月份是实际月份-1
                                    var serverTimes=new Date(xhr.getResponseHeader('Date')).getTime()
                                    var sys_second = (end_time-serverTimes)/1000;
                                    var sys_second_text=sys_second>0 ? '' :  "报名结束";
                                    var dom='<li class="layui-col-lg4 layui-col-md12 layui-col-sm12 layui-col-xs12">'
                                                +'<div class="nl-match">'
                                                    +'<div class="nl-match-header">'
                                                        +'<span class="nl-match-name  fs_16 c_blue">'+v.post_title+'</span>'
                                                        +isMe
                                                        +'<p class="long-name fs_12 c_black3">'+v.post_content+'</p>'
                                                    +'</div>'
                                                    +'<div class="nl-match-body">'
                                                        +'<div class="nl-match-detail layui-row">'
                                                            +'<div class="nl-match-label">开赛日期：</div>'
                                                            +'<div class="nl-match-info">'
                                                                +'<span class="c_black">'+v.match_start_time+'</span>'
                                                                +'<span class="nl-match-type '+match_status+'">'+v.match_status_cn+'</span>'
                                                            +'</div>'
                                                        +'</div>'
                                                        +'<div class="nl-match-detail layui-row">'
                                                            +'<div class="nl-match-label">比赛地点：</div>'
                                                            +'<div class="nl-match-info">'
                                                                +'<span class="c_black">'+v.match_address+'</span>'
                                                            +'</div>'
                                                        +'</div>'
                                                        +'<div class="nl-match-detail layui-row">'
                                                            +'<div class="nl-match-label">报名费用：</div>'
                                                            +'<div class="nl-match-info">'
                                                                +'<span class="c_black">¥'+v.match_cost+'</span>'
                                                            +'</div>'
                                                        +'</div>'
                                                        +'<div class="nl-match-detail layui-row">'
                                                            +'<div class="nl-match-label">报名截止：</div>'
                                                            +'<div class="nl-match-info">'
                                                                +'<span class="c_black getTimes" data-seconds="'+sys_second+'">'
                                                                +sys_second_text+'</span>'
                                                            +'</div>'
                                                        +'</div>'
                                                        +'<div class="nl-match-detail layui-row">'
                                                            +'<div class="nl-match-label">已报选手：</div>'
                                                            +'<div class="nl-match-info">'
                                                                +'<span class="c_black">'+v.entry_total+'人</span>'
                                                            +'</div>'
                                                        +'</div>'
                                                    +'</div>'

                                                    +'<div class="nl-match-footer flex-h">'
                                                        +'<div class="nl-match-button flex1">'
                                                            +'<button type="button" class="'+onBtn+'"  href="'+v.left_url+'">查看详情</button>'
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
                        }
                    })  
                     
            }
        });
    })

})