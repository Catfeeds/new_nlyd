<div class="layui-fluid">
    <div class="layui-row">

        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
           
            <div class="layui-row nl-border nl-content">
  
                <?php //if( $current_project['match_type'] == 'first' || isset($_GET['wait']) ){?>
                    <div class="count-wrapper">
                        <p class="match-name c_blue"><?=empty($match_title) ? '国际'.$project_alias_cn.'水平考级认证' : $match_title;?></p>
                        <div class="match_tips">
                            <?php

                            if(!empty($memory_lv)){
                                $project_alias_cn .= chinanum($memory_lv);
                            }
                            ?>
                            <p class="match-detail fs_14 c_black">
                                <span class="c_blue"><?=__('即将进行', 'nlyd-student')?>: </span><?=__('国际'.$project_alias_cn.'级水平考级认证','nlyd-student')?>
                            </p>
                        </div>

                        <?php if($count_down > 0 ){ ?>
                            <div class="wait">
                                <div class="inner">
                                    <p><?=__('倒计时', 'nlyd-student')?></p>
                                    <p class="count_down" data-seconds="<?=$count_down?>"><?=__('初始中', 'nlyd-student')?>...</p>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if($count_down < 0 ){ ?>
                            <a class="a-btn a-btn-table back" href="<?=$redirect_url?>"><div><?=__('立即进入', 'nlyd-student')?></div></a>
                        <?php } ?>
                        <div class="match_tips">
                            <p class="c_black"><i class="iconfont c_orange">&#xe64c;</i> <?=__('考级前请关闭一切无关后台应用，我们将记录你当前的系统运行环境以及你的所有操作行为。', 'nlyd-student')?></p>
                            <p class="c_black"><i class="iconfont c_orange">&#xe64c;</i> <?=__('考级过程中禁止切出页面，否则系统将强制自动提交你的当前考级项目。', 'nlyd-student')?></p>
                        </div>
                    </div>
            </div>
        </div>

    </div>
</div>
<script>
    jQuery(function($) {
        $.DelSession('match');//考级记录参数
        // $.DelSession('leavePage');//切换页面参数参数
        $.DelSession('grade_question');//准备页面题目参数
        $.DelSession('match_data');
        <?php if($match_status == 2 || $count_down <= 120): ?>
        matchWaitting()
        <?php endif;?>

        var endTimes=0;
        var counts_down=$('.count_down').attr('data-seconds')
        endTimes=$.GetEndTime(counts_down)
        if($('.count_down').attr('data-seconds')<=0){
            $.DelSession('leavePageWaitting')
            window.location.href="<?=$redirect_url?>"
        }
        $('.count_down').countdown(function(S, d){//倒计时
            var count_down=S
            var new_count=$.GetSecond(endTimes);
            // console.log(count_down,new_count)
            if(count_down-new_count>10 || count_down-new_count<-10){//相差10s重新刷新
                window.location.reload()
            }else{
                var D=d.day>0 ? d.day+'<?=__('天', 'nlyd-student')?>' : '';
                var h=d.hour<10 ? '0'+d.hour : d.hour;
                var m=d.minute<10 ? '0'+d.minute : d.minute;
                var s=d.second<10 ? '0'+d.second : d.second;
                var time=D+h+':'+m+':'+s;
                $(this).attr('data-seconds',S).text(time);
                if(S<=0){//
                    $.DelSession('leavePageWaitting')
                    window.location.href="<?=$redirect_url?>"
                }
            }
        });
        var height= $('.count-wrapper').height();
        var marginTop=height / 2;
        var top=$('.detail-content-wrapper').height() / 2;
        if(top>marginTop+25){
            $('.count-wrapper').css({
                'margin-top':-marginTop+'px',
                'top':top+'px',
                'width': '100%',
                'position': 'absolute',
                'left': '0',
            })
        }
    })
</script>