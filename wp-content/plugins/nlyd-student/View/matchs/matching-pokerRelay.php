
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><?=$post_title?></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row">
                        <span class="fs-14"><?=$match_title?><span class="blue-font">第一轮</span></span>
                        <span class="fs-14">第1/1题</span>
                        <span class="blue-font fs-14">
                            <i class="iconfont">&#xe685;</i>
                            <span class="count_down" data-seconds="<?=$count_down?>">00:00:00</span>
                        </span>
                        <div class="matching-sumbit" id="sumbit">提交</div>
                    </div>
                    <div class="matching-row">
                        <div class="matching-row-label">选中可操作</div>
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
                        <?php foreach ($list as $k => $v){ ?>
                        <div class="choose-color <?= $k==$list_keys[0] ? 'active' :'';?> <?=$k?>" id="<?=$k?>"><i class="iconfont">&#xe<?=$v['color']?></i></div>
                        <?php } ?>
                    </div>

                    <div class="porker-choose-zoo">
                        <div class="choose-zoo">
                            <div class="choose-window">
                                <?php foreach ($list as $k => $v){ ?>
                                <div class="choose-wrapper <?= $k==$list_keys[0]?'active':''?> <?=$k?>">
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
    submit=function(time){//提交答案
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
            match_id:<?=$_GET['match_id']?>,
            project_id:<?=$_GET['project_id']?>,
            match_more:<?=!empty($_GET['match_more']) ? $_GET['match_more'] : 1;?>,
            my_answer:my_answer,
            match_action:'subjectPokerRelay',
            surplus_time:time,
        }
        $.post(window.admin_ajax,data,function(res){
            if(res.success){
                if(res.data.url){
                    window.location.href=res.data.url
                }   
            }else{
                $.alerts(res.data.info)
            }
        })
    }
    if($('.count_down').attr('data-seconds')<=0){//进入页面判断时间是否结束
        $.alerts('比赛结束');
        setTimeout(() => {
            submit($('.count_down').attr('data-seconds'))
        }, 1000);
    }
    $('.count_down').countdown(function(S, d){//倒计时
        var D=d.day>0 ? d.day+'天' : '';
        var h=d.hour<10 ? '0'+d.hour : d.hour;
        var m=d.minute<10 ? '0'+d.minute : d.minute;
        var s=d.second<10 ? '0'+d.second : d.second;
        var time=D+h+':'+m+':'+s;
        var html="<span data-time='"+S+"' id='dataTime'>"+time+"</span>"
         $(this).html(html);
        if(S<=0){//本轮比赛结束
            if(S==0){
                $.alerts('倒计时结束，即将提交答案')
            }else{
                $.alerts('比赛结束')
            }
            setTimeout(() => {
                submit(S)
            }, 1000);
        }
    });

    layui.use(['layer'], function(){


//提交tap事件
var hammertime4 = new Hammer($('#sumbit')[0]);
    var time=$("#dataTime").attr('data-time')?$("#dataTime").attr('data-time'):0;
    hammertime4.on("tap", function (e) {
        layer.open({
            type: 1
            ,maxWidth:300
            ,title: '提示' //不显示标题栏
            ,skin:'nl-box-skin'
            ,id: 'certification' //防止重复弹出
            ,content: '<div class="box-conent-wrapper">是否立即提交？</div>'
            ,btn: ['提交', '按错了', ]
            ,success: function(layero, index){
            }
            ,yes: function(index, layero){
                layer.closeAll();
                setTimeout(() => {
                    submit(time)
                }, 1000);
            }
            ,btn2: function(index, layero){
                //按钮【按钮二】的回调
            }
            ,closeBtn:2
            ,btnAagn: 'c' //按钮居中
            ,shade: 0.3 //遮罩
            ,isOutAnim:true//关闭动画
        });
        
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
    initRight=function() {//控制滚动条
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
    $('.poker-wrapper .poker').each(function(i){//扑克展示区tap事件
        var _this=$(this)
        var dom=$(this)[0]
        var hammertime = new Hammer(dom);
        $(this).css('touch-action','auto');//允许默认浏览器tap事件，水平滚动
        hammertime.on("tap", function (e) {
            $('.poker-wrapper .poker').removeClass('active');
            _this.addClass('active');
        });
    })
    $('.porker-color .choose-color').each(function(i){//选择图片花色tap事件
        var _this=$(this);
        var dom=$(this)[0]
        var hammertime1 = new Hammer(dom);
        hammertime1.on("tap", function (e) {
            var id=_this.attr('id')
            $('.porker-color .choose-color').removeClass('active');
            _this.addClass('active');

            $('.choose-wrapper').removeClass('active');
            $('.choose-wrapper.'+id).addClass('active');
        });
    })
    $('.choose-wrapper .choose-poker').each(function(i){//扑克选择区tap事件
        var _this=$(this);
        var dom=$(this)[0];
        var hammertime = new Hammer(dom);
        $(this).css('touch-action','auto');//允许默认浏览器tap事件，水平滚动
        hammertime.on("tap", function (e) {
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
            // var poker='<div class="poker active" data-color="'+color+'">'+text+'</div>'
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
                var hammerdoms = new Hammer(active.next('.poker')[0]);
                active.next('.poker').css('touch-action','auto')
                hammerdoms.on("tap", function (ev) {
                    $('.poker-wrapper .poker').removeClass('active');
                    active.next('.poker').addClass('active');
                });
                active.removeClass('active')
            }else{
                $('.poker-wrapper .poker.active').removeClass('active')
                $('.poker-wrapper').append(poker)
                var hammerdom = new Hammer($('.poker-wrapper .poker').last()[0]);
                $('.poker-wrapper .poker').last().css('touch-action','auto')
                hammerdom.on("tap", function (ev) {
                    $('.poker-wrapper .poker').removeClass('active');
                    active.next('.poker').addClass('active');
                });
            }
            initWidth();
            initRight()
        });
    })
    //删除tap事件
    var hammerDel = new Hammer($('#del')[0]);
    hammerDel.on("tap", function (e) {
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
            initRight()
        }
    });
    //前移tap事件
    var hammerPrev = new Hammer($('#prev')[0]);
    hammerPrev.on("tap", function (e) {
        var active=$('.poker-wrapper .poker.active');
        if(active.prev('.poker').length>0){
            active.prev('.poker').addClass('active');
            active.removeClass('active')
            initRight()
        }
    });
    //后移tap事件
    var hammerNext = new Hammer($('#next')[0]);
    hammerNext.on("tap", function (e) {
        var active=$('.poker-wrapper .poker.active');
        if(active.next('.poker').length>0){
            active.next('.poker').addClass('active');
            active.removeClass('active')
            initRight()
        }
    });

    
})
</script>