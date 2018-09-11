
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper">
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
                            <span class="count_down" data-seconds="<?=$count_down?>"></span>
                        </span>
                        <div class="matching-sumbit match_info_font" id="sumbit">提交</div>
                    </div>
                    <div class="matching-fastScan">
                        <div class="item-wrapper">
                            <span class="blue-font fs-14">
                                <span class="count_downs"></span>
                            </span>
                            <div class="fastScan-item answer">开始答题</div>
                            <div id="selectWrapper" class="hide">
                                <div class="fastScan-item"></div>
                                <div class="fastScan-item"></div>
                                <div class="fastScan-item"></div>
                                <div class="fastScan-item"></div>
                                <div class="fastScan-item"></div>
                                <div class="fastScan-item"></div>
                            </div>

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
    leaveMatchPage(function(){//窗口失焦提交
        var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
        submit(time,4);
    })
    var ajaxData=[],
    items=5,//生成5个错误选项，外加一个正确选项，共六个选项
    itemLen=5,//生成每一条选项的长度
    itemAdd=2,//每隔itemAdd道题itemLen++
    nandu=0,//难度系数，越小越难
    stop=false,//停止计时
    answerHide=0.8,//正确答案消失的时间为0.8秒
    flaseQuestion=0,//错误答题，需要存入cookie
    flaseMax=10,//错题数量
    _count_time=<?=$child_count_down?>,//初始答题时间,会变化
    getAjaxTime=<?=$child_count_down?>;//程序获取时间
    showTime=function(){  
        if(!stop){
            _count_time--
        }
            var day = Math.floor((_count_time / 3600) / 24);
            var hour = Math.floor((_count_time / 3600) % 24);
            var minute = Math.floor((_count_time / 60) % 60);
            var second = Math.floor(_count_time % 60);
            day=day>0?day+'天':'';
            hour= hour<10?"0"+hour:hour;//计算小时
            minute= minute<10?"0"+minute:minute;//计算分钟
            second= second<10?"0"+second:second;//计算秒
            var text=day+hour+':'+minute+':'+second;
            $('.count_downs').text(text).attr('data-seconds',_count_time)
            if(_count_time<=-1){
                if(ajaxData.length%itemAdd==0){
                    itemLen++
                }
                initBuild(itemLen,items,nandu,false)
                showQusetion(ajaxData[ajaxData.length-1],answerHide,getAjaxTime)
                clearTimeout(timer)
            }
            if(flaseQuestion<flaseMax){
                timer=setTimeout("showTime()",1000);
            }
            

    }  
    var matchSession=$.GetSession('match','true');
    var isMatching=false;//判断用户是否刷新页面
    if(matchSession && matchSession['match_id']===$.Request('match_id') && matchSession['project_id']===$.Request('project_id') && matchSession['match_more']===$.Request('match_more')){
        isMatching=true;
        ajaxData=matchSession['ajaxData'];
        flaseQuestion=matchSession['flaseQuestion'];
        nandu=matchSession['nandu'];
        itemLen=matchSession['itemLen'];
    }
    if(!isMatching){
        $('body').on('click','.answer',function(){
            initBuild(itemLen,items,nandu,true)
            showQusetion(ajaxData[ajaxData.length-1],answerHide,getAjaxTime)
        })
    }else{
        showQusetion(ajaxData[ajaxData.length-1],answerHide,getAjaxTime)
    }
        
    
    function getHZ() {//生成随即汉字
        return String.fromCodePoint(Math.round(Math.random() * 20901) + 19968);
    }
    function randSZ() {//生成随即数字0-9
        return ( Math.floor ( Math.random ( ) * 9  ) );
    }
    function randZM(flag) {//生成随即小写字母
        if(flag==="lower"){ 
             character = String.fromCharCode(Math.floor(Math.random()*26)+"a".charCodeAt(0)); 
        } 
        if(flag==="upper"){ 
             character = String.fromCharCode(Math.floor(Math.random()*26)+"A".charCodeAt(0)); 
        } 
        return character; 
       
    }    
    function randZF() {//生成随即字符
            var arr=['~','!','@','#','$','￥','^','&','(',')','?','*','×','÷',';','"',':',];

            var pos = Math.round(Math.random() * (arr.length - 1));

            return arr[pos];
    }
    function randHS() {//随机执行一个函数
        var arr=['getHZ','randSZ','randZM1','randZF','randZM2'];

        var pos = Math.round(Math.random() * (arr.length - 1));
        if(arr[pos]=='getHZ'){
            return getHZ()
        }else if(arr[pos]=='randSZ'){
            return randSZ()
        }else if(arr[pos]=='randZM1'){
            return randZM('lower')
        }else if(arr[pos]=='randZF'){
            return randZF()
        }else if(arr[pos]=='randZM2'){
            return randZM('upper')
        }
    }

    function buildQuestion(len) {//生成题目
        var x="";
        for(var i=0;i<len;i++){
            x+=randHS()
        }
        return x;
    }
    function compare(old) {//比较字符是否相同
        var newStr=randHS();
        var oldStr=old
        if(oldStr==newStr){
            return compare(oldStr)
        }else{
            return newStr
        }
    }
    function levels(data,level,arrIndex,str,select) {
        var randIndex1 = Math.round(Math.random() * (arrIndex.length - 1));//随机取一个下标
        var arrtext=arrIndex[randIndex1]
        var newArrIndex=[];
        for(var i in arrIndex){
            if(arrIndex[i]!=arrtext){
                newArrIndex.push(arrIndex[i])
            }
        }
        var oldStr1=data.slice(arrtext,arrtext+1);
        var newStr1=compare(oldStr1);
        var newStrs= str.substring(0,arrtext) + newStr1 + str.substring(arrtext+1,data.length)
        
        if(level>0){
            levels(data,level-1,newArrIndex,newStrs,select)
        }else{
            select.push(newStrs)
        }
    }
    /*
    total生成选项个数
    level难度系数(越小越难，即不同字符个数)
    data
    */ 
    function buildSelect(data,total,level){//生成选项
        var len=data.length;
        var select=[];
        var checkIndex=[];//存储已选中下标，进行排除
        for(var k=0; k<len ; k++){
            checkIndex.push(k)
        }
        for(var i=0;i<total;i++){
            levels(data,level,checkIndex,data,select)
        }
        return select;
    }
    
    /*
    total生成选项个数
    level难度系数(越小越难，即不同字符个数)
    itemLen生成字符串长度
    isFalseAdd上一题是否正确
    */ 
    function initBuild(itemLen,total,level,isFalseAdd) {//处理数据
        if(!isFalseAdd){
            flaseQuestion++
        }
        if(flaseQuestion<flaseMax){
            var right=buildQuestion(itemLen)
            var select=buildSelect(right,total,level)
            var leng=select.length;
            var arr=[]
            for(var i=0;i<leng;i++){
                arr.push(i)
            }
            var randIndex = Math.round(Math.random() * (arr.length - 1));//随机取一个下标
            select.splice(arr[randIndex], 0,right); //随机插入
            var thisRow={rights:right,question:select,yours:'',isRight:false};
            ajaxData.push(thisRow)
            var sessionData={
                ajaxData:ajaxData,
                match_id:$.Request('match_id'),
                project_id:$.Request('project_id'),
                match_more:$.Request('match_more'),
                nandu:nandu,
                flaseQuestion:flaseQuestion,
                itemLen:itemLen,
            }
            $.SetSession('match',sessionData)
        }else{
            $.alerts('错误'+flaseMax+'题')
            submit($('.count_down').attr('data-seconds'),2)
        }
    }
    function getNewline(val) {
        var str = new String(val); 
        if(str.length>18){
            var bytesCount = 0;  
            var s="";
            for (var i = 0 ,n = str.length; i < n; i++) { 
                var c = str.charCodeAt(i);  
                //统计字符串的字符长度
                bytesCount += 1;  
                s += str.charAt(i);
                if(bytesCount>=18){  
                    s = s + '</br>';
                    //重置
                    bytesCount=0;
                } 
            }  
            return s;  
        }else{
            return str
        }

    } 

    function showQusetion(row,flashTime,answerTime){//处理页面
            $('.answer').html(getNewline(row.rights)).removeClass('hide');
            $('.count_downs').addClass('hide');
            $('#selectWrapper').addClass('hide');
            $('#selectWrapper .fastScan-item').removeClass('error-fastScan').removeClass('noClick').removeClass('right-fastScan')
            $('#selectWrapper .fastScan-item').each(function(i){
                var _this=$(this);
                var text=row['question'][i]
                _this.html(getNewline(text))
            })
            if(isMatching){
                $('.answer').addClass('hide');
                $('#selectWrapper').removeClass('hide')
                $('.count_downs').removeClass('hide')
                isMatching=!isMatching
            }else{
                setTimeout(function() {
                    $('.answer').addClass('hide');
                    $('#selectWrapper').removeClass('hide')
                    $('.count_downs').removeClass('hide')
                }, flashTime*1000);
            }

            //计时器
            _count_time=answerTime
            showTime()

    }


    mTouch('#selectWrapper').on('tap','.fastScan-item',function(){
        var _this=$(this);
        var isFalse=true;
        
            if(!_this.hasClass('noClick')){
                var text=_this.text()
                ajaxData[ajaxData.length-1].yours=text;//存储我的答案;
                if(text==ajaxData[ajaxData.length-1].rights){//选择正确
                    ajaxData[ajaxData.length-1].isRight=true;
                    _this.addClass('right-fastScan')
                }else{
                    isFalse=false;
                    ajaxData[ajaxData.length-1].isRight=false;
                    _this.addClass('error-fastScan')
                }
                $('#selectWrapper .fastScan-item').addClass('noClick');//确保无重复点击
                if(ajaxData.length%itemAdd==0){
                    itemLen++
                }
                initBuild(itemLen,items,nandu,isFalse)
                setTimeout(function(){
                    if(flaseQuestion<flaseMax){
                        showQusetion(ajaxData[ajaxData.length-1],answerHide,getAjaxTime)
                    }
                }, 300);
                clearTimeout(timer);
            }
        
    })
    function submit(time,submit_type){//提交答案
        var data={
            action:'answer_submit',
            _wpnonce:$('#inputSubmit').val(),
            match_id:<?=$_GET['match_id']?>,
            project_id:<?=$_GET['project_id']?>,
            match_more:<?=!empty($_GET['match_more']) ? $_GET['match_more'] : 1?>,
            my_answer:ajaxData,
            match_action:'subjectfastScan',
            surplus_time:time,
            submit_type:submit_type,//1:选手提交;2:错误达上限提交;3:时间到达提交;4:来回切
        }
        $.ajax({
            data:data,success(res,ajaxStatu,xhr){  
                $.DelSession('match')
                $.DelSession('leavePage')
                if(res.success){
                    if(res.data.url){
                        window.location.href=res.data.url
                    }
                }else{
                    $.alerts(res.data.info)
                }
            }
        })
    }
    if($('.count_down').attr('data-seconds')<=0){//进入页面判断时间是否结束
        $.alerts('比赛结束');
        setTimeout(function(){
            submit(0,3)
        }, 1000);
    }
    $('.count_down').countdown(function(S, d){//倒计时
        var D=d.day<10 ? '0'+d.day : d.day;
        var h=d.hour<10 ? '0'+d.hour : d.hour;
        var m=d.minute<10 ? '0'+d.minute : d.minute;
        var s=d.second<10 ? '0'+d.second : d.second;
        var time=h+':'+m+':'+s;
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
layui.use('layer', function(){
    mTouch('body').on('tap','#sumbit',function(e){
        var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
        layer.open({
                type: 1
                ,maxWidth:300
                ,title: '提示' //不显示标题栏
                ,skin:'nl-box-skin'
                ,id: 'certification' //防止重复弹出
                ,content: '<div class="box-conent-wrapper">是否立即提交？</div>'
                ,btn: ['提交', '按错了', ]
                ,success: function(layero, index){
                    stop=true;
                },
                cancel: function(index, layero){
                    layer.closeAll();
                    stop=false;
                }
                ,yes: function(index, layero){
                    layer.closeAll();
                    submit(time,1);
                    stop=false;
                }
                ,btn2: function(index, layero){
                    stop=false;
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