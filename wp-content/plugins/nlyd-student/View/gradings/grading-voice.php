
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><div><?=__($project_title, 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">

                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row layui-row have-submit">
                        <div class="c_black match_info_font"><div><?=__('语音记忆', 'nlyd-student')?></div></div>
                        <div class="c_blue match_info_font">
                            <div>
                                <span class="count_down" data-seconds="<?=$count_down?>"><?=__('初始中', 'nlyd-student')?>...</span>
                            </div>
                        </div>
                        <div class="matching-sumbit" id="sumbit" style="display:none"><div><?=__('提交', 'nlyd-student')?></div></div>
                    </div>
                    <!-- 记忆 -->
                    <div class="complete_zoo">
                        <div class="ta_c c_black voice_title">正在播放语音中...</div>
                        <div class="voice_wait">
                            <div class="voice_img">
                                <img src="<?=student_css_url.'image/grading/voice.png'?>" alt="<?=__('开始播放', 'nlyd-student')?>">
                            </div>
                        </div>
                         <div class="a-btn a-btn-table" style="position: relative;top:0;margin-top:30px;margin-bottom: 20px;" id="complete" href="match_zoo"><div><?=__('记忆完成', 'nlyd-student')?></div></div>
                    </div>

                    <!-- 比赛 -->
                    <div class="complete_zoo" id="match_zoo" style="display:none">
                        <div class="matching-row layui-row">
                            <div class="matching-row-label"><div><?=__('辅助操作', 'nlyd-student')?></div></div>
                            <div class="matching-row-list">
                                <button class="matching-btn active c_white" id="prev"><?=__('前插一位', 'nlyd-student')?></button>
                                <button class="matching-btn active c_white" id="next"><?=__('后插一位', 'nlyd-student')?></button>
                            </div>
                        </div>
                        <div class="matching-number-zoo layui-row match_zoo">

                        </div>

                        <div class="matching-keyboard layui-row match_number">
                            <div class="matching-keyboard-row">
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="1"><div>1</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="2"><div>2</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="3"><div>3</div></div>
                            </div>
                            <div class="matching-keyboard-row">
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="4"><div>4</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="5"><div>5</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="6"><div>6</div></div>
                            </div>
                            <div class="matching-keyboard-row">
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="7"><div>7</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="8"><div>8</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="9"><div>9</div></div>
                            </div>
                            <div class="matching-keyboard-row"> 
                                <div class="bg_orange matching-key fs_16 c_white _del"><div><?=__('删除', 'nlyd-student')?></div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="0"><div>0</div></div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
<audio id="audio" autoplay="autoplay" preload type="audio/mpeg"></audio>
<script>
jQuery(function($) { 
    var isSubmit=false;//是否正在提交
    var _show=1;//1,准备区展示，2答题区展示
    var questions_answer=[];//题目
    var _grad_id=$.Request('grad_id');
    var _grad_type=$.Request('grad_type');
    var _type=$.Request('type');
    var ready_time="<?=$memory_type['memory_time']?>";//记忆时间
    var sys_second=ready_time;
    var answer_time="<?=$memory_type['answer_time']?>";//记忆时间
    var endTime=$.GetEndTime(ready_time);//结束时间
    var que_len=<?=$memory_type['length']?>;//多少个字符
    var file_url="<?=leo_match_url.'/upload/voice/'?>"
    var _index=0;
    init_question(que_len,_show)
    leaveMatchPage(function(){//窗口失焦提交
        submit(4);
    })

    $('#complete').click(function(){//记忆完成
        var _this=$(this);
        var href=_this.attr('href');
        $('.complete_zoo').hide();
        $('#'+href).show()
        $('.matching-sumbit').show();
        _show=2
        sys_second=answer_time
        var endTime=$.GetEndTime(answer_time);//结束时间
        var sessionData={
            grad_id:_grad_id,
            grad_type:_grad_type,
            type:_type,
            endTime:endTime,
            _show:2,
            questions_answer:questions_answer
        }
        $.SetSession('grade_question',sessionData)
    })
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
                if(_show==1){//记忆页面
                    $('.complete_zoo').hide();
                    $('#match_zoo').show()
                    $('.matching-sumbit').show();
                    _show=2
                    sys_second=answer_time
                    var endTime=$.GetEndTime(answer_time);//结束时间
                    var sessionData={
                        grad_id:_grad_id,
                        grad_type:_grad_type,
                        type:_type,
                        endTime:endTime,
                        _show:2,
                        questions_answer:questions_answer
                    }
                    $.SetSession('grade_question',sessionData)
                }else if(_show==2){//答题页面
                    clearInterval(timer)
                    submit(3)
                }
            }

        }, 1000);
    } 
    function init_question(question_leng,_show) {//初始化题目
        var grade_question=$.GetSession('grade_question','true');
        if(grade_question && grade_question['grad_id']===_grad_id && grade_question['grad_type']===_grad_type && grade_question['type']===_type){
            questions_answer=grade_question['questions_answer'];
            _show=2
            endTime=grade_question['endTime'];
            sys_second=$.GetSecond(endTime);
        }else{
            for(var i=0;i<question_leng;i++){
                var num=Math.floor(Math.random()*10);//生成0-9的随机数
                questions_answer.push(num)
            }
            var sessionData={
                grad_id:_grad_id,
                grad_type:_grad_type,
                type:_type,
                _show:_show,
                endTime:endTime,
                questions_answer:questions_answer
            }
            $.SetSession('grade_question',sessionData)
        }
        $.each(questions_answer,function(i,v){
            var dom1=i==0 ? '<div class="matching-number-match active"></div>' : '<div class="matching-number-match"></div>';
            $('.match_zoo').append(dom1)
        })
        $('.complete_zoo').hide();
        $('.complete_zoo').eq(_show-1).show();
       if(_show==2){
         $('.matching-sumbit').show();
       }else{//准备页面播放语音
            var audio=document.getElementById('audio');
            $('#audio').attr("src",file_url+questions_answer[_index]+".wav");
            audio.loop = false;
            audio.addEventListener('ended', function () {  
                _index++
                if(_index<=que_len-1){
                    $('#audio').attr("src",file_url+questions_answer[_index]+".wav"); 
                }else{
                    $('.complete_zoo').hide();
                    $('#match_zoo').show()
                    $('.matching-sumbit').show();
                    _show=2
                    sys_second=answer_time
                    var endTime=$.GetEndTime(answer_time);//结束时间
                    var sessionData={
                        grad_id:_grad_id,
                        grad_type:_grad_type,
                        type:_type,
                        endTime:endTime,
                        _show:2,
                        questions_answer:questions_answer
                    }
                    $.SetSession('grade_question',sessionData)
                }  
            }, false);
       }
    }
    function submit(submit_type){//提交答案
        // $('#load').css({
        //         'display':'block',
        //         'opacity': '1',
        //         'visibility': 'visible',
        //     })
        var my_answer=[];
        $('.match_zoo .matching-number-match').each(function(){
            var answer=$(this).text();
            my_answer.push(answer)
        })
        var data={
            grading_id:_grad_id,
                grading_type:_grad_type,
                questions_type:_type,
                grading_questions:questions_answer,
                questions_answer:questions_answer,
                action:'grading_answer_submit',
                my_answer:my_answer,
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
                    //return false;
                    if(res.data.url){
                        setTimeout(function(){
                            window.location.href=res.data.url
                        },300)
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
    // 比赛事件
    $('.matching-number-match').each(function(){//填充区域
        var _this=$(this);
        new AlloyFinger(_this[0], {
            touchStart: function () {
                _this.addClass("opacity");
            },
            touchMove: function () {
                _this.removeClass("opacity");
            },
            touchEnd: function () {
                _this.removeClass("opacity");
            },
            touchCancel: function () {
                _this.removeClass("opacity");
            },
            tap:function(){
                $('.matching-number-match').removeClass('active');
                _this.addClass('active');
            }
        })
    })
    $('.matching-keyboard .number').each(function(){//数字键盘
        var _this=$(this);
        new AlloyFinger(_this[0], {
            touchStart: function () {
                _this.addClass("opacity");
            },
            touchMove: function () {
                _this.removeClass("opacity");
            },
            touchEnd: function () {
                _this.removeClass("opacity");
            },
            touchCancel: function () {
                _this.removeClass("opacity");
            },
            tap:function(){
                var number=_this.attr('date-number');
                var active=$('.matching-number-match.active');
                var len=$('.matching-number-match').length;
                if(!$('.matching-number-match').eq(len-1).hasClass('active')){
                    active.text(number).removeClass('active').next('.matching-number-match').addClass('active');
                }else{
                    active.text(number);
                }
            }
        })
    })
    //删除tap事件
$('._del').each(function(){//数字键盘
    var _this=$(this);
    new AlloyFinger(_this[0], {//删除
        touchStart: function () {
            _this.addClass("opacity");
        },
        touchMove: function () {
            _this.removeClass("opacity");
        },
        touchEnd: function () {
            _this.removeClass("opacity");
        },
        touchCancel: function () {
            _this.removeClass("opacity");
        },
        tap:function(){
        // if(!_this.hasClass('opcity')){
        //     _this.addClass('opcity')
            var active=$('.matching-number-match.active');
            if(active.text()==""){//已经为空
                if(!$('.matching-number-match').eq(0).hasClass('active')){
                    active.prev('.matching-number-match').addClass('active')
                    
                }else{
                    active.next('.matching-number-match').addClass('active')
                }
                active.remove()
                var dom='<div class="matching-number-match"></div>'
                $('.match_zoo').append(dom)
                var len=$('.matching-number-match').length;
                var newDom=$('.matching-number-match').eq(len-1)
                new AlloyFinger(newDom[0], {
                    touchStart: function () {
                        newDom.addClass("opacity");
                    },
                    touchMove: function () {
                        newDom.removeClass("opacity");
                    },
                    touchEnd: function () {
                        newDom.removeClass("opacity");
                    },
                    touchCancel: function () {
                        newDom.removeClass("opacity");
                    },
                    tap:function(){
                        $('.matching-number-match').removeClass('active');
                        newDom.addClass('active');
                    }
                })
            }else{
                active.text('');

            }
        }
    })
})
    //前插tap事件
    new AlloyFinger($('#prev')[0], {
        touchStart: function () {
            $('#prev').addClass("opacity");
        },
        touchMove: function () {
            $('#prev').removeClass("opacity");
        },
        touchEnd: function () {
            $('#prev').removeClass("opacity");
        },
        touchCancel: function () {
            $('#prev').removeClass("opacity");
        },
        tap: function () {
            var len=$('.matching-number-match').length;
            var _this=$('#prev')
            if(!$('.matching-number-match').eq(len-1).hasClass('active')){
                var active=$('.matching-number-match.active');
                var dom='<div class="matching-number-match active"></div>';
                active.removeClass('active').before(dom);
                $('.match_zoo .matching-number-match').last().remove()
                var newDom=$('.matching-number-match.active')
                new AlloyFinger(newDom[0], {
                    touchStart: function () {
                        newDom.addClass("opacity");
                    },
                    touchMove: function () {
                        newDom.removeClass("opacity");
                    },
                    touchEnd: function () {
                        newDom.removeClass("opacity");
                    },
                    touchCancel: function () {
                        newDom.removeClass("opacity");
                    },
                    tap:function(){
                        $('.matching-number-match').removeClass('active');
                        newDom.addClass('active');
                    }
                })
            }else{
                $('.matching-number-match.active').text('')
            }
        }
    });
    //后插tap事件
    new AlloyFinger($('#next')[0], {
        touchStart: function () {
            $('#next').addClass("opacity");
        },
        touchMove: function () {
            $('#next').removeClass("opacity");
        },
        touchEnd: function () {
            $('#next').removeClass("opacity");
        },
        touchCancel: function () {
            $('#next').removeClass("opacity");
        },
        tap: function () {
            var _this=$('#next')
            $('.matching-number-match').each(function(i){
                if(i!=$('.matching-number-match').length-1){//如果不是最后一位
                    if($(this).hasClass('active')){
                        var dom='<div class="matching-number-match active"></div>'
                        $(this).removeClass('active').after(dom);
                        $('.match_zoo .matching-number-match').last().remove()

                        var newDom=$('.matching-number-match.active')
                        new AlloyFinger(newDom[0], {
                            touchStart: function () {
                                newDom.addClass("opacity");
                            },
                            touchMove: function () {
                                newDom.removeClass("opacity");
                            },
                            touchEnd: function () {
                                newDom.removeClass("opacity");
                            },
                            touchCancel: function () {
                                newDom.removeClass("opacity");
                            },
                            tap:function(){
                                $('.matching-number-match').removeClass('active');
                                newDom.addClass('active');
                            }
                        })
                    }
                }
            })
        }
    });
    layui.use('layer', function(){
        new AlloyFinger($('#sumbit')[0], {
            tap:function(){
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
                            submit(1);  
                        }
                        ,closeBtn:2
                        ,btnAagn: 'c' //按钮居中
                        ,shade: 0.3 //遮罩
                        ,isOutAnim:true//关闭动画
                    });
                }
                
        });
    });
})
</script>