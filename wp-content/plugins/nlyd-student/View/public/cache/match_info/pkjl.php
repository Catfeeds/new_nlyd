                    <div class="matching-row">
                        <div class="matching-row-label">辅助操作</div>
                        <div class="matching-row-list">
                            <div class="matching-btn" id="prev">前移1位</div>
                            <div class="matching-btn" id="next">后移1位</div>
                            <div class="matching-btn" id="del">删 除</div>
                        </div>
                    </div>
                    <div class="matching-number-zoo">
                        <div class="porker-zoo">
                            <div class="poker-window">
                                <div class="poker-wrapper">
                                <!-- 扑克区域 -->
                                </div>
                            </div>
                        </div>    
                    </div>

                    <div class="porker-color">
                        <br />
<font size='1'><table class='xdebug-error xe-warning' dir='ltr' border='1' cellspacing='0' cellpadding='1'>
<tr><th align='left' bgcolor='#f57900' colspan="5"><span style='background-color: #cc0000; color: #fce94f; font-size: x-large;'>( ! )</span> Warning: Invalid argument supplied for foreach() in D:\wamp64\www\nlyd\wp-content\plugins\nlyd-student\View\abcd\matching-pokerRelay.php on line <i>47</i></th></tr>
<tr><th align='left' bgcolor='#e9b96e' colspan='5'>Call Stack</th></tr>
<tr><th align='center' bgcolor='#eeeeec'>#</th><th align='left' bgcolor='#eeeeec'>Time</th><th align='left' bgcolor='#eeeeec'>Memory</th><th align='left' bgcolor='#eeeeec'>Function</th><th align='left' bgcolor='#eeeeec'>Location</th></tr>
<tr><td bgcolor='#eeeeec' align='center'>1</td><td bgcolor='#eeeeec' align='center'>0.0000</td><td bgcolor='#eeeeec' align='right'>407800</td><td bgcolor='#eeeeec'>{main}(  )</td><td title='D:\wamp64\www\nlyd\index.php' bgcolor='#eeeeec'>...\index.php<b>:</b>0</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>2</td><td bgcolor='#eeeeec' align='center'>0.0000</td><td bgcolor='#eeeeec' align='right'>408088</td><td bgcolor='#eeeeec'>require( <font color='#00bb00'>'D:\wamp64\www\nlyd\wp-blog-header.php'</font> )</td><td title='D:\wamp64\www\nlyd\index.php' bgcolor='#eeeeec'>...\index.php<b>:</b>18</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>3</td><td bgcolor='#eeeeec' align='center'>0.1362</td><td bgcolor='#eeeeec' align='right'>7031592</td><td bgcolor='#eeeeec'>require_once( <font color='#00bb00'>'D:\wamp64\www\nlyd\wp-includes\template-loader.php'</font> )</td><td title='D:\wamp64\www\nlyd\wp-blog-header.php' bgcolor='#eeeeec'>...\wp-blog-header.php<b>:</b>19</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>4</td><td bgcolor='#eeeeec' align='center'>0.1496</td><td bgcolor='#eeeeec' align='right'>7012904</td><td bgcolor='#eeeeec'>include( <font color='#00bb00'>'D:\wamp64\www\nlyd\wp-content\themes\dt-armada\page.php'</font> )</td><td title='D:\wamp64\www\nlyd\wp-includes\template-loader.php' bgcolor='#eeeeec'>...\template-loader.php<b>:</b>74</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>5</td><td bgcolor='#eeeeec' align='center'>0.1736</td><td bgcolor='#eeeeec' align='right'>7302168</td><td bgcolor='#eeeeec'>the_content(  )</td><td title='D:\wamp64\www\nlyd\wp-content\themes\dt-armada\page.php' bgcolor='#eeeeec'>...\page.php<b>:</b>31</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>6</td><td bgcolor='#eeeeec' align='center'>0.1738</td><td bgcolor='#eeeeec' align='right'>7302136</td><td bgcolor='#eeeeec'>apply_filters(  )</td><td title='D:\wamp64\www\nlyd\wp-includes\post-template.php' bgcolor='#eeeeec'>...\post-template.php<b>:</b>240</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>7</td><td bgcolor='#eeeeec' align='center'>0.1738</td><td bgcolor='#eeeeec' align='right'>7302536</td><td bgcolor='#eeeeec'>WP_Hook->apply_filters(  )</td><td title='D:\wamp64\www\nlyd\wp-includes\plugin.php' bgcolor='#eeeeec'>...\plugin.php<b>:</b>203</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>8</td><td bgcolor='#eeeeec' align='center'>0.1740</td><td bgcolor='#eeeeec' align='right'>7303336</td><td bgcolor='#eeeeec'>do_shortcode(  )</td><td title='D:\wamp64\www\nlyd\wp-includes\class-wp-hook.php' bgcolor='#eeeeec'>...\class-wp-hook.php<b>:</b>286</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>9</td><td bgcolor='#eeeeec' align='center'>0.1740</td><td bgcolor='#eeeeec' align='right'>7307504</td><td bgcolor='#eeeeec'><a href='http://www.php.net/function.preg-replace-callback' target='_new'>preg_replace_callback</a>
(  )</td><td title='D:\wamp64\www\nlyd\wp-includes\shortcodes.php' bgcolor='#eeeeec'>...\shortcodes.php<b>:</b>197</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>10</td><td bgcolor='#eeeeec' align='center'>0.1740</td><td bgcolor='#eeeeec' align='right'>7308216</td><td bgcolor='#eeeeec'>do_shortcode_tag(  )</td><td title='D:\wamp64\www\nlyd\wp-includes\shortcodes.php' bgcolor='#eeeeec'>...\shortcodes.php<b>:</b>197</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>11</td><td bgcolor='#eeeeec' align='center'>0.1740</td><td bgcolor='#eeeeec' align='right'>7308216</td><td bgcolor='#eeeeec'>Student_Abcd->answerMatch(  )</td><td title='D:\wamp64\www\nlyd\wp-includes\shortcodes.php' bgcolor='#eeeeec'>...\shortcodes.php<b>:</b>319</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>12</td><td bgcolor='#eeeeec' align='center'>0.1751</td><td bgcolor='#eeeeec' align='right'>7298480</td><td bgcolor='#eeeeec'>load_view_template(  )</td><td title='D:\wamp64\www\nlyd\wp-content\plugins\nlyd-student\Controller\class-student-abcd.php' bgcolor='#eeeeec'>...\class-student-abcd.php<b>:</b>676</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>13</td><td bgcolor='#eeeeec' align='center'>0.1753</td><td bgcolor='#eeeeec' align='right'>7299352</td><td bgcolor='#eeeeec'>include_once( <font color='#00bb00'>'D:\wamp64\www\nlyd\wp-content\plugins\nlyd-student\View\abcd\match-answer.php'</font> )</td><td title='D:\wamp64\www\nlyd\wp-includes\functions.php' bgcolor='#eeeeec'>...\functions.php<b>:</b>6294</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>14</td><td bgcolor='#eeeeec' align='center'>0.1757</td><td bgcolor='#eeeeec' align='right'>7300296</td><td bgcolor='#eeeeec'>require_once( <font color='#00bb00'>'D:\wamp64\www\nlyd\wp-content\plugins\nlyd-student\View\abcd\matching-pokerRelay.php'</font> )</td><td title='D:\wamp64\www\nlyd\wp-content\plugins\nlyd-student\View\abcd\match-answer.php' bgcolor='#eeeeec'>...\match-answer.php<b>:</b>16</td></tr>
</table></font>
                    </div>

                    <div class="porker-choose-zoo">
                        <div class="choose-zoo">
                            <div class="choose-window">
                                <br />
