
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <h1 class="mui-title"><?=$match_title?></h1>
            </header>
            <div class="layui-row nl-border nl-content">
                <form class="layui-form" lay-filter='reading'>
                    <div class="remember width-margin width-margin-pc">
                        <div class="matching-row">
                            <span class="c_black"><?=$project_title?><span class="blue-font">第<?=$match_more_cn?>轮</span></span>
                            <span class="c_blue ml_10">第<span id="number">1</span>/<?=count($match_questions)?>题</span>
                            <span class="c_blue ml_10">
                                <i class="iconfont">&#xe685;</i>
                                <span class="count_down" data-seconds="<?=$count_down?>">00:00:00</span>
                            </span>
                            <div class="matching-sumbit" id="sumbit">提交</div>
                        </div>
                        <div class="reading-question">
                            <?php
                            $key = 0;
                            foreach ($match_questions as $k => $val ){ ?>
                            <div class="matching-reading <?=$key==0?'active':''?>" data-index="<?=$key;?>" data-id="<?=$k;?>">
                                <p class="c_black"><?=$key+1;?>.<?=$val?></p>
                                <?php
                                foreach ($questions_answer[$k]['problem_select'] as $y => $v ){
                                   // print_r($v);
                                ?>
                                <div class="reading-select">
                                    <input type="checkbox" name='<?=$y?>' class="select_answer" lay-skin="primary">
                                    <span  class="c_black"><?=get_select($y)?>. <?=$v;?></span>
                                </div>
                                <?php } ?>
                            </div>
                            <?php ++$key;?>
                            <?php } ?>
                        </div>
                        
                        <div class="a-btn two">
                            <div class="a-two left disabled">上一题</div>
                            <div class="a-two right">下一题</div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="_wpnonce" id="inputSubmit" value="<?=wp_create_nonce('student_answer_submit_code_nonce');?>">


<script>
jQuery(function($) { 
    layui.use(['form'], function(){

    })
    history.pushState(null, null, document.URL);
    window.addEventListener('popstate', function () {
        history.pushState(null, null, document.URL);
    });
    submit=function(time){//提交答案
        var my_answer={}
        $('.matching-reading').each(function(){
            var _this=$(this);
            var id=_this.attr('data-id');
            my_answer[id]=[];
            _this.find('.select_answer').each(function(e){
                var __this=$(this);
                if(__this.is(':checked')){
                    my_answer[id].push(__this.attr('name'))
                }
            })
        })

        var data={
            action:'answer_submit',
            _wpnonce:$('#inputSubmit').val(),
            match_id:<?=$_GET['match_id']?>,
            project_id:<?=$_GET['project_id']?>,
            match_more:<?=!empty($_GET['match_more']) ? $_GET['match_more'] : 1?>,
            my_answer:my_answer,
            match_action:'subjectReading',
            surplus_time:time,
        }
        //console.log(data)
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
                var time=$("#dataTime").attr('data-time')?$("#dataTime").attr('data-time'):0;
                setTimeout(function() {
                    submit(time);
                }, 1000);
                submit(time);
            }
        }
    });
    if($('.count_down').attr('data-seconds')<=0){//进入页面判断时间是否结束
        $.alerts('比赛结束');
        setTimeout(function() {
            submit($('.count_down').attr('data-seconds'))
        }, 1000);
    }
    $('.count_down').countdown(function(S, d){//倒计时
        var D=d.day>0 ? d.day+'天' : '';
        var h=d.hour<10 ? '0'+d.hour : d.hour;
        var m=d.minute<10 ? '0'+d.minute : d.minute;
        var s=d.second<10 ? '0'+d.second : d.second;
        var time=D+h+':'+m+':'+s;
        var html="<span data-time='"+S+"' id='dataTime'>"+time+"</span>"
         $(this).html(html);
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

    layui.use(['layer'], function(){


//提交tap事件
var hammertime4 = new Hammer($('#sumbit')[0]);
    var time=$("#dataTime").attr('data-time')?$("#dataTime").attr('data-time'):0;
    hammertime4.on("tap", function (e) {
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
                setTimeout(function() {
                    submit(time)
                }, 1000);
            }
            ,btn2: function(index, layero){
                //按钮【按钮二】的回调
            }
            ,closeBtn:2
            ,btnAagn: 'c' //按钮居中
            ,shade: 0.3 //遮罩
            ,isOutAnim:true//关闭动画
        });
        
    });
})

    var n=0;
    var hammertime1 = new Hammer($('.a-two.left')[0]);
    hammertime1.on("tap", function (e) {//上一题
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
        
    });
    var hammertime2 = new Hammer($('.a-two.right')[0]);
    
    hammertime2.on("tap", function (e) {//下一题
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
    });
})
</script>