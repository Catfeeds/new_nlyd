
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
                require_once leo_student_left_path.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
        <a class="mui-pull-left nl-goback">
        <i class="iconfont">&#xe610;</i>
        </a>
        <h1 class="mui-title">我的课程</h1>
        </header>
            <div class="layui-row nl-border nl-content layui-bg-white">
                <div class="no-info-page">
                    <div class="no-info-img">
                        <img src="<?=student_css_url.'image/noInfo/noCourse1043@2x.png'?>">
                    </div>
                    <p class="no-info-text">您暂未报名任何课程</p>
                    <a class="a-btn">浏览可报名课程</a>
                </div>
            </div>
        </div>            
    </div>
</div>

<script>
jQuery(function($) {

})
</script>