<font size='1'><table class='xdebug-error xe-warning' dir='ltr' border='1' cellspacing='0' cellpadding='1'>
<tr><th align='left' bgcolor='#f57900' colspan="5"><span style='background-color: #cc0000; color: #fce94f; font-size: x-large;'>( ! )</span> Warning: Invalid argument supplied for foreach() in D:\wamp64\www\nlyd\wp-content\plugins\nlyd-student\View\abcd\matching-pokerRelay.php on line <i>55</i></th></tr>
<tr><th align='left' bgcolor='#e9b96e' colspan='5'>Call Stack</th></tr>
<tr><th align='center' bgcolor='#eeeeec'>#</th><th align='left' bgcolor='#eeeeec'>Time</th><th align='left' bgcolor='#eeeeec'>Memory</th><th align='left' bgcolor='#eeeeec'>Function</th><th align='left' bgcolor='#eeeeec'>Location</th></tr>
<tr><td bgcolor='#eeeeec' align='center'>1</td><td bgcolor='#eeeeec' align='center'>0.0000</td><td bgcolor='#eeeeec' align='right'>407800</td><td bgcolor='#eeeeec'>{main}(  )</td><td title='D:\wamp64\www\nlyd\index.php' bgcolor='#eeeeec'>...\index.php<b>:</b>0</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>2</td><td bgcolor='#eeeeec' align='center'>0.0000</td><td bgcolor='#eeeeec' align='right'>408088</td><td bgcolor='#eeeeec'>require( <font color='#00bb00'>'D:\wamp64\www\nlyd\wp-blog-header.php'</font> )</td><td title='D:\wamp64\www\nlyd\index.php' bgcolor='#eeeeec'>...\index.php<b>:</b>18</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>3</td><td bgcolor='#eeeeec' align='center'>0.1362</td><td bgcolor='#eeeeec' align='right'>7031592</td><td bgcolor='#eeeeec'>require_once( <font color='#00bb00'>'D:\wamp64\www\nlyd\wp-includes\template-loader.php'</font> )</td><td title='D:\wamp64\www\nlyd\wp-blog-header.php' bgcolor='#eeeeec'>...\wp-blog-header.php<b>:</b>19</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>4</td><td bgcolor='#eeeeec' align='center'>0.1496</td><td bgcolor='#eeeeec' align='right'>7012904</td><td bgcolor='#eeeeec'>include( <font color='#00bb00'>'D:\wamp64\www\nlyd\wp-content\themes\dt-armada\page.php'</font> )</td><td title='D:\wamp64\www\nlyd\wp-includes\template-loader.php' bgcolor='#eeeeec'>...\template-loader.php<b>:</b>74</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>5</td><td bgcolor='#eeeeec' align='center'>0.1736</td><td bgcolor='#eeeeec' align='right'>7302168</td><td bgcolor='#eeeeec'>the_content(  )</td><td title='D:\wamp64\www\nlyd\wp-content\themes\dt-armada\page.php' bgcolor='#eeeeec'>...\page.php<b>:</b>31</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>6</td><td bgcolor='#eeeeec' align='center'>0.1738</td><td bgcolor='#eeeeec' align='right'>7302136</td><td bgcolor='#eeeeec'>apply_filters(  )</td><td title='D:\wamp64\www\nlyd\wp-includes\post-template.php' bgcolor='#eeeeec'>...\post-template.php<b>:</b>240</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>7</td><td bgcolor='#eeeeec' align='center'>0.1738</td><td bgcolor='#eeeeec' align='right'>7302536</td><td bgcolor='#eeeeec'>WP_Hook->apply_filters(  )</td><td title='D:\wamp64\www\nlyd\wp-includes\plugin.php' bgcolor='#eeeeec'>...\plugin.php<b>:</b>203</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>8</td><td bgcolor='#eeeeec' align='center'>0.1740</td><td bgcolor='#eeeeec' align='right'>7303336</td><td bgcolor='#eeeeec'>do_shortcode(  )</td><td title='D:\wamp64\www\nlyd\wp-includes\class-wp-hook.php' bgcolor='#eeeeec'>...\class-wp-hook.php<b>:</b>286</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>9</td><td bgcolor='#eeeeec' align='center'>0.1740</td><td bgcolor='#eeeeec' align='right'>7307504</td><td bgcolor='#eeeeec'><a href='http://www.php.net/function.preg-replace-callback' target='_new'>preg_replace_callback</a>
(  )</td><td title='D:\wamp64\www\nlyd\wp-includes\shortcodes.php' bgcolor='#eeeeec'>...\shortcodes.php<b>:</b>197</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>10</td><td bgcolor='#eeeeec' align='center'>0.1740</td><td bgcolor='#eeeeec' align='right'>7308216</td><td bgcolor='#eeeeec'>do_shortcode_tag(  )</td><td title='D:\wamp64\www\nlyd\wp-includes\shortcodes.php' bgcolor='#eeeeec'>...\shortcodes.php<b>:</b>197</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>11</td><td bgcolor='#eeeeec' align='center'>0.1740</td><td bgcolor='#eeeeec' align='right'>7308216</td><td bgcolor='#eeeeec'>Student_Abcd->answerMatch(  )</td><td title='D:\wamp64\www\nlyd\wp-includes\shortcodes.php' bgcolor='#eeeeec'>...\shortcodes.php<b>:</b>319</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>12</td><td bgcolor='#eeeeec' align='center'>0.1751</td><td bgcolor='#eeeeec' align='right'>7298480</td><td bgcolor='#eeeeec'>load_view_template(  )</td><td title='D:\wamp64\www\nlyd\wp-content\plugins\nlyd-student\Controller\class-student-abcd.php' bgcolor='#eeeeec'>...\class-student-abcd.php<b>:</b>676</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>13</td><td bgcolor='#eeeeec' align='center'>0.1753</td><td bgcolor='#eeeeec' align='right'>7299352</td><td bgcolor='#eeeeec'>include_once( <font color='#00bb00'>'D:\wamp64\www\nlyd\wp-content\plugins\nlyd-student\View\abcd\match-answer.php'</font> )</td><td title='D:\wamp64\www\nlyd\wp-includes\functions.php' bgcolor='#eeeeec'>...\functions.php<b>:</b>6294</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>14</td><td bgcolor='#eeeeec' align='center'>0.1757</td><td bgcolor='#eeeeec' align='right'>7300296</td><td bgcolor='#eeeeec'>require_once( <font color='#00bb00'>'D:\wamp64\www\nlyd\wp-content\plugins\nlyd-student\View\abcd\matching-pokerRelay.php'</font> )</td><td title='D:\wamp64\www\nlyd\wp-content\plugins\nlyd-student\View\abcd\match-answer.php' bgcolor='#eeeeec'>...\match-answer.php<b>:</b>16</td></tr>
</table></font>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>           
    </div>
