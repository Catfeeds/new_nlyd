
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><div><?=__('速记考级水平(自测)', 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">

                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row layui-row have-submit">
                        <div class="c_black match_info_font"><div><?=__('语音记忆', 'nlyd-student')?></div></div>
                        <div class="c_blue match_info_font">
                            <div>
                                <span class="count_down" data-seconds="<?=$count_down?>" style="display:none;"><?=__('初始中', 'nlyd-student')?>...</span>
                            </div>
                        </div>
                        <div class="matching-sumbit" id="sumbit" style="display:none"><div><?=__('提交', 'nlyd-student')?></div></div>
                    </div>
                    <!-- 记忆 -->
                    <div class="complete_zoo">
                        <div class="ta_c c_black voice_title"><?=__('正在播放语音中', 'nlyd-student')?>...</div>
                        <div class="voice_wait">
                            <div class="voice_img">
                                <img src="<?=student_css_url.'image/grading/voice.png'?>" alt="<?=__('开始播放', 'nlyd-student')?>">
                            </div>
                        </div>
                         <!-- <div class="a-btn a-btn-table" style="position: relative;top:0;margin-top:30px;margin-bottom: 20px;" id="complete" href="match_zoo"><div><?=__('记忆完成', 'nlyd-student')?></div></div> -->
                    </div>

                    <!-- 考级 -->
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
<script>
jQuery(function($) { 
    $.DelSession('count');
    var _grading_num=<?=$num?>;
    var _memory_lv=<?=isset($_GET['memory_lv']) ? $_GET['memory_lv'] : 1 ;?>;
    var isSubmit=false;//是否正在提交
    var _show=1;//1,准备区展示，2答题区展示
    var questions_answer=[];//题目
    var _genre_id=$.Request('genre_id');
    var _grad_type=$.Request('grad_type');
    var _type=$.Request('type');
    var answer_time="<?=$memory_type['answer_time']?>";//记忆时间
    var que_len=<?=$memory_type['length']?>;//多少个字符
    // var que_len=5;
    var ready_time=que_len+1;//记忆时间
    var sys_second=answer_time;
    var endTime=$.GetEndTime(answer_time);//结束时间
    // var file_url="<?=leo_match_url.'/upload/voice/'?>"
    var _index=0;
    var spriteData={
        0:{start:0,length:1},
        1:{start:1,length:1},
        2:{start:2,length:1},
        3:{start:3,length:1},
        4:{start:4,length:1},
        5:{start:5,length:1},
        6:{start:6,length:1},
        7:{start:7,length:1},
        8:{start:8,length:1},
        9:{start:9,length:1},
    }
    init_question(que_len,_show)
    console.log(questions_answer)
    // leaveMatchPage(function(){//窗口失焦提交
    //     submit();
    // })

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
            genre_id:_genre_id,
            grad_type:_grad_type,
            type:_type,
            endTime:endTime,
            _show:2,
            questions_answer:questions_answer
        }
        $.SetSession('grade_question',sessionData)
    })
    
    function count_down(){
        // sys_second=answer_time
        $('.count_down').show()
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
    function init_question(question_leng,_show) {//初始化题目
        var grade_question=$.GetSession('grade_question','true');
        if(grade_question && grade_question['genre_id']===_genre_id && grade_question['grad_type']===_grad_type && grade_question['type']===_type){
            questions_answer=grade_question['questions_answer'];
            _show=2
            if(!grade_question['endTime']){
                var sessionData={
                    genre_id:_genre_id,
                    grad_type:_grad_type,
                    type:_type,
                    endTime:endTime,
                    _show:2,
                    questions_answer:questions_answer
                }
                $.SetSession('grade_question',sessionData)
            }else{
                endTime=grade_question['endTime'];
                sys_second=$.GetSecond(endTime);
            }
            count_down()
        }else{
            for(var i=0;i<question_leng;i++){
                var num=Math.floor(Math.random()*10);//生成0-9的随机数
                questions_answer.push(num)
            }
            var sessionData={
                genre_id:_genre_id,
                grad_type:_grad_type,
                type:_type,
                _show:_show,
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
            var doms='<audio id="audio" autoplay="false" preload type="audio/mpeg">' 
                        +'<source src="<?=leo_match_url.'/upload/voice/all.wav'?>" type="audio/mpeg" />'
                    +'</audio>'
            $('body').append(doms)
            var audio=document.getElementById('audio');
            var voice_title=document.getElementsByClassName('voice_title')[0];
            var u = navigator.userAgent;
            if(u.indexOf('Android') > -1 || u.indexOf('Linux') > -1){
                audio.currentTime = spriteData[questions_answer[_index]].start;
            }else{
                audio.addEventListener("canplay",function() {
                        //设置播放时间
                    audio.currentTime = spriteData[questions_answer[_index]].start;
                });
            }
            if (typeof WeixinJSBridge == "object" && typeof WeixinJSBridge.invoke == "function") {
                audio.play();
            } else {
                //監聽客户端抛出事件"WeixinJSBridgeReady"
                if (document.addEventListener) {
                    document.addEventListener("WeixinJSBridgeReady", function(){
                        audio.play();
                    }, false);
                } else if (document.attachEvent) {
                    document.attachEvent("WeixinJSBridgeReady", function(){
                        audio.play();
                    });
                    document.attachEvent("onWeixinJSBridgeReady", function(){
                        audio.play();
                    });
                }
            }
            audio.play();
            audio.addEventListener('timeupdate', function(){
                if(_index<=que_len-1){
                    if(!spriteData[questions_answer[_index]]){

                    }else{
                        var start=spriteData[questions_answer[_index]]['start'];
                        var len=spriteData[questions_answer[_index]]['length'];
                        if (this.currentTime >= start+len) {
                            this.pause();
                            _index++
                            if(_index<=que_len-1){
                                this.currentTime = spriteData[questions_answer[_index]].start;
                                this.play();
                            }else{
                                $('.complete_zoo').hide();
                                $('#match_zoo').show()
                                $('.matching-sumbit').show();
                                _show=2
                                sys_second=answer_time
                                var endTime=$.GetEndTime(answer_time);//结束时间
                                var sessionData={
                                    genre_id:_genre_id,
                                    grad_type:_grad_type,
                                    type:_type,
                                    endTime:endTime,
                                    _show:2,
                                    questions_answer:questions_answer
                                }
                                $.SetSession('grade_question',sessionData)
                                count_down()
                            }
                            
                        }
                    }

                }
            }, false);
            voice_title.addEventListener("click",function(e){
                audio.play();
            }, false);

            // $('#audio').attr("src",file_url+questions_answer[_index]+".wav");
            // audio.loop = false;
            // audio.addEventListener('ended', function () {
            //     _index++
            //     if(_index<=que_len-1){
            //         $('#audio').attr("src",file_url+questions_answer[_index]+".wav"); 
            //     }else{
            //         $('.complete_zoo').hide();
            //         $('#match_zoo').show()
            //         $('.matching-sumbit').show();
            //         _show=2
            //         sys_second=answer_time
            //         var endTime=$.GetEndTime(answer_time);//结束时间
            //         var sessionData={
            //             genre_id:_genre_id,
            //             grad_type:_grad_type,
            //             type:_type,
            //             endTime:endTime,
            //             _show:2,
            //             questions_answer:questions_answer
            //         }
            //         $.SetSession('grade_question',sessionData)
            //         count_down()
            //     }  
            // }, false);

            // var str_que=questions_answer.join(',')
            // if(!('speechSynthesis' in window)) {
            //     throw alert("对不起，您的浏览器不支持")
            // }
            // $('.voice_title').click(function(){
            //     play()
            // })
            // play()
            // function play() {
            //     to_speak = new SpeechSynthesisUtterance(str_que);
            //     to_speak.rate = 0.545;// 设置播放语速，范围：0.1 - 10之间
            //     var voices = speechSynthesis.getVoices();
            //     to_speak.voice = voices[0];
            //     window.speechSynthesis.speak(to_speak);
            // }

            // to_speak.onend = function() { 
            //     window.speechSynthesis.cancel();
            //     $('.complete_zoo').hide();
            //     $('#match_zoo').show()
            //     $('.matching-sumbit').show();
            //     _show=2
            //     sys_second=answer_time
            //     var endTime=$.GetEndTime(answer_time);//结束时间
            //     var sessionData={
            //         genre_id:_genre_id,
            //         grad_type:_grad_type,
            //         type:_type,
            //         endTime:endTime,
            //         _show:2,
            //         questions_answer:questions_answer
            //     }
            //     $.SetSession('grade_question',sessionData)
            //     count_down()
            // }
       }
    }
    function submit(){//提交答案
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
            grading_num:_grading_num,
            memory_lv:_memory_lv,
            genre_id:_genre_id,
            grading_type:_grad_type,
            questions_type:_type,
            grading_questions:questions_answer,
            questions_answer:questions_answer,
            action:'grade_answer_submit',
            my_answer:my_answer,
        }

        // var leavePage= $.GetSession('leavePage','1');
        //     if(leavePage && leavePage['genre_id']===_genre_id && leavePage['grad_type']===_grad_type && leavePage['type']===_type){
        //         if(leavePage.Time){
        //             data['leave_page_time']=leavePage.Time;
        //         }
        //     }
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
                        var href="<?=home_url('matchs/answerLog/genre_id/'.$_GET['genre_id'].'/project_alias/'.$_GET['project_alias'].'/project_more_id/'.$_GET['project_more_id'].'/type/')?>"+_type;
                        window.location.href=href;
            　　　　}
                }
        })
    } 
    // 考级事件
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
                            submit();  
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