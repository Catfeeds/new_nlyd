
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title "><div class=""><?=__($match_title, 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">

                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row layui-row">
                        <div class="c_black match_info_font"><div><?=sprintf(__('第%s轮', 'nlyd-student'),$match_more)?></div></div>
                        <div class="c_blue match_info_font"><div>&nbsp;&nbsp;&nbsp;&nbsp;<?=__('第1/1题', 'nlyd-student')?></div></div>
                        <div class="c_blue match_info_font">
                            <div>
                                <i class="iconfont">&#xe685;</i>
                                <span class="count_down" data-seconds="<?=$count_down?>">00:00:00</span>
                            </div>
                        </div>
                    </div>
                    <div class="matching-row layui-row">
                        <div class="matching-row-label"><?=__('划辅助线', 'nlyd-student')?></div>
                        <div class="matching-row-list">
                            <button class="matching-btn active"><?=__('不划', 'nlyd-student')?></button>
                            <button class="matching-btn">2</button>
                            <button class="matching-btn">3</button>
                            <button class="matching-btn">4</button>
                            <button class="matching-btn">5</button>
                            <button class="matching-btn">8</button>
                        </div>
                    </div>
                    <div class="matching-number-zoo layui-row">
                        <div class="Glass"></div>
                    </div>
                </div>
                <a class="a-btn a-btn-table" style="position: relative;top:0;margin-top:30px;margin-bottom: 20px;" id="complete" href="<?=$url?>"><div><?=__('记忆完成', 'nlyd-student')?></div></a>
            </div>
        </div>
    </div>
</div>
<script>
jQuery(function($) { 
    var questions_answer=[]
    var leavePage= $.GetSession('train_match','1');
    if(leavePage && leavePage['genre_id']==$.Request('genre_id') && leavePage['type']=='szzb'){
        questions_answer=leavePage['train_questions']
    }else{
        for(var i=0;i<100;i++){
            var num=Math.floor(Math.random()*10);//生成0-9的随机数
            questions_answer.push(num)
        }
        var sessionData={//存储session
            train_questions:questions_answer,
            genre_id:$.Request('genre_id'),
            type:'szzb',
            end_time:$.GetEndTime($('.count_down').attr('data-seconds'))
        }
        $.SetSession('train_match',sessionData)
    }

    $.each(questions_answer,function(i,v){
        var dom='<div class="matching-number">'+v+'</div>';
        $('.matching-number-zoo').append(dom)
    })
    new AlloyFinger($('#complete')[0], {
        tap:function(){
            var _sessionData={//存储session
                train_questions:questions_answer,
                genre_id:$.Request('genre_id'),
                type:'szzb',
                end_time:$.GetEndTime($('.count_down').attr('data-seconds'))
            }
            $.SetSession('train_match',_sessionData)
        }
    })
    function submit(time){//提交答案
        $('#load').css({
                'display':'block',
                'opacity': '1',
                'visibility': 'visible',
            })
        var my_answer=[];
        $('.matching-number-zoo .matching-number').each(function(){
            my_answer.push('')
        })
        var match_more=$.Request('match_more') ? $.Request('match_more') : '1';
        var data={
            action:'trains_submit',
            genre_id:$.Request('genre_id'),
            project_type:'szzb',
            train_questions:questions_answer,
            train_answer:questions_answer,
            my_answer:my_answer,
            surplus_time:time,
            match_more:match_more,
        }
        $.ajax({
            data:data,
            success:function(res,ajaxStatu,xhr){
                if(res.success){
                    if(res.data.url){
                        setTimeout(function(){
                            window.location.href=res.data.url
                        },300)
                    }
                }else{
                    $('#load').css({
                            'display':'none',
                            'opacity': '0',
                            'visibility': 'hidden',
                        })
                    $.alerts(res.data.info)
                }
            },
            complete: function(XMLHttpRequest, textStatus){
                $('#load').css({
                            'display':'none',
                            'opacity': '0',
                            'visibility': 'hidden',
                        })
            }
        })
    } 
    $('.count_down').countdown(function(S, d){//倒计时
        var D=d.day>0 ? d.day : '';
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
    $('.matching-btn').each(function(){
        var _this=$(this);
        new AlloyFinger(_this[0], {
            tap:function(){
                $('.matching-btn').removeClass('active');
                _this.addClass('active')
                var text=parseInt(_this.text())
                $('.matching-number').removeClass('border-right');
                if(text!='NAN'){
                    $('.matching-number').each(function(j){
                        if((j+1)%text==0){
                            $(this).addClass('border-right')
                        }
                    })
                }
            }
        })
    })
})
</script>