<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><?=$post_title?></h1>
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
                        <div class="matching-sumbit" id="sumbit">提交</div>
                    </div>
                    <div class="matching-row">
                        <div class="matching-row-label">辅助操作</div>
                        <div class="matching-row-list">
                            <a class="matching-btn c_white" id="prev">前插一位</a>
                            <a class="matching-btn c_white" id="next">后插一位</a>
                        </div>
                    </div>
                    <div class="matching-number-zoo">
                        <?php for($i=0;$i<$str_length;++$i){ ?>
                        <div class="matching-number <?=$i==0?'active':'';?>"> </div>
                        <?php } ?>
                    </div>

                    <div class="matching-keyboard">
                        <div class="matching-keyboard-row">
                            <a class="matching-key fs_18 c_white number" date-number="1">1</a>
                            <a class="matching-key fs_18 c_white number" date-number="2">2</a>
                            <a class="matching-key fs_18 c_white number" date-number="3">3</a>
                        </div>
                        <div class="matching-keyboard-row">
                            <a class="matching-key fs_18 c_white number" date-number="4">4</a>
                            <a class="matching-key fs_18 c_white number" date-number="5">5</a>
                            <a class="matching-key fs_18 c_white number" date-number="6">6</a>
                        </div>
                        <div class="matching-keyboard-row">
                            <a class="matching-key fs_18 c_white number" date-number="7">7</a>
                            <a class="matching-key fs_18 c_white number" date-number="8">8</a>
                            <a class="matching-key fs_18 c_white number" date-number="9">9</a>
                        </div>
                        <div class="matching-keyboard-row">
                            <a class="matching-key fs_16 c_white" id="del">删除</a>
                            <a class="matching-key fs_18 c_white number" date-number="0">0</a>
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
    submit=function(time){//提交答案
            var my_answer=[];
            $('.matching-number-zoo .matching-number').each(function(){
                var answer=$(this).text();
                my_answer.push(answer)
            })
            var data={
                action:'answer_submit',
                _wpnonce:$('#inputSubmit').val(),
                match_id:<?=$_GET['match_id']?>,
                project_id:<?=$_GET['project_id']?>,
                match_more:<?=$_GET['match_more']?>,
                my_answer:my_answer,
                match_action:'subjectNumberBattle',
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
    if(window.location.host=='ydbeta.gjnlyd.com'){
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
        var D=d.day>0 ? d.day+'天' : '';
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
    $('.matching-number').each(function(i){//数字展示区tap事件
        var dom=$(this)[0]
        var hammertime = new Hammer(dom);
        hammertime.on("tap", function (e) {
            $('.matching-number').removeClass('active');
            $(e.target).addClass('active');
        });
    })
    $('.number').each(function(i){//键盘数字tap事件
        var dom=$(this)[0]
        var hammertime = new Hammer(dom);
        hammertime.on("tap", function (e) {
            var number=$(e.target).attr('date-number');
            var active=$('.matching-number.active');
            var len=$('.matching-number').length;
            if(!$('.matching-number').eq(len-1).hasClass('active')){
                active.text(number).removeClass('active').next('.matching-number').addClass('active');
            }else{
                active.text(number);
            }
        });
    })
    //删除tap事件
    var hammertime1 = new Hammer($('#del')[0]);
    hammertime1.on("tap", function (e) {
        var active=$('.matching-number.active');
        if(active.text().length<1){//已经为空
            if(!$('.matching-number').eq(0).hasClass('active')){
            active.removeClass('active').prev('.matching-number').addClass('active')
            }

        }else{

            active.text('');
        }
    });
    //前插tap事件
    var hammertime2 = new Hammer($('#prev')[0]);
    hammertime2.on("tap", function (e) {
        var len=$('.matching-number').length;
        if(!$('.matching-number').eq(len-1).hasClass('active')){
            
            var active=$('.matching-number.active');
            var dom='<div class="matching-number active"></div>';
            active.removeClass('active').before(dom);
            $('.matching-number-zoo .matching-number').last().remove()
            //新增dom绑定事件
            var hammerdom = new Hammer(active.prev('.matching-number')[0]);
            hammerdom.on("tap", function (ev) {
                $('.matching-number').removeClass('active');
                $(ev.target).addClass('active');
            });
        }else{
            $('.matching-number.active').text('')
        }
       
    });
    //后插tap事件
    var hammertime3 = new Hammer($('#next')[0]);
    hammertime3.on("tap", function (e) {
        $('.matching-number').each(function(i){
            if(i!=$('.matching-number').length-1){//如果不是最后一位
                if($(this).hasClass('active')){
                    var dom='<div class="matching-number active"></div>'
                    $(this).removeClass('active').after(dom);
                    $('.matching-number-zoo .matching-number').last().remove()
                    //新增dom绑定事件
                    var hammerdom = new Hammer($(this).next('.matching-number')[0]);
                    hammerdom.on("tap", function (ev) {
                        $('.matching-number').removeClass('active');
                        $(ev.target).addClass('active');
                    });
                }
            }
        })
    });
layui.use('layer', function(){
    var hammertime4 = new Hammer($('#sumbit')[0]);
    hammertime4.on("tap", function (e) {
        var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
        layer.open({
                type: 1
                ,maxWidth:300
                ,title: '提示' //不显示标题栏
                ,skin:'nl-box-skin'
                ,id: 'certification' //防止重复弹出
                ,content: '<div class="box-conent-wrapper">是否立即提交？</div>'
                ,btn: ['提交', '按错了', ]
                ,success: function(layero, index){
                }
                ,yes: function(index, layero){
                    layer.closeAll();
                    submit(time);    
                }
                ,btn2: function(index, layero){
                }
                ,closeBtn:2
                ,btnAagn: 'c' //按钮居中
                ,shade: 0.3 //遮罩
                ,isOutAnim:true//关闭动画
            });
            
    });
});

    
})
</script>