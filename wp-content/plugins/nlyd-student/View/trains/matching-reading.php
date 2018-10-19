
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <h1 class="mui-title"><?=__($title, 'nlyd-student')?></h1>
            </header>
            <div class="layui-row nl-border nl-content">
                <form class="layui-form" lay-filter='reading'>
                    <div class="remember width-margin width-margin-pc">
                        <div class="matching-row">
                            <span class="c_black match_info_font"><?=__($title, 'nlyd-student')?><span class="blue-font"><?=__('第一轮', 'nlyd-student')?></span></span>
                            <span class="c_blue ml_10 match_info_font"><?=__('第', 'nlyd-student')?><span id="number">1</span>/<?=!empty($match_questions) ? count($match_questions) : 1?><?=__('题', 'nlyd-student')?></span>
                            <span class="c_blue ml_10 match_info_font">
                                <i class="iconfont">&#xe685;</i>
                                <span class="count_down" data-seconds="<?=$count_down?>">00:00:00</span>
                            </span>
                            <div class="matching-sumbit match_info_font" id="sumbit"><?=__('提交', 'nlyd-student')?></div>
                        </div>
                        <div class="reading-question">
                            <?php
                            $key = 0;
                            if(!empty($match_questions)){
                                foreach ($match_questions as $k => $val ){

                                    $arr = array_count_values($questions_answer[$k]['problem_answer']);
                                    $checkbox = $arr[1] > 1 ? true : false;
                            ?>
                            <div class="matching-reading <?=$key==0?'active':''?>" data-index="<?=$key;?>" data-id="<?=$k;?>">
                                <p class="c_black"><?=$key+1;?>、<?=$val?></p>
                                <?php
                                foreach ($questions_answer[$k]['problem_select'] as $y => $v ){

                                ?>

                                 <div class="reading-select">
                                    <?php if($checkbox){ ?>
                                    <input type="checkbox" name='<?=$y?>' class="select_answer" data-name="<?=$y?>" lay-skin="primary">
                                    <?php }else{ ?>
                                    <input type="radio" name="<?=$key?>" class="select_answer" data-name="<?=$y?>" value="<?=$v;?>">
                                    <?php }?>
                                    <span><?=get_select($y)?>、<?=$v;?></span>
                                </div>

                                <?php } ?>
                            </div>
                            <?php ++$key;?>
                            <?php } }?>
                        </div>
                        
                        <div class="a-btn two">
                            <div class="a-two left disabled"><?=__('上一题', 'nlyd-student')?></div>
                            <div class="a-two right"><?=__('下一题', 'nlyd-student')?></div>
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
    var isSubmit=false;//是否正在提交
    /*var questions_answer=[];
        var match_questions=[];
        var checkIndex=['A','B','C','D','E','F','G']
        var leavePage= $.GetCookie('train_match','1');
        if(leavePage && leavePage['genre_id']==$.Request('genre_id') && leavePage['type']=='wzsd'){//记忆成功
            $('.count_down').attr('data-seconds',leavePage['count_down'])
            questions_answer=leavePage['questions_answer']
            match_questions=leavePage['match_questions']
            // var z=0
            // $.each(questions_answer,function(i,v){
            //     z++
            //     var select=''
            //     $.each(v.problem_select,function(index,value){
            //         select+='<div class="reading-select"><input type="radio" name="'+i+'" class="select_answer" data-name="'+value+'" value="'+value+'"><span>'+checkIndex[index]+'、'+value+'</span></div>'
            //     })
                
            //     var index0=z==1 ? 'active' : '';
            //     var matching_reading='<div class="matching-reading '+index0+'" data-index="'+z+'" data-id="'+i+'">'
            //         +'<p class="c_black">'+z+'、'+match_questions[i]+'</p>'
            //         +select
            //     +'</div>'
            //     $('.reading-question').append(matching_reading)
            // })
        }else{//未获取到比赛题目
            $.alerts('未检测到题目信息')
        }*/
    layui.use(['form'], function(){
        var form = layui.form
    })
    function submit(time){//提交答案
        if(!isSubmit){
            $('#load').css({
                'display':'block',
                'opacity': '1',
                'visibility': 'visible',
            })
            isSubmit=true;
            var my_answer={}
            $('.matching-reading').each(function(){
                var _this=$(this);
                var id=_this.attr('data-id');
                my_answer[id]=[];
                var flag=false;
                _this.find('.select_answer').each(function(e){
                    var __this=$(this);
                    if(__this.is(':checked')){
                        flag=true;
                        my_answer[id].push(__this.attr('data-name'));
                    };
                })
                if(!flag){//未作答
                    my_answer[id]=['-1']
                }
            })
            
        var data={
            action:'trains_submit',
            genre_id:$.Request('genre_id'),
            post_id:$.Request('post_id'),
            project_type:'wzsd',
            train_questions:<?=json_encode($match_questions)?>,
            train_answer:<?=json_encode($questions_answer)?>,
            my_answer:my_answer,
            surplus_time:time,
        }
            $.ajax({
                data:data,
                success:function(res,ajaxStatu,xhr){  
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
                error: function(jqXHR, textStatus, errorMsg){
                    isSubmit=false;
                    $('#load').css({
                            'display':'none',
                            'opacity': '0',
                            'visibility': 'hidden',
                        })
                }
            })
        }else{
            $.alerts('<?=__('正在提交答案', 'nlyd-student')?>')
        }
    }
    // if(<?=$count_down?><=0){//进入页面判断时间是否结束
    //     $.alerts('比赛结束');
    //     setTimeout(function() {
    //         submit(0)
    //     }, 1000);
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
            setTimeout(function() {
                submit(0)
            }, 1000);
        }
    });

    layui.use(['layer'], function(){


//提交tap事件
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
                ,btn: ['<?=__('按错了', 'nlyd-student')?>','<?=__('提交', 'nlyd-student')?>',  ]
                ,success: function(layero, index){
                }
                ,yes: function(index, layero){
                    layer.closeAll();
                }
                ,btn2: function(index, layero){
                    //按钮【按钮二】的回调
                    layer.closeAll();
                    submit(time)
                }
                ,closeBtn:2
                ,btnAagn: 'c' //按钮居中
                ,shade: 0.3 //遮罩
                ,isOutAnim:true//关闭动画
            });
        }
    });
})

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