
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><div><?=__($project_title, 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">

                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row layui-row have-submit">
                        <div class="c_black match_info_font"><div><?=__('随机数字记忆', 'nlyd-student')?><?=__('随机字母记忆', 'nlyd-student')?></div></div>
                        <div class="c_blue match_info_font">
                            <div>
                                <span class="count_down" data-seconds="<?=$count_down?>"><?=__('初始中', 'nlyd-student')?>...</span>
                            </div>
                        </div>
                        <div class="matching-sumbit" id="sumbit" style="display:none"><div><?=__('提交', 'nlyd-student')?></div></div>
                    </div>
                    <!-- 记忆 -->
                    <div class="complete_zoo">
                        <div class="matching-number-zoo layui-row ready_zoo">
                            <div class="Glass"></div>
                        </div>
                        <div class="a-btn a-btn-table" style="position: relative;top:0;margin-top:30px;margin-bottom: 20px;" id="complete" href="match_zoo"><div><?=__('记忆完成', 'nlyd-student')?></div></div>
                    </div>

                    <!-- 比赛 -->
                    <div class="complete_zoo" id="match_zoo" style="display:none">
                        <div class="matching-number-zoo layui-row match_zoo">

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
    var _show=1;//1,准备区展示，2答题区展示
    //var question_type="<?//=isset($_GET['type']) && $_GET['type'] == 'sz' ? 1 : 2;?>//";//1，数字.2,字母
    var questions_answer=[];//题目
    var _match_id=1;
    var _project_id=2;
    var _match_more=3;
    var ready_time=900;//记忆时间
    var sys_second=ready_time;
    var answer_time=300;//记忆时间
    var endTime=$.GetEndTime(ready_time);//结束时间
    var que_len=100;//多少个字符
    var remember_time=ready_time;
    init_question(que_len,_show)
    leaveMatchPage(function(){//窗口失焦提交
        var countTime=parseInt($('.count_down').attr('data-seconds'));
        var time=_show==1?countTime+answer_time:countTime;
        submit(time,4);
    })
    $.each(questions_answer,function(i,v){
        var dom='<div class="matching-number-match-word">'+v+'</div>';
        $('.ready_zoo').append(dom)
        var dom1='<div class="matching-number-match-word"><input class="matching-number-input" type="text"></div>';
        $('.match_zoo').append(dom1)
    })
    $('#complete').click(function(){//记忆完成
        var _this=$(this);
        var href=_this.attr('href');
        $('.complete_zoo').hide();
        $('#'+href).show()
        $('.matching-sumbit').show();
        _show=2
        sys_second=answer_time
        var endTime=$.GetEndTime(answer_time);//结束时间
        var sessionData={
            match_id:_match_id,
            project_id:_project_id,
            match_more:_match_more,
            endTime:endTime,
            remember_time:$('.count_down').attr('data-seconds'),
            _show:2,
            questions_answer:questions_answer
        }
        $.SetSession('grade_question',sessionData)
    })
    count_down()
    function count_down(){
        // sys_second=answer_time
        var timer = setInterval(function(){
            if (sys_second > 0) {
                sys_second -= 1;
                var day = Math.floor((sys_second / 3600) / 24);
                var hour = Math.floor((sys_second / 3600) % 24);
                var minute = Math.floor((sys_second / 60) % 60);
                var second = Math.floor(sys_second % 60);
                day=day>0?day+'<?=__('天', 'nlyd-student')?>':'';
                hour= hour<10?"0"+hour:hour;//计算小时
                minute= minute<10?"0"+minute:minute;//计算分钟
                second= second<10?"0"+second:second;//计算秒
                var text=day+hour+':'+minute+':'+second;
                $('.count_down').text(text).attr('data-seconds',sys_second)
            } else {//倒计时结束
                if(_show==1){//记忆页面
                    $('.complete_zoo').hide();
                    $('#match_zoo').show()
                    $('.matching-sumbit').show();
                    _show=2
                    sys_second=answer_time
                    var endTime=$.GetEndTime(answer_time);//结束时间
                    var sessionData={
                        match_id:_match_id,
                        project_id:_project_id,
                        match_more:_match_more,
                        endTime:endTime,
                        remember_time:0,
                        _show:2,
                        questions_answer:questions_answer
                    }
                    $.SetSession('grade_question',sessionData)
                }else if(_show==2){//答题页面
                    clearInterval(timer)
                    submit(0,3)
                }
            }

        }, 1000);
    } 
    function init_question(question_leng,_show) {//初始化题目
        var grade_question=$.GetSession('grade_question','true');
        if(grade_question && grade_question['match_id']===_match_id && grade_question['project_id']===_project_id && grade_question['match_more']===_match_more){
            questions_answer=grade_question['questions_answer'];
            _show=grade_question['_show']
            endTime=grade_question['endTime'];
            sys_second=$.GetSecond(endTime);
            if(_show==2){
                remember_time=grade_question['remember_time'];
            }
        }else{
            for(var i=0;i<question_leng;i++){
                var num=Math.floor(Math.random()*10);//生成0-9的随机数
                questions_answer.push(num)
            }
            var sessionData={
                match_id:_match_id,
                project_id:_project_id,
                match_more:_match_more,
                remember_time:ready_time,//剩余记忆时间
                _show:_show,
                endTime:endTime,
                questions_answer:questions_answer
            }
            $.SetSession('grade_question',sessionData)
        }
        $('.complete_zoo').hide();
        $('.complete_zoo').eq(_show-1).show();
        if(_show==2){
         $('.matching-sumbit').show();
       }
    }
    function submit(time,submit_type){//提交答案
        // $('#load').css({
        //         'display':'block',
        //         'opacity': '1',
        //         'visibility': 'visible',
        //     })
        var my_answer=[];
        $('.matching-number-zoo .matching-number').each(function(){
            var answer=$(this).text();
            my_answer.push(answer)
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
    layui.use('layer', function(){
        new AlloyFinger($('#sumbit')[0], {
            tap:function(){
                var countTime=parseInt($('.count_down').attr('data-seconds'));
                var time=_show==1?countTime+answer_time:countTime;
                layer.open({
                        type: 1
                        ,maxWidth:300
                        ,title: '<?=__('提示', 'nlyd-student')?>' //不显示标题栏
                        ,skin:'nl-box-skin'
                        ,id: 'certification' //防止重复弹出
                        ,content: '<div class="box-conent-wrapper"><?=__('是否立即提交', 'nlyd-student')?>？</div>'
                        ,btn: ['<?=__('按错了', 'nlyd-student')?>','<?=__('提交', 'nlyd-student')?>']
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
                }
                
        });
    });
})
</script>