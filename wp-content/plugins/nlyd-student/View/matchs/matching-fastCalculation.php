
<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><?=$project_title?></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row">
                        <span class="c_black"><?=$project_title?>第<?=$match_more_cn?>轮</span>
                        <span class="c_blue ml_10">第1题</span>
                        <span class="c_blue ml_10">
                            <i class="iconfont">&#xe685;</i>
                            <span class="count_down" data-seconds="<?=$count_down?>">初始中...</span>
                        </span>
                        <div class="matching-sumbit" id="sumbit">提交</div>
                    </div>
                   
                    <div class="matching-fast">
                        <p class="count_p c_black">
                            <span id="type"></span>
                            <span class="count_downs" data-seconds="10">初始中...</span>
                            <input type="hidden"id="even_add_time" value="<?=$child_count_down['even_add'] ?>">
                            <input type="hidden"id="add_and_subtract_time" value="<?=$child_count_down['add_and_subtract'] ?>">
                            <input type="hidden"id="wax_and_wane_time" value="<?=$child_count_down['wax_and_wane'] ?>">
                        </p>
                        <div class="item-wrapper">
                            <div class="fast-item" id="question"></div>
                            <div class="fast-item answer" id="answer"></div>
                        </div>
                    </div>

                    <div class="matching-keyboard">
                        <div class="matching-keyboard-row">
                            <div class="bg_gradient_blue matching-key number" date-number="1">1</div>
                            <div class="bg_gradient_blue matching-key number" date-number="2">2</div>
                            <div class="bg_gradient_blue matching-key number" date-number="3">3</div>
                        </div>
                        <div class="matching-keyboard-row">
                            <div class="bg_gradient_blue matching-key number" date-number="4">4</div>
                            <div class="bg_gradient_blue matching-key number" date-number="5">5</div>
                            <div class="bg_gradient_blue matching-key number" date-number="6">6</div>
                        </div>
                        <div class="matching-keyboard-row">
                            <div class="bg_gradient_blue matching-key number" date-number="7">7</div>
                            <div class="bg_gradient_blue matching-key number" date-number="8">8</div>
                            <div class="bg_gradient_blue matching-key number" date-number="9">9</div>
                        </div>
                        <div class="matching-keyboard-row">
                            <div class="matching-key bg_orange" id="del">删除</div>
                            <div class="bg_gradient_blue matching-key number" date-number="-">-</div>
                            <div class="bg_gradient_blue matching-key number" date-number="0">0</div>
                            <div class="matching-key bg_orange" id="next">下一题</div>
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
    var even_add_time = $('#even_add_time').val(); //连加
    var add_and_subtract_time = $('#add_and_subtract_time').val(); //加减
    var wax_and_wane_time = $('#wax_and_wane_time').val(); //乘除
    // var even_add_time = 2; //连加
    // var add_and_subtract_time = 2; //加减
    // var wax_and_wane_time = 2000; //乘除
    var level={number:2,symbol:1},//题目难度
    n_type=<?=$child_type?>,
    child_type_down=<?=!empty($child_type_down) ? $child_type_down : ''?>,
    type='',//当前子相运算类型
    ajaxData=[],//提交的数据
    nextBtn_click=0,//下一题点击次数，控制难度
    add_interval_times=3,//加减法每隔多少题增加一个难度
    cx_interval_times=6;//乘除法每隔多少题增加一个难度


    var matchSession=$.GetSession('match','true');
    var isMatching=false;//判断用户是否刷新页面
    if(matchSession && matchSession['match_id']===$.Request('match_id') && matchSession['project_id']===$.Request('project_id') && matchSession['match_more']===$.Request('match_more')){
        isMatching=true;
        ajaxData=matchSession['ajaxData'];
        level=matchSession['level'];
        n_type=matchSession['n_type'];
        nextBtn_click=matchSession['nextBtn_click'];
    }
    if(n_type==0){
        type="连加运算" 
        even_add_time=child_type_down
    }else if(n_type==1){
        type="加减运算" 
        add_and_subtract_time=child_type_down
    }else{
        type='乘除运算'
        wax_and_wane_time=child_type_down
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
                day=day>0?day+'天':'';
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
                    type="连加运算" 
                    sys_second=even_add_time;
                    nextBtn_click=0;
                }else if(n_type==1){
                    type="加减运算" 
                    sys_second=add_and_subtract_time;
                    nextBtn_click=0;
                }else if(n_type==2){
                    type="乘除运算" 
                    sys_second=wax_and_wane_time;
                    nextBtn_click=0;
                }else{
                    clearInterval(timer);
                    var thisAjaxRow=ajaxData[ajaxData.length-1]
                    var yours=$('#answer').text().length==0 ? '' : parseInt($('#answer').text());
                    thisAjaxRow['yours']=yours;
                    if(yours==thisAjaxRow['rights']){
                        thisAjaxRow['isRight']=true;
                        $('#answer').removeClass('answer').addClass('right-fast')
                    }else{
                        thisAjaxRow['isRight']=false;
                        $('#answer').removeClass('answer').addClass('error-fast')
                    }
                    submit(0)
                }
                
                $('.count_downs').text('00:00:00').attr('data-seconds',sys_second)
                $('#type').text(type)
                $('#answer').removeClass('error-fast').removeClass('right-fast').addClass('answer').text('') 
                inItFastCalculation(level,type);  
                nextQuestion()
            }

        }, 1000);
    }  
    function randSZ() {//生成随即数字0-9
        return ( Math.floor ( Math.random ( ) * 9  ) );
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
        for (let i = 0; i < N; i++) {
            var oneNumber=randSZ();
            if(i==0){
                oneNumber=compare(0)
            }
            firstNumber+=oneNumber;
        }
        answer=parseInt(firstNumber);
        for (var index = 0; index < L; index++) {
            var symbol=''
            if(type=='连加运算'){
                symbol='+';
            }else if(type=='加减运算'){
                symbol=randJJ()
            }
            
            result+=symbol
            var number='';
            for (let i = 0; i < N; i++) {
                var oneNumber=randSZ();
                if(i==0){
                    oneNumber=compare(0)
                }
                number+=oneNumber;
            }  
            if(symbol=='+'){
                answer+=parseInt(number);
            }else if(symbol=='-'){
                answer-=parseInt(number);
            }
            result+=number    
            
        }
       
        var row={question:firstNumber+result,rights:answer,yours:'',isRight:false,}
        return row;
    }
    function CX(level) {//乘除运算
        var firstNumber='';//符号左侧数字
        var answer='';//计算出的答案
        var secondNumber=compare(0);//符号右侧数字
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
            for (let i = 0; i < N; i++) {
                var oneNumber=randSZ();
                if(i==0){
                    oneNumber=compare(0)
                }
                firstNumber+=oneNumber;
            }  
            answer=parseInt(firstNumber)*parseInt(secondNumber)
            question=firstNumber+symbol+secondNumber
        }else if(symbol=='÷'){
            for (let i = 0; i < N; i++) {
                var oneNumber=randSZ();
                if(i==0){
                    oneNumber=compare(0)
                }
                firstNumber+=oneNumber;
            }  
            firstNumber=parseInt(firstNumber)
            firstNumber=firstNumber-firstNumber%parseInt(secondNumber)
            answer=parseInt(firstNumber)/parseInt(secondNumber)

            question=firstNumber+symbol+secondNumber
        }
        var row={question:question,rights:answer,yours:'',isRight:false,}
        return row
        
    }
    function inItFastCalculation(levels,type) {
        var text=''
        var row=''
        if(type=='连加运算'){//连加运算
            row=add(levels,type);
        }else if(type=='加减运算'){//加减运算
            row=add(levels,type);
        }else if(type=='乘除运算'){//乘除运算
            row=CX(levels);
        }
        ajaxData.push(row)
        var sessionData={
            ajaxData:ajaxData,
            match_id:$.Request('match_id'),
            project_id:$.Request('project_id'),
            match_more:$.Request('match_more'),
            level:level,
            n_type:n_type,
            nextBtn_click:nextBtn_click
        }
        $.SetSession('match',sessionData)
    }
    function nextQuestion() {
        $('#question').text(ajaxData[ajaxData.length-1]['question']+'=?')
    }
    $('.number').each(function(i){//键盘数字tap事件
        var _this=$(this);
        var dom=$(this)[0]
        var hammertime = new Hammer(dom);
        hammertime.on("tap", function (e) {
            var number=_this.attr('date-number');
            var text=$('.answer').text()
            $('.answer').text(text+number)
        });
    })
    //删除tap事件
    var hammertime1 = new Hammer($('#del')[0]);
    hammertime1.on("tap", function (e) {
        var text=$('.answer').text()
        var len=text.length;
        if(len>0){
            var news=text.substring(0,len-1)
            $('.answer').text(news)
        }

    });
    //下一题tap事件
    var hammertime2 = new Hammer($('#next')[0]);
    hammertime2.on("tap", function (e) {
        nextBtn_click++
        if (type=='乘除运算') {
            if(nextBtn_click%cx_interval_times==0){//难度控制
                level.symbol=1
                level.number++
                if(level.number>4){
                    level.number=4
                }
            }
        }else{
            if(nextBtn_click%add_interval_times==0){//难度控制，每点三次，数字长度加1
                level.number++
                if(level.number>4){//数字长度达到4位最大限度，数字长度变为2，符号加1，为最大限度，数字长度不变
                    level.symbol++
                    if(level.symbol>=4){
                        level.number=4
                    }else{
                        level.number=2
                    }
                    
                }
            }
        }
        var thisAjaxRow=ajaxData[ajaxData.length-1]
        var yours=$('#answer').text().length==0 ? '' : parseInt($('#answer').text());
        thisAjaxRow['yours']=yours;
        if(yours==thisAjaxRow['rights']){
            thisAjaxRow['isRight']=true;
            $('#answer').removeClass('answer').addClass('right-fast')
        }else{
            thisAjaxRow['isRight']=false;
            $('#answer').removeClass('answer').addClass('error-fast')
        }
        setTimeout(() => {
            $('#answer').removeClass('error-fast').removeClass('right-fast').addClass('answer').text('') 
            inItFastCalculation(level,type);
            nextQuestion()
        }, 500);
        
    });
    function submit(time){//提交答案
        var data={
            action:'answer_submit',
            _wpnonce:$('#inputSubmit').val(),
            match_id:<?=$_GET['match_id']?>,
            project_id:<?=$_GET['project_id']?>,
            match_more:<?=empty($_GET['match_more']) ? 1 : $_GET['match_more']?>,
            my_answer:ajaxData,
            match_action:'subjectFastCalculation',
            surplus_time:time,
        }

         $.post(window.admin_ajax+"?date="+new Date().getTime(),data,function(res){
             // $.DelSession('match')
             if(res.success){
                 if(res.data.url){
                     window.location.href=res.data.url
                 }
             }else{
                 $.alerts(res.data.info)
             }
         })
    }
    if($('.count_down').attr('data-seconds')<=0){//进入页面判断时间是否结束
        $.alerts('比赛结束');
        setTimeout(() => {
            submit(0)
        }, 1000);
    }

    $('.count_down').countdown(function(S, d){//倒计时
        var D=d.day>0 ? d.day+'天' : '';
        var h=d.hour<10 ? '0'+d.hour : d.hour;
        var m=d.minute<10 ? '0'+d.minute : d.minute;
        var s=d.second<10 ? '0'+d.second : d.second;
        var time=D+h+':'+m+':'+s;
        var html="<span data-time='"+S+"' id='dataTime'>"+time+"</span>"
         $(this).html(html);
        if(S<=0){//本轮比赛结束
            if(S==0){
                $.alerts('倒计时结束，即将提交答案')
            }else{
                $.alerts('比赛结束')
            }
            setTimeout(() => {
                submit(S)
            }, 1000);
        }
    });  
