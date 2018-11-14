<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><div><?=__($grading_title, 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row layui-row have-submit">
                        <div class="c_black match_info_font"><div><?=__('国学经典默写', 'nlyd-student')?> <span id="number">1</span>/<?=$memory_type['num']?></div></div>
                        <div class="c_blue match_info_font">
                           <div> 
                                <span class="count_down" data-seconds="1800">00:00:00</span>
                            </div>
                        </div>
                        <div class="matching-sumbit" id="sumbit"><div><?=__('提交', 'nlyd-student')?></div></div>
                    </div>
                </div> 
            </div>
        </div>           
    </div>
</div>
<div class="a-btn two">
    <div class="a-two left disabled"><div><?=__('上一题', 'nlyd-student')?></div></div>
    <div class="a-two right"><div><?=__('下一题', 'nlyd-student')?></div></div>
</div>

<script>
jQuery(function($) { 
    var isSubmit=false;//是否正在提交
    var _grad_id=$.Request('grad_id');
    var _grad_type=$.Request('grad_type');
    var _type=$.Request('type');
    var file_url="<?=leo_match_url.'/upload/book/memory.json'?>";
    var questions_answer=[];
    var sys_second="<?=$memory_type['memory_time']?>";
    var endTime=$.GetEndTime(sys_second);
    var grade_level="<?=$memory_type['lv']?>";//考级等级
    var how_ques=grade_level>3 ? 6 : 3;//多少道题目
    init_question()
    leaveMatchPage(function(){//窗口失焦提交
        submit(4);
    })
    count_down()
    $('body').on('focusout','.answer_q',function(e){
        var _this=$(this);
        var val=_this.val();
        var _len=val.length;
        var parent=_this.parents('.matching-reading')
        var dom_index=parseInt(_this.attr('data-index'))
        if(_len>1){
            for (var index = 0; index < _len; index++) {
                var v=val.charAt(index)
                parent.find('.answer_q').eq(dom_index+index).val(v)
            }
        }
        
    })
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
                submit(3)
            }

        }, 1000);
    } 
    function init_question(question_leng) {//初始化题目
        var grade_question=$.GetSession('grade_question','true');
        //标点符号
        var reg = /[\u00b7|\u3002|\uff1f|\uff01|\uff0c|\u3001|\uff1b|\uff1a|\u201c|\u201d|\u2018|\u2019|\uff08|\uff09|\u300a|\u300b|\u3008|\u3009|\u3010|\u3011|\u300e|\u300f|\u300c|\u300d|\ufe43|\ufe44|\u3014|\u3015|\u2026|\u2014|\uff5e|\ufe4f|\uffe5]/;
        if(grade_question && grade_question['grad_id']===_grad_id && grade_question['grad_type']===_grad_type && grade_question['type']===_type){
            endTime=grade_question['endTime'];
            sys_second=$.GetSecond(endTime);
            questions_answer=grade_question['questions_answer'];
           
            $.each(questions_answer,function(index,value){
                var str_dom=""
                var question=""
                var rights=value.rights;
                var _n=0;
                for (var i = 0; i < rights.length; i++) {
                    var v=rights.charAt(i)
                    if(i<50 || i>=150){
                    var dom='<div class="matching-number input"><input class="matching-number-input nl-foucs" value="'+v+'" disabled type="text"></div>'
                    str_dom+=dom;
                    question+=v;
                    }else{
                    var dom=''
                    if(reg.test(v) || !isNaN(parseInt(v))){
                        dom='<div class="matching-number input"><input class="matching-number-input nl-foucs" value="'+v+'" disabled type="text"></div>'
                        question+=v
                    }else{
                        dom='<div class="matching-number input"><input class="matching-number-input nl-foucs answer_q" type="text" data-value="'+v+'" data-index="'+_n+'"></div>';
                        _n++;
                        question+="#"
                    }
                    str_dom+=dom 
                    }
                }
                var active=index==0 ? "active" : "";
                var ques_dom='<div class="matching-reading layui-row '+active+'" data-index="'+index+'">'+str_dom+'</div>'
                $('.remember').append(ques_dom)
            })
        }else{
            $.getJSON(file_url, function (data){
               
                var question_bank=data;
                var _question="";//grade_level之前的题库
                var now_question=question_bank[grade_level];//当前等级题库
                if (grade_level>3) {
                    for (var index = 3; index < grade_level; index++) {
                        var item=question_bank[index];
                        _question+=item
                    }
                }else{//三级
                    _question=now_question;
                }

                var pos_arr=[];//储存grade_level之前的题库截取的下标
                var pos_arr_now=[];//储存当前等级题库截取的下标
                for (var index = 0; index < how_ques; index++) {
                    if(index>=3){
                        _slice_ques(pos_arr_now,now_question);
                    }else{
                        _slice_ques(pos_arr,_question);
                    }
                    // _slice_ques(pos_arr,_question);
                    var start_index=index>=3 ? pos_arr_now[index-3] : pos_arr[index];//截取的所有字符串的起始坐标
                    var end_index=start_index+200;//截取的所有字符串的结束坐标
                    var start_ques=start_index+50;//截取的所有字符串的答题区起始坐标
                    var end_ques=start_index+150;//截取的所有字符串答题区结束坐标
                    var rights=index>=3 ? now_question.substring(start_index,end_index):_question.substring(start_index,end_index);//题目
                    //   var str1=_question.substring(start_index,start_ques);//答题前面区域
                      var str2=_question.substring(start_ques,end_ques); //答题区域
                    //   var str3=_question.substring(end_ques,end_index); //答题后面区域
                    var question=""
                    var str_dom=""
                    var _n=0;
                    for (var i = 0; i < rights.length; i++) {
                        var v=rights.charAt(i)
                        if(i<50 || i>=150){
                            var dom='<div class="matching-number input"><input class="matching-number-input nl-foucs" value="'+v+'" disabled type="text"></div>'
                            str_dom+=dom;
                            question+=v;
                        }else{
                            var dom=''
                            if(reg.test(v) || !isNaN(parseInt(v))){
                                dom='<div class="matching-number input"><input class="matching-number-input nl-foucs" value="'+v+'" disabled type="text"></div>'
                                question+=v
                            }else{
                                dom='<div class="matching-number input"><input class="matching-number-input nl-foucs answer_q" type="text" data-value="'+v+'" data-index="'+_n+'"></div>';
                                _n++;
                                question+="#"
                            }
                            str_dom+=dom 
                        }
                    }
                    var item={rights:rights,question:question}
                    questions_answer.push(item)

                    var active=index==0 ? "active" : "";
                    var ques_dom='<div class="matching-reading layui-row '+active+'" data-index="'+index+'">'+str_dom+'</div>'
                    $('.remember').append(ques_dom)
                }
                var sessionData={
                    grad_id:_grad_id,
                    grad_type:_grad_type,
                    type:_type,
                    endTime:endTime,
                    questions_answer:questions_answer
                }
                $.SetSession('grade_question',sessionData) 
            })
        }

        console.log(questions_answer)
    }
    function _slice_ques(pos_arr,_question){//截取题目
        var _question_len=_question.length;
        var repeat=false;
        var pos = Math.floor(Math.random()*(_question_len-200));
        $.each(pos_arr,function(i,v){
            if(v-pos<=200 && v-pos>=-200){//重复题目
                repeat=true;
                return false;    
            }
        })
        if(!repeat){
            pos_arr.push(pos)
        }else{
            // console.log(1)
            _slice_ques(pos_arr,_question)
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
            // var my_answer=[];
            // $('.matching-number-zoo .matching-number').each(function(){
            //     var answer=$(this).text();
            //     my_answer.push(answer)
            // })

            // {rights:rights,question:question}
            var ajax_question=[]
            $('.matching-reading').each(function(i){
                var _this=$(this);
                var rights=[];
                var yours=[];
                _this.find('.matching-number-input.answer_q').each(function(){
                    var x=$(this).val();
                    var y=$(this).attr('data-value');
                    rights.push(y)
                    yours.push(x)
                })
                var item={rights:rights,yours:yours,question:rights}
                ajax_question.push(item)
            })
            // console.log(ajax_question);
            //  return false;
            var data={
                grading_id:_grad_id,
                grading_type:_grad_type,
                questions_type:_type,
                action:'grading_answer_submit',
                questions_answer:ajax_question,
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
                    isSubmit=true;
                    $('#load').css({
                        'display':'block',
                        'opacity': '1',
                        'visibility': 'visible',
                    })
                },
                success:function(res,ajaxStatu,xhr){  
                    // $.DelSession('leavePage')
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

                        window.location.href=href;
            　　　　}
                }
            })
        }else{
            $.alerts('<?=__('正在提交答案', 'nlyd-student')?>')
        }
    }
   
layui.use('layer', function(){
    // mTouch('body').on('tap','#sumbit',function(e){
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


    var n=0;
    if(how_ques<=1){
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