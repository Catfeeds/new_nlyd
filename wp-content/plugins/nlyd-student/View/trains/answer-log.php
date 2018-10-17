<?php
/**
 * 比赛答题记录页面
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/8/22
 * Time: 20:28
 */
switch ($type){
    case 'szzb';
        $title = '数字争霸';
        break;
    case 'pkjl';
        $title = '扑克接力';
        break;
    case 'wzsd';
        $title = '文章速读';
        break;
    case 'kysm';
        $title = '快眼扫描';
        break;
    case 'zxss';
        $title = '正向速算';
        break;
    case 'nxss';
        $title = '逆向速算';
        break;

    default:

        break;
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
                <?php if(isset($_GET['back'])){ ?>
                    <a class="mui-pull-left nl-goback"><i class="iconfont">&#xe610;</i></a>
                <?php } ?>
                <h1 class="mui-title"><?=$title?><?=__('答题记录', 'nlyd-student')?></h1>
            </header>
            <div class="layui-row nl-border nl-content" style="padding-bottom:80px;">
                <div class="width-margin">
                    <div class="match-subject-info">
                        <div class="subject-title flex-h">
                            <div class="c_black flex1 match_info_font"><?=$title?>第一轮</div>
                            <div class="c_blue flex1 ml_10 match_info_font"><?=__('您的得分', 'nlyd-student')?><?=$my_score?><?=__('分', 'nlyd-student')?></div>
                        </div>
                        <div class="subject-row flex-h">
                            <div class="one-info flex1">
                                <div class="left-label"><?=__('答题数量', 'nlyd-student')?>：</div><span class="c_blue"><?=$str_len;?></span>
                            </div>
                            <div class="one-info flex1">
                                <div class="left-label"><?=__('正确数量', 'nlyd-student')?>：</div><span class="c_blue"><?=$success_length;?></span>
                            </div>
                        </div>
                        <div class="subject-row flex-h">
                            <div class="one-info flex1">
                                <div class="left-label"><?=__('答题用时', 'nlyd-student')?>：</div><span class="c_blue"><?=$use_time;?>s</span>
                            </div>
                            <div class="one-info flex1">
                                <div class="left-label"><?=__('剩余时间', 'nlyd-student')?>：</div><span class="c_blue"><?=$surplus_time;?>s</span>
                            </div>
                        </div>
                        <div class="subject-row flex-h">
                            <div class="one-info flex1">
                                <div class="left-label">&nbsp;&nbsp;&nbsp;<?=__('正确率', 'nlyd-student')?>：</div><span class="c_blue"><?=$accuracy;?>%</span>
                            </div>
                            <?php if(!empty($ranking)):?>
                                <div class="one-info flex1">
                                    <div class="left-label"><?=__('本轮排名', 'nlyd-student')?>：</div><span class="c_blue"><?=$ranking?></span>
                                </div>
                            <?php endif;?>
                        </div>
                    </div>

                    <?php
                    switch ($type){
                        case 'szzb':    //数字争霸
                            require_once student_view_path.'trains/subject-numberBattle.php';
                            break;
                        case 'pkjl':    //扑克接力
                            require_once student_view_path.'trains/subject-pokerRelay.php';
                            break;
                        case 'zxss':    //正向速算
                            require_once student_view_path.'trains/subject-fastCalculation.php';
                            break;
                        case 'nxss':    //逆向速算
                            require_once student_view_path.'trains/subject-fastReverse.php';
                            break;
                        case 'wzsd':     //文章速读
                            require_once student_view_path.'trains/subject-reading.php';
                            break;
                        case 'kysm':    //快眼扫描
                            require_once student_view_path.'trains/subject-fastScan.php';
                            break;
                        default:
                            require_once student_view_path.'public/my-404.php';
                            break;
                    }
                    ?> 
                </div>        
            </div>
        </div>
    </div>
</div>
<?php if(!isset($_GET['back'])){ ?>
<div class="a-btn two get_footer">
    <a class="a-two left c_white" id="again" href="<?=$recur_url?>">再来一局</a>
    <a class="a-two right c_white" href="<?=$revert_url?>">返回列表</a>
</div>
<?php } ?>
<script>
jQuery(function($) {
    <?php if(!isset($_GET['back'])){ ?>
    history.pushState(null, null, document.URL);
    window.addEventListener('popstate', function () {
        history.pushState(null, null, document.URL);
    });
    <?php } ?>
    $('#again').click(function(){
        $.DelCookie('train_match','1')
    })
    <?php if($type == 'pkjl'): ?>
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