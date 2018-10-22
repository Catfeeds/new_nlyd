
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><div><?=$match_title?></div></h1>
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
                    </div>
                    <div class="matching-row">
                        <div class="matching-row-label"><?=__('显示张数', 'nlyd-student')?></div>
                        <div class="matching-row-list">
                            <div class="matching-btn active"><?=__('全部', 'nlyd-student')?></div>
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
                    <p class="ta_c" style="margin-top:20px"><?=__('当前记忆', 'nlyd-student')?> <span class="c_blue" id="number">0</span> <?=__('张', 'nlyd-student')?></p>
                </div>
                <a class="a-btn a-btn-table" style="position: relative;top:0;margin-top:30px" id="complete"  href="<?=$redirect_url?>"><div><?=__('记忆完成', 'nlyd-student')?></div></a>
            </div>
        </div>           
    </div>
</div>
<input type="hidden" name="_wpnonce" id="inputComplete" value="<?=wp_create_nonce('student_memory_complete_code_nonce');?>">
<input type="hidden" name="match_more" id="inputMatchMore" value="<?=$match_more;?>"/>
<input type="hidden" name="_wpnonce" id="inputSubmit" value="<?=wp_create_nonce('student_answer_submit_code_nonce');?>">
<script>
jQuery(function($) { 
    leaveMatchPage(function(){//窗口失焦提交
        var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
        submit(time,4);
    })
    var _match_id=<?=$_GET['match_id']?>;
    var _project_id=<?=$project_id?>;
    var _match_more=<?=$match_more;?>;
    var data_match=[];
    var questions_answer=[]
    var new_poker=["heart-A","heart-2","heart-3","heart-4","heart-5","heart-6","heart-7","heart-8","heart-9","heart-10","heart-J","heart-Q","heart-K","club-A","club-2","club-3","club-4","club-5","club-6","club-7","club-8","club-9","club-10","club-J","club-Q","club-K","diamond-A","diamond-2","diamond-3","diamond-4","diamond-5","diamond-6","diamond-7","diamond-8","diamond-9","diamond-10","diamond-J","diamond-Q","diamond-K","spade-A","spade-2","spade-3","spade-4","spade-5","spade-6","spade-7","spade-8","spade-9","spade-10","spade-J","spade-Q","spade-K"];
    // var arrColor=['heart','club','diamond','spade']
    // var arrNum=['A','2','3','4','5','6','7','8','9','10','J','Q','K']



    function splits(str) {
        return str.split('-');
    }
    function isNumber(str) {//非数字扑克转成number
        var newStr=parseInt(str);
        var result=newStr;
        if(isNaN(newStr)){//'A','J','Q','K'
            if(str=='A'){
                result=1
            }else if(str=='J'){
                result=11
            }else if(str=='Q'){
                result=12
            }else if(str=='K'){
                result=13
            }
        }
        return result;
    }
    function rand(data) {//生成随即字符
        var pokers=data;
        var length=pokers.length;
        if(length>0){
            var pos1 = Math.round(Math.random() * (length - 1));
            var _poker=pokers[pos1]    
            var question_len=questions_answer.length;//生成题目的长度
            if(length!=1){
                if(question_len>2){//取两个以上的扑克new_poker
                    var _poker0=_poker;//当前扑克
                    var _poker1=questions_answer[question_len-1];//前1张扑克
                    var _poker2=questions_answer[question_len-2];//前2张扑克
                    var _pokerArray0=splits(_poker0);//拆分
                    var _pokerArray1=splits(_poker1);//拆分
                    var _pokerArray2=splits(_poker2);//拆分
                    var color0=_pokerArray0[0];//花色
                    var color1=_pokerArray1[0];//花色
                    var color2=_pokerArray2[0];//花色
                    var number0=isNumber(_pokerArray0[1]);//number
                    var number1=isNumber(_pokerArray1[1]);//number
                    var number2=isNumber(_pokerArray2[1]);//number
                    var numbers=_pokerArray0[1]+_pokerArray1[1]+_pokerArray2[1]
                    if(color0==color1 && color0==color2){//同花色
                        var _flag=false;
                        if(numbers=="QKA" || numbers=="AKQ"){//QKA,AKQ单独判断
                            _flag=true;
                        }else{
                            if((number2-number1==1 && number1-number0==1) || (number2-number1==-1 && number1-number0==-1)){//num是顺子
                                _flag=true;
                            }   
                        }
                        console.log(color0,numbers)
                        if(!_flag){//非顺子
                            questions_answer.push(_poker)
                            pokers.splice(pos1, 1);
                        }else{
                            console.log(numbers)
                        }

                    }else{
                        questions_answer.push(_poker)
                        pokers.splice(pos1, 1);
                    }
                }else{
                    questions_answer.push(_poker)
                    pokers.splice(pos1, 1);
                }
                rand(pokers)
            }else{//最后一张扑克可能导致三张连续的顺子
                questions_answer.push(_poker)
            }
            
        }
    }
    var matching_question=$.GetSession('matching_question','true');
    if(matching_question && matching_question['match_id']===_match_id && matching_question['project_id']===_project_id && matching_question['match_more']===_match_more){
        questions_answer=matching_question['questions_answer']
    }else{
        $.DelSession('matching_question')
        // $.each(arrColor,function(i,v){
        //     $.each(arrNum,function(index,val){
        //         var item=v+'-'+val;
        //         new_poker.push(item)
        //     })
        // })
        rand(new_poker);//生成题目
        var sessionData={
            match_id:_match_id,
            project_id:_project_id,
            match_more:_match_more,
            questions_answer:questions_answer
        }
        $.SetSession('matching_question',sessionData)
    }
    $.each(questions_answer,function(i,v){
        var item=v.split('-')
        data_match.push(item)
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
            match_id:_match_id,
            project_id:_project_id,
            match_more:_match_more,
            project_alias:'pkjl',
            match_questions:questions_answer,
            questions_answer:questions_answer,
            project_more_id:$.Request('project_more_id'),

            my_answer:my_answer,
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
            data:data,
            success:function(res,ajaxStatu,xhr){  
                $.DelSession('leavePage')
                if(res.success){
                    if(res.data.url){
                        window.location.href=res.data.url
                        $.DelSession('matching_question')
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
        $.alerts('<?=__('比赛结束', 'nlyd-student')?>');
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
                $.alerts('<?=__('倒计时结束，即将提交答案', 'nlyd-student')?>')
            }else{
                $.alerts('<?=__('比赛结束', 'nlyd-student')?>')
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
        // })
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