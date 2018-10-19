<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><?=$match_title?></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row">
                        <span class="c_black match_info_font"><?=$project_title?><?php printf(__('第%s轮', 'nlyd-student'), $match_more_cn)?></span>
                        <span class="c_blue ml_10 match_info_font"><?=__('第', 'nlyd-student')?>1/1<?=__('题', 'nlyd-student')?></span>
                        <span class="c_blue ml_10 match_info_font">
                            <i class="iconfont">&#xe685;</i>
                            <span class="count_down" data-seconds="<?=$count_down?>">00:00:00</span>
                        </span>
                        <div class="matching-sumbit match_info_font" id="sumbit"><?=__('提交', 'nlyd-student')?></div>
                    </div>
                    <div class="matching-row">
                        <div class="matching-row-label"><?=__('辅助操作', 'nlyd-student')?></div>
                        <div class="matching-row-list">
                            <div class="matching-btn c_white" id="prev"><?=__('前插一位', 'nlyd-student')?></div>
                            <div class="matching-btn c_white" id="next"><?=__('后插一位', 'nlyd-student')?></div>
                        </div>
                    </div>
                    <div class="matching-number-zoo">
                        <?php for($i=0;$i<$str_length;++$i){ ?>
                        <div class="matching-number <?=$i==0?'active':'';?>"></div>
                        <?php } ?>
                    </div>

                    <div class="matching-keyboard">
                    <div class="matching-keyboard-row">
                            <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="1">1</div>
                            <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="2">2</div>
                            <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="3">3</div>
                        </div>
                        <div class="matching-keyboard-row">
                            <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="4">4</div>
                            <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="5">5</div>
                            <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="6">6</div>
                        </div>
                        <div class="matching-keyboard-row">
                            <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="7">7</div>
                            <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="8">8</div>
                            <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="9">9</div>
                        </div>
                        <div class="matching-keyboard-row">
                            <div class="bg_orange matching-key fs_16 c_white" id="del"><?=__('删除', 'nlyd-student')?></div>
                            <div class="bg_gradient_blue matching-key fs_18 c_white number" date-number="0">0</div>
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
    var _match_id=<?=$_GET['match_id']?>;
    var _project_id=<?=$project_id?>;
    var _match_more=<?=$match_more;?>;
    leaveMatchPage(function(){//窗口失焦提交
        var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
        submit(time,4);
    })
    var matching_question= $.GetSession('matching_question','1');
    if(matching_question && matching_question['match_id']===_match_id && matching_question['project_id']===_project_id && matching_question['match_more']===_match_more){//从Session获取比赛题目,
        questions_answer=matching_question['questions_answer'];
        $.each(questions_answer,function(i,v){
            var dom=i==0 ? '<div class="matching-number active"></div>' : '<div class="matching-number"></div>';
            $('.matching-number-zoo').append(dom)
        })
    }else{//未获取到比赛题目
        $.alerts('未检测到题目信息')
    }
    console.log(questions_answer)
    if(<?=$count_down?><=0){//进入页面判断时间是否结束
        $.alerts('<?=__('比赛结束', 'nlyd-student')?>');

        setTimeout(function() {
            submit(0,3)
        }, 1000);
    }
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
                submit(0,3)
            }, 1000);
        }
    });
    function submit(time,submit_type){//提交答案
        if(!isSubmit){
            $('#load').css({
                'display':'block',
                'opacity': '1',
                'visibility': 'visible',
            })
            isSubmit=true;
            var my_answer=[];
            $('.matching-number-zoo .matching-number').each(function(){
                var answer=$(this).text();
                my_answer.push(answer)
            })
            var data={
                // action:'answer_submit',
                // _wpnonce:$('#inputSubmit').val(),
                // match_id:_match_id,
                // project_id:_project_id,
                // match_more:_match_more,
                // my_answer:my_answer,
                // match_action:'subjectNumberBattle',
                // surplus_time:time,
                // submit_type:submit_type,//1:选手提交;2:错误达上限提交;3:时间到达提交;4:来回切
                action:'answer_submit',
                _wpnonce:$('#inputSubmit').val(),
                match_id:_match_id,
                project_id:_project_id,
                match_more:_match_more,
                project_alias:'szzb',
                match_questions:questions_answer,
                questions_answer:questions_answer,
                project_more_id:$.Request('project_more_id'),

                my_answer:my_answer,
                match_action:'subjectNumberBattle',
                surplus_time:time,
                submit_type:submit_type,//1:选手提交;2:错误达上限提交;3:时间到达提交;4:来回切
            }
            var leavePage= $.GetSession('leavePage','1');
            if(leavePage && leavePage['match_id']===$.Request('match_id') && leavePage['project_id']===$.Request('project_id') && leavePage['match_more']===$.Request('match_more')){
                if(leavePage.Time){
                    data['leave_page_time']=leavePage.Time;
                }
            }
            $.ajax({
                data:data,success:function(res,ajaxStatu,xhr){  
                    $.DelSession('leavePage')
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