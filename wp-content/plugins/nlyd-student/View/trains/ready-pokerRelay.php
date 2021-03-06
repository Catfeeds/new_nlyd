
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><div><?=__($match_title, 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row layui-row">
                        <div class="c_black match_info_font"><div><?=sprintf(__('第%s轮', 'nlyd-student'),$match_more)?></div></div>
                        <!-- <div class="c_blue match_info_font"><div><?=__('第1/1题', 'nlyd-student')?></div></div> -->
                        <div class="c_blue match_info_font">
                            <div>
                                <!-- <i class="iconfont">&#xe685;</i> -->
                                <span class="count_down" data-seconds="<?=$count_down?>">00:00:00</span>
                            </div>
                        </div>
                    </div>
                    <div class="matching-row layui-row">
                        <div class="matching-row-label"><?=__('显示张数', 'nlyd-student')?></div>
                        <div class="matching-row-list">
                            <button class="matching-btn active"><?=__('全部', 'nlyd-student')?></button>
                            <button class="matching-btn">1</button>
                            <button class="matching-btn">2</button>
                            <button class="matching-btn">3</button>
                            <button class="matching-btn">4</button>
                            <button class="matching-btn">5</button>
                            <button class="matching-btn">6</button>
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
                <a class="a-btn a-btn-table" style="position: relative;top:0;margin-top:30px;margin-bottom: 20px;" id="complete"  href="<?=$url?>"><div><?=__('记忆完成', 'nlyd-student')?></div></a>
            </div>
        </div>           
    </div>
</div>
<script>
jQuery(function($) { 
    history.pushState(null, null, document.URL);
    window.addEventListener('popstate', function () {
        history.pushState(null, null, document.URL);
    });
    var data_match=[];
    var questions_answer=[]
    var new_poker=["heart-A","heart-2","heart-3","heart-4","heart-5","heart-6","heart-7","heart-8","heart-9","heart-10","heart-J","heart-Q","heart-K","club-A","club-2","club-3","club-4","club-5","club-6","club-7","club-8","club-9","club-10","club-J","club-Q","club-K","diamond-A","diamond-2","diamond-3","diamond-4","diamond-5","diamond-6","diamond-7","diamond-8","diamond-9","diamond-10","diamond-J","diamond-Q","diamond-K","spade-A","spade-2","spade-3","spade-4","spade-5","spade-6","spade-7","spade-8","spade-9","spade-10","spade-J","spade-Q","spade-K"];   
    // var arrColor=['heart','club','diamond','spade']
    // var arrNum=['A','2','3','4','5','6','7','8','9','10','J','Q','K']
    var ready_poker= $.GetSession('train_match','1');
    if(ready_poker && ready_poker['genre_id']==$.Request('genre_id') && ready_poker['type']=='pkjl'){
        questions_answer=ready_poker['train_questions']
    }else{
        rand(new_poker)
        var sessionData={//存储session
            train_questions:questions_answer,
            genre_id:$.Request('genre_id'),
            type:'pkjl',
            end_time:$.GetEndTime($('.count_down').attr('data-seconds'))
        }
        $.SetSession('train_match',sessionData)
    }
    $.each(questions_answer,function(i,v){
        var item=v.split('-')
        data_match.push(item)
    })
    $('#complete').click(function(){
        var sessionData={//存储session
            train_questions:questions_answer,
            genre_id:$.Request('genre_id'),
            type:'pkjl',
            end_time:$.GetEndTime($('.count_down').attr('data-seconds'))
        }
        $.SetSession('train_match',sessionData)
    })
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
    // var file_path = '<?=leo_student_url."/conf/poker_create.json";?>'; 
    function submit(time){//提交答案
        var my_answer=[];
        var match_more=$.Request('match_more') ? $.Request('match_more') : '1';
        var data={
            action:'trains_submit',
            genre_id:$.Request('genre_id'),
            project_type:'pkjl',
            train_questions:questions_answer,
            train_answer:questions_answer,
            my_answer:my_answer,
            surplus_time:time,
            match_more:match_more,
        }

        $.ajax({
            data:data,
            beforeSend:function(XMLHttpRequest){
                $('#load').css({
                    'display':'block',
                    'opacity': '1',
                    'visibility': 'visible',
                })
            },
            success:function(res,ajaxStatu,xhr){  
                if(res.success){
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
                }
            },
            complete: function(XMLHttpRequest, textStatus){
                $.SetSession('train_data',data);
                if(textStatus=='timeout'){
                    var href="<?=home_url('trains/logs/type/'.$_GET['type'].'/match_more/'.$_GET['match_more'])?>";
                    window.location.href=href;
        　　　　}
            }
        })
    }
    $('.count_down').countdown(function(S, d){//倒计时
        var D=d.day>0 ? d.day : '';
        var h=d.hour<10 ? '0'+d.hour : d.hour;
        var m=d.minute<10 ? '0'+d.minute : d.minute;
        var s=d.second<10 ? '0'+d.second : d.second;
        var time=D+h+':'+m+':'+s;
        $(this).attr('data-seconds',S).text(time)
        if(S<=0){//本轮训练结束
            if(S==0){
                $.alerts('<?=__('倒计时结束，即将提交答案', 'nlyd-student')?>')
            }else{
                $.alerts('<?=__('训练结束', 'nlyd-student')?>')
            }
            // setTimeout(function() {
                submit(0)
            // }, 1000);
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
    function matchBtn(_this) {
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
    function left(){
        if($('.left').hasClass('disabled')){
            return false;
        }else{
            nowPage--;
            initPagation()
        }
    }
    function right() {
        if($('.right').hasClass('disabled')){
            return false;
        }else{
            nowPage++;
            initPagation()
        }
    }
    if('ontouchstart' in window){// 移动端
        //左翻页
        new AlloyFinger($('.left')[0], {
            tap:function(){
                left()
            }
        });
        //右翻页
        new AlloyFinger($('.right')[0], {
            tap:function(){
                right()
            }
        });
        $('.matching-btn').each(function(){
            var _this=$(this);
            new AlloyFinger(_this[0], {
                tap:function(){
                    matchBtn(_this)
                }
            })
        })
    }else{
        $('.porker-zoo').css({
            'height':'191px'
        })
        $('body').on('click','.left',function(){
            left()
        })
        $('body').on('click','.right',function(){
            right()
        })
        $('body').on('click','.matching-btn',function(){
            var _this=$(this);
            matchBtn(_this)
        })
    }
})
</script>