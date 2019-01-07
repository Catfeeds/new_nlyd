
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><div><?=__('记忆考级水平(自测)', 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">

                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row layui-row have-submit">
                        <div class="c_black match_info_font"><div><?=__($type_title, 'nlyd-student')?></div></div>
                        <div class="c_blue match_info_font">
                            <div>
                                <span class="count_down" data-seconds="<?=$memory_type['memory_time']?>"><?=__('初始中', 'nlyd-student')?>...</span>
                            </div>
                        </div>
                        <div class="matching-sumbit" id="sumbit" style="display:none"><div><?=__('提交', 'nlyd-student')?></div></div>
                    </div>
                    <!-- 记忆 -->
                    <div class="complete_zoo">
                        <div class="matching-row layui-row">
                            <div class="matching-row-label"><div><?=__('划辅助线', 'nlyd-student')?></div></div>
                            <div class="matching-row-list">
                                <button class="matching-btn ready-btn active"><?=__('不划', 'nlyd-student')?></button>
                                <button class="matching-btn ready-btn">2</button>
                                <button class="matching-btn ready-btn">3</button>
                                <button class="matching-btn ready-btn">4</button>
                                <button class="matching-btn ready-btn">5</button>
                                <button class="matching-btn ready-btn">8</button>
                            </div>
                        </div>
                        <div class="matching-number-zoo layui-row ready_zoo">
                            <div class="Glass"></div>
                        </div>
                        <div class="a-btn a-btn-table" style="position: relative;top:0;margin-top:30px;margin-bottom: 20px;" id="complete" href="match_zoo"><div><?=__('记忆完成', 'nlyd-student')?></div></div>
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

                        <div class="matching-keyboard layui-row match_zimu"  style="display:none">
                            <div class="matching-keyboard-row">
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="A"><div>A</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="B"><div>B</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="C"><div>C</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="D"><div>D</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="E"><div>E</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="F"><div>F</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="G"><div>G</div></div>
                                
                            </div>
                            <div class="matching-keyboard-row">
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="H"><div>H</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="I"><div>I</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="J"><div>J</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="K"><div>K</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="L"><div>L</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="M"><div>M</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="N"><div>N</div></div>

                            </div>
                            <div class="matching-keyboard-row">
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="O"><div>O</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="P"><div>P</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="Q"><div>Q</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="R"><div>R</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="S"><div>S</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="T"><div>T</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="U"><div>U</div></div>
                                
                            </div>
                            <div class="matching-keyboard-row">
                                <div class="bg_orange matching-key fs_16 c_white _del"><div><?=__('删除', 'nlyd-student')?></div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="V"><div>V</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="W"><div>W</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="X"><div>X</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="Y"><div>Y</div></div>
                                <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="Z"><div>Z</div></div>
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
    history.pushState(null, null, document.URL);
    window.addEventListener('popstate', function () {
        history.pushState(null, null, document.URL);
    });
    var _history_id=<?=$history_id?>;
    var _memory_lv=<?=isset($_GET['memory_lv']) ? $_GET['memory_lv'] : 1 ;?>;
    var isSubmit=false;//是否正在提交
    var _show=1;//1,准备区展示，2答题区展示
    var questions_answer=[];//题目
    var question_type="<?=isset($_GET['type']) && $_GET['type'] == 'sz' ? 1 : 2;?>";//1，数字.2,字母
    var _genre_id=$.Request('genre_id');
    var _grad_type=$.Request('grad_type');
    var _type=$.Request('type');
    var ready_time="<?=$memory_type['memory_time']?>";//记忆时间
    var sys_second=ready_time;
    var answer_time="<?=$memory_type['answer_time']?>";//记忆时间
    var endTime=$.GetEndTime(ready_time);//结束时间
    var que_len="<?=$memory_type['length']?>";//多少个字符
    init_question(que_len,_show,question_type)
    console.log(questions_answer)
    $.each(questions_answer,function(i,v){
        var dom='<div class="matching-number-readys">'+v+'</div>';
        $('.ready_zoo').append(dom)
        var dom1=i==0 ? '<div class="matching-number-match active"></div>' : '<div class="matching-number-match"></div>';
        $('.match_zoo').append(dom1)
    })
    $('#complete').click(function(){//记忆完成
        var _this=$(this);
        var href=_this.attr('href');
        $('.complete_zoo').hide();
        $('#'+href).show()
        $('.matching-sumbit').show();
        _show=2
        sys_second=answer_time;
        var endTime=$.GetEndTime(answer_time);//结束时间
        var sessionData={
            genre_id:_genre_id,
            grad_type:_grad_type,
            type:_type,
            question_type:question_type,
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
                        genre_id:_genre_id,
                        grad_type:_grad_type,
                        type:_type,
                        question_type:question_type,
                        endTime:endTime,
                        _show:2,
                        questions_answer:questions_answer
                    }
                    $.SetSession('grade_question',sessionData)
                }else if(_show==2){//答题页面
                    clearInterval(timer)
                    submit()
                }
            }

        }, 1000);
    } 
    function init_question(question_leng,_show,question_type) {//初始化题目
        var grade_question=$.GetSession('grade_question','true');
        if(grade_question && grade_question['genre_id']===_genre_id && grade_question['grad_type']===_grad_type && grade_question['type']===_type){
            questions_answer=grade_question['questions_answer'];
            question_type=grade_question['question_type']
            _show=grade_question['_show']
            endTime=grade_question['endTime'];
            sys_second=$.GetSecond(endTime)
        }else{
            for(var i=0;i<question_leng;i++){
                if(question_type==1){
                    var num=Math.floor(Math.random()*10);//生成0-9的随机数
                }else if(question_type==2){
                    var num=randZF();//生成A-Z的随机数
                }
                questions_answer.push(num)
            }
            var sessionData={
                genre_id:_genre_id,
                grad_type:_grad_type,
                type:_type,
                question_type:question_type,
                _show:_show,
                endTime:endTime,
                questions_answer:questions_answer
            }
            $.SetSession('grade_question',sessionData)
        }
        $('.matching-keyboard').hide();//键盘
        $('.complete_zoo').hide();
        $('.complete_zoo').eq(_show-1).show();
        $('.matching-keyboard').eq(question_type-1).show();
        $('.matching-keyboard').eq(question_type).remove()
        if(_show==2){
         $('.matching-sumbit').show();
       }
       
    }
    function randZF() {//生成随即字符
        var arr=["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"]
        var pos = Math.round(Math.random() * (arr.length - 1));
        return arr[pos];
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
            history_id:_history_id,
            memory_lv:_memory_lv,
            genre_id:_genre_id,
            grading_type:_grad_type,
            questions_type:_type,
            grading_questions:questions_answer,
            questions_answer:questions_answer,
            action:'grade_answer_submit',
            my_answer:my_answer,
        }
        console.log(data)
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
                        var href="<?=home_url('grade/answerLog/genre_id/'.$_GET['genre_id'].'/history_id/'.$_GET['history_id'].'/grad_type/'.$_GET['grad_type'].'/type/'.$_GET['type'].'/memory_lv/'.$_GET['memory_lv'])?>";
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
                    submit();
                }
                ,closeBtn:2
                ,btnAagn: 'c' //按钮居中
                ,shade: 0.3 //遮罩
                ,isOutAnim:true//关闭动画
            });
        }
        function zonePress(_this){//填充区域
            $('.matching-number-match').removeClass('active');
            _this.addClass('active');
        }
        function numberPress(_this){//数字键盘
            var number=_this.attr('date-number');
            var active=$('.matching-number-match.active');
            var len=$('.matching-number-match').length;
            if(!$('.matching-number-match').eq(len-1).hasClass('active')){
                active.text(number).removeClass('active').next('.matching-number-match').addClass('active');
            }else{
                active.text(number);
            }
        }
        function drawLine(_this){//划线
            $('.ready-btn').removeClass('active');
            _this.addClass('active')
            var text=parseInt(_this.text())
            $('.matching-number-readys').removeClass('border-right');
            if(text!='NAN'){
                $('.matching-number-readys').each(function(j){
                    if((j+1)%text==0){
                        $(this).addClass('border-right')
                    }
                })
            }
        }
        if('ontouchstart' in window){// 移动端
            // 考级事件
            $('.ready-btn').each(function(){//划线
                var _this=$(this);
                new AlloyFinger(_this[0], {
                    tap:function(){
                        drawLine(_this);
                    }
                })
            })
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
                        zonePress(_this)
                    }
                })
            })
            $('.matching-keyboard').eq(question_type-1).find('.number').each(function(){//数字键盘
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
            new AlloyFinger($('#sumbit')[0], {//提交
                tap:function(){
                    layOpen()
                }
            });
        }else{
            $('body').on('click','.ready-btn',function(){//划线
                var _this=$(this);
                drawLine(_this);
            })
            $('body').on('click','.matching-number-match',function(){
                var _this=$(this);
                zonePress(_this)
            })
            $('.matching-keyboard').eq(question_type-1).find('.number').click(function(){
                var _this=$(this);
                numberPress(_this)
            })
            $('body').on('click','._del',function(){
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
                }else{
                    active.text('');

                }
            })
            $('body').on('click','#prev',function(){
                var len=$('.matching-number-match').length;
                if(!$('.matching-number-match').eq(len-1).hasClass('active')){
                    var active=$('.matching-number-match.active');
                    var dom='<div class="matching-number-match active"></div>';
                    active.removeClass('active').before(dom);
                    $('.match_zoo .matching-number-match').last().remove()
                }else{
                    $('.matching-number-match.active').text('')
                }
            })
            $('body').on('click','#next',function(){
                $('.matching-number-match').each(function(i){
                    if(i!=$('.matching-number-match').length-1){//如果不是最后一位
                        if($(this).hasClass('active')){
                            var dom='<div class="matching-number-match active"></div>'
                            $(this).removeClass('active').after(dom);
                            $('.match_zoo .matching-number-match').last().remove()
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