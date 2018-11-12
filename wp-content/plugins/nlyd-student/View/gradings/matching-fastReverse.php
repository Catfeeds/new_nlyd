

<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><div><?=__($project_title, 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="remember width-margin width-margin-pc">
                    <!-- <div class="matching-row layui-row">
                        <div class="c_black match_info_font"><div><?=__($project_title, 'nlyd-student')?></div></div>
                    </div> -->
                    <div class="matching-row layui-row have-submit">
                        <div class="c_black match_info_font"><div><?=__('逆向运算', 'nlyd-student')?></div></div>
                        <div class="c_blue match_info_font"><div><?=__('第<span id="total">0</span>题', 'nlyd-student')?></div></div>
                        <div class="c_blue match_info_font">
                            <div>
                                <span class="count_down" data-seconds="540"><?=__('初始中', 'nlyd-student')?>...</span>
                            </div>
                        </div>
                        <div class="matching-sumbit" id="sumbit"><div><?=__('提交', 'nlyd-student')?></div></div>
                    </div>
                    <p class="count_p fs_14">
                        <span class="c_black"><?=__('请用完给出的4个数字，并利用运算符号使运算结果等于24！', 'nlyd-student')?></span>
                        <br>
                        <span>*<?=__('若您跳过此题不作答，则扣掉2秒时间', 'nlyd-student')?></span>
                        <br>
                        <span>*<?=__('连续作答“本题无解”不可超过5次，否则系统将强制提交本轮成绩', 'nlyd-student')?></span>
                    </p>
                    <div class="matching-fast">
                        <div class="item-wrapper">
                            <div class="fast-item answer" date-number="1"><div></div></div>
                            <!-- <div class="fast-item error-fast">4A@#$%</div>
                            <div class="fast-item right-fast">4A@#$%</div> -->
                        </div>
                    </div>
                    <div class="matching-keyboard">
                        <div class="matching-keyboard-row">
                            <div class="bg_yellow matching-key c_white fs_18 number rand" date-number="" data-index='1'><div></div></div>
                            <div class="bg_yellow matching-key c_white fs_18 number rand" date-number="" data-index='2'><div></div></div>
                            <div class="bg_yellow matching-key c_white fs_18 number rand" date-number="" data-index='3'><div></div></div>
                            <div class="bg_yellow matching-key c_white fs_18 number rand" date-number="" data-index='4'><div></div></div>
                            <div class="bg_gradient_blue matching-key c_white fs_18 number leftBrackets" date-number="("><div>(</div></div>
                        </div>
                        <div class="matching-keyboard-row">
                            <div class="bg_gradient_blue matching-key c_white fs_18 number operator" date-number="+"><div>+</div></div>
                            <div class="bg_gradient_blue matching-key c_white fs_18 number operator reduce" date-number="-"><div>-</div></div>
                            <div class="bg_gradient_blue matching-key c_white fs_18 number operator" date-number="*"><div>×</div></div>
                            <div class="bg_gradient_blue matching-key c_white fs_18 number operator" date-number="/"><div>÷</div></div>
                            <div class="bg_gradient_blue matching-key c_white fs_18 number rightBrackets" date-number=")"><div>)</div></div>
                        </div>
                        <div class="matching-keyboard-row">
                            <div class="bg_orange matching-key c_white fs_16" id="del"><div><?=__('删除', 'nlyd-student')?></div></div>
                            <div class="bg_gradient_blue matching-key c_white fs_16 number" date-number="本题无解"><div><?=__('本题无解', 'nlyd-student')?></div></div>
                            <div class="bg_orange matching-key c_white fs_16" id="next"><div><?=__('下一题', 'nlyd-student')?></div></div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>           
    </div>
</div>
<script>
jQuery(function($) {
    var isSubmit=false;//是否正在提交
    leaveMatchPage(function(){//窗口失焦提交
        $('#next').addClass('disabled')
        var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
        var answer_Text=$('.answer div').text();
        var answer_dateNumber=$('.answer').attr('date-number');
        if(answer_dateNumber=="本题无解"){
            isRights(answer_dateNumber)
        }else{
            isRights(answer_Text)
        }
        submit(time,4);
    })
    var _grad_id=$.Request('grad_id');
    var _grad_type=$.Request('grad_type');
    var _type=$.Request('type');
    var ajaxData=[],dataIndex=[];//记录选择数字得下标
    var sys_second=540;//倒计时的时间
    var end_time=0;
    var grade_question=$.GetSession('grade_question','true');
    var isMatching=false;//判断用户是否刷新页面
    var no_answer=50;//每隔多少题出现一次无解
    if(grade_question && grade_question['grad_id']===_grad_id && grade_question['grad_type']===_grad_type && grade_question['type']===_type){
        isMatching=true;
        ajaxData=grade_question['ajaxData'];
        end_time=grade_question['end_time'];
        sys_second=$.GetSecond(end_time);
        $('.count_down').attr('data-seconds',sys_second)
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
            var _flag=false;//重复题目是true
            var _flag1=false;//前面的4题连续无解是true
            var _flag2=false;//每50题出现一个无解题
            $.each(ajaxData,function(index,value){
                var _select=value.question;
                if(_select[0]==select[0] && _select[1]==select[1] && _select[2]==select[2] && _select[3]==select[3]){//重复题目
                    _flag=true;
                    return false;
                }
            })
            var _len=ajaxData.length;
            var examples=valid(select)
            var _rights='本题无解'
            var have_noanswer=_len==0 ? false : ajaxData[_len-1]['have_noanswer'];
            // var _rights=valid(select);
            if(examples.length>0){//有解
                _rights=examples[0]
            }
            if(_len>=0){
                if(_len%no_answer!=0){//每50道题出现一次本题无解
                    if(_rights=='本题无解'){//本题也无解
                        if(have_noanswer){
                            _flag2=true
                        }
                        have_noanswer=true;
                    }
                }else{
                    have_noanswer=false;
                }
            }
            // if(_len>=1){//前面的1题连续无解
            //     if(_rights=='本题无解'){//本题也无解
            //         if(ajaxData[_len-1]['rights']=='本题无解'){
            //             _flag1=true;
            //         }
            //     }
            // }
            if(_flag || _flag2){//重复题目，连续无解
                initQuestion()
            }else{
                var thisRow={question:select,yours:'',isRight:false,rights:_rights,examples:examples,have_noanswer:have_noanswer}
                ajaxData.push(thisRow) 
                if(_len>=1){
                   delete ajaxData[_len-1]['have_noanswer']
                }
                end_time=$.GetEndTime($('.count_down').attr('data-seconds'));//结束时间
                var sessionData={
                    ajaxData:ajaxData,
                    grad_id:_grad_id,
                    grad_type:_grad_type,
                    type:_type,
                    end_time:end_time
                }
                $.SetSession('grade_question',sessionData)
            }
    }
    function nextQuestion() {
        $('#total').text(ajaxData.length)
        var text=$('.answer div').text();
        $('.rand').each(function(i){
            var text= ajaxData[ajaxData.length-1]['question'][i]
            $(this).attr('date-number',text).children('div').text(text)
        }).removeClass('disabled')
        $('.answer div').text('')
        $('.answer').removeClass('error-fast').removeClass('right-fast');
    }
    function submit(time,submit_type){//提交答案
        if(!isSubmit){
            // $('#load').css({
            //     'display':'block',
            //     'opacity': '1',
            //     'visibility': 'visible',
            // })
            // isSubmit=true;
            var _len=ajaxData.length;
            delete ajaxData[_len-1]['examples'];
            delete ajaxData[_len-1]['have_noanswer'];
            var data={
                grading_id:_grad_id,
                grading_type:_grad_type,
                questions_type:_type,
                action:'grading_answer_submit',
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
                            setTimeout(function(){
                                window.location.href=res.data.url
                            }, 600);
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
                        var href="<?=home_url('matchs/answerLog/grad_id/'.$_GET['grad_id'].'/project_alias/'.$_GET['project_alias'].'/project_more_id/'.$_GET['project_more_id'].'/type/')?>"+_type;
                        window.location.href=href;
            　　　　}
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
                $('#next').addClass('disabled')
                $('.count_down').text('00:00:00').attr('data-seconds',sys_second)
                clearInterval(timer);
                var answer_Text=$('.answer div').text();
                var answer_dateNumber=$('.answer').attr('date-number');
                if(answer_dateNumber=="本题无解"){
                    isRights(answer_dateNumber)
                }else{
                    isRights(answer_Text)
                }
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
                var number_Text=_this.text();
                var number_dateNumber=_this.attr('date-number');
                var answer_Text=$('.answer div').text();
                var answer_dateNumber=$('.answer').attr('date-number');
                if(answer_dateNumber=='本题无解'){
                    answer_Text=''
                }
                if(number_dateNumber=='本题无解'){
                    $('.answer div').text(number_Text)
                    $('.number').removeClass('disabled')
                        _this.stop(true).animate({
                            'opacity':'0.6',
                            'filter': 'alpha(opacity=60)',
                        },50).animate({
                            'opacity':'1',
                            'filter': 'alpha(opacity=100)',
                        },50)
                    $('.answer').attr('date-number',"本题无解");
                }else{
                    var len=answer_Text.length;
                    var x=answer_Text.charAt(len-1,1);
                    if(!isNaN(parseInt(number_Text))){//数字，前一位必须是符号
                        if(len>0){
                            if(isNaN(parseInt(x)) && x!==')'){
                                $('.answer div').text(answer_Text+number_Text)
                                _this.addClass('disabled')
                                dataIndex.push(_this.attr('data-index'))
                                $('.answer').attr('date-number',"1");
                            }
                        }else{
                            $('.answer div').text(answer_Text+number_Text)
                            _this.addClass('disabled')
                            dataIndex.push(_this.attr('data-index'))
                            $('.answer').attr('date-number',"1");
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
                                    for (var i = 0; i < answer_Text.length; i++) {
                                        if (answer_Text.charAt(i) === "(") {
                                            leftBracket++;
                                        } else if(answer_Text.charAt(i) === ")") {
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
                                $('.answer div').text(answer_Text+number_Text) 
                                _this.stop(true).animate({
                                    'opacity':'0.6',
                                    'filter': 'alpha(opacity=60)',
                                },50).animate({
                                    'opacity':'1',
                                    'filter': 'alpha(opacity=100)',
                                },50)
                                $('.answer').attr('date-number',"1");
                            }
                        }else{//数字键盘全部按下且有（
                            if(_this.hasClass('rightBrackets')){//点击右括号
                                var leftBracket = 0, rightBracket = 0;
                                for (var i = 0; i < answer_Text.length; i++) {
                                    if (answer_Text.charAt(i) === "(") {
                                        leftBracket++;
                                    } else if(answer_Text.charAt(i) === ")") {
                                        rightBracket++;
                                    }
                                }
                                if(leftBracket>rightBracket){
                                    $('.answer div').text(answer_Text+number_Text)  
                                    _this.stop(true).animate({
                                        'opacity':'0.6',
                                        'filter': 'alpha(opacity=60)',
                                    },50).animate({
                                        'opacity':'1',
                                        'filter': 'alpha(opacity=100)',
                                    },50)
                                    $('.answer').attr('date-number',"1");
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
            // var text=$('.answer div').text()
        var answer_Text=$('.answer div').text();
        var answer_dateNumber=$('.answer').attr('date-number');
        var len=answer_Text.length;
        var news='';
        if(len>0){
            if(answer_dateNumber!='本题无解'){
                var end=answer_Text.substr(answer_Text.length-1,1);
                var end_1=answer_Text.substr(answer_Text.length-2,1)
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
                        news=answer_Text.substring(0,len-2);
                    }else{
                        news=answer_Text.substring(0,len-1);
                    }
                }else{
                    news=answer_Text.substring(0,len-1);
                }
                $('.answer div').text(news)
            }else{
                $('.answer div').text('')
            }
        }
        $('.answer').attr('date-number',"1");
    }
});
    //下一题tap事件
    // mTouch('body').on('tap','#next',function(e){
new AlloyFinger($('#next')[0], {
    tap:function(e){
        var _this=$('#next');
        if(!_this.hasClass('disabled')){
            _this.addClass('disabled')
            var flag=false;
            var _len=ajaxData.length;
            // var text=$('.answer div').text()
            var answer_Text=$('.answer div').text();
            var answer_dateNumber=$('.answer').attr('date-number');
            
            var new_text=answer_Text.replace(/×/g,'*');
            new_text=new_text.replace(/÷/g,'/');
            $('.rand').each(function(){//所有数字按钮都已使用后
                if(!$(this).hasClass('disabled')){
                    flag=true;
                    return false;
                }
            })
            if(answer_Text.length!=0){
                if(answer_dateNumber=='本题无解'){
                     if(ajaxData[_len-1].rights=='本题无解'){
                        $('.answer').addClass('right-fast')
                        ajaxData[_len-1]['isRight']=true;
                     }else{
                        $('.answer').addClass('error-fast')
                        ajaxData[_len-1]['isRight']=false;
                     }
                     ajaxData[_len-1].yours=answer_dateNumber;
                     if(_len>=5){
                        if(ajaxData[_len-1]['yours']=='本题无解' && ajaxData[_len-2]['yours']=='本题无解' && ajaxData[_len-3]['yours']=='本题无解' && ajaxData[_len-4]['yours']=='本题无解' && ajaxData[_len-5]['yours']=='本题无解'){
                            $.alerts("<?=__('检测到恶意答题，强制提交答案', 'nlyd-student')?>")
                            var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
                            submit(time,5)
                        }
                    }
                }else{
                    ajaxData[_len-1].yours=answer_Text;
                    if(flag){
                        _this.removeClass('disabled')
                        return false;
                    }else{
                        var _result=calculateResult(new_text)
                        if(_result==24){
                            $('.answer').addClass('right-fast')
                            ajaxData[_len-1]['isRight']=true;
                        }else{
                            if(_result=='error'){//符号错误
                                $('.answer').addClass('error-fast')
                                ajaxData[_len-1]['isRight']=false;
                            }else{
                                if(_result%1==0){//整数
                                    $('.answer').addClass('error-fast')
                                    ajaxData[_len-1]['isRight']=false;
                                }else{//浮点数
                                    var __flag=false;
                                    $.each(ajaxData[_len-1]['examples'],function(i,v){
                                        var _item=v;
                                        var AA='';
                                        try {
                                            AA=eval(_item); // no exception occured
                                        }
                                        catch (e) {
                                            AA='error'; 
                                        }
                                        // console.log(_result)
                                        if(AA==_result){//相同浮点数
                                            __flag=true;
                                            return false;
                                        }
                                    })
                                    if(__flag){//相同浮点数
                                        $('.answer').addClass('right-fast')
                                        ajaxData[_len-1]['isRight']=true;
                                    }else{
                                        $('.answer').addClass('error-fast')
                                        ajaxData[_len-1]['isRight']=false;
                                    }
                                }
                            }
                        }
                    }
                }
                setTimeout(function() {
                    initQuestion()
                    nextQuestion()
                    if(sys_second>0){
                        _this.removeClass('disabled')
                    }
                }, 300);
            }else{
                ajaxData[_len-1].yours=answer_Text;
                //跳过2s
                $('.answer').addClass('error-fast')
                ajaxData[_len-1]['isRight']=false;
                sys_second-=1
                setTimeout(function() {
                    initQuestion()
                    nextQuestion()
                    if(sys_second>0){
                        _this.removeClass('disabled')
                    }
                }, 300);
            }
            $('.answer').attr('date-number',"1");
            delete ajaxData[_len-1].examples;
        }
    }
});

function isRights(text) {
    var flag=false;
    var _len=ajaxData.length;
    ajaxData[_len-1].yours=text;
    var new_text=text.replace(/×/g,'*');
    new_text=new_text.replace(/÷/g,'/');
    $('.rand').each(function(){//所有数字按钮都已使用后
        if(!$(this).hasClass('disabled')){
            flag=true;
            return false;
        }
    })
    if(text.length!=0){
        if(text=='本题无解'){
                if(ajaxData[_len-1].rights=='本题无解'){
                ajaxData[_len-1]['isRight']=true;
                }else{
                ajaxData[_len-1]['isRight']=false;
                }
        }else{
            if(flag){
                ajaxData[_len-1]['isRight']=false;
            }else{
                var _result=calculateResult(new_text)
                if(_result==24){
                    ajaxData[_len-1]['isRight']=true;
                }else{
                    if(_result=='error'){//符号错误
                        ajaxData[_len-1]['isRight']=false;
                    }else{
                        if(_result%1==0){//整数
                            ajaxData[_len-1]['isRight']=false;
                        }else{//浮点数
                            var __flag=false;
                            $.each(ajaxData[_len-1]['examples'],function(i,v){
                                var _item=v;
                                var AA='';
                                try {
                                    AA=eval(_item); // no exception occured
                                }
                                catch (e) {
                                    AA='error'; 
                                }
                                if(AA==_result){//相同浮点数
                                    __flag=true;
                                    return false;
                                }
                            })
                            if(__flag){//相同浮点数
                                ajaxData[_len-1]['isRight']=true;
                            }else{
                                ajaxData[_len-1]['isRight']=false;
                            }
                        }
                    }
                }
            }
        }
    }else{
        //跳过2s
        ajaxData[_len-1]['isRight']=false;
    }
    delete ajaxData[_len-1].examples;
}
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
                        var answer_Text=$('.answer div').text();
                        var answer_dateNumber=$('.answer').attr('date-number');
                        if(answer_dateNumber=="本题无解"){
                            isRights(answer_dateNumber)
                        }else{
                            isRights(answer_Text)
                        }
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