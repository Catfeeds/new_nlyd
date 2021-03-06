
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><div><?=__($project_title, 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row layui-row">
                        <div class="c_black match_info_font"><div><?php printf(__('第%s轮', 'nlyd-student'), $match_more_cn)?></div></div>
                        <!-- <div class="c_blue match_info_font"><div><?=__('第1/1题', 'nlyd-student')?></div></div> -->
                        <div class="c_blue match_info_font">
                            <div>
                                <!-- <i class="iconfont">&#xe685;</i> -->
                                <span class="count_down" data-seconds="<?=$count_down?>">00:00:00</span>
                            </div>
                        </div>
                    </div>
                    <div class="matching-row layui-row">
                        <div class="matching-row-label"><div><?=__('划辅助线', 'nlyd-student')?></div></div>
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
                <a class="a-btn a-btn-table" style="position: relative;top:0;margin-top:30px;margin-bottom: 20px;" id="complete" href="<?=$redirect_url?>"><div><?=__('记忆完成', 'nlyd-student')?></div></a>
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
    var _match_id=<?=$_GET['match_id']?>;
    var _project_id=<?=$project_id?>;
    var _match_more=<?=$match_more;?>;
    var questions_answer=[]
    var matching_question=$.GetSession('matching_question','true');
    if(matching_question && matching_question['match_id']===_match_id && matching_question['project_id']===_project_id && matching_question['match_more']===_match_more){
        questions_answer=matching_question['questions_answer']
    }else{
        // $.DelSession('matching_question')
        for(var i=0;i<100;i++){
            var num=Math.floor(Math.random()*10);//生成0-9的随机数
            questions_answer.push(num)
        }
        var sessionData={
            match_id:_match_id,
            project_id:_project_id,
            match_more:_match_more,
            questions_answer:questions_answer
        }
        $.SetSession('matching_question',sessionData)
    }
    $.each(questions_answer,function(i,v){
            var dom='<div class="matching-number">'+v+'</div>';
            $('.matching-number-zoo').append(dom)
        })
    function submit(time,submit_type){//提交答案
        // $('#load').css({
        //         'display':'block',
        //         'opacity': '1',
        //         'visibility': 'visible',
        //     })
        var my_answer=[];
        $('.matching-number-zoo .matching-number').each(function(){
            my_answer.push('')
        })
        var data={
                action:'answer_submit',
                _wpnonce:$('#inputSubmit').val(),
                match_id:_match_id,
                project_id:_project_id,
                match_more:_match_more,
                project_alias:'szzb',
                match_questions:questions_answer,
                questions_answer:questions_answer,
                project_more_id:$.Request('project_more_id'),

                my_answer:my_answer,
                surplus_time:time,
                submit_type:submit_type,//1:选手提交;2:错误达上限提交;3:时间到达提交;4:来回切

        }

        var leavePage= $.GetSession('leavePage','1');
            if(leavePage && leavePage['match_id']===_match_id && leavePage['project_id']===_project_id && leavePage['match_more']===_match_more){
                if(leavePage.Time){
                    data['leave_page_time']=leavePage.Time;
                }
            }
        $.ajax({
            data:data,
            beforeSend:function(XMLHttpRequest){
                $('#load').css({
                    'display':'block',
                    'opacity': '1',
                    'visibility': 'visible',
                })
            },
            success:function(res,ajaxStatu,xhr){  
                // $.DelSession('leavePage')
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
            complete: function(jqXHR, textStatus){
                    if(textStatus=='timeout'){
                        $.SetSession('match_data',data);
                        var href="<?=home_url('matchs/answerLog/match_id/'.$_GET['match_id'].'/project_alias/'.$_GET['project_alias'].'/project_more_id/'.$_GET['project_more_id'].'/match_more/')?>"+_match_more;
                        window.location.href=href;
            　　　　}
                }
        })
    } 
    if(<?=$count_down?><=0){//进入页面判断时间是否结束
        $.alerts('<?=__('比赛结束', 'nlyd-student')?>');
        // setTimeout(function() {
            submit(0,3)
        // }, 1000);
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
            // setTimeout(function() {
                submit(0,3)
            // }, 1000);
        }
    });
    layui.use('layer', function(){
       function matchBtn(_this) {
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
        if('ontouchstart' in window){// 移动端
            $('.matching-btn').each(function(){
                var _this=$(this);
                new AlloyFinger(_this[0], {
                    tap:function(){
                        matchBtn(_this)
                    }
                })
            })
        }else{
            $('body').on('click','.matching-btn',function(){
                var _this=$(this)
                matchBtn(_this)
            })
        }
    })
})
</script>