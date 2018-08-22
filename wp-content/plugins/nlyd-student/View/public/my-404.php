<style>
@media screen and (max-width: 991px){
    #content,.detail-content-wrapper{
        background:#fff;
    }

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
                <i class="iconfont">&#xe610;</i>
            </a>
            <h1 class="mui-title">404</h1>
        </header>
            <div class="layui-row nl-border nl-content layui-bg-white">
                <div class="no-info-page">
                    <div class="no-info-img">
                        <img src="<?=student_css_url.'image/noInfo/404x2.png'?>">
                    </div>
                    <p class="no-info-text"><?=$data['message']?></p>
                    <p class="no-info-text">
                        <?php if(!empty($data['match_url'])):?>
                        <a href="<?=$data['match_url']?>">返回比赛详情</a>
                        <?php endif;?>
                        <?php if(!empty($data['waiting_url'])):?>
                        <a href="<?=$data['waiting_url']?>">返回比赛等待</a>
                        <?php endif;?>
                    </p>
                </div>
            </div>
        </div>           
    </div>
</div>
