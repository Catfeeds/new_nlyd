

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
                        <span class="c_blue ml_10"> 第1题</span>
                        <span class="c_blue ml_10">
                            <i class="iconfont">&#xe685;</i>
                            <span class="count_down" data-seconds="<?=$count_down?>">初始中...</span>
                        </span>
                        <div class="matching-sumbit" id="sumbit">提交</div>
                    </div>
                    <p class="count_p fs_14">
                        <span class="c_black">请用完给出的4个数字，并利用运算符号使运算结果等于24！</span>
                        <br>
                        <span>*若您跳过此题不作答，则扣掉2秒时间</span>
                    </p>
                    <div class="matching-fast">
                        <div class="item-wrapper">
                            <div class="fast-item answer"></div>
                            <!-- <div class="fast-item error-fast">4A@#$%</div>
                            <div class="fast-item right-fast">4A@#$%</div> -->
                        </div>
                    </div>

                    <div class="matching-keyboard">
                        <div class="matching-keyboard-row">
                            <div class="bg_yellow matching-key number rand" date-number="" data-index='1'></div>
                            <div class="bg_yellow matching-key number rand" date-number="" data-index='2'></div>
                            <div class="bg_yellow matching-key number rand" date-number="" data-index='3'></div>
                            <div class="bg_yellow matching-key number rand" date-number="" data-index='4'></div>
                            <div class="bg_gradient_blue matching-key number leftBrackets" date-number="(">(</div>
                        </div>
                        <div class="matching-keyboard-row">
                            <div class="bg_gradient_blue matching-key number operator" date-number="+">+</div>
                            <div class="bg_gradient_blue matching-key number operator reduce" date-number="-">-</div>
                            <div class="bg_gradient_blue matching-key number operator" date-number="*">×</div>
                            <div class="bg_gradient_blue matching-key number operator" date-number="/">÷</div>
                            <div class="bg_gradient_blue matching-key number rightBrackets" date-number=")">)</div>
                        </div>
                        <div class="matching-keyboard-row">
                            <div class="bg_orange matching-key" id="del">删除</div>
                            <div class="bg_gradient_blue matching-key number" date-number="本题无解">本题无解</div>
                            <div class="bg_orange matching-key" id="next">下一题</div>
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
    var ajaxData=[],dataIndex=[];//记录选择数字得下标
    var sys_second=$('.count_down').attr('data-seconds');//倒计时的时间
    var matchSession=$.GetSession('match','true');
    var isMatching=false;//判断用户是否刷新页面
    if(matchSession && matchSession['match_id']===$.Request('match_id') && matchSession['project_id']===$.Request('project_id') && matchSession['match_more']===$.Request('match_more')){
        isMatching=true;
        ajaxData=matchSession['ajaxData'];
    }
    if(!isMatching){
        initQuestion();//初始化数据
    }
    nextQuestion();//初始化dom
    count_down();//倒计时
    function randSZ() {//生成随即数字
            var arr=['1','2','3','4','5','6','7','8','9','10','11','12','13'];

            var pos = Math.round(Math.random() * (arr.length - 1));

            return arr[pos];
    }
    function initQuestion() {
        var select=[]
        for (var index = 0; index < 4; index++) {
            var number=randSZ();
            select.push(number)
        }
       var thisRow={question:select,yours:'',isRight:false}
        ajaxData.push(thisRow)
        if(!$.Request('test')){
            var sessionData={ajaxData:ajaxData,match_id:$.Request('match_id'),project_id:$.Request('project_id'),match_more:$.Request('match_more')}
            $.SetSession('match',sessionData)
        }
    }
    function nextQuestion() {
        var text=$('.answer').text();
        $('.rand').each(function(i){
            var text= ajaxData[ajaxData.length-1]['question'][i]
             $(this).text(text).attr('date-number',text)
        }).removeClass('disabled')
        $('.answer').text('').removeClass('error-fast').removeClass('right-fast');
    }

    function submit(time){//提交答案

        var data={
            action:'answer_submit',
            _wpnonce:$('#inputSubmit').val(),
            match_id:<?=$_GET['match_id']?>,
            project_id:<?=$_GET['project_id']?>,
            match_more:<?=empty($_GET['match_more']) ? 1 : $_GET['match_more']?>,
            my_answer:ajaxData,
            match_action:'subjectFastReverse',
            surplus_time:time,
        }
        $.post(window.admin_ajax+"?date="+new Date().getTime(),data,function(res){
            $.DelSession('match')
            if(res.success){
                if(res.data.url){
                    window.location.href=res.data.url
                }
            }else{
                $.alerts(res.data.info)
            }
        })
    }


     function count_down(){
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
                $('.count_down').text(text).attr('data-seconds',sys_second)
            } else {//倒计时结束
                $('.count_down').text('00:00:00').attr('data-seconds',sys_second)
                clearInterval(timer);
                var thisAjaxRow=ajaxData[ajaxData.length-1]
                var text=$('.answer').text()
                thisAjaxRow.yours=text;
                submit(0)
            }

        }, 1000);
    }
    $('.number').each(function(i){//键盘数字tap事件
        var _this=$(this);
        var dom=$(this)[0]
        var hammertime = new Hammer(dom);
        hammertime.on("tap", function (e) {
            var flag=false;
            $('.rand').each(function(){//所有数字按钮都已使用后
                if(!$(this).hasClass('disabled')){
                    flag=true;
                    return false;
                }
            })
            if(!_this.hasClass('disabled')){
                var number=_this.text();
                var text=$('.answer').text()
                if(text=="本题无解"){
                    text=''
                }
                if(number=="本题无解"){
                    $('.answer').text(number)
                    $('.number').removeClass('disabled')
                }else{
                    var len=text.length;
                    var x=text.charAt(len-1,1);
                    if(!isNaN(parseInt(number))){//数字，前一位必须是符号
                        if(len>0){
                            if(isNaN(parseInt(x)) && x!==')'){
                                $('.answer').text(text+number)
                                _this.addClass('disabled')
                                dataIndex.push(_this.attr('data-index'))
                            }
                        }else{

                            $('.answer').text(text+number)
                            _this.addClass('disabled')
                            dataIndex.push(_this.attr('data-index'))
                        }
                    }else{//符号
                        var flag=false
                        if(len>0){
                            if(_this.hasClass('operator')){//运算符
                                if(x===')'){
                                    flag=true
                                }
                                if (_this.hasClass('reduce')) {//减号
                                   if(x==='(' || !isNaN(parseInt(x))){
                                       flag=true   
                                   }
                                }else{
                                    if(!isNaN(parseInt(x))){
                                        flag=true  
                                    }
                                }
                            }
                            if(_this.hasClass('leftBrackets')){//左括号
                                if(isNaN(parseInt(x)) && x!==")"){
                                        flag=true
                                }   
                            }
                            if(_this.hasClass('rightBrackets')){//右括号
                                var leftBracket = 0, rightBracket = 0;
                                for (var i = 0; i < text.length; i++) {
                                    if (text.charAt(i) === "(") {
                                        leftBracket++;
                                    } else if(text.charAt(i) === ")") {
                                        rightBracket++;
                                    }
                                }
                                if(leftBracket>rightBracket){
                                    if(!isNaN(parseInt(x)) || x=== ")"){
                                        flag=true
                                    }
                                        
                                } 
                            }
                        }else{
                            if (_this.hasClass('reduce') || _this.hasClass('leftBrackets')) {//减号//左括号
                                flag=true  
                            }
                        }
                        if(flag){
                            $('.answer').text(text+number) 
                        }
                    }
                }
            }
        });
    })
    //删除tap事件
    var hammertime1 = new Hammer($('#del')[0]);
    hammertime1.on("tap", function (e) {
        var text=$('.answer').text()
        var len=text.length;
        var news='';
        if(len>0){
            if(text!="本题无解"){
                var end=text.substr(text.length-1,1);
                var end_1=text.substr(text.length-2,1)
                if(!isNaN(parseInt(end))){//删除的是数字
                    var endIndex=dataIndex.length-1
                    var data_index=dataIndex[endIndex];
                    $('.rand').each(function(){
                        if($(this).attr('data-index')==data_index){
                            $(this).removeClass('disabled')
                            dataIndex.splice(endIndex,1)
                            return false;
                        }
                    })
                    if(!isNaN(parseInt(end_1))){//删除的两位数数字
                        news=text.substring(0,len-2);
                    }else{
                        news=text.substring(0,len-1);
                    }
                }else{
                    news=text.substring(0,len-1);
                }
                

            }
            $('.answer').text(news)
        }

    });
    //下一题tap事件
    var hammertime2 = new Hammer($('#next')[0]);
    hammertime2.on("tap", function (e) {
        var _this=$('#next');
        if(!_this.hasClass('disabled')){
            _this.addClass('disabled')
            var text=$('.answer').text()
            var flag=false;
            var isRight=false;//是否是正确答案
            var text=$('.answer').text()
            $('.rand').each(function(){//所有数字按钮都已使用后
                if(!$(this).hasClass('disabled')){
                    flag=true;
                    return false;
                }
            })
            if(text.length!=0){
                if($('.answer').text()=='本题无解'){
                    text='unsolvable'
                }else{
                    if(flag){
                        _this.removeClass('disabled')
                        return false;
                    }
                }
            }
                ajaxData[ajaxData.length-1].yours=text;
                var thisAjaxRow=ajaxData[ajaxData.length-1]
                
                var data={
                    action:'get_24_result',
                    numbers:thisAjaxRow.question,
                    my_answer:thisAjaxRow.yours,
                    new_date:new Date().getTime(),
                    match_more:$.Request('match_more') ? $.Request('match_more') : 1,
                    project_alias:"<?=!empty($project_alias) ? $project_alias : ''?>",
                }
                //console.log(data);
                $.ajax({
                    type: "POST",
                    url: window.admin_ajax,
                    data: data,
                    dataType:'json',
                    timeout:2000,
                    success: function(res, textStatus, jqXHR){
                        if(text.length==0){//重新计算时间
                            isRight=false;
                            var newTime=res.data.info;
                            sys_second=newTime
                        }else{
                            if(res.success){
                                isRight=res.data.info
                            }
                        }
                        if(isRight){//正确答案
                            $('.answer').addClass('right-fast')
                        }else{
                            $('.answer').addClass('error-fast')
                        }
                        ajaxData[ajaxData.length-1]['isRight']=isRight
                        // ajaxData.push(thisRow);
                        setTimeout(() => {
                            initQuestion()
                            nextQuestion()
                        }, 500);
                        _this.removeClass('disabled')
                    },
                    error:function (XMLHttpRequest, textStatus, errorThrown) {
                        _this.removeClass('disabled')
                        if(errorThrown=='timeout'){
                            $.alerts('网络超时')
                        }
                        initQuestion()
                        nextQuestion()
                    }

                });
            }
    });
layui.use('layer', function(){
    var hammertime4 = new Hammer($('#sumbit')[0]);
    hammertime4.on("tap", function (e) {
        var time=$(".count_down").attr('data-seconds')?$(".count_down").attr('data-seconds'):0;
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
                    layer.closeAll();
                    var thisAjaxRow=ajaxData[ajaxData.length-1]
                    var text=$('.answer').text()
                    thisAjaxRow.yours=text;
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