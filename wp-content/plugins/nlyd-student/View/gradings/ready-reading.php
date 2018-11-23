
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><div><?=__($grading_title, 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">

                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row  layui-row">
                        <div class="c_black match_info_font"><div><?=__($project_title, 'nlyd-student')?> <?php printf(__('第%s轮', 'nlyd-student'), $type_cn)?></div></div>
                        <!-- <div class="c_blue match_info_font"><div><?=__('第1/1题', 'nlyd-student')?></div></div> -->
                        <div class="c_blue match_info_font">
                            <div>
                                <!-- <i class="iconfont">&#xe685;</i> -->
                                <span class="count_down" data-seconds="<?=$count_down?>">00:00:00</span>
                            </div>
                        </div>
                    </div>
                    <div class="matching-reading">
                        <div class="Glass"></div>
                        <div class="article-title fs_16 c_black"><?=$questions->post_title?></div>
                        <?=$questions->post_content?>
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
    var _grad_id=$.Request('grad_id');
    var _grad_type=$.Request('grad_type');
    var _type=$.Request('type');
    leaveMatchPage(function(){//窗口失焦提交
        var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
        submit(time,4);
    })
    new AlloyFinger($('#complete')[0], {//阅读完成
        tap:function(){
            if(!$('#complete').hasClass('disabled')){
                var time=$('.count_down').attr('data-seconds');
                var href=$(this).attr('data-href')
                var new_href=href+'/surplus_time/'+time;
                window.location.href=new_href
                $('#complete').addClass('disabled')
            }
        }

    });
    function submit(time,submit_type){//提交答案
        // $('#load').css({
        //         'display':'block',
        //         'opacity': '1',
        //         'visibility': 'visible',
        //     })
        var my_answer={};
        var data={
                action:'answer_submit',
                _wpnonce:$('#inputSubmit').val(),
                grad_id:_grad_id,
                grad_type:_grad_type,
                type:_type,
                project_alias:'wzsd',
                post_id:<?=$post_id?>,
                match_questions:<?=json_encode($match_questions)?>,
                questions_answer:<?=json_encode($questions_answer)?>,
                project_more_id:$.Request('project_more_id'),

                my_answer:my_answer,
                surplus_time:time,
                submit_type:submit_type,//1:选手提交;2:错误达上限提交;3:时间到达提交;4:来回切
        }
        var leavePage= $.GetSession('leavePage','1');
        if(leavePage && leavePage['grad_id']===_grad_id && leavePage['grad_type']===_grad_type && leavePage['type']===_type){
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
                        var href="<?=home_url('matchs/answerLog/grad_id/'.$_GET['grad_id'].'/project_alias/'.$_GET['project_alias'].'/project_more_id/'.$_GET['project_more_id'].'/type/')?>"+_type;
                        window.location.href=href;
            　　　　}
                }
        })
    }
    if(<?=$count_down?><=0){//进入页面判断时间是否结束
        $.alerts('<?=__('考级结束', 'nlyd-student')?>');
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
        if(S<=0){//本轮考级结束
            if(S==0){
                $.alerts('<?=__('倒计时结束，即将提交答案', 'nlyd-student')?>')
            }else{
                $.alerts('<?=__('考级结束', 'nlyd-student')?>')
            }
            // setTimeout(function() {
                submit(0,3)
            // }, 1000);
        }
    });
})
</script>