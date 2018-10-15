
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><?=$title?></h1>
        </header>
            <div class="layui-row nl-border nl-content">

                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row">
                        <span class="c_black match_info_font">第一轮</span>
                        <span class="c_blue ml_10 match_info_font">第1/1题</span>
                        <span class="c_blue ml_10 match_info_font">
                            <i class="iconfont">&#xe685;</i>
                            <span class="count_down" data-seconds="1200">00:00:00</span>
                        </span>
                    </div>
                    <div class="matching-row">
                        <div class="matching-row-label">划辅助线</div>
                        <div class="matching-row-list">
                            <div class="matching-btn active">不划</div>
                            <div class="matching-btn">2</div>
                            <div class="matching-btn">3</div>
                            <div class="matching-btn">4</div>
                            <div class="matching-btn">5</div>
                            <div class="matching-btn">8</div>
                        </div>
                    </div>
                    <div class="matching-number-zoo">
                        <div class="Glass"></div>
                    </div>
                </div>
                <a class="a-btn" id="complete" href="<?=home_url('trains/answer/genre_id/'.$_GET['genre_id'].'/type/'.$_GET['type'])?>">记忆完成</a>
            </div>
        </div>
    </div>
</div>
<script>
jQuery(function($) { 
    // $.DelSession('train_match')
    var questions_answer=[]
    var file_path = '<?=leo_student_url."/conf/rang_str.json";?>';

    $.getJSON(file_path,function(JsonData){
        var leavePage= $.GetSession('train_match','1');
        if(leavePage && leavePage['genre_id']==$.Request('genre_id') && leavePage['type']=='szzb'){
            questions_answer=leavePage['train_questions']
        }else{
            var questions_answers=JsonData;
            var pos = Math.round(Math.random() * (questions_answers.length - 1));
            var xx=questions_answers[pos]
            questions_answer=xx.sort(function() {
                return .5 - Math.random();
            });
            var sessionData={//存储session
                train_questions:questions_answer,
                genre_id:$.Request('genre_id'),
                type:'szzb',
            }
            $.SetSession('train_match',sessionData)
        }

        $.each(questions_answer,function(i,v){
            var dom='<div class="matching-number">'+v+'</div>';
            $('.matching-number-zoo').append(dom)
        })
    })
// new AlloyFinger($('#complete')[0], {//记忆完成
//     tap:function(){
//         var _this=$(this);
//         var sessionData={
//             train_questions:questions_answer,
//             genre_id:$.Request('genre_id'),
//             type:'szzb',
//         }
//         $.SetSession('train_match',sessionData)
//     }
// })
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
        var data={
            action:'trains_submit',
            genre_id:$.Request('genre_id'),
            project_type:'szzb',
            train_questions:questions_answer,
            train_answer:my_answer,
            surplus_time:time,
        }
        $.ajax({
            data:data,
            success:function(res,ajaxStatu,xhr){  
                $.DelSession('train_match')
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
            error: function(jqXHR, textStatus, errorMsg){
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
                $.alerts('倒计时结束，即将提交答案')
            }else{
                $.alerts('比赛结束')
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