layui.use('layer', function(){
    var hammertime4 = new Hammer($('#sumbit')[0]);
    hammertime4.on("tap", function (e) {
        var time=$("#dataTime").attr('data-time')?$("#dataTime").attr('data-time'):0;
        layer.open({
                type: 1
                ,maxWidth:300
                ,title: '提示' //不显示标题栏
                ,skin:'nl-box-skin'
                ,id: 'certification' //防止重复弹出
                ,content: '<div class="box-conent-wrapper">是否立即提交？</div>'
                ,btn: ['提交', '按错了', ]
                ,success: function(layero, index){
                }
                ,yes: function(index, layero){
                    
                    var thisAjaxRow=ajaxData[ajaxData.length-1]
                    var yours=$('#answer').text().length==0 ? '' : parseInt($('#answer').text());
                    thisAjaxRow['yours']=yours;
                    if(yours==thisAjaxRow['rights']){
                        thisAjaxRow['isRight']=true;
                    }else{
                        thisAjaxRow['isRight']=false;
                    }
                    layer.closeAll();
                    submit(time);
                }
                ,btn2: function(index, layero){
                }
                ,closeBtn:2
                ,btnAagn: 'c' //按钮居中
                ,shade: 0.3 //遮罩
                ,isOutAnim:true//关闭动画
            });
            
    });
});

    
})
</script>