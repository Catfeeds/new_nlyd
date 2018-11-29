
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><div><?=__('速读考级水平(自测)', 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">

                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row  layui-row">
                        <!-- <div class="c_blue match_info_font"><div><?=__('第1/1题', 'nlyd-student')?></div></div> -->
                        <div class="c_blue match_info_font">
                            <div>
                                <!-- <i class="iconfont">&#xe685;</i> -->
                                <span class="count_down" data-seconds="900"><?=__('初始中', 'nlyd-student')?>...</span>
                            </div>
                        </div>
                    </div>
                    <div class="matching-reading">
                        <div class="Glass"></div>
                        <div class="article-title fs_16 c_black"><?=$questions->post_title?></div>
                        <div id="post_content"><?=$questions->post_content?></div>
                    </div>
                </div>
                <input type="hidden" name="questions_id" value="<?=$questions->ID?>">
                <div class="a-btn a-btn-table focus_none" style="position: relative;top:0;margin-top:30px;margin-bottom: 20px;" id="complete"  data-href="<?=$redirect_url?>"><div><?=__('阅读完成', 'nlyd-student')?></div></div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="_wpnonce" id="inputComplete" value="<?=wp_create_nonce('student_memory_complete_code_nonce');?>">
<input type="hidden" name="type" id="inputMatchMore" value="<?=isset($_GET['type']) ? $_GET['type'] : 1;?>"/>
<input type="hidden" name="_wpnonce" id="inputSubmit" value="<?=wp_create_nonce('student_answer_submit_code_nonce');?>">
<script>
jQuery(function($) {
    $.DelSession('count');
    var _genre_id=$.Request('genre_id');
    var _grading_num=<?=$num?>;
    var _grad_type=$.Request('grad_type');
    var _type=$.Request('grad_type');
    var _more=<?=isset($_GET['more']) ? $_GET['more'] : 1; ?>;
    var _length=$('#post_content').text().length;
    var init_time=900;
    var sys_second=900;//记忆时间
    var endTime=$.GetEndTime(sys_second);//结束时间
    leaveMatchPage(function(){//窗口失焦提交
        submit(4);
    })
    new AlloyFinger($('#complete')[0], {//阅读完成
        tap:function(){
            if(!$('#complete').hasClass('disabled')){
                var time=init_time-sys_second;
                var href=$(this).attr('data-href')
                var new_href=href+'/usetime/'+time+'/length/'+_length+'/more/'+_more;
                $.DelSession('grade_question')
                window.location.href=new_href;
                $('#complete').addClass('disabled')
            }
        }

    });
    init_question()
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
                clearInterval(timer)
                submit(3)
            }

        }, 1000);
    } 
    function init_question(question_leng,_show) {//初始化
        var grade_question=$.GetSession('grade_question','true');
        if(grade_question && grade_question['genre_id']===_genre_id && grade_question['grad_type']===_grad_type && grade_question['type']===_type){
            endTime=grade_question['endTime'];
            sys_second=$.GetSecond(endTime);
        }else{
            var sessionData={
                grad_id:_genre_id,
                grad_type:_grad_type,
                type:_type,
                endTime:endTime,
            }
            $.SetSession('grade_question',sessionData)
        }
    }
    function submit(submit_type){//提交答案
        // $('#load').css({
        //         'display':'block',
        //         'opacity': '1',
        //         'visibility': 'visible',
        //     })
        var my_answer={};
        var time=init_time-sys_second;
        var data={
            genre_id:_genre_id,
            grading_num:_grading_num,
            grading_type:_grad_type,
            questions_type:_type,
            post_id:<?=$post_id?>,
            more:<?=isset($_GET['more']) ? $_GET['more'] : 1?>,
            grading_questions:<?=json_encode($match_questions)?>,
            questions_answer:<?=json_encode($questions_answer)?>,
            action:'grade_answer_submit',
            length:_length,
            usetime:time,
            my_answer:my_answer,
            submit_type:submit_type,//1:选手提交;2:错误达上限提交;3:时间到达提交;4:来回切
        }
        var leavePage= $.GetSession('leavePage','1');
        if(leavePage && leavePage['grad_id']===_genre_id && leavePage['grad_type']===_grad_type && leavePage['type']===_type){
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
                }
            },
            complete: function(jqXHR, textStatus){
                    if(textStatus=='timeout'){
                        $.SetSession('match_data',data);
                        var href="<?=home_url('matchs/answerLog/grad_id/'.$_GET['grad_id'].'/project_alias/'.$_GET['project_alias'].'/project_more_id/'.$_GET['project_more_id'].'/type/')?>"+_type+'/more/'+_more;
                        window.location.href=href;
            　　　　}
                }
        })
    }
})
</script>