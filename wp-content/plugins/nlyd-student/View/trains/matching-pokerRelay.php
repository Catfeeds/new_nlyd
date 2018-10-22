
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><div><?=__($match_title, 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row">
                        <span class="c_black match_info_font"><?=__($title, 'nlyd-student')?>  <?=sprintf(__('第%s轮', 'nlyd-student'),$match_more)?></span>
                        <span class="c_blue ml_10 match_info_font"><?=__('第1/1题', 'nlyd-student')?></span>
                        <span class="c_blue ml_10 match_info_font">
                            <i class="iconfont">&#xe685;</i>
                            <span class="count_down" data-seconds="<?=$count_down?>">00:00:00</span>
                        </span>
                        <div class="matching-sumbit match_info_font" id="sumbit"><?=__('提交', 'nlyd-student')?></div>
                    </div>
                    <div class="matching-row">
                        <div class="matching-row-label"><?=__('辅助操作', 'nlyd-student')?></div>
                        <div class="matching-row-list">
                            <div class="matching-btn" id="prev"><?=__('前移1位', 'nlyd-student')?></div>
                            <div class="matching-btn" id="next"><?=__('后移1位', 'nlyd-student')?></div>
                            <div class="matching-btn" id="del"><?=__('删 除', 'nlyd-student')?></div>
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
                        <?php foreach ($list as $k => $v){ ?>
                        <div class="choose-color <?= $k=='spade' ? 'active' :'';?> <?=$k?>" id="<?=$k?>"><i class="iconfont">&#xe<?=$v['color']?></i></div>
                        <?php } ?>
                    </div>

                    <div class="porker-choose-zoo">
                        <div class="choose-zoo">
                            <div class="choose-window">
                                <?php foreach ($list as $k => $v){ ?>
                                <div class="choose-wrapper <?= $k=='spade'?'active':''?> <?=$k?>">
                                    <?php foreach ($v['content'] as $val){ ?>
                                    <div class="choose-poker" data-color="<?=$k?>" data-text="<?=$val?>">
                                        <div class="small poker-detail poker-top">
                                            <div class="poker-name"><?=$val?></div>
                                            <div class="poker-type"><i class="iconfont">&#xe<?=$v['color']?></i></div>
                                        </div>
                                        <div class="small poker-detail poker-bottom">
                                            <div class="poker-name"><?=$val?></div>
                                            <div class="poker-type"><i class="iconfont">&#xe<?=$v['color']?></i></div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                                <?php } ?>
                            </div>
                        </div>    
                    </div>
                </div>
            </div>
        </div>           
    </div>
</div>
<input type="hidden" name="_wpnonce" id="inputSubmit" value="<?=wp_create_nonce('student_answer_submit_code_nonce');?>">

<script>
jQuery(function($) { 
    var isSubmit=false;//是否正在提交
    // if(<?=$count_down?><=0){//进入页面判断时间是否结束
    //     $.alerts('比赛结束');
    //     setTimeout(function() {
    //         submit(0,3)
    //     }, 1000);
    // }
    var questions_answer=[];
    var ready_poker= $.GetCookie('train_match','1');
    if(ready_poker && ready_poker['genre_id']==$.Request('genre_id') && ready_poker['type']=='pkjl'){//记忆成功
        questions_answer=ready_poker['train_questions'];
        $('.count_down').attr('data-seconds',ready_poker['count_down'])
    }else{//未获取到比赛题目
        $.alerts('<?=__('未检测到题目信息', 'nlyd-student')?>')
    }
    $('.count_down').countdown(function(S, d){//倒计时
        var D=d.day>0 ? d.day+'<?=__('天', 'nlyd-student')?>' : '';
        var h=d.hour<10 ? '0'+d.hour : d.hour;
        var m=d.minute<10 ? '0'+d.minute : d.minute;
        var s=d.second<10 ? '0'+d.second : d.second;
        var time=D+h+':'+m+':'+s;
        $(this).attr('data-seconds',S).text(time)
        if(S<=0){//本轮比赛结束
            if(S==0){
                $.alerts('<?=__('倒计时结束，即将提交答案', 'nlyd-student')?>')
            }else{
                $.alerts('<?=__('比赛结束', 'nlyd-student')?>')
            }
            setTimeout(function() {
                submit(0)
            }, 1000);
        }
    });
    function submit(time){//提交答案
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
            var match_more=$.Request('match_more') ? $.Request('match_more') : '1';
            var data={
                action:'trains_submit',
                genre_id:$.Request('genre_id'),
                project_type:'pkjl',
                train_questions:questions_answer,
                train_answer:questions_answer,
                my_answer:my_answer,
                surplus_time:time,
                match_more:match_more,
            }
            $.ajax({
                data:data,success:function(res,ajaxStatu,xhr){
                    $.DelCookie('train_match','1')
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
                error: function(jqXHR, textStatus, errorMsg){
                    isSubmit=false;
                    $('#load').css({
                            'display':'none',
                            'opacity': '0',
                            'visibility': 'hidden',
                        })
                }
            })
        }else{
            $.alerts('<?=__('正在提交答案', 'nlyd-student')?>')
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
                ,title: '<?=__('提示', 'nlyd-student')?>' //不显示标题栏
                ,skin:'nl-box-skin'
                ,id: 'certification' //防止重复弹出
                ,content: '<div class="box-conent-wrapper"><?=__('是否立即提交', 'nlyd-student')?>？</div>'
                ,btn: [ '<?=__('按错了', 'nlyd-student')?>', '<?=__('提交', 'nlyd-student')?>',]
                ,success: function(layero, index){
                }
                ,yes: function(index, layero){
                    layer.closeAll();
                }
                ,btn2: function(index, layero){
                    //按钮【按钮二】的回调
                    layer.closeAll();
                    submit(time)
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
$('.choose-poker').each(function(e){//扑克选择区tap事件
    var _this=$(this);
    new AlloyFinger(_this[0], {
        tap:function(){
            if(!_this.hasClass('active')){
                // $('.choose-poker').addClass('disabled')
                _this.addClass('active');
                var text=_this.attr('data-text');
                var color=_this.attr('data-color');
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
                                +'<img src="<?=student_css_url.'image/nlyd-big.png'?>">'
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
                // $('.choose-poker').removeClass('disabled')
            }
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