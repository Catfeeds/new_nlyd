
<div class="layui-fluid noCopy">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title"><div><?=__('记忆考级水平(自测)', 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">

                <div class="remember width-margin width-margin-pc">
                    <div class="matching-row layui-row have-submit">
                        <div class="c_black match_info_font"><div><?=__('人脉信息', 'nlyd-student')?></div></div>
                        <div class="c_blue match_info_font">
                            <div>
                                <span class="count_down" data-seconds="<?=$count_down?>"><?=__('初始中', 'nlyd-student')?>...</span>
                            </div>
                        </div>
                        <div class="matching-sumbit" id="sumbit" style="display:none"><div><?=__('提交', 'nlyd-student')?></div></div>
                    </div>
                    <div class="complete_zoo">
                        <div class="matching-number-zoo layui-row ready_zoo">
                        </div>
                        <div class="a-btn a-btn-table" style="position: relative;top:0;margin-top:30px;margin-bottom: 20px;" id="complete" href="match_zoo"><div><?=__('记忆完成', 'nlyd-student')?></div></div>
                    </div>

                    <!-- 考级 -->
                    <div class="complete_zoo" id="match_zoo" style="display:none">
                        <div class="matching-number-zoo layui-row match_zoo">

                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
<script>
jQuery(function($) { 
    $.DelSession('count');
    history.pushState(null, null, document.URL);
    window.addEventListener('popstate', function () {
        history.pushState(null, null, document.URL);
    });
    var _history_id=<?=$history_id?>;
    var _memory_lv=<?=isset($_GET['memory_lv']) ? $_GET['memory_lv'] : 1 ;?>;
    var isSubmit=false;//是否正在提交
    var _show=1;//1,准备区展示，2答题区展示
    var questions_answer=[];//题目
    var _genre_id=$.Request('genre_id');
    var _grad_type=$.Request('grad_type');
    var _type=$.Request('type');
    var ready_time="<?=$memory_type['memory_time']?>";//记忆时间
    var sys_second=ready_time;
    var answer_time="<?=$memory_type['answer_time']?>";//记忆时间
    var endTime=$.GetEndTime(ready_time);//结束时间
    var remember_time=ready_time;
    var second_name=["伟","刚","勇","毅","俊","峰","强","军","平","保","东","文","辉","力","明","永","健","世","广","志","义","兴","良","海","山","仁","波","宁","贵","福","生","龙","元","全","国","胜","学","祥","才","发","武","新","利","清","飞","彬","富","顺","信","子","杰","涛","昌","成","康","星","光","天","达","安","岩","中","茂","进","林","有","坚","和","彪","博","诚","先","敬","震","振","壮","会","思","群","豪","心","邦","承","乐","绍","功","松","善","厚","庆","磊","民","友","裕","河","哲","江","超","浩","亮","政","谦","亨","奇","固","之","轮","翰","朗","伯","宏","言","若","鸣","朋","斌","梁","栋","维","启","克","伦","翔","旭","鹏","泽","晨","辰","士","以","建","家","致","树","炎","德","行","时","泰","盛","雄","琛","钧","冠","策","腾","楠","榕","风","航","弘","秀","娟","英","华","慧","巧","美","娜","静","淑","惠","珠","翠","雅","芝","玉","萍","红","娥","玲","芬","芳","燕","彩","春","菊","兰","凤","洁","梅","琳","素","云","莲","真","环","雪","荣","爱","妹","霞","香","月","莺","媛","艳","瑞","凡","佳","嘉","琼","勤","珍","贞","莉","桂","娣","叶","璧","璐","娅","琦","晶","妍","茜","秋","珊","莎","锦","黛","青","倩","婷","姣","婉","娴","瑾","颖","露","瑶","怡","婵","雁","蓓","纨","仪","荷","丹","蓉","眉","君","琴","蕊","薇","菁","梦","岚","苑","婕","馨","瑗","琰","韵","融","园","艺","咏","卿","聪","澜","纯","毓","悦","昭","冰","爽","琬","茗","羽","希","欣","飘","育","滢","馥","筠","柔","竹","霭","凝","晓","欢","霄","枫","芸","菲","寒","伊","亚","宜","可","姬","舒","影","荔","枝","丽","阳","妮","宝","贝","初","程","梵","罡","恒","鸿","桦","骅","剑","娇","纪","宽","苛","灵","玛","媚","琪","晴","容","睿","烁","堂","唯","威","韦","雯","苇","萱","阅","彦","宇","雨","洋","忠","宗","曼","紫","逸","贤","蝶","菡","绿","蓝","儿","翠","烟","小","轩"];
    var first_name=["赵","钱","孙","李","周","吴","郑","王","冯","陈","褚","卫","蒋","沈","韩","杨","朱","秦","尤","许","何","吕","施","张","孔","曹","严","华","金","魏","陶","姜","戚","谢","邹","喻","柏","水","窦","章","云","苏","潘","葛","奚","范","彭","郎","鲁","韦","昌","马","苗","凤","花","方","任","袁","柳","鲍","史","唐","费","薛","雷","贺","倪","汤","滕","殷","罗","毕","郝","安","常","傅","卞","齐","元","顾","孟","平","黄","穆","萧","尹","姚","邵","湛","汪","祁","毛","狄","米","伏","成","戴","谈","宋","茅","庞","熊","纪","舒","屈","项","祝","董","梁","杜","阮","蓝","闵","季","贾","路","娄","江","童","颜","郭","梅","盛","林","钟","徐","邱","骆","高","夏","蔡","田","樊","胡","凌","霍","虞","万","支","柯","管","卢","莫","柯","房","裘","缪","解","应","宗","丁","宣","邓","单","杭","洪","包","诸","左","石","崔","吉","龚","程","嵇","邢","裴","陆","荣","翁","荀","于","惠","甄","曲","封","储","仲","伊","宁","仇","甘","武","符","刘","景","詹","龙","叶","幸","司","黎","溥","印","怀","蒲","邰","从","索","赖"," 卓","屠","池","乔","胥","闻","莘","党","翟","谭","贡","劳","逄","姬","申","扶","堵","冉","宰","雍","桑","寿","通","燕","浦","尚","农","温","别","庄","晏","柴","瞿","阎","连","习","容","向","古","易","廖","庾","终","步","都","耿","满","弘","匡","国","文"," 寇","广","禄","阙","东","欧","利","师","巩","聂","关","荆","司马","上官","欧阳","夏侯","诸葛","闻人","东方","赫连","皇甫","尉迟","公羊","澹台","公冶","宗政","濮阳","淳于","单于","太叔","申屠","公孙","仲孙","轩辕","令狐","徐离","宇文","长孙","慕容","司徒","司空"];
    var phone_num=["130","131","132","133","134","135","136","137","138","147","150","152","153","155","156","157","158","159","170","178","180","181","182","183","188","185","186","187","189"];
    var pic=[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,206,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,248,249,250,251,252,253,254,255,256,257,258,259,260,261,262,263,264,265,266,267,268,269,270,271,272,273,274,275,276,277,278,279,280,281,282,283,284,285,286,287,288,289,290,291,292,293,294,295,296,297,298,299,300,301,302,303,304,305,306,307,308,309,310,311,312,313,314,315,316,317,318,319,320,321,322,323,324,325,326,327,328,329,330,331,332,333,334,335,336,337,338,339,340,341,342,343,344,345,346,347,348,349,350,351,352,353,354,355,356,357,358,359,360,361,362,363,364,365,366,367,368,369,370,371,372,373,374,375,376,377,378,379,380,381,382,383,384,385,386,387,388,389,390,391,392,393,394,395,396,397,398,399,400,401,402,403,404,405,406,407,408,409,410,411,412,413,414,415,416,417,418,419,420,421,422,423,424,425,426,427,428,429,430,431,432,433,434,435,436,437,438,439,440,441,442,443,444,445,446,447,448,449,450,451,452,453,454,455,456,457,458,459,460,461,462,463,464,465,466,467,468,469,470,471,472,473,474,475,476];
    var questions_answer=[]
    var que_len="<?=$memory_type['length']?>";

    init_question(_show)
    console.log(questions_answer)
    $('#complete').click(function(){//记忆完成
        var _this=$(this);
        var href=_this.attr('href');
        $('.complete_zoo').hide();
        $('#'+href).show()
        $('.matching-sumbit').show();
        _show=2
        sys_second=answer_time
        var endTime=$.GetEndTime(answer_time);//结束时间
        var sessionData={
            genre_id:_genre_id,
            grad_type:_grad_type,
            type:_type,
            endTime:endTime,
            remember_time:$('.count_down').attr('data-seconds'),
            _show:2,
            questions_answer:questions_answer
        }
        $.SetSession('grade_question',sessionData)
    })
    count_down()
    function count_down(){
        // sys_second=answer_time
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
                if(_show==1){//记忆页面
                    $('.complete_zoo').hide();
                    $('#match_zoo').show()
                    $('.matching-sumbit').show();
                    _show=2
                    sys_second=answer_time
                    var endTime=$.GetEndTime(answer_time);//结束时间
                    var sessionData={
                        genre_id:_genre_id,
                        grad_type:_grad_type,
                        type:_type,
                        endTime:endTime,
                        remember_time:0,
                        _show:2,
                        questions_answer:questions_answer
                    }
                    $.SetSession('grade_question',sessionData)
                }else if(_show==2){//答题页面
                    clearInterval(timer)
                    submit()
                }
            }

        }, 1000);
    } 
    function init_question(_show) {//初始化题目
        var grade_question=$.GetSession('grade_question','true');
        if(grade_question && grade_question['genre_id']===_genre_id && grade_question['grad_type']===_grad_type && grade_question['type']===_type){
            questions_answer=grade_question['questions_answer'];
            _show=grade_question['_show']
            endTime=grade_question['endTime'];
            sys_second=$.GetSecond(endTime);
            if(_show==2){
                remember_time=grade_question['remember_time'];
            }
            $.each(questions_answer,function(i,v){
                var dom='<div class="matching-card">'
                            +'<div class="img-box card_img">'
                                +'<img class="_img" src="'+window.home_url+'/wp-content/plugins/nlyd-match/upload/people/'+v.picture+'.jpg">'
                            +'</div>'
                            +'<div class="card_detail">'
                                +'<div class="card_name c_black">'+v.name+'</div>'
                                +'<div class="card_phone c_black">'+v.phone+'</div>'
                            +'</div>'
                        +'</div>'
                var dom1='<div class="matching-card" data-img="'+v.picture+'" >'
                            +'<div class="img-box card_img">'
                                +'<img class="_img" src="'+window.home_url+'/wp-content/plugins/nlyd-match/upload/people/'+v.picture+'.jpg">'
                            +'</div>'
                            +'<div class="card_detail layui-bg-white">'
                                +'<div class="card_name c_black pd_"><input class="matching-number-input" type="text"></div>'
                                +'<div class="card_phone c_black pd_"><input class="matching-number-input" type="tel"></div>'
                            +'</div>'
                        +'</div>'
                
                $('.ready_zoo').append(dom)
                $('.match_zoo').append(dom1)
            })
            var width=$('._img').width()
            $('._img').height(width)
        }else{
            for (var index = 0; index < que_len; index++) {
                var first_len=first_name.length;//姓
                var first_pos = Math.round(Math.random() * (first_len - 1));
                var second_len=second_name.length;//名
                var second_pos = Math.round(Math.random() * (second_len - 1));
                var phone_len=phone_num.length;//电话
                var phone_pos = Math.round(Math.random() * (phone_len - 1));
                var pic_len=pic.length;//图片
                var pic_pos = Math.round(Math.random() * (pic_len - 1));
                
                var name=first_name[first_pos]+second_name[second_pos];//名字生成
                if(Math.random()>0.5){
                    var new_pos=Math.round(Math.random() * (second_len - 1));
                    name+=second_name[new_pos]
                }
                var phone=phone_num[phone_pos]
                for (var i = 0; i < 8; i++) {//电话号码生成
                    phone+=Math.floor(Math.random()*10)
                }
                var picture=pic[pic_pos];//图片生成
                var dom='<div class="matching-card">'
                        +'<div class="img-box card_img">'
                            +'<img class="_img" src="'+window.home_url+'/wp-content/plugins/nlyd-match/upload/people/'+picture+'.jpg">'
                        +'</div>'
                        +'<div class="card_detail">'
                            +'<div class="card_name c_black">'+name+'</div>'
                            +'<div class="card_phone c_black">'+phone+'</div>'
                        +'</div>'
                    +'</div>'
                var dom1='<div class="matching-card" data-img="'+picture+'" >'
                    +'<div class="img-box card_img">'
                        +'<img class="_img" src="'+window.home_url+'/wp-content/plugins/nlyd-match/upload/people/'+picture+'.jpg">'
                    +'</div>'
                    +'<div class="card_detail layui-bg-white">'
                        +'<div class="card_name c_black pd_"><input class="matching-number-input" type="text"></div>'
                        +'<div class="card_phone c_black pd_"><input class="matching-number-input" type="tel"></div>'
                    +'</div>'
                +'</div>'
                
                $('.ready_zoo').append(dom)
                $('.match_zoo').append(dom1)     
                var item={name:name,phone:phone,picture:picture}
                questions_answer.push(item)
                first_name.splice(first_pos,1)
                pic.splice(pic_pos,1)
            }

            var sessionData={
                genre_id:_genre_id,
                grad_type:_grad_type,
                type:_type,
                remember_time:ready_time,//剩余记忆时间
                _show:_show,
                endTime:endTime,
                questions_answer:questions_answer
            }
            $.SetSession('grade_question',sessionData)
            var width=$('._img').width()
            $('._img').height(width)
        }
        $('.complete_zoo').hide();
        $('.complete_zoo').eq(_show-1).show();
        if(_show==2){
            $('.matching-sumbit').show();
        }
    }
    function submit(){//提交答案
        // $('#load').css({
        //         'display':'block',
        //         'opacity': '1',
        //         'visibility': 'visible',
        //     })
        var my_answer=[];
        $('.match_zoo .matching-card').each(function(){
            var _this=$(this);
            var picture=_this.attr('data-img');
            var name=_this.find('.card_name input').val().replace(/\s+/g, "");
            var phone=_this.find('.card_phone input').val().replace(/\s+/g, "");
            var item={name:name,picture:picture,phone:phone};
            my_answer.push(item)
        })
        var data={
            history_id:_history_id,
            memory_lv:_memory_lv,
            genre_id:_genre_id,
            grading_type:_grad_type,
            questions_type:_type,
            grading_questions:questions_answer,
            questions_answer:questions_answer,
            action:'grade_answer_submit',
            my_answer:my_answer,

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
                    //return false;
                    if(res.data.url){
                        setTimeout(function(){
                            window.location.href=res.data.url
                        },300)
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
            complete: function(jqXHR, textStatus){
                    if(textStatus=='timeout'){
                        $.SetSession('match_data',data);
                        var href="<?=home_url('grade/answerLog/genre_id/'.$_GET['genre_id'].'/history_id/'.$_GET['history_id'].'/grad_type/'.$_GET['grad_type'].'/type/'.$_GET['type'].'/memory_lv/'.$_GET['memory_lv'])?>";
                        window.location.href=href;
            　　　　}
                }
        })
    } 
    layui.use('layer', function(){
        function layOpen() {//提交
            layer.open({
                type: 1
                ,maxWidth:300
                ,title: '<?=__('提示', 'nlyd-student')?>' //不显示标题栏
                ,skin:'nl-box-skin'
                ,id: 'certification' //防止重复弹出
                ,content: '<div class="box-conent-wrapper"><?=__('是否立即提交', 'nlyd-student')?>？</div>'
                ,btn: ['<?=__('按错了', 'nlyd-student')?>','<?=__('提交', 'nlyd-student')?>']
                ,success: function(layero, index){
                }
                ,yes: function(index, layero){
                    layer.closeAll();
                }
                ,btn2: function(index, layero){
                    layer.closeAll();
                    submit();
                }
                ,closeBtn:2
                ,btnAagn: 'c' //按钮居中
                ,shade: 0.3 //遮罩
                ,isOutAnim:true//关闭动画
            });
        }
        if('ontouchstart' in window){// 移动端
            new AlloyFinger($('#sumbit')[0], {//提交
                tap:function(){
                    layOpen()
                }
            });
        }else{
            $('body').on('click','#sumbit',function(){//提交
                layOpen()
            })
        }
    })
})
</script>