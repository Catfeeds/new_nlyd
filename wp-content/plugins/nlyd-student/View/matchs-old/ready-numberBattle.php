
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><div><?=$match_title?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">

                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row layui-row">
                        <span class="c_black match_info_font"><?=$project_title?><?php printf(__('第%s轮', 'nlyd-student'), $match_more_cn)?></span>
                        <span class="c_blue ml_10 match_info_font"><?=__('第', 'nlyd-student')?>1/1<?=__('题', 'nlyd-student')?></span>
                        <span class="c_blue ml_10 match_info_font">
                            <i class="iconfont">&#xe685;</i>
                            <span class="count_down" data-seconds="<?=$count_down?>">00:00:00</span>
                        </span>
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
                <div class="a-btn a-btn-table" style="position: relative;top:0;margin-top:30px" id="complete"><div><?=__('记忆完成', 'nlyd-student')?></div></div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="_wpnonce" id="inputComplete" value="<?=wp_create_nonce('student_memory_complete_code_nonce');?>">
<input type="hidden" name="match_more" id="inputMatchMore" value="<?=isset($_GET['match_more']) ? $_GET['match_more'] : 1;?>"/>
<input type="hidden" name="_wpnonce" id="inputSubmit" value="<?=wp_create_nonce('student_answer_submit_code_nonce');?>">
<script>
jQuery(function($) { 
    leaveMatchPage(function(){//窗口失焦提交
        var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
        submit(time,4);
    })
    var questions_answer=[]
    var file_path = '<?=leo_student_url."/conf/rang_str.json";?>';
    $.getJSON(file_path,function(JsonData){
        var matchSession=$.GetSession('ready_shuzi','true');
        if(matchSession && matchSession['match_id']===$.Request('match_id') && matchSession['project_id']===$.Request('project_id') && matchSession['match_more']===$.Request('match_more')){
            questions_answer=matchSession['questions_answer']
        }else{
            var questions_answers=JsonData;
            var pos = Math.round(Math.random() * (questions_answers.length - 1));
            var xx=questions_answers[pos]
            questions_answer=xx.sort(function() {
                return .5 - Math.random();
            });
            var sessionData={
                match_id:$.Request('match_id'),
                project_id:$.Request('project_id'),
                match_more:$.Request('match_more'),
                questions_answer:questions_answer
            }
            $.SetSession('ready_shuzi',sessionData)
        }
        $.each(questions_answer,function(i,v){
            var dom='<div class="matching-number">'+v+'</div>';
            $('.matching-number-zoo').append(dom)
        })
    })
    // mTouch('body').on('tap','#complete',function(){//记忆完成
new AlloyFinger($('#complete')[0], {
    tap:function(){
        var _this=$(this);
        if(!_this.hasClass('disabled')){
            _this.addClass('disabled')
            var data={
                action:'memory_complete',
                _wpnonce:$('#inputComplete').val(),
                match_id:<?=$_GET['match_id']?>,
                project_id:<?=$_GET['project_id']?>,
                match_more:$('#inputMatchMore').val(),
                match_action:'numberBattle',
                match_questions:questions_answer,
                type:'szzb'
            }
            $.ajax({
                data:data,
                success:function(res,ajaxStatu,xhr){  
                    if(res.success){
                        if(res.data.url){
                            window.location.href=res.data.url;
                            $.DelSession('ready_shuzi')
                        }   
                    }else{
                        $.alerts(res.data.info)
                        _this.removeClass('disabled')
                    }
                    
                },
                error: function(jqXHR, textStatus, errorMsg){
                    _this.removeClass('disabled')
                }
            })
        }
    }
})
    function submit(time,submit_type){//提交答案
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
            action:'answer_submit',
            _wpnonce:$('#inputSubmit').val(),
            match_id:<?=$_GET['match_id']?>,
            project_id:<?=$_GET['project_id']?>,
            match_more:$('#inputMatchMore').val(),
            my_answer:my_answer,
            match_action:'subjectNumberBattle',
            surplus_time:time,
            match_questions:questions_answer,
            submit_type:submit_type,//1:选手提交;2:错误达上限提交;3:时间到达提交;4:来回切
        }

        var leavePage= $.GetSession('leavePage','1');
            if(leavePage && leavePage['match_id']===$.Request('match_id') && leavePage['project_id']===$.Request('project_id') && leavePage['match_more']===$.Request('match_more')){
                if(leavePage.Time){
                    data['leave_page_time']=leavePage.Time;
                }
            }
        $.ajax({
            data:data,
            success:function(res,ajaxStatu,xhr){  
                $.DelSession('leavePage')
                if(res.success){
                    //return false;
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
    if(<?=$count_down?><=0){//进入页面判断时间是否结束
        $.alerts('<?=__('比赛结束', 'nlyd-student')?>');
        setTimeout(function() {
            submit(0,3)
        }, 1000);
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
                submit(0,3)
            }, 1000);
        }
    });
    // mTouch('body').on('tap','.matching-btn',function(e){
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