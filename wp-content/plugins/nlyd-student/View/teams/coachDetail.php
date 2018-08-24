<!-- 比赛详情 -->
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
        <a class="mui-pull-left nl-goback">
        <i class="iconfont">&#xe610;</i>
        </a>
        <h1 class="mui-title">教练详情</h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="width-margin-pc layui-row">
                    <div class="layui-col-lg6 layui-col-md6 layui-col-sm12 layui-col-xs12 have-abtn">
                        <div class="coachDetail-row width-padding width-margin-pc">
                            <div class="width-padding-pc">
                                <div class="coachDetail-headImg img-box">
                                    <img src="<?=$user_infos['user_head']?>">
                                </div>
                                <div class="coachDetail-coachInfo">
                                    <div class="coachDetail-infoRow">
                                        <span class="fs_16 c_black"><?=$user_infos['real_name']?></span>
                                        <?php if(!empty($user_infos['user_gender'])):?>
                                        <span> <?=$user_infos['user_gender']?> </span>
                                        <?php endif;?>
                                        <span>ID <?=$user_infos['user_ID']?></span>
                                    </div>
                                    <div class="coachDetail-infoRow">
                                        <span>国际脑力运动委员会（IISC） <?=$user_infos['user_coach_level']?></span>
                                    </div>
                                    <?php if(!empty($skill)):?>
                                    <div class="coachDetail-infoRow coach-detail-footer">
                                        <?php foreach ($skill as $v){ ?>
                                        <div class="coach-type text_1 is_current c_blue"><div class="nl-badge bg_gradient_blue"><i class="iconfont">&#xe608;</i></div> <?=$v['post_title']?></div>
                                        <!-- <div class="coach-type fs_12 text_1"><?=$v['post_title']?></div> -->
                                        <?php } ?>
                                    </div>
                                    <?php endif;?>
                                    <?php if(!empty($skill)):?>
                                    <div class="coachDetail-infoRow coach-detail-footer">
                                        <?php foreach ($skill as $v){ ?>
                                        <div class="coach-type text_1 is_current c_orange"><div class="nl-badge bg_gradient_orange"><i class="iconfont">&#xe608;</i></div> <?=$v['post_title']?></div>
                                        <!-- <div class="coach-type fs_12 text_1"><?=$v['post_title']?></div> -->
                                        <?php } ?>
                                    </div>
                                    <?php endif;?>
                                    <?php if(!empty($skill)):?>
                                    <div class="coachDetail-infoRow coach-detail-footer">
                                        <?php foreach ($skill as $v){ ?>
                                        <div class="coach-type text_1 is_current"><?=$v['post_title']?></div>
                                        <!-- <div class="coach-type fs_12 text_1"><?=$v['post_title']?></div> -->
                                        <?php } ?>
                                    </div>
                                    <?php endif;?>
                                </div>
                            </div>
                        </div>
                        <div class="coachDetail-row have-metal width-padding width-margin-pc">
                            <div class="width-padding-pc">
                                <div class="coachDetail-metal">简 介</div>
                                <p>国际一级脑力健将（记忆类）</p>
                                <p>成都电视台《超越梦想·脑力世界杯》栏目选手导师 </p>
                                <p>2017脑力世界杯全球总决赛记忆类总亚军 </p>
                                <p>2017脑力世界杯中国赛、全球总决赛优秀教练 </p>
                                <p>国际脑力运动推广大使</p>
                            </div>
                        </div>
