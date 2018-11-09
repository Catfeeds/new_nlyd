<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><div><?=__($project_title, 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row layui-row have-submit">
                        <div class="c_black match_info_font"><div><?=__('国学经典默写', 'nlyd-student')?> <span id="number">1</span>/2</div></div>
                        <div class="c_blue match_info_font">
                           <div> 
                                <span class="count_down" data-seconds="<?=$count_down?>">00:00:00</span>
                            </div>
                        </div>
                        <div class="matching-sumbit" id="sumbit"><div><?=__('提交', 'nlyd-student')?></div></div>
                    </div>
                    <!-- <div class="layui-row">
                        <span class="count_down" data-seconds="<?=$count_down?>"><?=__('初始中', 'nlyd-student')?>...</span>
                    </div> -->
                        <div class="matching-reading layui-row active" data-index="0">
                            <div class="matching-number input"><input class="matching-number-input nl-foucs" type="text"></div>
                            <div class="matching-number input"><input class="matching-number-input nl-foucs" type="text"></div>
                            <div class="matching-number input"><input class="matching-number-input nl-foucs" type="text"></div>
                            <div class="matching-number input"><input class="matching-number-input nl-foucs" type="text"></div>
                            <div class="matching-number input"><input class="matching-number-input nl-foucs" type="text"></div>
                            <div class="matching-number input"><input class="matching-number-input nl-foucs" type="text"></div>
                            <div class="matching-number input"><input class="matching-number-input nl-foucs" type="text"></div>
                            <div class="matching-number input"><input class="matching-number-input nl-foucs" value="1" disabled type="text"></div>
                            <div class="matching-number input"><input class="matching-number-input nl-foucs" value="1" disabled type="text"></div>
                        </div>
                        <div class="matching-reading layui-row" data-index="1">
                            <div class="matching-number input"><input class="matching-number-input nl-foucs" value="1" disabled type="text"></div>
                            <div class="matching-number input"><input class="matching-number-input nl-foucs" type="text"></div>
                            <div class="matching-number input"><input class="matching-number-input nl-foucs" type="text"></div>
                            <div class="matching-number input"><input class="matching-number-input nl-foucs" type="text"></div>
                            <div class="matching-number input"><input class="matching-number-input nl-foucs" value="1" disabled type="text"></div>
                            <div class="matching-number input"><input class="matching-number-input nl-foucs" value="1" disabled type="text"></div>
                        </div>
                </div> 
            </div>
        </div>           
    </div>
</div>
<div class="a-btn two">
    <div class="a-two left disabled"><div><?=__('上一题', 'nlyd-student')?></div></div>
    <div class="a-two right"><div><?=__('下一题', 'nlyd-student')?></div></div>
</div>
<input type="hidden" name="_wpnonce" id="inputSubmit" value="<?=wp_create_nonce('student_answer_submit_code_nonce');?>">

<script>
jQuery(function($) { 
    var isSubmit=false;//是否正在提交
    var _match_id=1;
    var _project_id=2;
    var _match_more=3;
    leaveMatchPage(function(){//窗口失焦提交
        var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
        submit(time,4);
    })
    // var grade_question= $.GetSession('grade_question','1');
    // if(grade_question && grade_question['match_id']===_match_id && grade_question['project_id']===_project_id && grade_question['match_more']===_match_more){//从Session获取比赛题目,
    //     questions_answer=grade_question['questions_answer'];
    //     $.each(questions_answer,function(i,v){
    //         var dom='<div class="matching-number"><input class="matching-number-input nl-foucs" type="text"></div>'
    //         $('.matching-number-zoo').append(dom)
    //     })
    // }else{//未获取到比赛题目

    //     $.alerts('触发防作弊系统')
    //     window.location.href = '<?=home_url("/matchs/initialMatch/project_alias/szzb/match_id/")?>'+_match_id+'/project_more_id/'+$.Request('project_more_id');
    // }
    var questions_answer=['','','','','','','','','','','','',]
        $.each(questions_answer,function(i,v){
            var dom='<div class="matching-number input"><input class="matching-number-input nl-foucs" type="text"></div>'
            +'<div class="matching-number input"><input class="matching-number-input nl-foucs" value="1" disabled type="text"></div>'
            $('.matching-number-zoo').append(dom)
        })
    // if(<?=$count_down?><=0){//进入页面判断时间是否结束
    //     $.alerts('<?=__('比赛结束', 'nlyd-student')?>');
    //         submit(0,3)
    // }
    $('.count_down').countdown(function(S, d){//倒计时
        var D=d.day>0 ? d.day+'<?=__('天', 'nlyd-student')?>' : '';
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
    function submit(time,submit_type){//提交答案
        if(!isSubmit){
            // $('#load').css({
            //     'display':'block',
            //     'opacity': '1',
            //     'visibility': 'visible',
            // })
            // isSubmit=true;
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
                    isSubmit=true;
                    $('#load').css({
                        'display':'block',
                        'opacity': '1',
                        'visibility': 'visible',
                    })
                },
                success:function(res,ajaxStatu,xhr){  
                    // $.DelSession('leavePage')
                    if(res.success){
                        isSubmit=false;
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
                        isSubmit=false;
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
        }else{
            $.alerts('<?=__('正在提交答案', 'nlyd-student')?>')
        }
    }
   
layui.use('layer', function(){
    // mTouch('body').on('tap','#sumbit',function(e){
    new AlloyFinger($('#sumbit')[0], {
        tap:function(){
            var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
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


    var n=0;
    if($('.matching-reading').length<=1){
        $('.a-two.right').addClass('disabled')
    }
    // mTouch('body').on('tap','.a-two.left',function(e){//上一题
    new AlloyFinger($('.a-two.left')[0], {
        touchStart: function () {
            var left=$('.a-two.left');
            if(!left.hasClass('disabled')){
                left.addClass("opacity");
            }
        },
        touchMove: function () {
            $('.a-two.left').removeClass("opacity");
        },
        touchEnd: function () {
            $('.a-two.left').removeClass("opacity");
        },
        touchCancel: function () {
            $('.a-two.left').removeClass("opacity");
        },
        tap:function(){
            var left=$('.a-two.left');
            var len=$('.matching-reading').length-1;
            if(!left.hasClass('disabled')){
                if(n>0){
                    n--
                    $('#number').text(n+1)
                    if(n==0){
                        left.addClass('disabled')
                    }
                    $('.a-two.right').removeClass('disabled')
                    $('.matching-reading').each(function(){
                        $(this).removeClass('active')
                        if($(this).attr('data-index')==n){
                            $(this).addClass('active')
                        }
                    })
                    
                }

            }else{
                return false;
            }
        }
    });
    // mTouch('body').on('tap','.a-two.right',function(e){//下一题
    new AlloyFinger($('.a-two.right')[0], {
        touchStart: function () {
            var right=$('.a-two.right');
            if(!right.hasClass('disabled')){
                $('.a-two.right').addClass("opacity");
            }
        },
        touchMove: function () {
            $('.a-two.right').removeClass("opacity");
        },
        touchEnd: function () {
            $('.a-two.right').removeClass("opacity");
        },
        touchCancel: function () {
            $('.a-two.right').removeClass("opacity");
        },
        tap:function(){
            var right=$('.a-two.right');
            var len=$('.matching-reading').length-1;
            if(!right.hasClass('disabled')){
                if(n<len){
                    n++
                    $('#number').text(n+1)
                    if(n==len){
                        right.addClass('disabled')  
                    }
                    $('.a-two.left').removeClass('disabled')  
                    $('.matching-reading').each(function(){
                        $(this).removeClass('active')
                        if($(this).attr('data-index')==n){
                            $(this).addClass('active')
                        }
                    })
                }
            }else{
                return false;
            }
        }
    });
    
})
</script>