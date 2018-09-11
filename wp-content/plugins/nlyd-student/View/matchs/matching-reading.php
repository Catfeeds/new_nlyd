
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
                            <span class="c_black match_info_font"><?=$project_title?><span class="blue-font">第<?=$match_more_cn?>轮</span></span>
                            <span class="c_blue ml_10 match_info_font">第<span id="number">1</span>/<?=!empty($match_questions) ? count($match_questions) : 1?>题</span>
                            <span class="c_blue ml_10 match_info_font">
                                <i class="iconfont">&#xe685;</i>
                                <span class="count_down" data-seconds="<?=$count_down?>">00:00:00</span>
                            </span>
                            <div class="matching-sumbit match_info_font" id="sumbit">提交</div>
                        </div>
                        <div class="reading-question">
                            <?php
                            $key = 0;
                            if(!empty($match_questions)){
                                foreach ($match_questions as $k => $val ){ ?>
                            <div class="matching-reading <?=$key==0?'active':''?>" data-index="<?=$key;?>" data-id="<?=$k;?>">
                                <p class="c_black"><?=$key+1;?>、<?=$val?></p>
                                <?php
                                foreach ($questions_answer[$k]['problem_select'] as $y => $v ){
                                   // print_r($v);
                                ?>
                                <!-- 多选 -->
                                <div class="reading-select">
                                    <input type="checkbox" name='<?=$y?>' class="select_answer" data-name="<?=$y?>" lay-skin="primary" title="<?=get_select($y)?>、<?=$v;?>">
                                </div>
                                <!-- 单选 -->
                                <!-- <div class="reading-select">
                                    <input type="radio" name="<?=$key?>" class="select_answer" data-name="<?=$y?>" value="<?=$v;?>" title="<?=get_select($y)?>、<?=$v;?>">
                                </div> -->
                                <?php } ?>
                            </div>
                            <?php ++$key;?>
                            <?php } }?>
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
    leaveMatchPage(function(){//窗口失焦提交
        var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
        submit(time,4);
    })
    layui.use(['form'], function(){

    })

    function submit(time,submit_type){//提交答案
        var my_answer={}
        $('.matching-reading').each(function(){
            var _this=$(this);
            var id=_this.attr('data-id');
            my_answer[id]=[];
            _this.find('.select_answer').each(function(e){
                var __this=$(this);
                if(__this.is(':checked')){
                    my_answer[id].push(__this.attr('data-name'))
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
            submit_type:submit_type,//1:选手提交;2:错误达上限提交;3:时间到达提交;4:来回切
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
    if($('.count_down').attr('data-seconds')<=0){//进入页面判断时间是否结束
        $.alerts('比赛结束');
        setTimeout(function() {
            submit(0,3)
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
                submit(0,3)
            }, 1000);
        }
    });

    layui.use(['layer'], function(){


//提交tap事件
    mTouch('body').on('tap','#sumbit',function(e){
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
                setTimeout(function() {
                    submit(time,1)
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
    if($('.matching-reading').length==1){
        $('.a-two.right').addClass('disabled')
    }
    mTouch('body').on('tap','.a-two.left',function(e){//上一题
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
    mTouch('body').on('tap','.a-two.right',function(e){//下一题
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