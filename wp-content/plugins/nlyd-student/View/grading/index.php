<?php require_once PLUGINS_PATH.'nlyd-student/View/public/student-footer-menu.php' ;?>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            <div class="main-header">
                <header class="mui-bar mui-bar-nav main">
                    <a class="mui-pull-left nl-goback static" href="<?=home_url('supervisor/logs')?>">
                        <div><i class="iconfont">&#xe610;</i></div>
                    </a>
                    <h1 class="mui-title"><div><?=__('考级中心', 'nlyd-student')?></div></h1>
                </header>
            </div> 
            <div class="layui-row nl-border nl-content">
                <a href="<?=home_url('grading/ready_szzb/type/1')?>">随机数字记忆</a>
                <a href="<?=home_url('grading/ready_szzb/type/2')?>">随机字母记忆</a>
                <a href="<?=home_url('grading/ready_word/')?>">随机中文词语记忆</a>
                <a href="<?=home_url('grading/matching_PI/')?>">圆周率默写</a>
                <a href="<?=home_url('grading/ready_card/')?>">人脉信息记忆</a>
            </div>
        </div>
    </div>
</div>
<script>
jQuery(document).ready(function($) {

})
</script>