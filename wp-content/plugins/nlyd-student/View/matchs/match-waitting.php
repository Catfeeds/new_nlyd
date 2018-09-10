<div class="layui-fluid">
    <div class="layui-row">

        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper">
           
            <div class="layui-row nl-border nl-content">
  
                <?php //if( $current_project['match_type'] == 'first' || isset($_GET['wait']) ){?>
                    <div class="count-wrapper">

                        <p class="match-name c_blue"><?=$match_title?></p>
                        <div class="match_tips">
                            <p class="c_black6">1、比赛前请关闭一切无关后台应用，我们将记录你当前的系统运行环境以及你的所有操作行为。</p>
                            <p class="c_black6">2、比赛过程中禁止切出页面，否则系统将强制自动提交你的当前比赛项目。</p>
                            <!-- <p class="c_black6">3、请调整好心态准备比赛，脑力中国预祝您取得优异的比赛成绩！</p> -->
                        </div>
                        <?php if($count_down > 0 ){ ?>
                            <div class="wait">
                                <div class="inner">
                                    <p>倒计时</p>
                                    <p class="count_down" data-seconds="<?=$count_down?>">初始中...</p>
                                </div>
                            </div>
                        <?php }else{ ?>
                            <a class="wait" href="<?=$match_url?>">进入比赛</a>
                        <?php }?>
                        <div class="match_tips">
                            <?php if( empty($current_project['match_type'])): ?>
                            <p class="match-detail c_blue fs_14">
                                <?=$current_project['project_title']?>第<?=$current_project['match_more']?>轮</span>已经开赛
                            </p>
                            <?php endif;?>
                            <!-- <p class="match-detail c_blue fs_14">
                                即将开赛<?= empty($current_project['match_type']) ? '下一个' : '第'.$project_num.'个';?>项目“<?=!empty($next_project['project_title']) ? $next_project['project_title'] : $current_project['project_title']?>”，第<?=$next_more_num?>轮
                            </p> -->

                            <!-- 即将开赛 -->

                            <?php if( empty($current_project['match_type'])): ?>
                            <p class="match-detail fs_14">
                                <span class="c_blue">当前比赛：</span><?=$current_project['project_title']?>第<?=$current_project['match_more']?>轮</span>
                            </p>
                            <?php endif;?>
                            <p class="match-detail fs_14">
                                <span class="c_blue">即将进行：</span><?=!empty($next_project['project_title']) ? $next_project['project_title'] : $current_project['project_title']?>第<?=$next_more_num?>轮
                            </p>
                        </div>
                        <p class="fs_12 ta_c bottom_tips">
                            您本轮比赛已经提交，本轮比赛完成后可继续参与下一轮比赛。
                        </p>
                        <p class="fs_12 ta_c bottom_tips">
                            数字争霸第1轮已开赛，禁止进入比赛，您可等待下一轮开赛。
                        </p>
                        
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

    })
</script>