
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <h1 class="mui-title"><div><?=__('速读考级水平(自测)', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content">
                <form class="layui-form" lay-filter='reading'>
                    <div class="remember width-margin width-margin-pc">
                        <div class="matching-row layui-row have-submit">
                            <?php $count_match_questions = !empty($match_questions) ? count($match_questions) : 1; ?>
                            <div class="c_blue match_info_font"><div><?=sprintf(__('第<span id="number">1</span>/%s题', 'nlyd-student'),$count_match_questions)?></div></div>
                            <div class="c_blue match_info_font">
                                <div>
                                    <span class="count_down" data-seconds="300"><?=__('初始中', 'nlyd-student')?>...</span>
                                </div>
                            </div>
                            <div class="matching-sumbit" id="sumbit"><div><?=__('提交', 'nlyd-student')?></div></div>
                        </div>
                        <div class="reading-question">
                            <?php
                            $key = 0;
                            if(!empty($match_questions)){
                                foreach ($match_questions as $k => $val ){

                                    //var_dump($questions_answer[$k]['problem_answer']);
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
                                    <!-- 多选 -->
                                    <input type="checkbox" name='<?=$y?>' class="select_answer" data-name="<?=$y?>" lay-skin="primary">
                                    <?php }else{ ?>
                                    <!-- 单选 -->
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
                            <div class="a-two left disabled"><div><?=__('上一题', 'nlyd-student')?></div></div>
                            <div class="a-two right"><div><?=__('下一题', 'nlyd-student')?></div></div>
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
    $.DelSession('count');
    history.pushState(null, null, document.URL);
    window.addEventListener('popstate', function () {
        history.pushState(null, null, document.URL);
    });
    var isSubmit=false;//是否正在提交
    var _genre_id=$.Request('genre_id');
    var _history_id=$.Request('history_id');
    var _grad_type=$.Request('grad_type');
    var _type=$.Request('grad_type');
    var _length=$.Request('length');
    var sys_second=300;//记忆时间
    var endTime=$.GetEndTime(sys_second);//结束时间
    layui.use(['form'], function(){

    })
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
                submit()
            }

        }, 1000);
    } 
    function init_question(question_leng,_show) {//初始化时间
        var grade_question=$.GetSession('grade_question','true');
        if(grade_question && grade_question['genre_id']===_genre_id && grade_question['grad_type']===_grad_type && grade_question['type']===_type){
            endTime=grade_question['endTime'];
            sys_second=$.GetSecond(endTime);
        }else{
            var sessionData={
                genre_id:_genre_id,
                grad_type:_grad_type,
                type:_type,
                endTime:endTime,
            }
            $.SetSession('grade_question',sessionData)
        }
    }
    function submit(submit_type){//提交答案
        if(!isSubmit){
            // $('#load').css({
            //     'display':'block',
            //     'opacity': '1',
            //     'visibility': 'visible',
            // })
            // isSubmit=true;
            var my_answer={}
            $('.matching-reading').each(function(i){

                var _this=$(this);
                var id=_this.attr('data-id');
                //console.log(i,id)
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
                genre_id:_genre_id,
                history_id:_history_id,
                grading_type:_grad_type,
                questions_type:_type,
                post_id:$.Request('post_id'),
                grading_questions:<?=json_encode($match_questions)?>,
                questions_answer:<?=json_encode($questions_answer)?>,
                action:'grade_answer_submit',
                length:_length,
                usetime:$.Request('usetime'),
                my_answer:my_answer,
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
                        var href="<?=home_url('grade/answerLog/genre_id/'.$_GET['genre_id'].'/history_id/'.$_GET['history_id'].'/grad_type/'.$_GET['grad_type'].'/type/'.$_GET['type'].'/memory_lv/'.$_GET['memory_lv'])?>";
                        window.location.href=href;
            　　　　}
                }
            })
        }else{
            $.alerts('<?=__('正在提交答案', 'nlyd-student')?>')
        }
    }

    layui.use('layer', function(){
        var n=0;
        if($('.matching-reading').length<=1){
            $('.a-two.right').addClass('disabled')
        }
        function layOpen() {//提交
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
                    submit()
                }
                ,closeBtn:2
                ,btnAagn: 'c' //按钮居中
                ,shade: 0.3 //遮罩
                ,isOutAnim:true//关闭动画
            });
        }
        function left() {
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
        function right() {
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
        if('ontouchstart' in window){// 移动端
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
                    left()
                }
            });
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
                    right()
                }
            });
            new AlloyFinger($('#sumbit')[0], {//提交
                tap:function(){
                    layOpen()
                }
            });
        }else{
            $('body').on('click','.a-two.left',function(){//提交
                left()
            })
            $('body').on('click','.a-two.right',function(){//提交
                right()
            })
            $('body').on('click','#sumbit',function(){//提交
                layOpen()
            })
        }
    })
})
</script>