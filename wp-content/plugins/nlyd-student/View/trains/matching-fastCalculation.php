
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><div><?=__($match_title, 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row">
                        <div class="c_black match_info_font"><div><?=__($title, 'nlyd-student')?> <?=sprintf(__('第%s轮', 'nlyd-student'),$match_more)?></div></div>
                        <div class="c_blue match_info_font"><div>&nbsp;&nbsp;&nbsp;&nbsp;<?=__('第<span id="total">0</span>题', 'nlyd-student')?></div></div>
                        <div class="c_blue match_info_font">
                            <div>
                                <i class="iconfont">&#xe685;</i>
                                <span class="count_down" data-seconds="<?=$count_down?>"><?=__('初始中', 'nlyd-student')?>...</span>
                            </div>
                        </div>
                        <div class="matching-sumbit" id="sumbit"><div><?=__('提交', 'nlyd-student')?></div></div>
                    </div>
                   
                    <div class="matching-fast">
                        <p class="count_p c_black">
                            <span id="type"></span>
                            <span class="count_downs" data-seconds="10"><?=__('初始中', 'nlyd-student')?>...</span>
                            <!-- <span><?=!empty($child_type_down) ? $child_type_down : ''?></span> -->
                            <input type="hidden"id="even_add_time" value="<?=$child_count_down['even_add'] ?>">
                            <input type="hidden"id="add_and_subtract_time" value="<?=$child_count_down['add_and_subtract'] ?>">
                            <input type="hidden"id="wax_and_wane_time" value="<?=$child_count_down['wax_and_wane'] ?>">
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
<input type="hidden" name="_wpnonce" id="inputSubmit" value="<?=wp_create_nonce('student_answer_submit_code_nonce');?>">

<script>
jQuery(function($) {
    var isSubmit=false;//是否正在提交
    var even_add_time = 180; //连加
    var add_and_subtract_time = 180; //加减
    var wax_and_wane_time = 180; //乘除
    var level={number:2,symbol:1},//题目难度
    n_type=0,
    type='',//当前子相运算类型
    ajaxData=[],//提交的数据
    nextBtn_click=0,//下一题点击次数，控制难度
    add_interval_times=3,//加减法每隔多少题增加一个难度
    cx_interval_times=6;//乘除法每隔多少题增加一个难度

    if(n_type==0){
        type="<?=__('连加运算', 'nlyd-student')?>"
        even_add_time=<?=$count_down?>-add_and_subtract_time-wax_and_wane_time
    }else if(n_type==1){
        type="<?=__('加减运算', 'nlyd-student')?>"
        add_and_subtract_time=<?=$count_down?>-wax_and_wane_time
    }else{
        type='<?=__('乘除运算', 'nlyd-student')?>'
        wax_and_wane_time=<?=$count_down?>
    }
    $('#type').text(type)

    inItFastCalculation(level,type);
    
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
                day=day>0?day+"<?_('天', 'nlyd-student')?>":'';
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
                    var yours=$('#answer div').text().length==0 ? '' : parseInt($('#answer div').text());
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
        for (var index = 0; index < L; index++) {
            var symbol=''
            if(type=='<?=__('连加运算', 'nlyd-student')?>'){
                symbol='+';
            }else if(type=='<?=__('加减运算', 'nlyd-student')?>'){
                symbol=randJJ()
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
    }
    function nextQuestion() {
        $('#total').text(ajaxData.length)
        $('#question div').text(ajaxData[ajaxData.length-1]['question']+'=?')
    }
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
                    var number=_this.attr('date-number');
                    var text=$('#answer div').text()
                    if(text.length<21){
                        $('#answer div').text(text+number)
                    }
                }
            })

    })
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
            var _this=$('#del');
            var text=$('#answer div').text()
            var len=text.length;
            if(len>0){
                var news=text.substring(0,len-1)
                $('#answer div').text(news)
            }
        }
    });
    //下一题tap事件
    // mTouch('body').on('tap','#next',function(e){
    new AlloyFinger($('#next')[0], {
        tap: function () {
            var _this=$('#next')
            if(!_this.hasClass('disabled')){
                _this.addClass('disabled')
                nextBtn_click++
                if (type=='<?=__('乘除运算', 'nlyd-student')?>') {
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
                    _this.removeClass('disabled')
                }, 300);
            }
        }
        
    });
    function submit(time){//提交答案
        if(!isSubmit){
            // $('#load').css({
            //     'display':'block',
            //     'opacity': '1',
            //     'visibility': 'visible',
            // })
            // isSubmit=true;
            var match_more=$.Request('match_more') ? $.Request('match_more') : '1';
            var data={
                action:'trains_submit',
                genre_id:$.Request('genre_id'),
                project_type:'zxss',
                my_answer:ajaxData,
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
                        //$.SetSession('train_data',data);
                        var href="<?=home_url('trains/logs/type/'.$_GET['type'].'/match_more/'.$_GET['match_more'])?>";
                        window.location.href=href;
            　　　　}
                }
            })
        }else{
            $.alerts('<?=__('正在提交答案', 'nlyd-student')?>')
        }
    }
    if(<?=$count_down?><=0){//进入页面判断时间是否结束
        $.alerts('<?=__('比赛结束', 'nlyd-student')?>');
        setTimeout(function() {
            submit(0)
        }, 1000);
    }

    $('.count_down').countdown(function(S, d){//倒计时
        var D=d.day>0 ? d.day+'<?=__('天', 'nlyd-student')?>' : '';
        var h=d.hour<10 ? '0'+d.hour : d.hour;
        var m=d.minute<10 ? '0'+d.minute : d.minute;
        var s=d.second<10 ? '0'+d.second : d.second;
        var time=D+h+':'+m+':'+s;
        $(this).text(time).attr('data-seconds',S)
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
                ,btn: [ '<?=__('按错了', 'nlyd-student')?>','<?=__('提交', 'nlyd-student')?>', ]
                ,success: function(layero, index){
                }
                ,yes: function(index, layero){
                    layer.closeAll();
                }
                ,btn2: function(index, layero){
                    var thisAjaxRow=ajaxData[ajaxData.length-1]
                    var yours=$('#answer div').text().length==0 ? '' : parseInt($('#answer div').text());
                    thisAjaxRow['yours']=yours;
                    if(yours==thisAjaxRow['rights']){
                        thisAjaxRow['isRight']=true;
                    }else{
                        thisAjaxRow['isRight']=false;
                    }
                    layer.closeAll();
                    submit(time);
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