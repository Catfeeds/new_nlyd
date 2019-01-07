
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><div><?=__($grading_title, 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row layui-row have-submit">
                        <div class="c_black match_info_font"><div><?=__('正向运算', 'nlyd-student')?></div></div>
                        <div class="c_blue match_info_font"><div><?=__('第<span id="total">0</span>题', 'nlyd-student')?></div></div>
                        <div class="c_blue match_info_font">
                            <div>
                                <span class="count_down" data-seconds="540"><?=__('初始中', 'nlyd-student')?>...</span>
                            </div>
                        </div>
                        <div class="matching-sumbit" id="sumbit"><div><?=__('提交', 'nlyd-student')?></div></div>
                    </div>
                    <div class="matching-fast">
                        <p class="count_p c_black">
                            <span id="type"></span>
                            <span class="count_downs" data-seconds="10"><?=__('初始中', 'nlyd-student')?>...</span>
                        </p>
                        <div class="item-wrapper">
                            <div class="fast-item" id="question"><div></div></div>
                            <div class="fast-item answer" id="answer"><div></div></div>
                        </div>
                    </div>

                    <div class="matching-keyboard">
                        <div class="matching-keyboard-row">
                            <div class="bg_gradient_blue c_white fs_18 matching-key number" date-number="1"><div>1</div></div>
                            <div class="bg_gradient_blue c_white fs_18 matching-key number" date-number="2"><div>2</div></div>
                            <div class="bg_gradient_blue c_white fs_18 matching-key number" date-number="3"><div>3</div></div>
                        </div>
                        <div class="matching-keyboard-row">
                            <div class="bg_gradient_blue c_white fs_18 matching-key number" date-number="4"><div>4</div></div>
                            <div class="bg_gradient_blue c_white fs_18 matching-key number" date-number="5"><div>5</div></div>
                            <div class="bg_gradient_blue c_white fs_18 matching-key number" date-number="6"><div>6</div></div>
                        </div>
                        <div class="matching-keyboard-row">
                            <div class="bg_gradient_blue c_white fs_18 matching-key number" date-number="7"><div>7</div></div>
                            <div class="bg_gradient_blue c_white fs_18 matching-key number" date-number="8"><div>8</div></div>
                            <div class="bg_gradient_blue c_white fs_18 matching-key number" date-number="9"><div>9</div></div>
                        </div>
                        <div class="matching-keyboard-row">
                            <div class="matching-key c_white fs_16 bg_orange" id="del"><div><?=__('删除', 'nlyd-student')?></div></div>
                            <div class="bg_gradient_blue c_white fs_18 matching-key number" date-number="-"><div>-</div></div>
                            <div class="bg_gradient_blue c_white fs_18 matching-key number" date-number="0"><div>0</div></div>
                            <div class="matching-key c_white fs_16 bg_orange" id="next"><div><?=__('下一题', 'nlyd-student')?></div></div>
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
    leaveMatchPage(function(){//窗口失焦提交
        $('#next').addClass('disabled')
        var yours=$('#answer div').text().length==0 ? '' : $('#answer div').text();
        ajaxData[ajaxData.length-1]['yours']=yours;
        if(yours==ajaxData[ajaxData.length-1]['rights']){
            ajaxData[ajaxData.length-1]['isRight']=true;
        }else{
            ajaxData[ajaxData.length-1]['isRight']=false;
        }
        var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
        submit(time,4);
    })
    var _grad_id=$.Request('grad_id');
    var _grad_type=$.Request('grad_type');
    var _type=$.Request('type');
    var even_add_time = 180; //连加
    var add_and_subtract_time = 180; //加减
    var wax_and_wane_time = 180; //乘除
    var level={number:2,symbol:1};//题目难度
    var sys_second=540;
    var end_time=0,//结束时间
    n_type=0,
    type="<?=__('连加运算', 'nlyd-student')?>",//当前子相运算类型
    ajaxData=[],//提交的数据
    nextBtn_click=0,//下一题点击次数，控制难度
    add_interval_times=3,//加减法每隔多少题增加一个难度
    cx_interval_times=6;//乘除法每隔多少题增加一个难度

    var grade_question=$.GetSession('grade_question','true');
    var isMatching=false;//判断用户是否刷新页面
    if(grade_question && grade_question['grad_id']===_grad_id && grade_question['grad_type']===_grad_type && grade_question['type']===_type){
        isMatching=true;
        ajaxData=grade_question['ajaxData'];
        level=grade_question['level'];
        n_type=grade_question['n_type'];
        nextBtn_click=grade_question['nextBtn_click'];
        end_time=grade_question['end_time'];
        sys_second=$.GetSecond(end_time);
        $('.count_down').attr('data-seconds',sys_second)
    }
    if(n_type==0){
        type="<?=__('连加运算', 'nlyd-student')?>"
        even_add_time=sys_second-add_and_subtract_time-wax_and_wane_time
    }else if(n_type==1){
        type="<?=__('加减运算', 'nlyd-student')?>"
        add_and_subtract_time=sys_second-wax_and_wane_time
    }else{
        type='<?=__('乘除运算', 'nlyd-student')?>'
        wax_and_wane_time=sys_second
    }
    $('#type').text(type)
    if(!isMatching){
        inItFastCalculation(level,type);
    }
    nextQuestion()
    count_down()  

    function count_down(){
        var sys_second='';
        if(n_type==0){
            sys_second=even_add_time
        }else if(n_type==1){
            sys_second=add_and_subtract_time
        }else{
            sys_second=wax_and_wane_time
        }
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
                $('.count_downs').text(text).attr('data-seconds',sys_second)
                $('#type').text(type)
            } else {//倒计时结束
                level={number:2,symbol:1};//初始化难度
                n_type++
                if(n_type==0){
                    type="<?=__('连加运算', 'nlyd-student')?>"
                    sys_second=even_add_time;
                    nextBtn_click=0;
                }else if(n_type==1){
                    type="<?=__('加减运算', 'nlyd-student')?>"
                    sys_second=add_and_subtract_time -1 ;
                    nextBtn_click=0;
                }else if(n_type==2){
                    type="<?=__('乘除运算', 'nlyd-student')?>"
                    sys_second=wax_and_wane_time -1 ;
                    nextBtn_click=0;
                }else{
                    var yours=$('#answer div').text().length==0 ? '' : parseInt($('#answer').text());
                    ajaxData[ajaxData.length-1]['yours']=yours;
                    if(yours==ajaxData[ajaxData.length-1]['rights']){
                        ajaxData[ajaxData.length-1]['isRight']=true;
                        $('#answer').removeClass('answer').addClass('right-fast')
                    }else{
                        ajaxData[ajaxData.length-1]['isRight']=false;
                        $('#answer').removeClass('answer').addClass('error-fast')
                    }
                    clearInterval(timer);
                }
                if(n_type<=2){
                    $('.count_downs').text('<?=__('初始中', 'nlyd-student')?>...').attr('data-seconds',sys_second)
                    $('#type').text(type)
                    $('#answer').removeClass('error-fast').removeClass('right-fast').addClass('answer')
                    $('#answer div').text('') 
                    inItFastCalculation(level,type);  
                    nextQuestion()
                }
            }

        }, 1000);
    }  
    function randSZ() {//生成随即数字0-9
        return ( Math.floor ( Math.random ( ) * 9  ) );
    }
    function rand29() {//生成随即数字2-9
        var arr=['2','3','4','5','6','7','8','9'];

        var pos = Math.round(Math.random() * (arr.length - 1));

        return arr[pos];
    }
    function rand19() {//生成随即数字1-9
        var arr=['1','2','3','4','5','6','7','8','9'];

        var pos = Math.round(Math.random() * (arr.length - 1));

        return arr[pos];
    }
    function randJJ() {//生成随机+-
            var arr=['+','-'];

            var pos = Math.round(Math.random() * (arr.length - 1));

            return arr[pos];
    }
    function randCC() {//生成随机×÷
            var arr=['×','÷'];
            var pos = Math.round(Math.random() * (arr.length - 1));
            return arr[pos];
    }
    function compare(old) {
        var newStr=randSZ();
        var oldStr=old
        if(oldStr==newStr){
            return compare(oldStr)
        }else{
            return newStr
        }
    }
    function add(level,type) {//连加运算   
        var result='';
        var L=level['symbol'];
        var N=level['number'];
        var firstNumber=''
        var answer='';
        // var arr=[];
        if(level['symbol']>4){
            L=4;
        }
        if(level['symbol']<1){
            L=1;
        }
        if(level['number']>4){
            N=4;
        }
        if(level['number']<2){
            N=2;
        }
        for (var i = 0; i < N; i++) {
            var oneNumber=randSZ();
            if(i==0){
                oneNumber=rand19()
            }
            firstNumber+=oneNumber;
        }
        answer=parseInt(firstNumber);
        var arr=['+','-'];
        for (var index = 0; index < L; index++) {
            var symbol=''
            if(type=='<?=__('连加运算', 'nlyd-student')?>'){
                symbol='+';
            }else if(type=='<?=__('加减运算', 'nlyd-student')?>'){
                // symbol=randJJ()
                if(L==1){//一个符号
                    symbol="-"
                }else{//多个符号，前两个为加减
                    if(index==0){
                        var pos = Math.round(Math.random() * (arr.length - 1));
                        symbol=arr[pos]
                        arr.splice(pos, 1);
                    }else if (index==1) {
                        symbol=arr[0]
                    }else{
                        symbol=randJJ()
                    }
                }
            }
            var number=''
            for (var i = 0; i < N; i++) {
                var oneNumber=randSZ();
                if(i==0){//第一个数字不能是0
                    oneNumber=rand19()
                }
                number+=oneNumber;
            }  
            if(symbol=='+'){
                answer+=parseInt(number)
            }else if(symbol=='-'){
                // if(answer<parseInt(number)){//相减<0
                //     symbol='+';
                //     answer+=parseInt(number)
                // }else{
                //     answer-=parseInt(number)
                // }
                answer-=parseInt(number)
            }
            result+=symbol
            result+=number  
        }
        var question=firstNumber+result;
        var questionLen=question.length;
        var row={question:question,rights:answer,yours:'',isRight:false,}
        if(ajaxData.length>0){
            if(question==ajaxData[ajaxData.length-1]['question']){//生成题目和上一题一样
                var lastcode=question.substring(questionLen-1,questionLen);
                lastcode=parseInt(lastcode)+1>9 ? parseInt(lastcode)-1 : parseInt(lastcode)+1;
                var newQuestion= question.substring(0,questionLen-1) + lastcode;
                row['question']=newQuestion
            }
        }
        return row;
    }
    function CX(level) {//乘除运算
        var firstNumber='';//符号左侧数字
        var answer='';//计算出的答案
        var secondNumber=rand29();//符号右侧数字
        var symbol=randCC()//符号
        var question='';//运算
        var N=level['number'];
        if(level['number']>4){
            N=4;
        }
        if(level['number']<2){
            N=2;
        }
        if(symbol=='×'){
            for (var i = 0; i < N; i++) {
                var oneNumber=randSZ();
                if(i==0){
                    oneNumber=rand19()
                }
                firstNumber+=oneNumber;
            }  
            answer=parseInt(firstNumber)*parseInt(secondNumber)
            question=firstNumber+symbol+secondNumber

            if(ajaxData.length>0){
                if(question==ajaxData[ajaxData.length-1]['question']){//生成题目和上一题一样
                    firstNumber=parseInt(firstNumber)+1;
                    if(firstNumber.toString().length>N){
                        firstNumber=parseInt(firstNumber)-2;
                    }
                     question= firstNumber+symbol+secondNumber
                     answer=parseInt(firstNumber)*parseInt(secondNumber)
                }
            }
        }else if(symbol=='÷'){
            for (var i = 0; i < N; i++) {
                var oneNumber=randSZ();
                if(i==0){//第一个字符不能是0
                    oneNumber=rand19()
                }
                firstNumber+=oneNumber;
            }  
            firstNumber=parseInt(firstNumber)+(parseInt(secondNumber)-parseInt(firstNumber)%parseInt(secondNumber))
            if(firstNumber.toString().length>N){
                firstNumber=parseInt(firstNumber)-parseInt(secondNumber)
            }
            answer=parseInt(firstNumber)/parseInt(secondNumber)
            question=firstNumber+symbol+secondNumber


            if(ajaxData.length>0){
                if(question==ajaxData[ajaxData.length-1]['question']){//生成题目和上一题一样
                    firstNumber=parseInt(firstNumber)+parseInt(secondNumber);
                    if(firstNumber.toString().length>N){
                        firstNumber=parseInt(firstNumber)-2*parseInt(secondNumber);
                    }
                    question= firstNumber+symbol+secondNumber
                    answer=parseInt(firstNumber)/parseInt(secondNumber)
                }
            }
        }
        var row={question:question,rights:answer,yours:'',isRight:false,}
        return row
        
    }
    function inItFastCalculation(levels,type) {
        var text=''
        var row=''
        if(type=='<?=__('连加运算', 'nlyd-student')?>'){//连加运算
            row=add(levels,type);
        }else if(type=='<?=__('加减运算', 'nlyd-student')?>'){//加减运算
            row=add(levels,type);
        }else if(type=='<?=__('乘除运算', 'nlyd-student')?>'){//乘除运算
            row=CX(levels);
        }
        ajaxData.push(row)
        end_time=$.GetEndTime($('.count_down').attr('data-seconds'));//结束时间
        var sessionData={
            ajaxData:ajaxData,
            grad_id:_grad_id,
            grad_type:_grad_type,
            type:_type,
            level:level,
            n_type:n_type,
            nextBtn_click:nextBtn_click,
            end_time:end_time
        }
        $.SetSession('grade_question',sessionData)
    }
    function nextQuestion() {
        $('#total').text(ajaxData.length)
        $('#question div').text(ajaxData[ajaxData.length-1]['question']+'=?')
    }
    function submit(time,submit_type){//提交答案
        if(!isSubmit){
            // $('#load').css({
            //     'display':'block',
            //     'opacity': '1',
            //     'visibility': 'visible',
            // })
            // isSubmit=true;
            var data={
                grading_id:_grad_id,
                grading_type:_grad_type,
                questions_type:'zxys',
                grading_questions:'zx',
                questions_answer:'zx',
                action:'grading_answer_submit',
                my_answer:ajaxData,
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
                    // $.DelSession('match')
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
                        var href="<?=home_url('gradings/answerLog/grad_id/'.$_GET['grad_id'].'/grad_type/'.$_GET['grad_type'].'/type/'.$_GET['type'].'/memory_lv/'.$_GET['memory_lv'])?>";
                        window.location.href=href;
            　　　　}
                }
            })
        }else{
            $.alerts('<?=__("正在提交答案", "nlyd-student")?>')
        }
    }
    if(sys_second<=0){//进入页面判断时间是否结束
        // $.alerts('<?=__('考级结束', 'nlyd-student')?>');
        $('#next').addClass('disabled')
        // setTimeout(function() {
            var yours=$('#answer div').text().length==0 ? '' : $('#answer div').text();
            ajaxData[ajaxData.length-1]['yours']=yours;
            if(yours==ajaxData[ajaxData.length-1]['rights']){
                ajaxData[ajaxData.length-1]['isRight']=true;
            }else{
                ajaxData[ajaxData.length-1]['isRight']=false;
            }
            submit(0,3)
        // }, 1000);
    }

    $('.count_down').countdown(function(S, d){//倒计时
        var D=d.day>0 ? d.day+'<?=__('天', 'nlyd-student')?>' : '';
        var h=d.hour<10 ? '0'+d.hour : d.hour;
        var m=d.minute<10 ? '0'+d.minute : d.minute;
        var s=d.second<10 ? '0'+d.second : d.second;
        var time=D+h+':'+m+':'+s;
        $(this).text(time).attr('data-seconds',S)
        if(S<=0){//本轮考级结束
            $('#next').addClass('disabled')
            // if(S==0){
            //     $.alerts('<?=__('倒计时结束，即将提交答案', 'nlyd-student')?>')
            // }else{
            //     $.alerts('<?=__('考级结束', 'nlyd-student')?>')
            // }
            // setTimeout(function() {
                var yours=$('#answer div').text().length==0 ? '' : $('#answer div').text();
                ajaxData[ajaxData.length-1]['yours']=yours;
                if(yours==ajaxData[ajaxData.length-1]['rights']){
                    ajaxData[ajaxData.length-1]['isRight']=true;
                }else{
                    ajaxData[ajaxData.length-1]['isRight']=false;
                }
                submit(0,3)
            // }, 1000);
        }
    });  
    layui.use('layer', function(){
        function layOpen() {//提交
            var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
            layer.open({
                type: 1
                ,maxWidth:300
                ,title: '<?=__("提示", "nlyd-student")?>' //不显示标题栏
                ,skin:'nl-box-skin'
                ,id: 'certification' //防止重复弹出
                ,content: '<div class="box-conent-wrapper"><?=__("是否立即提交", "nlyd-student")?>？</div>'
                ,btn: [ '<?=__("按错了", "nlyd-student")?>','<?=__("提交", "nlyd-student")?>', ]
                ,success: function(layero, index){
                }
                ,yes: function(index, layero){
                    layer.closeAll();
                }
                ,btn2: function(index, layero){
                    var yours=$('#answer div').text().length==0 ? '' : $('#answer div').text();
                    ajaxData[ajaxData.length-1]['yours']=yours;
                    if(yours==ajaxData[ajaxData.length-1]['rights']){
                        ajaxData[ajaxData.length-1]['isRight']=true;
                    }else{
                        ajaxData[ajaxData.length-1]['isRight']=false;
                    }
                    layer.closeAll();
                    submit(time,1);
                }
                ,closeBtn:2
                ,btnAagn: 'c' //按钮居中
                ,shade: 0.3 //遮罩
                ,isOutAnim:true//关闭动画
            });
        }
        function numberPress(dom){//数字键盘
            var number=dom.attr('date-number');
            var text=$('#answer div').text()
            if(text.length<21){
                $('#answer div').text(text+number)
            }
        }
        function del() {//删除
            var text=$('#answer div').text()
            var len=text.length;
            if(len>0){
                var news=text.substring(0,len-1)
                $('#answer div').text(news)
            }
        }
        function nextQues(dom) {//下一题
            if(!dom.hasClass('disabled')){
                dom.addClass('disabled')
                nextBtn_click++
                if (type=='<?=__("乘除运算", "nlyd-student")?>') {
                    if(nextBtn_click%cx_interval_times==0){//难度控制
                        level.symbol=1
                        level.number++
                        if(level.number>4){
                            level.number=4
                        }
                    }
                }else{
                    if(nextBtn_click%add_interval_times==0){//难度控制，每点三次，数字长度加1
                        level.symbol++
                        if(level.symbol>4){
                            level.number++
                            if(level.number>4){
                                level.symbol=4
                            }else{
                                level.symbol=1
                            }
                        }
                    }
                }
                var thisAjaxRow=ajaxData[ajaxData.length-1]
                var yours=$('#answer div').text()
                var flag=true;
                if(yours.length>0){
                    for(var i=0;i< yours.length;i++){
                        if(yours.charAt(i)=="-"){
                            if(i!=0 || yours.length==1){//-是否出现在第一个或者出现-号长度为1
                                flag=false;
                                break;
                            }
                        }
                    } 
                    
                }
                thisAjaxRow['yours']=yours;
                if(flag){//符合parseInt函数
                    if(parseInt(yours)==thisAjaxRow['rights']){
                        thisAjaxRow['isRight']=true;
                        $('#answer').removeClass('answer').addClass('right-fast')
                    }else{
                        thisAjaxRow['isRight']=false;
                        $('#answer').removeClass('answer').addClass('error-fast')
                    }
                }else{
                    thisAjaxRow['isRight']=false;
                    $('#answer').removeClass('answer').addClass('error-fast')
                }
                setTimeout(function() {
                    $('#answer').removeClass('error-fast').removeClass('right-fast').addClass('answer')
                    $('#answer div').text('') 
                    inItFastCalculation(level,type);
                    nextQuestion()
                    if($('.count_down').attr('data-seconds')>0){
                        dom.removeClass('disabled')
                    }
                }, 300);
            }
        }
            if('ontouchstart' in window){// 移动端
                $('.number').each(function(e){//数字键盘
                    var _this=$(this)
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
                        tap: function () {
                            numberPress(_this)
                        }
                    })
                });
    
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
                    tap: function () {
                        del()
                    }
                });
                //下一题tap事件
                new AlloyFinger($('#next')[0], {
                    tap: function () {
                        var _this=$('#next')
                        nextQues(_this)
                    }
                    
                });
                new AlloyFinger($('#sumbit')[0], {
                    tap:function(){
                        layOpen()
                    }
                });
        }else{
            $('body').on('click','.number',function(){//数字键盘
                var _this=$(this)
                numberPress(_this)
            })
            $('body').on('click','#del',function(){//删除
                del()
            })
            $('body').on('click','#next',function(){//下一题
                var _this=$('#next')
                nextQues(_this)
            })
            $('body').on('click','#sumbit',function(){//下一题
                layOpen()
            })
        }
    })
    
})
</script>