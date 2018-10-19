

<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><?=$project_title?></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row">
                        <span class="c_black match_info_font"><?=$project_title?><?php printf(__('第%s轮', 'nlyd-student'), $match_more_cn)?></span>
                        <span class="c_blue ml_10 match_info_font"> <?=__('第', 'nlyd-student')?><span id="total">0</span><?=__('题', 'nlyd-student')?></span>
                        <span class="c_blue ml_10 match_info_font">
                            <i class="iconfont">&#xe685;</i>
                            <span class="count_down" data-seconds="<?=$count_down?>"><?=__('初始中', 'nlyd-student')?>...</span>
                        </span>
                        <div class="matching-sumbit match_info_font" id="sumbit"><?=__('提交', 'nlyd-student')?></div>
                    </div>
                    <p class="count_p fs_14">
                        <span class="c_black"><?=__('请用完给出的4个数字，并利用运算符号使运算结果等于24！', 'nlyd-student')?></span>
                        <br>
                        <span>*<?=__('若您跳过此题不作答，则扣掉2秒时间', 'nlyd-student')?></span>
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
                            <div class="bg_yellow matching-key c_white fs_18 number rand" date-number="" data-index='1'></div>
                            <div class="bg_yellow matching-key c_white fs_18 number rand" date-number="" data-index='2'></div>
                            <div class="bg_yellow matching-key c_white fs_18 number rand" date-number="" data-index='3'></div>
                            <div class="bg_yellow matching-key c_white fs_18 number rand" date-number="" data-index='4'></div>
                            <div class="bg_gradient_blue matching-key c_white fs_18 number leftBrackets" date-number="(">(</div>
                        </div>
                        <div class="matching-keyboard-row">
                            <div class="bg_gradient_blue matching-key c_white fs_18 number operator" date-number="+">+</div>
                            <div class="bg_gradient_blue matching-key c_white fs_18 number operator reduce" date-number="-">-</div>
                            <div class="bg_gradient_blue matching-key c_white fs_18 number operator" date-number="*">×</div>
                            <div class="bg_gradient_blue matching-key c_white fs_18 number operator" date-number="/">÷</div>
                            <div class="bg_gradient_blue matching-key c_white fs_18 number rightBrackets" date-number=")">)</div>
                        </div>
                        <div class="matching-keyboard-row">
                            <div class="bg_orange matching-key c_white fs_16" id="del"><?=__('删除', 'nlyd-student')?></div>
                            <div class="bg_gradient_blue matching-key c_white fs_16 number" date-number="本题无解">本题无解</div>
                            <div class="bg_orange matching-key c_white fs_16" id="next"><?=__('下一题', 'nlyd-student')?></div>
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
    leaveMatchPage(function(){//窗口失焦提交
        var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
        // console.log(data);
        submit(time,4);
    })
    var _match_id=<?=$_GET['match_id']?>;
    var _project_id=<?=$project_id?>;
    var _match_more=<?=$match_more;?>;
    var ajaxData=[],dataIndex=[];//记录选择数字得下标
    var sys_second=<?=$count_down?>;//倒计时的时间
    var matchSession=$.GetSession('match','true');
    var isMatching=false;//判断用户是否刷新页面
    if(matchSession && matchSession['match_id']===_match_id && matchSession['project_id']===_project_id && matchSession['match_more']===_match_more){
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
        // if(valid(select)=="本题无解"){
        //     initQuestion()
        // }else{
            var thisRow={question:select,yours:'',isRight:false,rights:valid(select)}
            ajaxData.push(thisRow)
            if(!$.Request('test')){
                var sessionData={ajaxData:ajaxData,match_id:_match_id,project_id:_project_id,match_more:_match_more}
                $.SetSession('match',sessionData)
            }
        // }
    }
    function nextQuestion() {
        $('#total').text(ajaxData.length)
        var text=$('.answer').text();
        $('.rand').each(function(i){
            var text= ajaxData[ajaxData.length-1]['question'][i]
             $(this).text(text).attr('date-number',text)
             $(this).next('input').val(text)
        }).removeClass('disabled')
        $('.answer').text('').removeClass('error-fast').removeClass('right-fast');
    }
    function submit(time,submit_type){//提交答案
        if(!isSubmit){
            $('#load').css({
                'display':'block',
                'opacity': '1',
                'visibility': 'visible',
            })
            isSubmit=true;
            var data={
                action:'answer_submit',
                _wpnonce:$('#inputSubmit').val(),
                match_id:<?=$_GET['match_id']?>,
                project_id:<?=$_GET['project_id']?>,
                match_more:<?=empty($_GET['match_more']) ? 1 : $_GET['match_more']?>,
                my_answer:ajaxData,
                match_action:'subjectFastReverse',
                surplus_time:time,
                submit_type:submit_type,//1:选手提交;2:错误达上限提交;3:时间到达提交;4:来回切
            }
            var leavePage= $.GetSession('leavePage','1');
            if(leavePage && leavePage['match_id']===_match_id && leavePage['project_id']===_project_id && leavePage['match_more']===_match_more){
                if(leavePage.Time){
                    data['leave_page_time']=leavePage.Time;
                }
            }
            $.ajax({
                data:data,success:function(res,ajaxStatu,xhr){    
                    $.DelSession('match')
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
     function count_down(){
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
                $('.count_down').text('00:00:00').attr('data-seconds',sys_second)
                clearInterval(timer);
                var thisAjaxRow=ajaxData[ajaxData.length-1]
                var text=$('.answer').text()
                thisAjaxRow.yours=text;
                submit(0,3)
            }

        }, 1000);
    }
        // mTouch('body').on('tap','.number',function(e){
$('.number').each(function(){
    var _this=$(this)
    new AlloyFinger(_this[0], {
        tap: function () {
            var _this=$(this);
            if(!_this.hasClass('disabled')){
                var flag=false;
                $('.rand').each(function(){//所有数字按钮都已使用后
                    if(!$(this).hasClass('disabled')){
                        flag=true;
                        return false;
                    }
                })
                var number=_this.text();
                var text=$('.answer').text()
                if(text=="本题无解"){
                    text=''
                }
                if(number=="本题无解"){
                    $('.answer').text(number)
                    $('.number').removeClass('disabled')
                    $('.answer').text(number) 
                        _this.stop(true).animate({
                            'opacity':'0.6',
                            'filter': 'alpha(opacity=60)',
                        },50).animate({
                            'opacity':'1',
                            'filter': 'alpha(opacity=100)',
                        },50)
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
                        if(flag){//数字没有全部按下
                            var flag1=false
                            if(len>0){
                                if(_this.hasClass('operator')){//运算符
                                    if(x===')'){
                                        flag1=true
                                    }
                                    if (_this.hasClass('reduce')) {//减号
                                    if(x==='(' || !isNaN(parseInt(x))){
                                        flag1=true   
                                    }
                                    }else{
                                        if(!isNaN(parseInt(x))){
                                            flag1=true  
                                        }
                                    }
                                }
                                if(_this.hasClass('leftBrackets')){//左括号
                                    if(isNaN(parseInt(x)) && x!==")"){
                                        flag1=true
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
                                            flag1=true
                                        }
                                            
                                    } 
                                }
                            }else{
                                if (_this.hasClass('reduce') || _this.hasClass('leftBrackets')) {//减号//左括号
                                    flag1=true  
                                }
                            }
                            if(flag1){
                                $('.answer').text(text+number) 
                                _this.stop(true).animate({
                                    'opacity':'0.6',
                                    'filter': 'alpha(opacity=60)',
                                },50).animate({
                                    'opacity':'1',
                                    'filter': 'alpha(opacity=100)',
                                },50)
                            }
                        }else{//数字键盘全部按下且有（
                            if(_this.hasClass('rightBrackets')){//点击右括号
                                var leftBracket = 0, rightBracket = 0;
                                for (var i = 0; i < text.length; i++) {
                                    if (text.charAt(i) === "(") {
                                        leftBracket++;
                                    } else if(text.charAt(i) === ")") {
                                        rightBracket++;
                                    }
                                }
                                if(leftBracket>rightBracket){
                                    $('.answer').text(text+number)  
                                    _this.stop(true).animate({
                                        'opacity':'0.6',
                                        'filter': 'alpha(opacity=60)',
                                    },50).animate({
                                        'opacity':'1',
                                        'filter': 'alpha(opacity=100)',
                                    },50)
                                }
                             
                            }
                        }
                    }
                }
            }
        }
    });
})
    //删除tap事件
    // mTouch('body').on('tap','#del',function(e){
