
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><div><?=__($grading_title, 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">

                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row layui-row have-submit">
                        <div class="c_black match_info_font"><div><?=__('圆周率', 'nlyd-student')?></div></div>
                        <div class="c_blue match_info_font">
                            <div>
                                <span class="count_down" data-seconds="900"><?=__('初始中', 'nlyd-student')?>...</span>
                            </div>
                        </div>
                        <div class="matching-sumbit" id="sumbit"><div><?=__('提交', 'nlyd-student')?></div></div>
                    </div>
                    <!-- 考级 -->
                    <div class="complete_zoo" id="match_zoo">
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
    var isSubmit=false;//是否正在提交
    var _show=1;//1,准备区展示，2答题区展示
    var _grad_id=$.Request('grad_id');
    var _grad_type=$.Request('grad_type');
    var _type=$.Request('type');
   
    var answer_time=<?=$memory_type['memory_time']?>;//记忆时间
    var sys_second=answer_time;
    var endTime=$.GetEndTime(answer_time);//结束时间
    var que_len="<?=$memory_type['length']?>";//多少个字符
    var que_PI="14159265358979323846264338327950288419716939937510582097494459230781640628620899862803482534211706798214808651328230664709384460955058223172535940812848111745028410270193852110555964462294895493038196"
    var questions_answer=que_PI.substring(0,que_len).split('')
    init_question(que_len)
    leaveMatchPage(function(){//窗口失焦提交
        submit(4);
    });
    count_down();
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
                $('.count_down').text(text).attr('data-seconds',sys_second);
            } else {//倒计时结束
                clearInterval(timer);
                submit(3);
            };

        }, 1000);
    } 
    function init_question(question_leng) {//初始化题目
        var grade_question=$.GetSession('grade_question','true');
        if(grade_question && grade_question['grad_id']===_grad_id && grade_question['grad_type']===_grad_type && grade_question['type']===_type){
            endTime=grade_question['endTime'];
            sys_second=$.GetSecond(endTime);
        }else{
            var sessionData={
                grad_id:_grad_id,
                grad_type:_grad_type,
                type:_type,
                endTime:endTime
            }
            $.SetSession('grade_question',sessionData)
        }
        for (var index = 0; index < question_leng; index++) {
            var dom=index==0?'<div class="matching-number active"></div>' : '<div class="matching-number"></div>';
            $('.match_zoo').append(dom)
        }
    }
    function submit(submit_type){//提交答案
        // $('#load').css({
        //         'display':'block',
        //         'opacity': '1',
        //         'visibility': 'visible',
        //     })
        var my_answer=[];
        $('.matching-number-zoo .matching-number').each(function(){
            var answer=$(this).text();
            my_answer.push(answer)
        })
        var data={
            grading_id:_grad_id,
            grading_type:_grad_type,
            questions_type:_type,
            action:'grading_answer_submit',
            grading_questions:questions_answer,
            questions_answer:questions_answer,
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
                        var href="<?=home_url('gradings/answerLog/grad_id/'.$_GET['grad_id'].'/grad_type/'.$_GET['grad_type'].'/type/'.$_GET['type'].'/memory_lv/'.$_GET['memory_lv'])?>";
                        window.location.href=href;
            　　　　}
                }
        })
    } 

    layui.use('layer', function(){
        function layOpen() {//提交
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
        function zonePress(_this){//填充区域
            $('.matching-number').removeClass('active');
            _this.addClass('active');
        }
        function numberPress(_this){//数字键盘
            var number=_this.attr('date-number');
            var active=$('.matching-number.active');
            var len=$('.matching-number').length;
            if(!$('.matching-number').eq(len-1).hasClass('active')){
                active.text(number).removeClass('active').next('.matching-number').addClass('active');
            }else{
                active.text(number);
            }
        }
        if('ontouchstart' in window){// 移动端
             // 考级事件
            $('.matching-number').each(function(){//填充区域
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
                        zonePress(_this)
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
                        numberPress(_this)
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
                        var active=$('.matching-number.active');
                        if(active.text()==""){//已经为空
                            if(!$('.matching-number').eq(0).hasClass('active')){
                                active.prev('.matching-number').addClass('active')
                                
                            }else{
                                active.next('.matching-number').addClass('active')
                            }
                            active.remove()
                            var dom='<div class="matching-number"></div>'
                            $('.match_zoo').append(dom)
                            var len=$('.matching-number').length;
                            var newDom=$('.matching-number').eq(len-1)
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
                                    $('.matching-number').removeClass('active');
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
                    var len=$('.matching-number').length;
                    if(!$('.matching-number').eq(len-1).hasClass('active')){
                        var active=$('.matching-number.active');
                        var dom='<div class="matching-number active"></div>';
                        active.removeClass('active').before(dom);
                        $('.match_zoo .matching-number').last().remove()
                        var newDom=$('.matching-number.active')
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
                                $('.matching-number').removeClass('active');
                                newDom.addClass('active');
                            }
                        })
                    }else{
                        $('.matching-number.active').text('')
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
                    $('.matching-number').each(function(i){
                        if(i!=$('.matching-number').length-1){//如果不是最后一位
                            if($(this).hasClass('active')){
                                var dom='<div class="matching-number active"></div>'
                                $(this).removeClass('active').after(dom);
                                $('.match_zoo .matching-number').last().remove()

                                var newDom=$('.matching-number.active')
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
                                        $('.matching-number').removeClass('active');
                                        newDom.addClass('active');
                                    }
                                })
                            }
                        }
                    })
                }
            });
            new AlloyFinger($('#sumbit')[0], {//提交
                tap:function(){
                    layOpen()
                }
            });
        }else{
            $('body').on('click','.matching-number',function(){
                var _this=$(this);
                zonePress(_this)
            })
            $('body').on('click','.matching-keyboard .number',function(){
                var _this=$(this);
                numberPress(_this)
            })
            $('body').on('click','._del',function(){
                var active=$('.matching-number.active');
                if(active.text()==""){//已经为空
                    if(!$('.matching-number').eq(0).hasClass('active')){
                        active.prev('.matching-number').addClass('active')
                    }else{
                        active.next('.matching-number').addClass('active')
                    }
                    active.remove()
                    var dom='<div class="matching-number"></div>'
                    $('.match_zoo').append(dom)
                }else{
                    active.text('');
                }
            })
            $('body').on('click','#prev',function(){
                var len=$('.matching-number').length;
                if(!$('.matching-number').eq(len-1).hasClass('active')){
                    var active=$('.matching-number.active');
                    var dom='<div class="matching-number active"></div>';
                    active.removeClass('active').before(dom);
                    $('.match_zoo .matching-number').last().remove()
                }else{
                    $('.matching-number.active').text('')
                }
            })
            $('body').on('click','#next',function(){
                $('.matching-number').each(function(i){
                    if(i!=$('.matching-number').length-1){//如果不是最后一位
                        if($(this).hasClass('active')){
                            var dom='<div class="matching-number active"></div>'
                            $(this).removeClass('active').after(dom);
                            $('.match_zoo .matching-number').last().remove()
                        }
                    }
                })
            })
            $('body').on('click','#sumbit',function(){//提交
                layOpen()
            })
        }
    })
})
</script>