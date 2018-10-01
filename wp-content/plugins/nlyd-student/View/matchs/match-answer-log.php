<?php
/**
 * 比赛答题记录页面
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/8/22
 * Time: 20:28
 */


if(!in_array($project_alias,array('szzb','pkjl','zxss','nxss','wzsd','kysm'))){
    $data['message'] = '比赛项目未绑定';
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
        <div class="layui-col-lg8 layui-col-md8 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <?php if(isset($_GET['type'])){ ?>
                    <a class="mui-pull-left nl-goback"><i class="iconfont">&#xe610;</i></a>
                <?php } ?>
                <h1 class="mui-title"><?=$match_title?>答题记录</h1>
            </header>
            <div class="layui-row nl-border nl-content ">
                <div class="width-margin">
                    <div class="match-subject-info">
                        <div class="subject-title flex-h">
                            <div class="c_black flex1 match_info_font"><?=$project_title?> 第<?=$match_more_cn?>轮</div>
                            <div class="c_blue flex1 ml_10 match_info_font">您的得分<?=$my_score?>分</div>
                            <?php if( (ACTION == 'checkAnswerLog') || $_GET['match_more'] == $match_more):?>
                            <div class="subject-title-info flex1"><a <?= !empty($ranking) ? "class='c_blue' href='{$record_url}'" :'class="disabled-a"';?> >全部排名</a></div>
                            <?php endif;?>
                        </div>
                        <div class="subject-row flex-h">
                            <div class="one-info flex1">
                                <div class="left-label">答题数量：</div><span class="c_blue"><?=$str_len;?></span>
                            </div>
                            <div class="one-info flex1">
                                <div class="left-label">正确数量：</div><span class="c_blue"><?=$success_length;?></span>
                            </div>
                        </div>
                        <div class="subject-row flex-h">
                            <div class="one-info flex1">
                                <div class="left-label">答题用时：</div><span class="c_blue"><?=$use_time;?>s</span>
                            </div>
                            <div class="one-info flex1">
                                <div class="left-label">剩余时间：</div><span class="c_blue"><?=$surplus_time;?>s</span>
                            </div>
                        </div>
                        <div class="subject-row flex-h">
                            <div class="one-info flex1">
                                <div class="left-label">&nbsp;&nbsp;&nbsp;正确率：</div><span class="c_blue"><?=$accuracy;?>%</span>
                            </div>
                            <?php if(!empty($ranking)):?>
                                <div class="one-info flex1">
                                    <div class="left-label">本轮排名：</div><span class="c_blue"><?=$ranking?></span>
                                </div>
                            <?php endif;?>
                        </div>
                    </div>

                    <?php

                        if($match_row['submit_type'] == 2){
                            $error='由于比赛过程中错误达上限，该轮答案由系统强制提交';
                        }elseif($match_row['submit_type'] == 3){
                            $error='由于比赛倒计时结束，该轮比赛答题由系统自动提交';
                        }elseif ($match_row['submit_type'] == 4){
                            $error='由于比赛过程中有切出系统行为，该轮答案由系统强制提交';
                            if(!empty($match_row['leave_page_time'])){
                                $arr = json_decode($match_row['leave_page_time'],true);
                                $end_time = end($arr)['out'];
                                //print_r($end_time);
                            }
                        }
                    ?>
                    <div style="color:#CF1818;"><?=$error?></div>
                    <?php if(!empty($end_time)): ?>
                        <div>切出页面时间：<span class="c_blue"><?=$end_time?></span></div>
                    <?php endif;?>


                    <?php
                    switch ($project_alias){
                        case 'szzb':    //数字争霸
                            require_once student_view_path.'matchs/subject-numberBattle.php';
                            break;
                        case 'pkjl':    //扑克接力
                            require_once student_view_path.'matchs/subject-pokerRelay.php';
                            break;
                        case 'zxss':    //正向速算
                            require_once student_view_path.'matchs/subject-fastCalculation.php';
                            break;
                        case 'nxss':    //逆向速算
                            require_once student_view_path.'matchs/subject-fastReverse.php';
                            break;
                        case 'wzsd':     //文章速读
                            require_once student_view_path.'matchs/subject-reading.php';
                            break;
                        case 'kysm':    //快眼扫描
                            require_once student_view_path.'matchs/subject-fastScan.php';
                            break;
                        default:
                            require_once student_view_path.'public/my-404.php';
                            break;
                    }
                    ?>
                </div>
                <!-- <?php if(!empty($end_time_count_down)):?>
                    <div class="a-btn" style="display: none"><span class="count_down next_more_down" data-seconds="<?=$end_time_count_down?>">00:00:00</span></div>
                <?php endif;?> -->
                <?php if($next_type == 1 && !isset($_GET['type'])): ?>
                    <div class="a-btn" href="<?=$next_project_url?>">距下一轮开赛&nbsp;&nbsp;&nbsp;&nbsp; <span class="count_down next_more_down" data-seconds="<?=$next_count_down?>">00:00:00</span></div>
                <?php endif;?>
                <?php if($next_type == 2 && !isset($_GET['type'])): ?>
                    <div class="a-btn" href="<?=$next_project_url?>">距下一项目开赛 <span class="count_down next_project_down" data-seconds="<?=$next_count_down?>">00:00:00</span></div>
                <?php endif;?>
                <?php if($next_type == 3):?>
                    <a class="a-btn" href="<?=$next_project_url?>">下一项已开赛,进入比赛</a>
                <?php endif;?>
                <?php if($next_type == 4):?>
                    <a class="a-btn" href="<?=$next_project_url?>">所有答题结束,查看详情</a>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function($) {
        <?php if(!isset($_GET['type'])): ?>
          leavePageLoad('<?=$wait_url?>');
        $('.count_down').countdown(function(S, d){//倒计时
            var _this=$(this);
            var D=d.day>0 ? d.day+'天' : '';
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
        });
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