<!--                        <div class="coachDetail-row have-metal width-padding width-margin-pc">-->
<!--                            <div class="width-padding-pc">-->
<!--                                <div class="coachDetail-metal">课 程</div>-->
<!--                                <div class="course-zoo">-->
<!--                                    <div class="course-window">-->
<!--                                        <div class="course-wrapper">-->
<!--                                            <a class="course">-->
<!--                                                <div class="course-body">-->
<!--                                                    <div class="course-name c_blue">高效记忆术·G第1期</div>-->
<!--                                                    <div class="c_black6">四川.成都.武侯区</div>-->
<!--                                                </div>-->
<!--                                                <div class="course-footer">抢占名额中</div>-->
<!--                                            </a>-->
<!--                                            <a class="course">-->
<!--                                                <div class="course-body">-->
<!--                                                    <div class="course-name c_blue">高效记忆术·G第1期</div>-->
<!--                                                    <div class="c_black6">四川.成都.武侯区</div>-->
<!--                                                </div>-->
<!--                                                <div class="course-footer disabled">名额已满</div>-->
<!--                                            </a>-->
<!--                                            <a class="course">-->
<!--                                                <div class="course-body">-->
<!--                                                    <div class="course-name c_blue">高效记忆术·G第1期</div>-->
<!--                                                    <div class="c_black6">四川.成都.武侯区</div>-->
<!--                                                </div>-->
<!--                                                <div class="course-footer disabled">名额已满</div>-->
<!--                                            </a>-->
<!--                                            <a class="course">-->
<!--                                                <div class="course-body">-->
<!--                                                    <div class="course-name c_blue">高效记忆术·G第1期</div>-->
<!--                                                    <div class="c_black6">四川.成都.武侯区</div>-->
<!--                                                </div>-->
<!--                                                <div class="course-footer">抢占名额中</div>-->
<!--                                            </a>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
                        <?php if(empty($my_coach_id)): ?>
                        <div class="a-btn">请TA当教练</div>
                        <?php endif;?>
                    </div>
                    <div class="layui-col-lg6 layui-col-md6 layui-col-sm12 layui-col-xs12">
                        <div class="coachDetail-row have-metal width-padding width-margin-pc">
                            <div class="width-padding-pc">
                                <div class="coachDetail-metal">学 员</div>
                                <div class="coachDetail-top">&nbsp;<span class="c_blue"><?=$content['student_count']?>名学员【<?=$content['major_count']?>位主训】</span></div>
                                <p>*M、R、A分别代表记忆、速读、心算</p>
                                <table class="nl-table" id="flow-table">
                                    <tr>
                                        <td>头像</td>
                                        <td>学员姓名</td>
                                        <td>学员ID</td>
                                        <td>M级别</td>
                                        <td>R级别</td>
                                        <td>A级别</td>
                                        <td>脑力健将级别</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>           
    </div>
</div>
<script>
jQuery(function($) { 
    //设置窗口宽度
    initWidth=function() {
        var len=$('.course-wrapper .course').length;
        var width=$('.course-wrapper .course').width()+2;
        var marginRight=parseInt($('.course-wrapper .course').css('marginRight'))
        var W=width*len+marginRight*(len-1)+'px';
        $('.course-wrapper').css('width',W);
    }
    initWidth()
    layui.use(['flow','layer'], function(){
        var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
        
        var flow = layui.flow;//流加载
        flow.load({
            elem: '#flow-table' //流加载容器
            ,scrollElem: '#flow-table' //滚动条所在元素，一般不用填，此处只是演示需要。
            ,isAuto: false
            ,isLazyimg: true
            ,done: function(page, next){ //加载下一页
            //模拟插入
            var cocah_id=$.Request('coach_id');
                var postData={
                    action:'get_cocah_member',
                    coach_id:parseInt(cocah_id),
                    page:page
                }
                var lis = [];
                $.post(window.admin_ajax+"?date="+new Date().getTime(),postData,function(res){
                    // console.log(res)
                        if(res.success){ 
                            $.each(res.data.list,function(index,value){
                                var dom='<tr>'
                                            +'<td>'
                                                +'<div class="player-img img-box">'
                                                    +'<img src="'+value.user_head+'">'
                                                +'</div>'
                                            +'</td>'
                                            +'<td>'+value.nickname+'</td>'
                                            +'<td>'+value.user_ID+'</td>'
                                            +'<td >'+value.memory+'</td>'
                                            +'<td>'+value.read+'</td>'
                                            +'<td>'+value.compute+'</td>'
                                            +'<td>'+value.mental+'</td>'
                                        +'</tr>'
                                lis.push(dom)                           
                            })  
                            if (res.data.list.length<10) {
                                next(lis.join(''),false)
                            }else{
                                next(lis.join(''),true)
                            }
                        }else{
                            // if(page==1){
                            //     var dom='<tr><td colspan="7">无学员信息</td></tr>'
                            //     lis.push(dom) 
                            // }else{
                            //     $.alerts('没有更多了')
                            // }
                            next(lis.join(''),false)
                        }
                })   
            }
        });
        $('.a-btn').click(function(){
            layer.open({
                type: 1
                ,maxWidth:300
                ,title: '提示' //不显示标题栏
                ,skin:'nl-box-skin'
                ,id: 'certification' //防止重复弹出
                ,content: '<div class="box-conent-wrapper">是否请TA当教练？</div>'
                ,btn: ['再想想', '确认', ]
                ,success: function(layero, index){
                    
                }
                ,yes: function(index, layero){
                    layer.closeAll();
                }
                ,btn2: function(index, layero){
                    //按钮【按钮二】的回调
                    $.alerts('确认')
                }
                ,closeBtn:2
                ,btnAagn: 'c' //按钮居中
                ,shade: 0.3 //遮罩
                ,isOutAnim:true//关闭动画
            });
        })
    })
})
</script>