<div class="layui-fluid">
    <div class="layui-row">

        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
           
            <div class="layui-row nl-border nl-content">
  
                <?php //if( $current_project['match_type'] == 'first' || isset($_GET['wait']) ){?>
                    <div class="count-wrapper">
                        <p class="match-name c_blue"><?=$match_title?></p>
                        <?php if($current_project['time_type'] != 'end'):?>
                        <div class="match_tips">
                            <p class="match-detail fs_14 c_black">
                                <span class="c_blue">即将进行：</span><?=!empty($next_project['project_title']) ? $next_project['project_title'] : $current_project['project_title']?>第<?=$next_more_num?>轮
                            </p>
                        </div>
                        <?php endif;?>
                        <?php if($answer_status == 1): ?>
                        <p class="fs_12 ta_c bottom_tips c_black">
                            您本轮比赛已经提交，本轮比赛完成后可继续参与下一轮比赛
                        </p>
                        <?php endif;?>
                        <?php if((empty($answer_status) || $answer_status == -1) && empty($current_project['match_type'])): ?>
                        <?php
                            if(isset($current_project['time_type']) && $current_project['time_type'] == 'end' && empty($next_project)){
                                $text = '本轮结束后查看答题详情';
                            }else{
                                $text = '下一轮开赛';
                            }
                        ?>
                        <p class="fs_12 ta_c bottom_tips c_black">
                            <?=$current_project['project_title']?>第<?=$current_project['match_more']?>轮已开赛，禁止进入比赛，您可等待<?=$text?>
                        </p>
                        <?php endif;?>
                        <?php if($count_down > 0 ){ ?>
                            <div class="wait">
                                <div class="inner">
                                    <p>倒计时</p>
                                    <p class="count_down" data-seconds="<?=$count_down?>">初始中...</p>
                                </div>
                            </div>
                        <?php }
                        if($buffer_time){
                        ?>
                            <a class="a-btn back" href="<?=$buffer_url?>">
                                进入比赛
                            </a>
                        <?php }?>
                        <div class="match_tips">
                            <p class="c_black"><i class="iconfont c_orange">&#xe64c;</i> 比赛前请关闭一切无关后台应用，我们将记录你当前的系统运行环境以及你的所有操作行为。</p>
                            <p class="c_black"><i class="iconfont c_orange">&#xe64c;</i> 比赛过程中禁止切出页面，否则系统将强制自动提交你的当前比赛项目。</p>
                        </div>
                    </div>
                <?php //}else{ ?>
                    <!--<div class="count-wrapper">
                        <?php /*if(empty($next_project)){ */?>
                            <p class="tips fs_16">
                                <span class="c_blue"><?/*=$current_project['project_title']*/?>第<?/*=$current_project['match_more']*/?>轮</span>已经开赛，您可等待本轮比赛完成后进入下一轮比赛
                            </p>
                            <a href="<?/*=$wait_url*/?>" class="a-btn wait">进入下一轮等待页面</a>
                        <?php /*}else{ */?>
                            <p class="tips fs_16">
                                <span class="c_blue"><?/*=$current_project['project_title']*/?>第<?/*=$current_project['match_more']*/?>轮</span>已经开赛，您可等待本轮排名统计完成后进入下一项比赛
                            </p>
                            <a href="<?/*=$wait_url*/?>" class="a-btn wait">进入下一项等待页面</a>
                        <?php /*} */?>
                        <a class="a-btn back" href="<?/*=home_url('account/recentMatch');*/?>">返回我的比赛列表</a>
                    </div>-->
                <?php //} ?>
            </div>
        </div>

    </div>
</div>
<script>
    jQuery(function($) {
        <?php if($match_status == 2 || $count_down <= 120): ?>

         history.pushState(null, null, document.URL);
         window.addEventListener('popstate', function () {
             history.pushState(null, null, document.URL);
         });

        $(window).on("blur",function(){
            var sessionData={
                match_id:$.Request('match_id')
            }
            $.SetSession('leavePageWaitting',sessionData)
        })
        $(window).on("focus", function(e) {
            var leavePageWaitting= $.GetSession('leavePageWaitting','1');
            if(leavePageWaitting && leavePageWaitting['match_id']===$.Request('match_id')){
                window.location.reload()
                $.DelSession('leavePageWaitting')
            }
        });
        <?php endif;?>
        if($('.count_down').attr('data-seconds')<=0){
            $.DelSession('leavePageWaitting')
            window.location.href="<?=$match_url?>"
        }
        $('.count_down').countdown(function(S, d){//倒计时
            var D=d.day>0 ? d.day+'天' : '';
            var h=d.hour<10 ? '0'+d.hour : d.hour;
            var m=d.minute<10 ? '0'+d.minute : d.minute;
            var s=d.second<10 ? '0'+d.second : d.second;
            var time=D+h+':'+m+':'+s;
            $(this).text(time);
            if(S<=0){//
                $.DelSession('leavePageWaitting')
                window.location.href="<?=$match_url?>"
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