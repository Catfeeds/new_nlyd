<?php
/**
 * 比赛答题记录页面
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/8/22
 * Time: 20:28
 */

if(empty($_SESSION['match_data']) && ACTION =='answerLog'){ ?>
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

if(!in_array($project_alias,array('szzb','pkjl','zxss','nxss','wzsd','kysm'))){
    $data['message'] = __('比赛项目未绑定', 'nlyd-student');
    require_once student_view_path.'public/my-404.php';
    return;
}
?>
<!--头部-->
<?php if(isset($_GET['type'])): ?>
<?php require_once PLUGINS_PATH.'nlyd-student/View/public/student-footer-menu.php' ;?>
<?php endif;?>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
        if(!is_mobile()){
            require_once leo_student_public_view.'leftMenu.php';
        }
        ?>
        <div class="nl-right-content layui-col-lg8 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper <?php if(!isset($_GET['back'])){ ?>have-bottom<?php } ?>">
            <header class="mui-bar mui-bar-nav">
                <?php if(isset($_GET['type'])){ ?>
                    <a class="mui-pull-left nl-goback"><div><i class="iconfont">&#xe610;</i></div></a>
                <?php } ?>
                <h1 class="mui-title"><div><?=$match_title?><?=__('答题记录', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content ">
                <div class="width-margin">
                    <div class="match-subject-info">
                        <div class="subject-title">
                            <div class="c_black match_info_font"><div><?=__($project_title, 'nlyd-student')?> <?=sprintf(__('第%s轮', 'nlyd-student'),$match_more_cn)?> </div></div>
                            <div class="c_blue ml_10 match_info_font"><div><?=__('您的得分', 'nlyd-student')?> <?=$my_score > 0 ? $my_score : 0;?><?=__('分', 'nlyd-student')?></div></div>
                            <?php if( (ACTION == 'checkAnswerLog') || $_GET['match_more'] == $match_more):?>
                            <a <?= !empty($ranking) ? "class='c_blue subject-title-info' href='{$record_url}'" :'class="disabled-a subject-title-info"';?> ><div><?=__('全部排名', 'nlyd-student')?></div></a>
                            <?php endif;?>
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
                                <div class="left-label"><?=__('答题用时', 'nlyd-student')?>:</div><span class="c_blue"><?=$use_time > 0 ? $use_time : 0;?>s</span>
                            </div>
                            <div class="one-info flex1">
                                <div class="left-label"><?=__('剩余时间', 'nlyd-student')?>:</div><span class="c_blue"><?=$surplus_time > 0 ? $surplus_time : 0;?>s</span>
                            </div>
                        </div>
                        <div class="subject-row flex-h">
                            <div class="one-info flex1">
                                <div class="left-label"><?=__('正确率', 'nlyd-student')?>:</div><span class="c_blue"><?=$accuracy;?>%</span>
                            </div>
                            <?php if(!empty($ranking)):?>
                                <!--<div class="one-info flex1">
                                    <div class="left-label"><?/*=__('本轮排名', 'nlyd-student')*/?>:</div><span class="c_blue"><?/*=$ranking*/?></span>
                                </div>-->
                            <?php endif;?>
                        </div>
                    </div>

                    <?php

                        if($match_row['submit_type'] == 2){
                            $error=__('由于比赛过程中错误达上限，该轮答案由系统强制提交', 'nlyd-student');
                        }elseif($match_row['submit_type'] == 3){
                            $error=__('由于比赛倒计时结束，该轮比赛答题由系统自动提交', 'nlyd-student');
                        }elseif ($match_row['submit_type'] == 4){
                            $error=__('由于比赛过程中有切出系统行为，该轮答案由系统强制提交', 'nlyd-student');
                            if(!empty($match_row['leave_page_time'])){
                                $arr = json_decode($match_row['leave_page_time'],true);
                                $end_time = end($arr)['out'];
                                //print_r($end_time);
                            }
                        }elseif ($match_row['submit_type'] == 5){
                            $error=__('连续作答“本题无解”超过5次，该轮答案由系统强制提交');
                        }
                    ?>
                    <div style="color:#CF1818;"><?=$error?></div>
                    <?php if(!empty($end_time)): ?>
                        <div><?=__('切出页面时间', 'nlyd-student')?>:<span class="c_blue"><?=$end_time?></span></div>
                    <?php endif;?>
                    <?php
                    if($match_row['is_true'] == 2){
                        echo '<div style="color:#CF1818;">由于你本轮未进入赛场,本轮分数不参与排名</div>';
                    }
                    ?>

                    <?php
                    switch ($project_alias){
                        case 'szzb':    //数字争霸
                            require_once student_view_path.CONTROLLER.'/subject-numberBattle.php';
                            break;
                        case 'pkjl':    //扑克接力
                            require_once student_view_path.CONTROLLER.'/subject-pokerRelay.php';
                            break;
                        case 'zxss':    //正向速算
                            require_once student_view_path.CONTROLLER.'/subject-fastCalculation.php';
                            break;
                        case 'nxss':    //逆向速算
                            require_once student_view_path.CONTROLLER.'/subject-fastReverse.php';
                            break;
                        case 'wzsd':     //文章速读
                            require_once student_view_path.CONTROLLER.'/subject-reading.php';
                            break;
                        case 'kysm':    //快眼扫描
                            require_once student_view_path.CONTROLLER.'/subject-fastScan.php';
                            break;
                        default:
                            require_once student_view_path.'public/my-404.php';
                            break;
                    }
                    ?>
                </div>
                <?php if($next_count_down > 0):
                    if($next_project == 'y'){
                        $title = '项';
                    }elseif ($next_project == 'n'){
                        $title = '轮';
                    }
                ?>
                    <div class="a-btn a-btn-table" href="<?=$next_project_url?>"><div><?=__('距下一'.$title.'开赛', 'nlyd-student')?>&nbsp;&nbsp;&nbsp;&nbsp; <span class="count_down next_more_down" data-seconds="<?=$next_count_down?>">00:00:00</span></div></div>
                <?php endif;?>
                <?php if(empty($next_project) && $_GET['type'] != 'select'): ?>
                    <a class="a-btn a-btn-table" href="<?=$next_project_url?>"><div><?=__('所有答题结束,查看详情', 'nlyd-student')?></div></a>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function($) {
        $.DelSession('match');//比赛记录参数
        $.DelSession('leavePage');//切换页面参数参数
        $.DelSession('matching_question');//准备页面题目参数
        $.DelSession('match_data')
        <?php if(isset($_GET['project_more_id'])): ?>
            leavePageLoad('<?=$wait_url?>');
            var endTimes=0;
            var counts_down=$('.count_down').attr('data-seconds')
            endTimes=$.GetEndTime(counts_down)
            $('.count_down').countdown(function(S, d){//倒计时
                var count_down=S
                var new_count=$.GetSecond(endTimes);
                // console.log(count_down,new_count)
                if(count_down-new_count>10 || count_down-new_count<-10){//相差10s重新刷新
                    window.location.reload()
                }else{
                    var _this=$(this);
                    var D=d.day>0 ? d.day+'<?=__('天', 'nlyd-student')?>' : '';
                    var h=d.hour<10 ? '0'+d.hour : d.hour;
                    var m=d.minute<10 ? '0'+d.minute : d.minute;
                    var s=d.second<10 ? '0'+d.second : d.second;
                    var time=D+h+':'+m+':'+s;
                    $(this).attr('data-seconds',S).text(time)

                    if(S==0){
                        var href=_this.parents('.a-btn').attr('href');
                        $.DelSession('leavePageWaits')
                        if(href){
                            window.location.href=href
                        }else{
                            window.location.reload();
                        }
                    }
                }
            });
        <?php endif;?>
        <?php if($next_count_down > 0):
                    if($next_project == 'y'){
                        $title = '项';
                    }elseif ($next_project == 'n'){
                        $title = '轮';
                    }
                ?>
        <?php endif;?>
         <?php if($project_alias == 'pkjl'): ?>
            initWidth=function() {
                var len=$('.first_wap.poker-wrapper .poker').length;
                var width=$('.first_wap.poker-wrapper .poker').width()+2;
                var marginRight=parseInt($('.first_wap.poker-wrapper .poker').css('marginRight'))
                var W=width*len+marginRight*(len-1)+'px';
                $('.first_wap.poker-wrapper').css('width',W);

                var len1=$('.second_wap.poker-wrapper .poker').length;
                var width1=$('.second_wap.poker-wrapper .poker').width()+2;
                var marginRight1=parseInt($('.second_wap.poker-wrapper .poker').css('marginRight'))
                var W1=width1*len1+marginRight1*(len1-1)+'px';
                $('.second_wap.poker-wrapper').css('width',W1);
            }
            initWidth();
            
        <?php endif;?>

    })
</script>
<?php } ?>