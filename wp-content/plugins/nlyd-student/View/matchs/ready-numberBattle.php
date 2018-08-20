
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><?=$post_title?></h1>
        </header>
            <div class="layui-row nl-border nl-content">

                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row">
                        <span class="fs-14"><?=$match_title?><span class="blue-font">第<?=$match_more_cn?>轮</span></span>
                        <span class="fs-14">第1/1题</span>
                        <span class="blue-font fs-14">
                            <i class="iconfont">&#xe685;</i>
                            <span class="count_down" data-seconds="<?=$count_down?>">00:00:00</span>
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
                        <?php if(!empty($list)):
                            foreach ($list as $v){
                        ?>
                        <div class="matching-number"><?=$v?></div>
                            <?php } ?>
                        <?php endif;?>
                    </div>
                </div>
                <div class="a-btn" id="complete">记忆完成</div>
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
            match_action:'numberBattle',
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
        }
        $.post(window.admin_ajax+"?date="+new Date().getTime(),data,function(res){
            if(res.success){
                //return false;
                if(res.data.url){
                    setTimeout(function(){
                        window.location.href=res.data.url
                    },300)
                }   
            }else{
                $.alerts(res.data.info)
            }
        })
    }
    if($('.count_down').attr('data-seconds')<=0){//进入页面判断时间是否结束
        $.alerts('比赛结束');
        setTimeout(() => {
            submit($('.count_down').attr('data-seconds'))
        }, 1000);
    }
    $('.count_down').countdown(function(S, d){//倒计时
        var D=d.day>0 ? d.day : '';
        var h=d.hour<10 ? '0'+d.hour : d.hour;
        var m=d.minute<10 ? '0'+d.minute : d.minute;
        var s=d.second<10 ? '0'+d.second : d.second;
        var time=D+h+':'+m+':'+s;
        var items = $(this).text(time);
        if(S<=0){//本轮比赛结束
            if(S==0){
                $.alerts('倒计时结束，即将提交答案')
            }else{
                $.alerts('比赛结束')
            }
            setTimeout(() => {
                submit(S)
            }, 1000);
        }
    });
    $('.matching-btn').each(function(i){
        var hammertime = new Hammer($('.matching-btn')[i]);
        hammertime.on("tap", function (e) {
            $('.matching-btn').removeClass('active');
            $(e.target).addClass('active')
            var text=parseInt($(e.target).text())
            $('.matching-number').removeClass('border-right');
            if(text!='NAN'){
                $('.matching-number').each(function(j){
                    if((j+1)%text==0){
                        $(this).addClass('border-right')
                    }
                })
            }
        });
    })
})
</script>