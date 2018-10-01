
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><?=$match_title?></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row">
                        <span class="c_black match_info_font"><?=$project_title?>第<?=$match_more_cn?>轮</span>
                        <span class="c_blue ml_10 match_info_font">第1/1题</span>
                        <span class="c_blue ml_10 match_info_font">
                            <i class="iconfont">&#xe685;</i>
                            <span class="count_down" data-seconds="<?=$count_down?>">00:00:00</span>
                        </span>
                    </div>
                    <div class="matching-row">
                        <div class="matching-row-label">显示张数</div>
                        <div class="matching-row-list">
                            <div class="matching-btn active">全部</div>
                            <div class="matching-btn">1</div>
                            <div class="matching-btn">2</div>
                            <div class="matching-btn">3</div>
                            <div class="matching-btn">4</div>
                            <div class="matching-btn">5</div>
                            <div class="matching-btn">6</div>
                        </div>
                    </div>
                    <div class="matching-number-zoo">
                        <i class="iconfont pokerBtn left disabled" style="display:none">&#xe647;</i>
                    
                        <i class="iconfont pokerBtn right disabled" style="display:none">&#xe648;</i>
                    
                        <div class="porker-zoo">
                            <div class="poker-window">
                                <div class="poker-wrapper">

                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="ta_c" style="margin-top:20px">当前记忆 <span class="c_blue" id="number">0</span> 张</p>
                </div>
                <div class="a-btn" id="complete">记忆完成</div>
            </div>
        </div>           
    </div>