new AlloyFinger($('#del')[0], {
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
    tap:function(e){
        var _this=$('#del');
        // if(!_this.hasClass('disabled')){
        //     _this.addClass('disabled')
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
                    $('.answer').text(news)
                }else{
                    $('.answer').text('')
                }
            }
        }
    });
    //下一题tap事件
    // mTouch('body').on('tap','#next',function(e){
new AlloyFinger($('#next')[0], {
    tap:function(e){
        // $.DelSession('leavePage')
        var _this=$('#next');
        if(!_this.hasClass('disabled')){
            _this.addClass('disabled')
            var flag=false;
            var text=$('.answer').text()
            ajaxData[ajaxData.length-1].yours=text;
            var new_text=text.replace(/×/g,'*');
            new_text=new_text.replace(/÷/g,'/');
            $('.rand').each(function(){//所有数字按钮都已使用后
                if(!$(this).hasClass('disabled')){
                    flag=true;
                    return false;
                }
            })
            if(text.length!=0){
                if($('.answer').text()=='本题无解'){
                     text='unsolvable';
                     if(ajaxData[ajaxData.length-1].rights=="本题无解"){
                        $('.answer').addClass('right-fast')
                        ajaxData[ajaxData.length-1]['isRight']=true;
                     }else{
                        $('.answer').addClass('error-fast')
                        ajaxData[ajaxData.length-1]['isRight']=false;
                     }
                }else{
                    if(flag){
                        _this.removeClass('disabled')
                        return false;
                    }else{
                        if(calculateResult(new_text)==24){
                            $('.answer').addClass('right-fast')
                            ajaxData[ajaxData.length-1]['isRight']=true;
                            
                        }else{
                            $('.answer').addClass('error-fast')
                            ajaxData[ajaxData.length-1]['isRight']=false;
                        }
                    }
                }
                setTimeout(function() {
                    initQuestion()
                    nextQuestion()
                    _this.removeClass('disabled')
                }, 300);
            }else{
                //跳过2s
                $('.answer').addClass('error-fast')
                ajaxData[ajaxData.length-1]['isRight']=false;
                var thisAjaxRow=ajaxData[ajaxData.length-1]
                var data={
                    action:'get_24_result',
                    numbers:thisAjaxRow.question,
                    my_answer:'',
                    new_date:new Date().getTime(),
                    match_more:_match_more ? _match_more : 1,
                    project_alias:"<?=!empty($project_alias) ? $project_alias : ''?>",
                }
                $.ajax({
                    url: window.admin_ajax,
                    data: data,
                    success: function(res, textStatus, jqXHR){
                        if(text.length==0){//重新计算时间
                            var newTime=res.data.info;
                            sys_second=newTime
                        }
                        setTimeout(function() {
                            initQuestion()
                            nextQuestion()
                            _this.removeClass('disabled')
                        }, 300);
                    },
                    error:function (XMLHttpRequest, textStatus, errorThrown) {
                        _this.removeClass('disabled')
                        $.alerts('<?=__('网络超时', 'nlyd-student')?>')
                        initQuestion()
                        nextQuestion()
                    }

                });
            }
        }
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
                    ,btn: ['<?=__('按错了', 'nlyd-student')?>','<?=__('提交', 'nlyd-student')?>',  ]
                    ,success: function(layero, index){
                    }
                    ,yes: function(index, layero){
                        layer.closeAll();
                    }
                    ,btn2: function(index, layero){
                        layer.closeAll();
                        var thisAjaxRow=ajaxData[ajaxData.length-1]
                        var text=$('.answer').text()
                        thisAjaxRow.yours=text;
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