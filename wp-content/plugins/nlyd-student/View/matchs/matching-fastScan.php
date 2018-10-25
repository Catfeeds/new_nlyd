
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><div><?=$match_title?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="fastScan-item answer"><?=__('开始答题', 'nlyd-student')?></div>
                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row layui-row">
                        <div class="c_black match_info_font"><div><?=__($project_title, 'nlyd-student')?> <?php printf(__('第%s轮', 'nlyd-student'), $match_more_cn)?></div></div>
                        <div class="c_blue match_info_font"><div>&nbsp;&nbsp;&nbsp;&nbsp;<?=__('第<span id="total">0</span>题', 'nlyd-student')?></div></div>
                        <div class="c_blue match_info_font">
                            <div>
                                <i class="iconfont">&#xe685;</i>
                                <span class="count_down" data-seconds="<?=$count_down?>"></span>
                            </div>
                        </div>
                        <div class="matching-sumbit" id="sumbit"><div><?=__('提交', 'nlyd-student')?></div></div>
                    </div>
                    <div class="matching-fastScan">
                        <div class="item-wrapper">
                            <span class="blue-font fs-14">
                                <span class="count_downs"></span>
                            </span>
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
    var isSubmit=false;//是否正在提交
    leaveMatchPage(function(){//窗口失焦提交
        var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
        submit(time,4);
    })
    var _match_id=<?=$_GET['match_id']?>;
    var _project_id=<?=$project_id?>;
    var _match_more=<?=$match_more;?>;
    var ajaxData=[],
    items=5,//生成5个错误选项，外加一个正确选项，共六个选项
    itemLen=5,//生成每一条选项的长度
    showHZ=7,//每5道题添加一个汉字
    itemAdd=2,//每隔itemAdd道题itemLen++
    nandu=0,//难度系数，越小越难,有几个不同字符（可变项）
    // nanduMax=4,//替换项的上线
    nanduLen=7,//每nanduLen题nandu++
    // stop=false,//停止计时
    answerHide=0.8,//正确答案消失的时间为0.8秒
    flaseQuestion=0,//错误答题，需要存入cookie
    flaseMax=10,//错题数量
    breakRow=20,//字符长度达到breakRow开始换行
    _count_time=<?=$child_count_down?>+1,//初始答题时间,会变化
    fetchPage_time=0;
    getAjaxTime=<?=$child_count_down?>+1;//程序获取时间
    showTime=function(){ 
        fetchPage_time=getAjaxTime
        // if(!stop){
            _count_time--
        // }
            var day = Math.floor((_count_time / 3600) / 24);
            var hour = Math.floor((_count_time / 3600) % 24);
            var minute = Math.floor((_count_time / 60) % 60);
            var second = Math.floor(_count_time % 60);
            day=day>0?day+'<?=__('天', 'nlyd-student')?>':'';
            hour= hour<10?"0"+hour:hour;//计算小时
            minute= minute<10?"0"+minute:minute;//计算分钟
            second= second<10?"0"+second:second;//计算秒
            var text=day+hour+':'+minute+':'+second;
            $('.count_downs').text(text).attr('data-seconds',_count_time)
            if(_count_time<=-1){
                if(ajaxData.length%itemAdd==0){
                    itemLen++
                }
                
                if(ajaxData.length%nanduLen==0){
                    nandu++
                }
                initBuild(itemLen,items,nandu,false)
                showQusetion(ajaxData[ajaxData.length-1],answerHide,getAjaxTime)
                clearTimeout(timer)
                if(flaseQuestion<flaseMax){
                    timer=setTimeout("showTime()",1000+answerHide*1000);
                }
                
            }else{
                if(flaseQuestion<flaseMax){
                    var matchSessions=$.GetSession('match','true');
                    if(matchSessions && matchSessions['match_id']===_match_id && matchSessions['project_id']===_project_id && matchSessions['match_more']===_match_more){
                        matchSessions['fetchPage_time']=_count_time;//记录_count_time
                        $.SetSession('match',matchSessions);
                    }
                    if(_count_time+1==getAjaxTime){
                        timer=setTimeout("showTime()",1000+answerHide*1000);
                    }else{
                        timer=setTimeout("showTime()",1000);
                    }
                }

            }
            

    }  

    var matchSession=$.GetSession('match','true');
    var isMatching=false;//判断用户是否刷新页面
    if(matchSession && matchSession['match_id']===_match_id && matchSession['project_id']===_project_id && matchSession['match_more']===_match_more){
        isMatching=true;
        ajaxData=matchSession['ajaxData'];
        flaseQuestion=matchSession['flaseQuestion'];
        nandu=matchSession['nandu'];
        itemLen=matchSession['itemLen'];
        fetchPage_time=matchSession['fetchPage_time']?matchSession['fetchPage_time']:0;
    }
    if(!isMatching){
        // $('.matching-fastScan').css('paddingTop','40%');
        $('body').one('click','.answer',function(){
            var _this=$(this);
            _this.addClass('start')
            setTimeout(function(){
                _this.removeClass('start')
                // $('.matching-fastScan').css('paddingTop','50px');
                initBuild(itemLen,items,nandu,true)
                showQusetion(ajaxData[ajaxData.length-1],answerHide,getAjaxTime)
            },500)

        })
    }else{
        if(fetchPage_time==0){//倒计时结束，加载下一题
            initBuild(itemLen,items,nandu,false)
        }
        showQusetion(ajaxData[ajaxData.length-1],answerHide,getAjaxTime)
    }
    function getHZ() {//生成随即汉字
        var arr=$.validationLayui.hanzi;
        var pos = Math.round(Math.random() * (arr.length - 1));
        return arr[pos];
    }  
    function randZF() {//生成随即字符
            var arr=["a","b","c","d","e","f","g","h","i","j","k","m","n","p","q","r","s","t","u","v","w","x","y","z","2","3","4","5","6","7","8","9","#","$","%","!","*","&","￥"]
            var pos = Math.round(Math.random() * (arr.length - 1));
            return arr[pos];
    }
    function randZF5_1() {//生成随即字符
            var arr=["a","b","c","d","e","f","g","h","i","j","k","m","n","p","q","r","s","t","u","v","w","x","y","z","2","3","4","5","6","7","8","9","#","$","%","!","*","&","￥"]
            var pos = Math.round(Math.random() * (arr.length - 6));
            var newArr=[]
            for(var i=0;i<5;i++){
                newArr.push(arr[pos+i])
            }
            var newPos = Math.round(Math.random() * (newArr.length - 1));
            return newArr[newPos]
    }       
    function pushHZ(string) {
        var arr=[];
        var len=string.length;
        for (var index = 0; index < len; index++) {
            arr.push(index)
        }
        var pos = Math.round(Math.random() * (arr.length - 1));
        var indexs=arr[pos];
        var newStrs= string.substring(0,indexs) + getHZ() + string.substring(indexs,len)
        return newStrs;
    }
    function buildQuestion(len) {//生成题目
        var x="";
        var _n=Math.floor((ajaxData.length)/showHZ)
        var times=_n+1;
        for(var i=0;i<len-times;i++){
            x+=randZF()
        }
        for(var i=0;i<times;i++){
            x=pushHZ(x)
        }
        return x;
    }
    function compare(old) {//比较字符是否相同
        var newStr=randZF5_1();
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
        // var newArrIndex=[];
        // for(var i in arrIndex){//去除已选中下标
        //     if(arrIndex[i]!=arrtext){
        //         newArrIndex.push(arrIndex[i])
        //     }
        // }
        var oldStr1=data.slice(arrtext,arrtext+1);
        var newStr1=compare(oldStr1);
        var newStrs= str.substring(0,arrtext) + newStr1 + str.substring(arrtext+1,data.length)
        
        if(level>0){
            levels(data,level-1,arrIndex,newStrs,select)
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
            var code=data.charAt(k)
            var reg=/^[\u4e00-\u9fa5]+$/;
            if(!reg.test(code)){//如果不是汉字
                checkIndex.push(k)   
            }
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
                match_id:_match_id,
                project_id:<?=$project_id?>,
                match_more:<?=$match_more?>,
                nandu:nandu,
                flaseQuestion:flaseQuestion,
                itemLen:itemLen,
            }
            $.SetSession('match',sessionData)
        }else{
            $.alerts('<?=__('错误', 'nlyd-student')?>'+flaseMax+'<?=__('题', 'nlyd-student')?>')
            submit($('.count_down').attr('data-seconds'),2)
        }
    }
    function getNewline(val) {
        var str = new String(val); 
        if(str.length>breakRow){
            var bytesCount = 0;  
            var s="";
            for (var i = 0 ,n = str.length; i < n; i++) { 
                var c = str.charCodeAt(i);  
                //统计字符串的字符长度
                bytesCount += 1;  
                s += str.charAt(i);
                if(bytesCount>=breakRow){  
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
            $('#total').text(ajaxData.length)
            $('.answer').html(getNewline(row.rights)).removeClass('hide');
            $('.count_downs').addClass('hide');
            $('#selectWrapper').addClass('hide');
            $('#selectWrapper .fastScan-item').each(function(i){
                var _this=$(this);
                var text=row['question'][i]
                _this.html(getNewline(text))
            })
            if(isMatching){
                $('.answer').addClass('hide').text('');
                $('#selectWrapper').removeClass('hide')
                $('.count_downs').removeClass('hide')
                $('#selectWrapper .fastScan-item').removeClass('error-fastScan').removeClass('noClick').removeClass('right-fastScan')
                isMatching=!isMatching
            }else{
                timers=setTimeout(function() {
                    $('.answer').addClass('hide').text('')
                    $('#selectWrapper').removeClass('hide')
                    $('#selectWrapper .fastScan-item').removeClass('error-fastScan').removeClass('noClick').removeClass('right-fastScan')
                    $('.count_downs').removeClass('hide')
                }, flashTime*1000);
            }
            //计时器

            if(fetchPage_time!=0){
                _count_time=fetchPage_time
            }else{
                _count_time=answerTime
            }
             
            showTime()

    }

$('#selectWrapper .fastScan-item').each(function(){
    // mTouch('#selectWrapper').on('tap','.fastScan-item',function(){
    var _this=$(this)
    new AlloyFinger(_this[0], {
        tap: function () {
            // var _this=$(this);
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
                if(ajaxData.length%nanduLen==0){
                    nandu++
                }
                initBuild(itemLen,items,nandu,isFalse)
                setTimeout(function(){
                    if(flaseQuestion<flaseMax){
                        showQusetion(ajaxData[ajaxData.length-1],answerHide,getAjaxTime)
                    }
                }, 300);
                if(typeof(timer)!="undefined"){
                    clearTimeout(timer);
                }
            }
        }
    })
})
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
                match_id:_match_id,
                project_id:_project_id,
                match_more:_match_more,
                project_alias:'kysm',
                project_more_id:$.Request('project_more_id'),

                my_answer:ajaxData,
                surplus_time:time,
                submit_type:submit_type,//1:选手提交;2:错误达上限提交;3:时间到达提交;4:来回切
            }
            var leavePage= $.GetSession('leavePage','1');
            if(leavePage && leavePage['match_id']===_match_id && leavePage['project_id']===_project_id && leavePage['match_more']===_match_more){
                if(leavePage.Time){
                    data['leave_page_time']=leavePage.Time;
                }
            }
            /*console.log(data);
            return false;*/
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
                complete: function(XMLHttpRequest, textStatus){
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
    if(<?=$count_down?><=0){//进入页面判断时间是否结束
        $.alerts('<?=__('比赛结束', 'nlyd-student')?>');
        if(typeof(timer)!="undefined"){
            clearTimeout(timer);
        }
        $('#selectWrapper .fastScan-item').addClass('noClick');//确保无重复点击
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
                $.alerts('<?=__('倒计时结束，即将提交答案', 'nlyd-student')?>')
            }else{
                $.alerts('<?=__('比赛结束', 'nlyd-student')?>')
            }
            if(typeof(timer)!="undefined"){
                clearTimeout(timer);
            }
            
            $('#selectWrapper .fastScan-item').addClass('noClick');//确保无重复点击
            setTimeout(function() {
                submit(0,3)
            }, 1000);
        }
    });
layui.use('layer', function(){
    // mTouch('body').on('tap','#sumbit',function(e){
    new AlloyFinger($('#sumbit')[0], {
    tap:function(){
        layer.open({
                type: 1
                ,maxWidth:300
                ,title: '<?=__('提示', 'nlyd-student')?>' //不显示标题栏
                ,skin:'nl-box-skin'
                ,id: 'certification' //防止重复弹出
                ,content: '<div class="box-conent-wrapper"><?=__('是否立即提交', 'nlyd-student')?>？</div>'
                ,btn: [ '<?=__('按错了', 'nlyd-student')?>','<?=__('提交', 'nlyd-student')?>',]
                ,success: function(layero, index){
                    // stop=true;
                },
                cancel: function(index, layero){
                    layer.closeAll();
                    // stop=false;
                }
                ,yes: function(index, layero){
                    layer.closeAll();
                    // stop=false;
                }
                ,btn2: function(index, layero){
                    layer.closeAll();
                    var time=$('.count_down').attr('data-seconds')?$('.count_down').attr('data-seconds'):0;
                    submit(time,1);
                    // stop=false;
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