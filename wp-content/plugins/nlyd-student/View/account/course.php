
<style>
@media screen and (max-width: 1199px){
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


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
        <a class="mui-pull-left nl-goback">
        <div><i class="iconfont">&#xe610;</i></div>
        </a>
        <h1 class="mui-title"><div><?=__('我的课程', 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content layui-bg-white">
                <div class="no-info-page">
                    <div class="no-info-img">
                        <img src="<?=student_css_url.'image/noInfo/noCourse1043@2x.png'?>">
                    </div>
                    <p class="no-info-text"><?=__('您暂未报名任何课程', 'nlyd-student')?></p>
                    <a class="a-btn a-btn-table"><div><?=__('浏览可报名课程', 'nlyd-student')?></div></a>
                </div>
            </div>
        </div>            
    </div>
</div>
