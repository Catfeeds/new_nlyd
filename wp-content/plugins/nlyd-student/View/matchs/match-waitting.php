<div class="layui-fluid">
    <div class="layui-row">

       <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper">
       <header class="mui-bar mui-bar-nav">
            <h1 class="mui-title">比赛等待</h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <?php if( $first_time > get_time() || isset($_GET['wait']) ){?>
                <div class="count-wrapper">
                    <p class="match-name c_blue"><?=$match_title?></p>
                    <?php if($count_down > 0 ){ ?>
                    <div class="a-btn wait">倒计时<span class="count_down" data-seconds="<?=$count_down?>">初始中...</span></div>
                    <?php }else{ ?>
                    <a class="a-btn wait" href="<?=$match_url?>">进入比赛</a>
                    <?php }?>
                    <p class="match-detail c_black fs_16">第<?=$project_num?>个项目“<?=$project_title?>”，第<?=$next_more_num?>轮</p>

                </div> 
                <?php }else{ ?>
                 <div class="count-wrapper">
                    <?php if($match_type){ ?>
                        <p class="tips fs_16">
                            <span class="c_blue"><?=$project_title?>第<?=$more_num?>轮</span>已经开赛，您可等待本轮比赛完成后进入下一轮比赛
                        </p>
                        <a href="<?=$wait_url?>" class="a-btn wait">进入下一轮等待页面</a>
                    <?php }else{ ?>
                        <p class="tips fs_16">
                            <span class="c_blue"><?=$project_title?>第<?=$more_num?>轮</span>已经开赛，您可等待本轮排名统计完成后进入下一项比赛
                        </p>
                        <a href="<?=$wait_url?>" class="a-btn wait">进入下一项等待页面</a>
                    <?php } ?>
                    <a class="a-btn back" href="<?=home_url('account/recentMatch');?>">返回我的比赛列表</a>
                </div>
                <?php } ?>
            </div>           
        </div>

    </div>
</div>

<script>
jQuery(function($) { 
    if($('.count_down').attr('data-seconds')<=0){
        window.location.href="<?=$match_url?>"
    }
    $('.count_down').countdown(function(S, d){//倒计时
        var D=d.day>0 ? d.day+'天' : '';
        var h=d.hour<10 ? '0'+d.hour : d.hour;
        var m=d.minute<10 ? '0'+d.minute : d.minute;
        var s=d.second<10 ? '0'+d.second : d.second;
        var time=D+h+':'+m+':'+s;
        $(this).text(time);
        if(S<=0){//本轮比赛结束
            window.location.href="<?=$match_url?>"
        }
    });

})
</script>