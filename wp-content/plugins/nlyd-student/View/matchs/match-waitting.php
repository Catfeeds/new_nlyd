<div class="layui-fluid">
    <div class="layui-row">

       <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper">
       <header class="mui-bar mui-bar-nav">
            <a class="mui-pull-left nl-goback">
                <i class="iconfont">&#xe610;</i>
            </a>
            <h1 class="mui-title">比赛等待</h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <div class="count-wrapper">
                    <div class="count-nl-logo">
                        <img src="<?=student_css_url.'image/nlyd.png'?>">
                    </div>
                    <p class="match-name c_blue"><?=$match_title?></p>
                    <?php if($count_down > 0 ){ ?>
                    <div class="a-btn wait">倒计时<span class="count_down" data-seconds="<?=$count_down?>">00:00:00</span></div>
                    <?php }else{ ?>
                    <div class="a-btn wait"><a href="<?=$match_url?>">进入比赛</a></div>
                    <?php }?>
                    <p class="match-detail">第1个项目“<?=$match_title?>”，第1轮</p>
                </div> 
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