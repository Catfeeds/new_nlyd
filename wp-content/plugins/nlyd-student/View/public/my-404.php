<?php get_header();?>
<style>
.a-btn.wait{
    position: relative;
    top:0;
}
.a-btn.back{
    position: relative;
    top: 0;
    margin-top: 15px;
    border: 1px solid #2484FE;
    color: #2484FE;
    background: #fff;
}
.a-btn.back:hover{
    color: #2484FE!important;
}
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


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <a class="mui-pull-left nl-goback">
                <i class="iconfont">&#xe610;</i>
            </a>
            <h1 class="mui-title"><?=__('国际脑力运动', 'nlyd-student')?></h1>
        </header>
            <div class="layui-row nl-border nl-content layui-bg-white">

                 <div class="count-wrapper">
                    <p class="tips fs_16">
                        <?=$data['message']?>
                        <?php if(!empty($start_count_down)):?>
                        <span class="count_down" data-seconds="<?=$start_count_down?>"><?=__('初始中', 'nlyd-student')?>...</span>
                        <?php endif;?>
                    </p>
                    <?php if(!empty($data['match_url'])):?>
                    <a class="a-btn" style="position: relative;bottom: 0;" href="<?=$data['match_url']?>"><?=__('返回比赛详情', 'nlyd-student')?></a>
                    <?php endif;?>
                    <?php if(!empty($data['waiting_url'])):?>
                    <a class="a-btn back" href="<?=$data['waiting_url']?>"><?=__('返回比赛等待', 'nlyd-student')?></a>
                    <?php endif;?>
                     <?php if(!empty($data['return_url'])):?>
                         <a class="a-btn back" href="<?=$data['return_url']?>"><?=__('返回上一页', 'nlyd-student')?></a>
                     <?php endif;?>
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
            var D=d.day>0 ? d.day+'<?=__('天', 'nlyd-student')?>' : '';
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

