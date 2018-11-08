
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><div><?=__($project_title, 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">

                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row layui-row">
                        <div class="c_black match_info_font"><div><?=__('人脉信息记忆', 'nlyd-student')?></div></div>
                        <div class="c_blue match_info_font">
                            <div>
                                <!-- <i class="iconfont">&#xe685;</i> -->
                                <span class="count_down" data-seconds="<?=$count_down?>">00:00:00</span>
                            </div>
                        </div>
                    </div>
                    <div class="matching-number-zoo layui-row">
                     
                    </div>
                </div>
                <a class="a-btn a-btn-table" style="position: relative;top:0;margin-top:30px;margin-bottom: 20px;" id="complete" href="<?=home_url('grading/match_card')?>"><div><?=__('记忆完成', 'nlyd-student')?></div></a>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="_wpnonce" id="inputSubmit" value="<?=wp_create_nonce('student_answer_submit_code_nonce');?>">
<script>
jQuery(function($) { 
    var question_leng=100;//题目长度
    var question_type=2;//1，数字.2,字母
    leaveMatchPage(function(){//窗口失焦提交
        var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
        submit(time,4);
    })
    var _match_id=1;
    var _project_id=2;
    var _match_more=3;
    var questions_answer=['','','']
    // var matching_question=$.GetSession('matching_question','true');
    // if(matching_question && matching_question['match_id']===_match_id && matching_question['project_id']===_project_id && matching_question['match_more']===_match_more){
    //     questions_answer=matching_question['questions_answer']
    // }else{
    //     // $.getJSON("js/userinfo.json", function (data){
            
    //     // }) 
    //     for(var i=0;i<question_leng;i++){
    //         if(question_type==1){
    //             var num=Math.floor(Math.random()*10);//生成0-9的随机数
    //         }else if(question_type==2){
    //             var num=randZF();//生成A-Z的随机数
    //         }
            
    //         questions_answer.push(num)
    //     }
    //     var sessionData={
    //         match_id:_match_id,
    //         project_id:_project_id,
    //         match_more:_match_more,
    //         question_type:question_type,
    //         questions_answer:questions_answer
    //     }
    //     $.SetSession('matching_question',sessionData)
    // }
    function randZF() {//生成随即字符
        var arr=["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"]
        var pos = Math.round(Math.random() * (arr.length - 1));
        return arr[pos];
    }
    $.each(questions_answer,function(i,v){
        var dom='<div class="matching-card">'
                    +'<div class="img-box card_img">'
                        +'<img src="<?=student_css_url.'image/noInfo/noMatch1042@2x.png'?>">'
                    +'</div>'
                    +'<div class="card_detail">'
                        +'<div class="card_name c_black">名字</div>'
                        +'<div class="card_phone c_black">18140022053</div>'
                    +'</div>'
                +'</div>'
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
    // if(<?=$count_down?><=0){//进入页面判断时间是否结束
    //     $.alerts('<?=__('比赛结束', 'nlyd-student')?>');
    //         submit(0,3)
    // }
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

})
</script>