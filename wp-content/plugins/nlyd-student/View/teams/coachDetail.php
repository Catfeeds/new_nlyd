<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
        <a class="mui-pull-left nl-goback">
        <div><i class="iconfont">&#xe610;</i></div>
        </a>
        <h1 class="mui-title"><div><?=__('教练详情', 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="width-margin-pc layui-row">
                    <div class="coachDetail-row width-padding width-margin-pc">
                        <div class="width-padding-pc">
                            <div class="coachDetail-headImg img-box"  id="imgBox">
                                <img src="<?=$user_infos['user_head']?>">
                            </div>
                            <div class="coachDetail-coachInfo">
                                <div class="coachDetail-infoRow">
                                    <span class="fs_16 c_black"><?=$user_infos['real_name']?></span>
                                    <?php if(!empty($user_infos['user_gender'])):?>
                                    <span> <?=$user_infos['user_gender']?> </span>
                                    <?php endif;?>
                                    <span><?=__('ID', 'nlyd-student')?> <?=$user_infos['user_ID']?></span>
                                    <a class="c_blue fs_12 pointer" id="see">&nbsp;&nbsp;<?=__('查看教练证书', 'nlyd-student')?></a>
                                </div>
                                <div class="coachDetail-infoRow">
                                    <span><?=__('国际脑力运动委员会', 'nlyd-student')?>（IISC） <?=$user_infos['user_coach_level']?></span>
                                </div>
                                <?php if(!empty($skill)):?>
                                <div class="coachDetail-infoRow coach-detail-footer flex-h">


                                </div>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                    <div class="coachDetail-row have-metal width-padding width-margin-pc">
                        <div class="width-padding-pc">
                            <div class="coachDetail-metal"><?=__('简 介', 'nlyd-student')?></div>
                            <?php if($skill['coach_detail']){
                                echo $skill['coach_detail'];
                            }else { ?>
                                <p>国际一级脑力健将（记忆类）</p>
                                <p>成都电视台《超越梦想·脑力世界杯》栏目选手导师 </p>
                                <p>2017脑力世界杯全球总决赛记忆类总亚军 </p>
                                <p>2017脑力世界杯中国赛、全球总决赛优秀教练 </p>
                                <p>国际脑力运动推广大使</p>
                            <?php } ?>

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
<!--                        <a class="a-btn a-btn-table"><div>请TA当教练</div></a>-->
                        <?php endif;?>
                    <div class="coachDetail-row">
                            <!-- <div class="coachDetail-metal"><?=__('学 员', 'nlyd-student')?></div>
                            <div class="coachDetail-top">&nbsp;<span class="c_blue"><?php printf(__('%1$s名学员【%2$s位主训】', 'nlyd-student'), $content['student_count'],$content['major_count'])?></span></div>
                            <p>*<?=__('M、R、A分别代表记忆、速读、心算', 'nlyd-student')?></p>
                            <div class="nl-table-wapper">
                                <table class="nl-table">
                                    <thead>
                                        <tr>
                                            <td><?=__('序 号', 'nlyd-student')?></td>
                                            <td><?=__('姓 名', 'nlyd-student')?></td>
                                            <td><?=__('记 忆', 'nlyd-student')?></td>
                                            <td><?=__('速 读', 'nlyd-student')?></td>
                                            <td><?=__('心 算', 'nlyd-student')?></td>
                                            <td><?=__('脑力健将级别', 'nlyd-student')?></td>
                                        </tr>
                                    </thead>
                                    <tbody id="flow-table">

                                    </tbody> 
                                </table>
                            </div> -->
                            <div class="layui-tab layui-tab-brief" lay-filter="tabs">
                                <ul style="margin-left: 0" class="layui-tab-title">
                                    <li data-id="1" class="layui-this"><?=__('学员列表', 'nlyd-student')?></li>
                                    <li data-id="2"><?=__('课程列表', 'nlyd-student')?></li>
                                    <div class="nl-transform" data-y="-5"><?=__('学员列表', 'nlyd-student')?></div>
                                </ul>
                                <div class="layui-tab-content width-margin width-margin-pc" style="padding: 0;">
                                    <!-- 普通队员 -->
                                    <div class="layui-tab-item layui-show" id="1">
                                        <div class="nl-table-wapper">
                                            <table class="nl-table">
                                                <thead>
                                                    <tr>
                                                        <td><?=__('头 像', 'nlyd-student')?></td>
                                                        <td><?=__('姓 名', 'nlyd-student')?></td>
                                                        <td><?=__('记 忆', 'nlyd-student')?></td>
                                                        <td><?=__('速 读', 'nlyd-student')?></td>
                                                        <td><?=__('心 算', 'nlyd-student')?></td>
                                                        <td><?=__('脑力健将级别', 'nlyd-student')?></td>
                                                    </tr>
                                                </thead>
                                                <tbody  id="flow-table1">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- 认证教练 -->
                                    <div class="layui-tab-item"  id="2">
                                        <div class="ta_c mt_10"><?=__('暂无课程', 'nlyd-student')?></div>
                                    </div>
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
    // initWidth=function() {
    //     var len=$('.course-wrapper .course').length;
    //     var width=$('.course-wrapper .course').width()+2;
    //     var marginRight=parseInt($('.course-wrapper .course').css('marginRight'))
    //     var W=width*len+marginRight*(len-1)+'px';
    //     $('.course-wrapper').css('width',W);
    // }
    // initWidth()
    layui.use(['flow','layer','element'], function(){
        var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
        
        var flow = layui.flow;//流加载
        layer.photos({//图片预览
            photos: '#imgBox',
            anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
        })  
        $('#see').click(function(){
            var _src = '<?=$skill['coach_book']?>';
            if(_src == false) return false;
            var json={
                "title": "<?=__('教练证书', 'nlyd-student')?>", //相册标题
                "id": "coach_see", //相册id
                "start": 0, //初始显示的图片序号，默认0
                "data": [   //相册包含的图片，数组格式
                    {
                    "alt": "",
                    "pid": 1, //图片id
                    "src":_src, //原图地址
                    "thumb": _src, //缩略图地址
                    // "src":window.plugins_url+'/nlyd-student/Public/css/image/loading.gif', //原图地址
                    // "thumb": window.plugins_url+'/nlyd-student/Public/css/image/loading.gif', //缩略图地址
                    }
                ]
            }
            if(json['data'].length==0){
                $.alerts('<?=__('当前教练未上传证书', 'nlyd-student')?>')
            }else{
                layer.photos({//图片预览
                    photos: json,
                    anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
                })  
            }
        })
        function pagation(id,match_page){
            flow.load({
                elem: '#flow-table'+id //流加载容器
                ,isAuto: false
                ,isLazyimg: true
                ,done: function(page, next){ //加载下一页
                //模拟插入
                    var cocah_id=$.Request('coach_id');
                    if(id=="1"){//学员列表

                    }else if(id=="2"){//课程列表

                    }
                    var postData={
                        action:'get_cocah_member',
                        coach_id:parseInt(cocah_id),
                        page:page
                    }
                    var lis = [];
                    $.ajax({
                        data:postData,
                        success:function(res,ajaxStatu,xhr){
                        console.log(res)
                            match_page++
                            isClick[id]=true
                            if(res.success){ 
                                $.each(res.data.list,function(index,value){
                                    var dom='<tr>'
                                                +'<td>'
                                                    +'<div class="player-img img-box">'
                                                        +'<img src="'+value.user_head+'">'
                                                    +'</div>'
                                                +'</td>'
                                                +'<td><div class="table_content">'+value.nickname+'</div></td>'
                                                +'<td><div class="table_content">'+value.memory+'</div></td>'
                                                +'<td><div class="table_content">'+value.read+'</div></td>'
                                                +'<td><div class="table_content">'+value.compute+'</div></td>'
                                                +'<td><div class="table_content">'+value.mental+'</div></td>'
                                            +'</tr>'
                                    lis.push(dom)                           
                                })  
                                if (res.data.list.length<50) {
                                    next(lis.join(''),false)
                                }else{
                                    next(lis.join(''),true)
                                }
                            }else{
                                next(lis.join(''),false)
                            }
                        },
                        complete:function(XMLHttpRequest, textStatus){
                            if(textStatus=='timeout'){
                                $.alerts('<?=__('网络质量差,请重试', 'nlyd-student')?>')
                                next(lis.join(''),true)
                            }
                        }
                    })   
                }
            });
        }
        // $('.a-btn').click(function(){
        //     layer.open({
        //         type: 1
        //         ,maxWidth:300
        //         ,title: '<?=__('提示', 'nlyd-student')?>' //不显示标题栏
        //         ,skin:'nl-box-skin'
        //         ,id: 'certification' //防止重复弹出
        //         ,content: '<div class="box-conent-wrapper"><?=__('是否请TA当教练', 'nlyd-student')?>？</div>'
        //         ,btn: ['<?=__('再想想', 'nlyd-student')?>', '<?=__('确认', 'nlyd-student')?>', ]
        //         ,success: function(layero, index){
                    
        //         }
        //         ,yes: function(index, layero){
        //             layer.closeAll();
        //         }
        //         ,btn2: function(index, layero){
        //             //按钮【按钮二】的回调
        //             $.alerts('<?=__('确认', 'nlyd-student')?>')
        //         }
        //         ,closeBtn:2
        //         ,btnAagn: 'c' //按钮居中
        //         ,shade: 0.3 //遮罩
        //         ,isOutAnim:true//关闭动画
        //     });
        // })
  
        var isClick={}
        pagation($('.layui-this').attr('data-id'),1)
          //获取hash来切换选项卡，假设当前地址的hash为lay-id对应的值
        // var layid = location.hash.replace(/^#matchList=/, '');
        // if(layid.length>0){
        //     $('.layui-tab-title li').each(function(){
        //         var _this=$(this)
        //         var lay_id=_this.attr('data-id');
        //         if(lay_id==layid){
        //             setTimeout(function() {
        //                 _this.click()
        //             }, 200);
        //             return false
        //         }
        //     })
        // }
        
        element.on('tab(tabs)', function(){//tabs
            // location.hash = 'matchList='+ $(this).attr('data-id');
            var left=$(this).position().left;
            var id=$(this).attr('data-id')
            $('.nl-transform').css({
                'transform':'translate3d('+left+'px, -5px, 0px)'
            })
            if(!isClick[id]){
                pagation(id,1)
            }
        });
    })
})
</script>