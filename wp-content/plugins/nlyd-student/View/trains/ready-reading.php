
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><div><?=__($match_title, 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">

                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row layui-row">
                        <div class="c_black match_info_font"><div><?=sprintf(__('第%s轮', 'nlyd-student'),$match_more)?></div></div>
                        <div class="c_blue match_info_font"><div><?=__('第1/1题', 'nlyd-student')?></div></div>
                        <div class="c_blue match_info_font">
                            <div>
                                <i class="iconfont">&#xe685;</i>
                                <span class="count_down" data-seconds="<?=$count_down?>">00:00:00</span>
                            </div>
                        </div>
                    </div>
                    <div class="matching-reading">
                        <div class="Glass"></div>
                        <div class="article-title fs_16 c_black"><?=$content->post_title?></div>
                        <?=$content->post_content?>
                        
                    </div>
                </div>
                <input type="hidden" name="questions_id" value="<?=$content->ID?>">
                <div class="a-btn a-btn-table" style="position: relative;top:0;margin-top:30px;margin-bottom: 20px;" id="complete"  data-href="<?=$url?>"><div><?=__('阅读完成', 'nlyd-student')?></div></div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(function($) {
    history.pushState(null, null, document.URL);
    window.addEventListener('popstate', function () {
        history.pushState(null, null, document.URL);
    });
    new AlloyFinger($('#complete')[0], {//阅读完成
        tap:function(){
            if(!$('#complete').hasClass('disabled')){
                var time=$('.count_down').attr('data-seconds');
                var href=$(this).attr('data-href')
                var new_href=href+'/end_time/'+$.GetEndTime($('.count_down').attr('data-seconds'))
                window.location.href=new_href
                $('#complete').addClass('disabled')
            }
        }
      
    });
    function submit(time){//提交答案
        // $('#load').css({
        //         'display':'block',
        //         'opacity': '1',
        //         'visibility': 'visible',
        //     })
        var my_answer={};
        var match_more=$.Request('match_more') ? $.Request('match_more') : '1';
        var data={
            action:'trains_submit',
            genre_id:$.Request('genre_id'),
            project_type:'wzsd',
            train_questions:<?=json_encode($match_questions)?>,
            train_answer:<?=json_encode($questions_answer)?>,
            my_answer:my_answer,
            surplus_time:time,
            match_more:match_more,
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
            complete: function(XMLHttpRequest, textStatus){
                if(textStatus=='timeout'){
                    var href="<?=home_url('trains/logs/type/'.$_GET['type'].'/match_more/'.$_GET['match_more'])?>";
                    window.location.href=href;
        　　　　}
            }
        })
    }
    // if(<?=$count_down?><=0){//进入页面判断时间是否结束
    //     $.alerts('比赛结束');
    //     setTimeout(function() {
    //         submit(0,3)
    //     }, 1000);
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
                submit(0)
            // }, 1000);
        }
    });
})
</script>