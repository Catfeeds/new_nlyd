<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><?=$match_title?></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row">
                        <span class="c_black match_info_font"><?=$project_title?>第<?=$match_more_cn?>轮</span>
                        <span class="c_blue ml_10 match_info_font">第1/1题</span>
                        <span class="c_blue ml_10 match_info_font">
                            <i class="iconfont">&#xe685;</i>
                            <span class="count_down" data-seconds="<?=$count_down?>">00:00:00</span>
                        </span>
                        <div class="matching-sumbit match_info_font" id="sumbit">提交</div>
                    </div>
                    <div class="matching-row">
                        <div class="matching-row-label">辅助操作</div>
                        <div class="matching-row-list">
                            <div class="matching-btn c_white" id="prev">前插一位</div>
                            <div class="matching-btn c_white" id="next">后插一位</div>
                        </div>
                    </div>
                    <div class="matching-number-zoo">
                        <?php for($i=0;$i<$str_length;++$i){ ?>
                        <div class="matching-number <?=$i==0?'active':'';?>"></div>
                        <?php } ?>
                    </div>

                    <div class="matching-keyboard">
                        <div class="matching-keyboard-row">
                            <div class="matching-key fs_18 c_white number" date-number="1">1</div>
                            <div class="matching-key fs_18 c_white number" date-number="2">2</div>
                            <div class="matching-key fs_18 c_white number" date-number="3">3</div>
                        </div>
                        <div class="matching-keyboard-row">
                            <div class="matching-key fs_18 c_white number" date-number="4">4</div>
                            <div class="matching-key fs_18 c_white number" date-number="5">5</div>
                            <div class="matching-key fs_18 c_white number" date-number="6">6</div>
                        </div>
                        <div class="matching-keyboard-row">
                            <div class="matching-key fs_18 c_white number" date-number="7">7</div>
                            <div class="matching-key fs_18 c_white number" date-number="8">8</div>
                            <div class="matching-key fs_18 c_white number" date-number="9">9</div>
                        </div>
                        <div class="matching-keyboard-row">
                            <div class="matching-key fs_16 c_white" id="del">删除</div>
                            <div class="matching-key fs_18 c_white number" date-number="0">0</div>
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
    leaveMatchPage(function(){//窗口失焦提交
        var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
        submit(time,4);
    })
    if($('.count_down').attr('data-seconds')<=0){//进入页面判断时间是否结束
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
            isSubmit=true;
            var my_answer=[];
            $('.matching-number-zoo .matching-number').each(function(){
                var answer=$(this).text();
                my_answer.push(answer)
            })
            var data={
                action:'answer_submit',
                _wpnonce:$('#inputSubmit').val(),
                match_id:<?=$_GET['match_id']?>,
                project_id:<?=$_GET['project_id']?>,
                match_more:<?=$_GET['match_more']?>,
                my_answer:my_answer,
                match_action:'subjectNumberBattle',
                surplus_time:time,
                submit_type:submit_type,//1:选手提交;2:错误达上限提交;3:时间到达提交;4:来回切
            }
            $.ajax({
                data:data,success:function(res,ajaxStatu,xhr){  
                    $.DelSession('leavePage')
                    if(res.success){
                        if(res.data.url){
                            window.location.href=res.data.url
                        }   
                    }else{
                        $.alerts(res.data.info)
                        isSubmit=false;
                    }
                },
                error: function(jqXHR, textStatus, errorMsg){
                    isSubmit=false;
                }
            })
        }else{
            $.alerts('正在提交答案')
        }
    }
    mTouch('body').on('tap','.matching-number',function(e){

        $('.matching-number').removeClass('active');
        $(this).addClass('active');
        
    })
    mTouch('body').on('tap','.number',function(e){
        var _this=$(this);
        // if(!_this.hasClass('opcity')){
        //     _this.addClass('opcity')
            var number=_this.attr('date-number');
            var active=$('.matching-number.active');
            var len=$('.matching-number').length;
            if(!$('.matching-number').eq(len-1).hasClass('active')){
                active.text(number).removeClass('active').next('.matching-number').addClass('active');
            }else{
                active.text(number);
            }
            _this.stop(true).animate({
                'opacity':'0.6',
                'filter': 'alpha(opacity=60)',
            },50).animate({
                'opacity':'1',
                'filter': 'alpha(opacity=100)',
            },50)
        // }
    })
    //删除tap事件
    mTouch('body').on('tap','#del',function(e){
        var _this=$(this);
        // if(!_this.hasClass('opcity')){
        //     _this.addClass('opcity')
            var active=$('.matching-number.active');
            if(active.text()==""){//已经为空
                if(!$('.matching-number').eq(0).hasClass('active')){
                    active.prev('.matching-number').addClass('active')
                    
                }else{
                    active.next('.matching-number').addClass('active')
                }
                active.remove()
                var dom='<div class="matching-number"></div>'
                $('.matching-number-zoo').append(dom)
            }else{
                active.text('');

            }
            _this.stop(true).animate({
                'opacity':'0.6',
                'filter': 'alpha(opacity=60)',
            },50).animate({
                'opacity':'1',
                'filter': 'alpha(opacity=100)',
            },50)
        // }
    })
    //前插tap事件
    mTouch('body').on('tap','#prev',function(e){
        var len=$('.matching-number').length;
        var _this=$(this)
        if(!$('.matching-number').eq(len-1).hasClass('active')){
            var active=$('.matching-number.active');
            var dom='<div class="matching-number active"></div>';
            active.removeClass('active').before(dom);
            $('.matching-number-zoo .matching-number').last().remove()
        }else{
            $('.matching-number.active').text('')
        }
        _this.stop(true).animate({
            'opacity':'0.6',
            'filter': 'alpha(opacity=60)',
        },50).animate({
            'opacity':'1',
            'filter': 'alpha(opacity=100)',
        },50)
    });
    //后插tap事件
    mTouch('body').on('tap','#next',function(e){
        var _this=$(this)
        $('.matching-number').each(function(i){
            if(i!=$('.matching-number').length-1){//如果不是最后一位
                if($(this).hasClass('active')){
                    var dom='<div class="matching-number active"></div>'
                    $(this).removeClass('active').after(dom);
                    $('.matching-number-zoo .matching-number').last().remove()
                }
            }
        })
        _this.stop(true).animate({
            'opacity':'0.6',
            'filter': 'alpha(opacity=60)',
        },50).animate({
            'opacity':'1',
            'filter': 'alpha(opacity=100)',
        },50)
    });
layui.use('layer', function(){
    mTouch('body').on('tap','#sumbit',function(e){
        var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
        layer.open({
                type: 1
                ,maxWidth:300
                ,title: '提示' //不显示标题栏
                ,skin:'nl-box-skin'
                ,id: 'certification' //防止重复弹出
                ,content: '<div class="box-conent-wrapper">是否立即提交？</div>'
                ,btn: ['按错了','提交']
                ,success: function(layero, index){
                }
                ,yes: function(index, layero){
                    layer.closeAll();
                }
                ,btn2: function(index, layero){
                    layer.closeAll();
                    submit(time,1);  
                }
                ,closeBtn:2
                ,btnAagn: 'c' //按钮居中
                ,shade: 0.3 //遮罩
                ,isOutAnim:true//关闭动画
            });
            
    });
});

    
})
</script>