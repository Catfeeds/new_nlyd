
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><?=$match_title?></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row">
                        <span class="c_black"><?=$project_title?>第<?=$match_more_cn?>轮</span>
                        <span class="c_blue ml_10">第1/1题</span>
                        <span class="c_blue ml_10">
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
                        <i class="iconfont pokerBtn left disabled">&#xe60c;</i>
                    
                        <i class="iconfont pokerBtn right disabled">&#xe60b;</i>
                    
                        <div class="porker-zoo">
                            <div class="poker-window">
                                <div class="poker-wrapper">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <a class="a-btn" id="complete">记忆完成</a>
            </div>
        </div>           
    </div>
</div>
<input type="hidden" name="_wpnonce" id="inputComplete" value="<?=wp_create_nonce('student_memory_complete_code_nonce');?>">
<input type="hidden" name="match_more" id="inputMatchMore" value="<?=isset($_GET['match_more']) ? $_GET['match_more'] : 1;?>"/>
<input type="hidden" name="_wpnonce" id="inputSubmit" value="<?=wp_create_nonce('student_answer_submit_code_nonce');?>">
<script>
jQuery(function($) { 

    $('body').on('click','#complete',function(){//记忆完成
        var data={
            action:'memory_complete',
            _wpnonce:$('#inputComplete').val(),
            match_id:<?=$_GET['match_id']?>,
            project_id:<?=$_GET['project_id']?>,
            match_more:$('#inputMatchMore').val(),
            match_action:'pokerRelay',
        }
        $.post(window.admin_ajax+"?date="+new Date().getTime(),data,function(res){

            if(res.success){
                if(res.data.url){
                    window.location.href=res.data.url
                }
            }else{
                $.alerts(res.data.info)
            }
        })
    })
    submit=function(time){//提交答案
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
        }
        $.post(window.admin_ajax+"?date="+new Date().getTime(),data,function(res){
            $.DelSession('leavePage')
            if(res.success){
                if(res.data.url){
                    window.location.href=res.data.url
                }   
            }else{
                $.alerts(res.data.info)
            }
        })
    }
    if(window.location.host=='ydbeta.gjnlyd.com'){
        history.pushState(null, null, document.URL);
        window.addEventListener('popstate', function () {
            history.pushState(null, null, document.URL);
        });
        $(window).on("blur",function(){
            var leavePage = $.GetSession('leavePage','1');
            if(leavePage && leavePage['match_id']===$.Request('match_id') && leavePage['project_id']===$.Request('project_id') && leavePage['match_more']===$.Request('match_more')){
                leavePage['leavePage']+=1;
            }else{
                var sessionData={
                    match_id:$.Request('match_id'),
                    project_id:$.Request('project_id'),
                    match_more:$.Request('match_more'),
                    leavePage:1
                }
                leavePage= sessionData
            }
            $.SetSession('leavePage',leavePage)
        })  
        $(window).on("focus", function(e) {
            var leavePage= $.GetSession('leavePage','1');
            if(leavePage && leavePage['match_id']===$.Request('match_id') && leavePage['project_id']===$.Request('project_id') && leavePage['match_more']===$.Request('match_more')){
                var leveTimes=parseInt(leavePage['leavePage'])
                if(leveTimes>0 && leveTimes<3){
                    $.alerts('第'+leveTimes+'次离开考试页面,超过2次自动提交答题')
                }
                if(leveTimes>=3){
                    $.alerts('第'+leveTimes+'次离开考试页面,自动提交本轮答题')
                    var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
                    setTimeout(function() {
                        submit(time);
                    }, 1000);
                    submit(time);
                }
            }
        });
    }
    if($('.count_down').attr('data-seconds')<=0){//进入页面判断时间是否结束
        $.alerts('比赛结束');
        setTimeout(function() {
            submit(0)
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
                submit(S)
            }, 1000);
        }
    });
    //设置扑克窗口宽度
    initWidth=function() {
        var len=$('.poker-wrapper .poker').length;
        var width=$('.poker-wrapper .poker').width()+2;
        var marginRight=parseInt($('.poker-wrapper .poker').css('marginRight'))
        var W=width*len+marginRight*(len-1)+'px';
        $('.poker-wrapper').css('width',W);
    }
    // initWidth()
    var AllData=<?=empty($questions) ? '" "' : $questions;?>;
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
        return {left:left,right:right,showData:pageData}
    }


    initPagation=function(){//初始化分业，按钮是否禁用，宽度得初始化
        var data=pagation(AllData,nowPage,onePageItems)
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
    }
    initPagation()
    $('.matching-btn').each(function(i){
        var hammertime = new Hammer($('.matching-btn')[i]);
        hammertime.on("tap", function (e) {
            nowPage=1;
            $('.matching-btn').removeClass('active');
            $(e.target).addClass('active');
            var text=parseInt($(e.target).text())
            if(text!='NAN'){
                onePageItems=text;
                
            }else{
                onePageItems=false
            }
            initPagation()
        });
    })
    //左翻页
    var hammerleft = new Hammer($('.left')[0]);
    hammerleft.on("tap", function (e) {
        if($(e.target).hasClass('disabled')){
            return false;
        }else{
            nowPage--;
            initPagation()
        }
    });
    //右翻页
    var hammerright = new Hammer($('.right')[0]);
    hammerright.on("tap", function (e) {
        if($(e.target).hasClass('disabled')){
            return false;
        }else{
            nowPage++;
            initPagation()
        }

    });

})
</script>