
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper">
            
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><?=$match_title?></h1>
        </header>
            <div class="layui-row nl-border nl-content">

                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row">
                        <span class="c_black"><?=$project_title?>第<?=$match_more_cn?>轮</span>
                        <span class="c_blue ml_10">第1/1题</span>
                        <span class="c_blue ml_10">
                            <i class="iconfont">&#xe685;</i>
                            <span class="count_down" data-seconds="<?=$count_down?>">00:00:00</span>
                        </span>
                    </div>
                    <div class="matching-reading">
                        <div class="article-title"><?=$questions->post_title?></div>
                        <?=$questions->post_content?>
                    </div>
                </div>
                <input type="hidden" name="questions_id" value="<?=$questions->ID?>">
                <a class="a-btn" id="complete">阅读完成</a>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="_wpnonce" id="inputComplete" value="<?=wp_create_nonce('student_memory_complete_code_nonce');?>">
<input type="hidden" name="match_more" id="inputMatchMore" value="<?=isset($_GET['match_more']) ? $_GET['match_more'] : 1;?>"/>
<input type="hidden" name="_wpnonce" id="inputSubmit" value="<?=wp_create_nonce('student_answer_submit_code_nonce');?>">
<script>
jQuery(function($) { 

    $('body').on('click','#complete',function(){//记忆完成
        var data={
            action:'memory_complete',
            _wpnonce:$('#inputComplete').val(),
            match_id:<?=$_GET['match_id']?>,
            project_id:<?=$_GET['project_id']?>,
            match_more:$('#inputMatchMore').val(),
            match_action:'reading',
            question_id:$('input[name="question_id"]').val()
        }
        $.post(window.admin_ajax+"?date="+new Date().getTime(),data,function(res){

            if(res.success){
                if(res.data.url){
                    window.location.href=res.data.url
                }
            }else{
                $.alerts(res.data.info)
            }
        })
    })
    submit=function(time){//提交答案
        var my_answer={};
        var data={
            action:'answer_submit',
            _wpnonce:$('#inputSubmit').val(),
            match_id:<?=$_GET['match_id']?>,
            project_id:<?=$_GET['project_id']?>,
            match_more:<?=!empty($_GET['match_more']) ? $_GET['match_more'] : 1;?>,
            my_answer:my_answer,
            match_action:'subjectReading',
            surplus_time:time,
        }
        $.post(window.admin_ajax+"?date="+new Date().getTime(),data,function(res){
            $.DelSession('leavePage')
            if(res.success){
                if(res.data.url){
                    window.location.href=res.data.url
                }   
            }else{
                $.alerts(res.data.info)
            }
        })
    }
    if(window.location.host!='ydbeta.gjnlyd.com'){
        history.pushState(null, null, document.URL);
        window.addEventListener('popstate', function () {
            history.pushState(null, null, document.URL);
        });
        $(window).on("blur",function(){
            var leavePage = $.GetSession('leavePage','1');
            if(leavePage && leavePage['match_id']===$.Request('match_id') && leavePage['project_id']===$.Request('project_id') && leavePage['match_more']===$.Request('match_more')){
                leavePage['leavePage']+=1;
            }else{
                var sessionData={
                    match_id:$.Request('match_id'),
                    project_id:$.Request('project_id'),
                    match_more:$.Request('match_more'),
                    leavePage:1
                }
                leavePage= sessionData
            }
            
            $.SetSession('leavePage',leavePage)
        })  
        $(window).on("focus", function(e) {
            var leavePage= $.GetSession('leavePage','1');
            if(leavePage && leavePage['match_id']===$.Request('match_id') && leavePage['project_id']===$.Request('project_id') && leavePage['match_more']===$.Request('match_more')){
                var leveTimes=parseInt(leavePage['leavePage'])
                if(leveTimes>0 && leveTimes<3){
                    $.alerts('第'+leveTimes+'次离开考试页面,超过2次自动提交答题')
                }
                if(leveTimes>=3){
                    $.alerts('第'+leveTimes+'次离开考试页面,自动提交本轮答题')
                    var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
                    setTimeout(function() {
                        submit(time);
                    }, 1000);
                    submit(time);
                }
            }
        });
    }
    if($('.count_down').attr('data-seconds')<=0){//进入页面判断时间是否结束
        $.alerts('比赛结束');
        setTimeout(function() {
            submit(0)
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
                $.alerts('倒计时结束，即将提交答案')
            }else{
                $.alerts('比赛结束')
            }
            setTimeout(function() {
                submit(S)
            }, 1000);
        }
    });
})
</script>