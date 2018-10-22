<style>
p.tips{
    margin-bottom: 20px;
    text-align: center;
}
.count-wrapper{
    width: 90%;
    height: 250px;
    position: absolute;
    top: 50%;
    left: 5%;
    margin-top: -150px;
}
</style>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <a class="mui-pull-left nl-goback">
                <div><i class="iconfont">&#xe610;</i></div>
            </a>
            <h1 class="mui-title"><div><?=__('定时器', 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content layui-bg-white">

                 <div class="count-wrapper">
                    <p class="tips fs_16">
                        <?=__('自定义定时器默认页面(无实际意义页面)', 'nlyd-student')?>
                        <?=get_time('mysql');?>
                    </p>
                </div>
            </div>
        </div>           
    </div>
</div>
<script>
jQuery(function($) { 
    if($('.count_down').length>0){
        if($('.count_down').attr('data-seconds')<=0){
            window.location.reload();
        }
        $('.count_down').countdown(function(S, d){//倒计时
            var D=d.day>0 ? d.day+' <?=__('天', 'nlyd-student')?>' : '';
            var h=d.hour<10 ? '0'+d.hour : d.hour;
            var m=d.minute<10 ? '0'+d.minute : d.minute;
            var s=d.second<10 ? '0'+d.second : d.second;
            var time=D+h+':'+m+':'+s;
            $(this).text(time);
            if(S<=0){//本轮比赛结束
                window.location.reload();
            }
        });
    }
})
</script>
