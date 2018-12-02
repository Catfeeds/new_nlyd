<?php
/**
 * 考级答题记录页面
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/8/22
 * Time: 20:28
 */

if(empty($_SESSION['match_data']) && ACTION =='answerLog' && !isset($_GET['log_id'])){ ?>
    <style>
        @media screen and (max-width: 1199px){
            #page {
                top: 0;
            }
        }
        .count_down_wrap{
            position: absolute;
            top: 50%;
            width: 100%;
            text-align: center;
            margin-top: -30px;
        }
    </style>
    <div class="detail-content-wrapper">
        <div class="count_down_wrap c_bkue"><span class="count_down_" data-seconds="5"></span><?=__('秒', 'nlyd-student')?><?=__('之后跳转', 'nlyd-student')?>......</div>
    </div>

    <script>
        jQuery(function($) {

            var data = $.GetSession('match_data', '1');
            if(data == null){
                $.alerts("<?=__('未检测到答题记录', 'nlyd-student')?>");
                window.location.href= '<?=home_url("/matchs/matchWaitting/match_id/".$_GET['match_id'])?>';
                return false;
            }
            //console.log(data);return false;
            history.pushState(null, null, document.URL);
            window.addEventListener('popstate', function () {
                history.pushState(null, null, document.URL);
            });
            // var data=$.GetSession('match_data','1')
            $('.count_down_').countdown(function (S, d) {
                $('.count_down_').text(S).attr('data-seconds',S)
                if (S <= 0) {//取消遮擋

                    //var data = $.GetSession('match_data', '1')
                    $.ajax({
                        data: data,
                        success: function (res, ajaxStatu, xhr) {
                            if(res.success){
                                $.DelSession('match_data')
                                if(res.data.url){
                                    window.location.href=res.data.url
                                }
                            }else{
                                $.alerts(res.data.info)
                            }
                        },
                        complete:function (XMLHttpRequest, textStatus) {
                            if(textStatus=='timeout'){
                                $.alerts("<?=__('网络延迟', 'nlyd-student')?>")
                                $.DelSession('match_data')
                                window.location.href= '<?=home_url("/matchs/matchWaitting/match_id/")?>'+data.match_id
                            }
                        }
                    })
                }
            })
        })
    </script>