</div>
<input type="hidden" name="_wpnonce" id="inputComplete" value="<?=wp_create_nonce('student_memory_complete_code_nonce');?>">
<input type="hidden" name="match_more" id="inputMatchMore" value="<?=isset($_GET['match_more']) ? $_GET['match_more'] : 1;?>"/>
<input type="hidden" name="_wpnonce" id="inputSubmit" value="<?=wp_create_nonce('student_answer_submit_code_nonce');?>">
<script>
jQuery(function($) { 
    leaveMatchPage(function(){//窗口失焦提交
        var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
        submit(time,4);
    })
    var data_match=[];
    var questions_answer=[]
    var file_path = '<?=leo_student_url."/conf/poker_create.json";?>'; 
    // mTouch('body').on('tap','#complete',function(){//记忆完成
new AlloyFinger($('#complete')[0], {
    tap:function(){
        var _this=$(this);
        if(!_this.hasClass('disabled')){
            _this.addClass('disabled')
            var data={
                action:'memory_complete',
                _wpnonce:$('#inputComplete').val(),
                match_id:<?=$_GET['match_id']?>,
                project_id:<?=$_GET['project_id']?>,
                match_more:$('#inputMatchMore').val(),
                match_action:'pokerRelay',
                match_questions:questions_answer,
                type:'pkjl'
            }
            $.ajax({
                data:data,
                success:function(res,ajaxStatu,xhr){
                    if(res.success){
                        if(res.data.url){
                            window.location.href=res.data.url
                        }
                    }else{
                        $.alerts(res.data.info)
                        _this.removeClass('disabled')
                    }
                },
                error: function(jqXHR, textStatus, errorMsg){
                    _this.removeClass('disabled')
                }
            })
        }
    }
})
    function submit(time,submit_type){//提交答案
        $('#load').css({
                'display':'block',
                'opacity': '1',
                'visibility': 'visible',
            })
        var my_answer=[];
        var data={
            action:'answer_submit',
            _wpnonce:$('#inputSubmit').val(),
            match_id:<?=$_GET['match_id']?>,
            project_id:<?=$_GET['project_id']?>,
            match_more:<?=!empty($_GET['match_more']) ? $_GET['match_more'] : 1 ?>,
            my_answer:my_answer,
            match_action:'subjectPokerRelay',
            surplus_time:time,
            match_questions:questions_answer,
            submit_type:submit_type,//1:选手提交;2:错误达上限提交;3:时间到达提交;4:来回切
        }
        var leavePage= $.GetSession('leavePage','1');
            if(leavePage && leavePage['match_id']===$.Request('match_id') && leavePage['project_id']===$.Request('project_id') && leavePage['match_more']===$.Request('match_more')){
                if(leavePage.Time){
                    data['leave_page_time']=leavePage.Time;
                }
            }
        $.ajax({
            data:data,
            success:function(res,ajaxStatu,xhr){  
                $.DelSession('leavePage')
                if(res.success){
                    if(res.data.url){
                        window.location.href=res.data.url
                        $.DelSession('ready_poker')
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
            error: function(jqXHR, textStatus, errorMsg){
                $('#load').css({
                            'display':'none',
                            'opacity': '0',
                            'visibility': 'hidden',
                        })
            }
        })
    }
    if(<?=$count_down?><=0){//进入页面判断时间是否结束
        $.alerts('比赛结束');
        setTimeout(function() {
            submit(0,3)
        }, 1000);
    }
    $('.count_down').countdown(function(S, d){//倒计时
        var D=d.day>0 ? d.day : '';
        var h=d.hour<10 ? '0'+d.hour : d.hour;
        var m=d.minute<10 ? '0'+d.minute : d.minute;
        var s=d.second<10 ? '0'+d.second : d.second;
        var time=D+h+':'+m+':'+s;
        $(this).attr('data-seconds',S).text(time)
        if(S<=0){//本轮比赛结束
            if(S==0){
                $.alerts('倒计时结束，即将提交答案')
            }else{
                $.alerts('比赛结束')
            }
            setTimeout(function() {
                submit(0,3)
            }, 1000);
        }
    });
    //设置扑克窗口宽度
    initWidth=function() {
        var len=$('.poker-wrapper .poker').length;
        var width=$('.poker-wrapper .poker').width()+2;
        var marginRight=parseInt($('.poker-wrapper .poker').css('marginRight'))
        var W=width*len+marginRight*(len-1)+'px';
        if(parseInt(W)<$('.poker-window').width()){
            $('.poker-wrapper').css({
                'position': 'relative'
            })
        }else{
            $('.poker-wrapper').css({
                'position': 'absolute'
            })
        }
        $('.poker-wrapper').css('width',W);
    }
    // initWidth()
    // var AllData=<?=empty($questions) ? '" "' : $questions;?>;
    var nowPage=1;//当前页
    var onePageItems=false;//false则展示所有
    pagation=function name(data,pages,oneItems) {//数据分页获取数据
        //data所有数据
        //pages 当前页
        //每页的数据条数，false则展示所有
        var len=data.length;
        var pageData=[];
        oneItems=oneItems ? oneItems : len;
        var totalPage= Math.ceil(len/oneItems);
        var page=pages-1;
        var left=false;
        var right=false;
        if(page<=0){
            left=false;
        }else{
            left=true;
        }
        if(pages>=totalPage){
            right=false;
        }else{
            right=true;
        }
        var endData=(page+1)*oneItems;
        if(len<=endData){
            endData=len
        }
        for(var i = page*oneItems ;i < endData; i++){
            pageData.push(data[i])
        }
        var number=oneItems*pages;
        number=number>len ? len : number;
        $('#number').text(number);
        return {left:left,right:right,showData:pageData}
    }


    initPagation=function(){//初始化分业，按钮是否禁用，宽度得初始化
        $.getJSON(file_path,function(JsonData){

            var matchSession=$.GetSession('ready_poker','true');
            if(matchSession && matchSession['match_id']===$.Request('match_id') && matchSession['project_id']===$.Request('project_id') && matchSession['match_more']===$.Request('match_more')){
                data_match=matchSession['data_match'];
                questions_answer=matchSession['questions_answer']
            }else{
                var _answers=JsonData;
                var pos = Math.round(Math.random() * (_answers.length - 1));
                questions_answer=_answers[pos]
                $.each(questions_answer,function(i,v){
                    var item=v.split('-')
                    data_match.push(item)
                })
                var sessionData={
                    data_match:data_match,
                    match_id:$.Request('match_id'),
                    project_id:$.Request('project_id'),
                    match_more:$.Request('match_more'),
                    questions_answer:questions_answer
                }
                $.SetSession('ready_poker',sessionData)
            }

            var data=pagation(data_match,nowPage,onePageItems)
       
        
            $('.poker-wrapper').empty()
            if(data.left){
                $('.left').removeClass('disabled')
            }else{
                $('.left').addClass('disabled')
            }
            if(data.right){
                $('.right').removeClass('disabled')
            }else{
                $('.right').addClass('disabled')
            }
            $.each(data.showData,function(index,value){
                var i='';
                if(value[0]=='club'){
                    i='<i class="iconfont">&#xe635;</i>'
                }else if(value[0]=='heart'){
                    i='<i class="iconfont">&#xe638;</i>'
                }else if(value[0]=='spade'){
                    i='<i class="iconfont">&#xe636;</i>'
                }else if(value[0]=='diamond'){
                    i='<i class="iconfont">&#xe634;</i>'
                }
                var dom='<div class="poker '+value[0]+'">'
                            +'<div class="Glass"></div>'
                            +'<div class="poker-detail poker-top">'
                                +'<div class="poker-name">'+value[1]+'</div>'
                                +'<div class="poker-type">'+i+'</div>'
                            +'</div>'
                            +'<div class="poker-logo">'
                                +'<img src="<?=student_css_url.'image/nlyd-big.png'?>">'
                            +'</div>'
                            +'<div class="poker-detail poker-bottom">'
                                +'<div class="poker-name">'+value[1]+'</div>'
                                +'<div class="poker-type">'+i+'</div>'
                            +'</div>'
                        +'</div>'
                $('.poker-wrapper').append(dom)
            })
            initWidth()
        })
    }
    initPagation()
    // mTouch('body').on('tap','.matching-btn',function(e){
    $('.matching-btn').each(function(){
        var _this=$(this);
        new AlloyFinger(_this[0], {
            tap:function(){
                nowPage=1;
                $('.matching-btn').removeClass('active');
                _this.addClass('active');
                var text=parseInt(_this.text())
                if(!isNaN(text)){
                    onePageItems=text;
                    $('.left').css('display','block')
                    $('.right').css('display','block')
                }else{
                    onePageItems=false
                    $('.left').css('display','none')
                    $('.right').css('display','none')
                }
                initPagation()
            }
        })
    })
    //左翻页
    // mTouch('body').on('tap','.left',function(e){
new AlloyFinger($('.left')[0], {
    tap:function(){
        if($('.left').hasClass('disabled')){
            return false;
        }else{
            nowPage--;
            initPagation()
        }
    }
});
    //右翻页
    // mTouch('body').on('tap','.right',function(e){
new AlloyFinger($('.right')[0], {
    tap:function(){
        if($('.right').hasClass('disabled')){
            return false;
        }else{
            nowPage++;
            initPagation()
        }
    }
});

})
</script>