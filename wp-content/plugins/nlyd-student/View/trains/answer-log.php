<?php
/**
 * 比赛答题记录页面
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/8/22
 * Time: 20:28
 */

//unset($_SESSION['train_list']);
if(empty($_SESSION['train_list']) && !isset($_GET['back'])){ ?>
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
        <div class="count_down_wrap c_bkue"><span class="count_down_" data-seconds="5"></span><?=__('秒', 'nlyd-student')?><?=__('之后跳转', 'nlyd-student')?>...</div>
    </div>

    <script>
        jQuery(function($) {
            var data = $.GetSession('match_data', '1');
            if(data == null){
                $.alerts("<?=__('未检测到答题记录', 'nlyd-student')?>");
                window.location.href= '<?=home_url("/trains/history/")?>';
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
                    var data = $.GetSession('train_data', '1')
                    $.ajax({
                        data: data,
                        success: function (res, ajaxStatu, xhr) {
                            console.log(res)
                            if(res.success){
                                $.DelSession('train_data')
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
                                $.DelSession('train_data')
                                window.location.href= '<?=home_url("/trains/history/")?>'
                            }
                        }
                    })
                }
            })
        })
    </script>
<?php }else{
switch ($type){
    case 'szzb';
        $title = __('数字争霸', 'nlyd-student');
        break;
    case 'pkjl';
        $title = __('扑克接力', 'nlyd-student');
        break;
    case 'wzsd';
        $title = __('文章速读', 'nlyd-student');
        break;
    case 'kysm';
        $title = __('快眼扫描', 'nlyd-student');
        break;
    case 'zxss';
        $title = __('正向速算', 'nlyd-student');
        break;
    case 'nxss';
        $title = __('逆向速算', 'nlyd-student');
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
        <div class="nl-right-content layui-col-lg8 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <?php if(isset($_GET['back'])){ ?>
                    <a class="mui-pull-left nl-goback static" href="<?=home_url('/trains/history_list/')?>"><div><i class="iconfont">&#xe610;</i></div></a>
                <?php } ?>
                <h1 class="mui-title"><div><?=$title?> <?=__('答题记录', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content <?php if(!isset($_GET['back'])){ ?>have-bottom<?php } ?>">
                <div class="width-margin width-margin-pc">
                    <div class="match-subject-info">
                        <div class="subject-title">
                            <div class="c_black match_info_font"><div><?=$title?> <?/*=__('第'.$match_more.'轮', 'nlyd-student')*/?></div></div>
                            <div class="c_blue ml_10 match_info_font"><div><?=sprintf(__('您的得分%s分', 'nlyd-student'), $my_score > 0 ? $my_score : 0)?></div></div>
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
                                <div class="one-info flex1">
                                    <div class="left-label"><?=__('本轮排名', 'nlyd-student')?>:</div><span class="c_blue"><?=$ranking?></span>
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
            <?php if(!isset($_GET['back'])){ ?>
            <div class="a-btn two get_footer">
                <a class="a-two left c_white" id="again" href="<?=$recur_url?>"><div><?=__('再来一局', 'nlyd-student')?></div></a>
                <a class="a-two right c_white" href="<?=$revert_url?>"><div><?=__('返回列表', 'nlyd-student')?></div></a>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<script>
jQuery(function($) {
    $.DelSession('train_match')
    $.DelSession('_match_train')
    $.DelSession('train_data')
    <?php if(!isset($_GET['back'])){ ?>
    history.pushState(null, null, document.URL);
    window.addEventListener('popstate', function () {
        history.pushState(null, null, document.URL);
    });
    <?php } ?>
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
            if('ontouchstart' in window){// 移动端
                
            }else{
                $('.porker-zoo').height(477)
            }
        <?php endif;?>
})
</script>
<?php } ?>