</div>
<input type="hidden" name="_wpnonce" id="inputSubmit" value="7bfcc3a473">

<script>
jQuery(function($) { 
    var isSubmit=false;//是否正在提交
    leaveMatchPage(function(){//窗口失焦提交
        var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
        submit(time,4);
    })
    if(-222<=0){//进入页面判断时间是否结束
        $.alerts('比赛结束');
        setTimeout(function() {
            submit(0,3)
        }, 1000);
    }
    $('.count_down').countdown(function(S, d){//倒计时
        var D=d.day>0 ? d.day+'天' : '';
        var h=d.hour<10 ? '0'+d.hour : d.hour;
        var m=d.minute<10 ? '0'+d.minute : d.minute;
        var s=d.second<10 ? '0'+d.second : d.second;
        var time=D+h+':'+m+':'+s;
        $(this).attr('data-seconds',S).text(time)
        if(S<=0){//本轮比赛结束
            if(S==0){
                $.alerts('倒计时结束，即将提交答案')
            }else{
                $.alerts('比赛结束')
            }
            setTimeout(function() {
                submit(0,3)
            }, 1000);
        }
    });
    function submit(time,submit_type){//提交答案
        if(!isSubmit){
            $('#load').css({
                'display':'block',
                'opacity': '1',
                'visibility': 'visible',
            })
            isSubmit=true;
            var my_answer=[];
            $('.poker-wrapper .poker').each(function(){
                var text=$(this).attr('data-text');
                var color=$(this).attr('data-color');
                var answer=color+'-'+text;
                my_answer.push(answer)
            })
            var data={
                action:'answer_submit',
                _wpnonce:$('#inputSubmit').val(),
                match_id:765,
                project_id:202,
                match_more:1,
                my_answer:my_answer,
                match_action:'subjectPokerRelay',
                surplus_time:time,
                submit_type:submit_type,//1:选手提交;2:错误达上限提交;3:时间到达提交;4:来回切
            }
            var leavePage= $.GetSession('leavePage','1');
            if(leavePage && leavePage['match_id']===$.Request('match_id') && leavePage['project_id']===$.Request('project_id') && leavePage['match_more']===$.Request('match_more')){
                if(leavePage.Time){
                    data['leave_page_time']=leavePage.Time;
                }
            }
            $.ajax({
                data:data,success:function(res,ajaxStatu,xhr){  
                    $.DelSession('leavePage')
                    if(res.success){
                        isSubmit=false;
                        if(res.data.url){
                            window.location.href=res.data.url
                        }   
                    }else{
                        $('#load').css({
                            'display':'none',
                            'opacity': '0',
                            'visibility': 'hidden',
                        })
                        $.alerts(res.data.info)
                        isSubmit=false;
                    }
                },
                complete: function(XMLHttpRequest, textStatus){
                    isSubmit=false;
                    $('#load').css({
                            'display':'none',
                            'opacity': '0',
                            'visibility': 'hidden',
                        })
                }
            })
        }else{
            $.alerts('正在提交答案')
        }
    }
layui.use(['layer'], function(){


//提交tap事件
// mTouch('body').on('tap','#sumbit',function(e){
    new AlloyFinger($('#sumbit')[0], {
        tap:function(){
            var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
            layer.open({
                type: 1
                ,maxWidth:300
                ,title: '提示' //不显示标题栏
                ,skin:'nl-box-skin'
                ,id: 'certification' //防止重复弹出
                ,content: '<div class="box-conent-wrapper">是否立即提交？</div>'
                ,btn: [ '按错了', '提交',]
                ,success: function(layero, index){
                }
                ,yes: function(index, layero){
                    layer.closeAll();
                }
                ,btn2: function(index, layero){
                    //按钮【按钮二】的回调
                    layer.closeAll();
                    submit(time,1)
                }
                ,closeBtn:2
                ,btnAagn: 'c' //按钮居中
                ,shade: 0.3 //遮罩
                ,isOutAnim:true//关闭动画
            });
        }
    });
})
    //设置扑克窗口宽度
    initWidth=function() {
        //扑克展示区
        var len=$('.poker-wrapper .poker').length;
        var width=$('.poker-wrapper .poker').width()+2;
        var marginRight=parseInt($('.poker-wrapper .poker').css('marginRight'))
        var W=width*len+marginRight*(len-1)+'px';
        $('.poker-wrapper').css('width',W);
        //扑克选择区
        $('.choose-wrapper').each(function(i){
            var _this=$(this);
            var n=0;
            _this.children('.choose-poker').each(function(j){
                if($(this).hasClass('active')){
                    n++;
                }
            })
            var len1=_this.children('.choose-poker').length-n;
            var width1=_this.children('.choose-poker').width()+2;
            var marginRight1=parseInt(_this.children('.choose-poker').css('marginRight'))
            var W1=width1*len1+marginRight1*(len1)+'px';
            _this.css('width',W1);            
        })
    }
    initScroll=function() {//控制滚动条
        if($('.poker-wrapper .poker.active').length>0){
            var active=$('.poker-wrapper .poker.active');
            var window=$('.poker-window').width();
            
            var offset=active.offset().left;
            var position=active.position().left;
            var margin=parseInt(active.css('marginRight'));
            var left=0;
            if(typeof(active.position())!='undefined'){
                if(offset>window){
                    $('.poker-window').scrollLeft(position)
                }else if(offset<0+active.width()+margin){
                    $('.poker-window').scrollLeft(position-window+active.width())
                }
            };
            
        }
    }
    initWidth();//设置扑克窗口宽度
    $('.choose-color').each(function(){
        var _this=$(this);
        new AlloyFinger(_this[0], {
            tap:function(e){
                var id=_this.attr('id')
                $('.porker-color .choose-color').removeClass('active');
                _this.addClass('active');

                $('.choose-wrapper').removeClass('active');
                $('.choose-wrapper.'+id).addClass('active');
            }
        })
    })
    $('.poker-wrapper .poker').each(function(){
        var _this=$(this);
        new AlloyFinger(_this[0], {
            tap:function(e){
                var active=$('.poker-wrapper .poker.active')
                active.removeClass('active');
                _this.addClass('active');
            }
        })
    })
    // mTouch('.choose-wrapper ').on('tap','.choose-poker',function (e) {//扑克选择区tap事件
$('.choose-poker').each(function(e){
    var _this=$(this);
    new AlloyFinger(_this[0], {
        tap:function(){
           var text=_this.attr('data-text');
           var color=_this.attr('data-color');
           _this.addClass('active');
           var i='';
           if(color=='club'){
                i='<i class="iconfont">&#xe635;</i>'
            }else if(color=='heart'){
                i='<i class="iconfont">&#xe638;</i>'
            }else if(color=='spade'){
                i='<i class="iconfont">&#xe636;</i>'
            }else if(color=='diamond'){
                i='<i class="iconfont">&#xe634;</i>'
            }
            var poker='<div class="poker '+color+' active" data-color="'+color+'" data-text="'+text+'">'
                        +'<div class="poker-detail poker-top">'
                            +'<div class="poker-name">'+text+'</div>'
                            +'<div class="poker-type">'+i+'</div>'
                        +'</div>'
                        +'<div class="poker-logo">'
                            +'<img src="http://127.0.0.1/nlyd/wp-content/plugins/nlyd-student/Public/css/image/nlyd-big.png">'
                        +'</div>'
                        +'<div class="poker-detail poker-bottom">'
                            +'<div class="poker-name">'+text+'</div>'
                            +'<div class="poker-type">'+i+'</div>'
                        +'</div>'
                    +'</div>'
            
            if($('.poker-wrapper .poker.active').length>0){//绑定事件
                var active=$('.poker-wrapper .poker.active')
                active.after(poker);
                active.removeClass('active')
            }else{
                $('.poker-wrapper .poker.active').removeClass('active')
                $('.poker-wrapper').append(poker)
            }

            var newDom=$('.poker.active')
            new AlloyFinger(newDom[0], {
                tap:function(){
                    var active=$('.poker-wrapper .poker.active')
                    active.removeClass('active');
                    newDom.addClass('active');
                }
            })
            initWidth();
            initScroll()
        }
    });
});
    //删除tap事件
    // mTouch('body').on('tap','#del',function(e){
    new AlloyFinger($('#del')[0], {
        tap:function(){
            if($('.poker-wrapper .poker.active').length>0){
                var active=$('.poker-wrapper .poker.active');
                var color=active.attr('data-color');
                var text=active.attr('data-text');
                $('.choose-wrapper.'+color).children('.choose-poker.active').each(function(){
                    if($(this).attr('data-text')==text){
                        $(this).removeClass('active')
                    }
                })
                if(active.prev('.poker').length>0){
                    active.prev('.poker').addClass('active');
                }else{
                    active.next('.poker').addClass('active');
                }
                active.remove()
                initWidth()
                initScroll()
            }
        }
    });
    //前移tap事件
    // mTouch('body').on('tap','#prev',function(e){
new AlloyFinger($('#prev')[0], {
    tap:function(){
        var active=$('.poker-wrapper .poker.active');
        var htmlActive=$('.poker-wrapper .poker.active').html();
        var colorActive=$('.poker-wrapper .poker.active').attr('data-color')
        var textActive=$('.poker-wrapper .poker.active').attr('data-text')
        if(active.prev('.poker').length>0){
            var html=active.prev('.poker').html();
            var color=active.prev('.poker').attr('data-color')
            var text=active.prev('.poker').attr('data-text')
            active.prev('.poker').addClass('active').html(htmlActive).attr('data-color',colorActive).attr('data-text',textActive)
            active.removeClass('active').html(html).attr('data-color',color).attr('data-text',text)
            if(colorActive!=color){
                active.prev('.poker').removeClass(color).addClass(colorActive);
                active.removeClass(colorActive).addClass(color);
            }
            initScroll()
        }
    }
});
    //后移tap事件
    // mTouch('body').on('tap','#next',function(e){
new AlloyFinger($('#next')[0], {
    tap:function(){
        var active=$('.poker-wrapper .poker.active');
        var htmlActive=$('.poker-wrapper .poker.active').html();
        var colorActive=$('.poker-wrapper .poker.active').attr('data-color')
        var textActive=$('.poker-wrapper .poker.active').attr('data-text')
        if(active.next('.poker').length>0){
            var html=active.next('.poker').html();
            var color=active.next('.poker').attr('data-color')
            var text=active.next('.poker').attr('data-text')

            active.next('.poker').addClass('active').html(htmlActive).attr('data-color',colorActive).attr('data-text',textActive)
            active.removeClass('active').html(html).attr('data-color',color).attr('data-text',text)
            if(colorActive!=color){
                active.next('.poker').removeClass(color).addClass(colorActive);
                active.removeClass(colorActive).addClass(color);
            }
            initScroll()
        }
    }
});

    
})
</script>