<?php }else{

?>
<!--头部-->
<?php if(!isset($_GET['type'])): ?>
<?php require_once PLUGINS_PATH.'nlyd-student/View/public/student-footer-menu.php' ;?>
<?php endif;?>
<style>
.layui-layer.nl-box-skin .layui-layer-btn .layui-layer-btn0{
    color:#fff;
    background: #4394F9!important;
    width:100%;
}
.layui-layer.nl-box-skin .layui-layer-btn div{
    width:100%;
}
</style>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
        if(!is_mobile()){
            require_once leo_student_public_view.'leftMenu.php';
        }
        ?>
        <div class="nl-right-content layui-col-lg8 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <?php if(!isset($_GET['type'])){ ?>
                    <a class="mui-pull-left nl-goback"><div><i class="iconfont">&#xe610;</i></div></a>
                <?php } ?>
                <h1 class="mui-title"><div><?=__('答题记录', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content <?php if(!isset($_GET['back'])){ ?>have-bottom<?php } ?>">
                <div class="width-margin">
                    <div class="match-subject-info">
                        <div class="subject-title 
                        <?php if(ACTION == 'myAnswerLog'): ?>
                        ta_c
                        <?php endif;?>
                        ">
                            <?php if(ACTION == 'myAnswerLog'): ?>
                            <?php if(!empty($prev)){ ?>
                                <a class="pull-left c_blue" href="<?=$prev?>"><i class="iconfont" style="font-size:0.20rem">&#xe647;</i></a>
                            <?php }else{ ?>
                                <a class="pull-left c_grey"><i class="iconfont" style="font-size:0.20rem">&#xe647;</i></a>
                            <?php } ?>
                            <?php if(!empty($next)){ ?>
                                <a class="pull-right c_blue" href="<?=$next?>"><i class="iconfont" style="font-size:0.20rem">&#xe648;</i></a>
                            <?php }else{ ?>
                                <a class="pull-right c_grey"><i class="iconfont" style="font-size:0.20rem">&#xe648;</i></a>
                            <?php } ?>
                            <?php endif;?>
                            <div class="c_black match_info_font"><div><?=__($match_row['questions_type_cn'], 'nlyd-student')?> </div></div>
                        </div>
                        <div class="subject-row flex-h">
                            <div class="one-info flex1">
                                <div class="left-label"><?=__('答题数量', 'nlyd-student')?>:</div><span class="c_blue"><?=$str_len > 0 ? $str_len : 0;?></span>
                            </div>
                            <div class="one-info flex1">
                                <div class="left-label"><?=__('正确数量', 'nlyd-student')?>:</div><span class="c_blue"><?=$success_length > 0 ? $success_length : 0;?></span>
                            </div>
                        </div>
                        <div class="subject-row flex-h">
                            <div class="one-info flex1">
                                <div class="left-label"><?=__('正确率', 'nlyd-student')?>:</div><span class="c_blue"><?=$accuracy;?>%</span>
                            </div>
                            <?php if(!empty($_GET['reading']=='grad_type')):?>
                                <div class="one-info flex1">
                                    <div class="left-label"><?=__('速率', 'nlyd-student')?>:</div><span class="c_blue"><?=$reading_rate;?>字/分钟</span>
                                </div>
                            <?php endif;?>
                            <?php if(!empty($_GET['grad_type']=='arithmetic')):?>
                                <div class="one-info flex1">
                                    <div class="left-label"><?=__('得分', 'nlyd-student')?>:</div><span class="c_blue"><?=empty($match_row['my_score']) ? 0 : $match_row['my_score'];?>分</span>
                                </div>
                            <?php endif;?>
                        </div>
                    </div>

                    <?php

                    switch ($match_row['questions_type']){
                        case 'sz':    //随机数字
                        case 'yzl':    //圆周率
                        case 'tl':    //听记数字
                        case 'zm':    //随机数字
                            require_once student_view_path.CONTROLLER.'/subject-numberBattle.php';
                            break;
                        case 'cy':    //随机词汇
                            require_once student_view_path.CONTROLLER.'/subject-zwcy.php';
                            break;
                        case 'rm':    //正向速算
                            require_once student_view_path.CONTROLLER.'/subject-rmjy.php';
                            break;
                        case 'wz':    //国学默写
                            require_once student_view_path.CONTROLLER.'/subject-silent.php';
                            break;
                        case 'reading':    //文章速读
                            require_once student_view_path.CONTROLLER.'/subject-reading.php';
                            break;
                        case 'zxys':    //正向运算
                            require_once student_view_path.CONTROLLER.'/subject-fastCalculation.php';
                            break;
                        case 'nxys':    //逆向运算
                            require_once student_view_path.CONTROLLER.'/subject-fastReverse.php';
                            break;
                        default:
                            require_once student_view_path.'public/my-404.php';
                            break;
                    }
                    ?>
                </div>
                <?php
                    if($next_count_down > 0 && !empty($next_project)):
                ?>
                    <div class="a-btn a-btn-table a-btn-top" href="<?=$next_project_url?>"><div><?=__('距下一项开始', 'nlyd-student')?>&nbsp;&nbsp;&nbsp;&nbsp; <span class="count_down next_more_down" data-seconds="<?=$next_count_down?>">00:00:00</span></div></div>
                <?php endif;?>

                <?php
                    if(ACTION == 'answerLog') {
                        if (empty($next_project)) { ?>

                            <div class="a-btn two get_footer">
                                <a class="a-two left c_white" id="again" href="<?=$recur_url?>"><div><?=__('再来一局', 'nlyd-student')?></div></a>
                                <a class="a-two right c_white" href="<?=$revert_url?>"><div><?=__('返回列表', 'nlyd-student')?></div></a>
                            </div>
                        <?php } else { ?>
                            <a class="a-btn a-btn-table ingnore" href="<?= $next_project_url ?>">
                                <div><?= __('跳过等待', 'nlyd-student') ?></div>
                            </a>
                            <?php
                        }
                    }
                ?>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function($) {
        $.DelSession('match');//考级记录参数
        $.DelSession('grade_question');//准备页面题目参数
        $.DelSession('match_data');
        <?php if(isset($_GET['history_id'])): ?>
        history.pushState(null, null, document.URL);
        window.addEventListener('popstate', function () {
            history.pushState(null, null, document.URL);
        });
            if($('.count_down').length>0){
                var endTimes=0;
                var countSession=$.GetSession('count')
                if(countSession && !isNaN(countSession)){
                    endTimes=countSession;
                }else{
                    var counts_down=$('.count_down').attr('data-seconds')
                    endTimes=$.GetEndTime(counts_down)
                    $.SetSession('count',endTimes)
                }
                var new_count=$.GetSecond(endTimes);
                console.log(new_count)
                // if(new_count=0){
                //     $.DelSession('count');
                //     var href=$('.count_down').parents('.a-btn').attr('href');
                //     if(href){
                //         window.location.href=href
                //     }else{
                //         window.location.reload();
                //     }
                // }
                $('.count_down').attr('data-seconds',new_count).countdown(function(S, d){//倒计时
                    // var count_down=S
                    // var new_count=$.GetSecond(endTimes);
                    // // console.log(count_down,new_count)
                    // if(count_down-new_count>10 || count_down-new_count<-10){//相差10s重新刷新
                    //     window.location.reload()
                    // }else{
                        var _this=$(this);
                        var D=d.day>0 ? d.day+'<?=__('天', 'nlyd-student')?>' : '';
                        var h=d.hour<10 ? '0'+d.hour : d.hour;
                        var m=d.minute<10 ? '0'+d.minute : d.minute;
                        var s=d.second<10 ? '0'+d.second : d.second;
                        var time=D+h+':'+m+':'+s;
                        $(this).attr('data-seconds',S).text(time)

                        if(S==0){
                            var href=_this.parents('.a-btn').attr('href');
                            $.DelSession('count');
                            if(href){
                                window.location.href=href
                            }else{
                                window.location.reload();
                            }
                        }
                    // }
                });
            }
            $('.ingnore').click(function(){
                $.DelSession('count');
            })
            <?php if(empty($next_project)): ?>
            layui.use('layer', function(){
                layer.open({
                    type: 1
                    ,maxWidth:300
                    ,title: '<?=__('考级认证结果', 'nlyd-student')?>' //不显示标题栏
                    ,skin:'nl-box-skin'
                    ,id: 'certifications' //防止重复弹出
                    ,content: '<div class="box-conent-wrapper"><span class="<?=$grading_result == 1 ? 'c_green' : '';?>"><?=__($grade_result, 'nlyd-student')?></span></div>'
                    ,btn: ['确认']
                    ,success: function(layero, index){
                    }
                    ,yes: function(index, layero){
                        layer.closeAll();
                    }
                    ,btn2: function(index, layero){
                        layer.closeAll();
                    }
                    ,closeBtn:2
                    ,btnAagn: 'c' //按钮居中
                    ,shade: 0.3 //遮罩
                    ,isOutAnim:true//关闭动画
                });
            })
            <?php endif;?>
        <?php endif;?>
    })
</script>
<?php } ?>