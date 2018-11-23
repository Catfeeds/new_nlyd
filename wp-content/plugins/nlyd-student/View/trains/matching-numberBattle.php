<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><div><?=__($match_title, 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row layui-row have-submit">
                        <div class="c_black match_info_font"><div><?=sprintf(__('第%s轮', 'nlyd-student'),$match_more)?></div></div>
                        <!-- <div class="c_blue match_info_font"><div><?=__('第1/1题', 'nlyd-student')?></div></div> -->
                        <div class="c_blue match_info_font">
                            <div>
                                <!-- <i class="iconfont">&#xe685;</i> -->
                                <span class="count_down" data-seconds="<?=$count_down?>">00:00:00</span>
                            </div>
                        </div>
                        <div class="matching-sumbit" id="sumbit"><div><?=__('提交', 'nlyd-student')?></div></div>
                    </div>
                    <div class="matching-row layui-row">
                        <div class="matching-row-label"><div><?=__('辅助操作', 'nlyd-student')?></div></div>
                        <div class="matching-row-list">
                            <button class="matching-btn active c_white" id="prev"><?=__('前插一位', 'nlyd-student')?></button>
                            <button class="matching-btn active c_white" id="next"><?=__('后插一位', 'nlyd-student')?></button>
                        </div>
                    </div>
                    <div class="matching-number-zoo layui-row">
                    </div>

                    <div class="matching-keyboard">
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
                            <div class="bg_orange matching-key fs_16 c_white" id="del"><div><?=__('删除', 'nlyd-student')?></div></div>
                            <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="0"><div>0</div></div>
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
    var isSubmit=false;//是否正在提交
    var questions_answer=[];
    var leavePage= $.GetSession('train_match','1');
    if(leavePage && leavePage['genre_id']==$.Request('genre_id') && leavePage['type']=='szzb'){//记忆成功
        history.pushState(null, null, document.URL);
        window.addEventListener('popstate', function () {
            history.pushState(null, null, document.URL);
        });
        questions_answer=leavePage['train_questions'];
        var end_time=leavePage['end_time'];
        $('.count_down').attr('data-seconds',$.GetSecond(end_time))
        $.each(questions_answer,function(i,v){
            var dom=i==0 ? '<div class="matching-number active"></div>' : '<div class="matching-number"></div>';
            $('.matching-number-zoo').append(dom)
        })
    }else{//未获取到训练题目
        $.alerts('<?=__('触发防作弊系统', 'nlyd-student')?>')
        window.location.href = '<?=home_url("/trains/initial/type/szzb/genre_id/")?>'+$.Request('genre_id')+'/match_more/'+$.Request('match_more');
    }
    $('.count_down').countdown(function(S, d){//倒计时
        var D=d.day>0 ? d.day+'<?=__('天', 'nlyd-student')?>' : '';
        var h=d.hour<10 ? '0'+d.hour : d.hour;
        var m=d.minute<10 ? '0'+d.minute : d.minute;
        var s=d.second<10 ? '0'+d.second : d.second;
        var time=D+h+':'+m+':'+s;
        $(this).attr('data-seconds',S).text(time)
        if(S<=0){//本轮训练结束
            if(S==0){
                $.alerts('<?=__('倒计时结束，即将提交答案', 'nlyd-student')?>')
            }else{
                $.alerts('<?=__('训练结束', 'nlyd-student')?>')
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
            var match_more=$.Request('match_more') ? $.Request('match_more') : '1';
            var data={
                action:'trains_submit',
                genre_id:$.Request('genre_id'),
                project_type:'szzb',
                train_questions:questions_answer,
                train_answer:questions_answer,
                my_answer:my_answer,
                surplus_time:time,
                match_more:match_more,
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
                complete: function(XMLHttpRequest, textStatus){
                    if(textStatus=='timeout'){
                        $.SetSession('train_data',data);
                        var href="<?=home_url('trains/logs/type/'.$_GET['type'].'/match_more/'.$_GET['match_more'])?>";
                        window.location.href=href;
            　　　　}
                }
            })
        }else{
            $.alerts('<?=__('正在提交答案', 'nlyd-student')?>')
        }
        return false;
    }
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
                $('.matching-number').removeClass('active');
                _this.addClass('active');
            }
        })
    })
    $('.number').each(function(){//数字键盘
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
                var active=$('.matching-number.active');
                var len=$('.matching-number').length;
                if(!$('.matching-number').eq(len-1).hasClass('active')){
                    active.text(number).removeClass('active').next('.matching-number').addClass('active');
                }else{
                    active.text(number);
                }
            }
        })
    })
    //删除tap事件
    // mTouch('body').on('tap','#del',function(e){
    new AlloyFinger($('#del')[0], {//删除
        touchStart: function () {
            $('#del').addClass("opacity");
        },
        touchMove: function () {
            $('#del').removeClass("opacity");
        },
        touchEnd: function () {
            $('#del').removeClass("opacity");
        },
        touchCancel: function () {
            $('#del').removeClass("opacity");
        },
        tap:function(){
            var _this=$('#del')
        // if(!_this.hasClass('opcity')){
        //     _this.addClass('opcity')
            var active=$('.matching-number.active');
            if(active.text()==""){//已经为空
                if(!$('.matching-number').eq(0).hasClass('active')){
                    active.prev('.matching-number').addClass('active')
                    
                }else{
                    active.next('.matching-number').addClass('active')
                }
                active.remove()
                var dom='<div class="matching-number"></div>'
                $('.matching-number-zoo').append(dom)
                var len=$('.matching-number').length;
                var newDom=$('.matching-number').eq(len-1)
                // console.log($('.matching-number').eq(len-1))
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
    //前插tap事件
    // mTouch('body').on('tap','#prev',function(e){
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
            var _this=$('#prev')
            if(!$('.matching-number').eq(len-1).hasClass('active')){
                var active=$('.matching-number.active');
                var dom='<div class="matching-number active"></div>';
                active.removeClass('active').before(dom);
                $('.matching-number-zoo .matching-number').last().remove()
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
    // mTouch('body').on('tap','#next',function(e){
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
            $('.matching-number').each(function(i){
                if(i!=$('.matching-number').length-1){//如果不是最后一位
                    if($(this).hasClass('active')){
                        var dom='<div class="matching-number active"></div>'
                        $(this).removeClass('active').after(dom);
                        $('.matching-number-zoo .matching-number').last().remove()

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

    
})
</script>