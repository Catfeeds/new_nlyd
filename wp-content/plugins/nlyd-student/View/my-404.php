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
                </div>
            </div>
        </div>           
    </div>
</div>

<script>
jQuery(function($) {
})
</script>
