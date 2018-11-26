

<?php require_once PLUGINS_PATH.'nlyd-student/View/public/student-footer-menu.php' ;?>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
            require_once leo_student_public_view.'leftMenu.php';
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12  detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title">
                <div><?=__('训练类型', 'nlyd-student')?></div>
                </h1>
            </header>
        
                <div class="layui-row nl-border nl-content have-footer">
                    <div class="width-padding width-padding-pc">
                        <a class="train_row his_list <?=$v->post_status == 'draft' ? 'disable' : 'c_black';?>" <?php if($v->post_status == 'draft') echo 'onclick="return false;"'?> href="<?=home_url('/trains/history/');?>">
                            <div class="train-img" style="background:red"></div>
                            <div class="train_name">
                                <span class="bold fs_16"><?=__("脑力世界杯专项训练", 'nlyd-student')?></span>
                                <br>
                                <span class="fs_12 c_black6">2018/11/8 13:18</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="fs_12 c_black6">训练记录16条</span>
                            </div>
                            <div class="arrow_box"><img src="<?=student_css_url.'image/trains/arrow.png'?>"></div>
                        </a>
                        <a class="train_row his_list <?=$v->post_status == 'draft' ? 'disable' : 'c_black';?>" <?php if($v->post_status == 'draft') echo 'onclick="return false;"'?> href="<?=home_url('/trains/history/');?>">
                            <div class="train-img" style="background:yellow"></div>
                            <div class="train_name">
                                <span class="bold fs_16"><?=__("考级专项训练", 'nlyd-student')?></span>
                                <br>
                                <span class="fs_12 c_black6">2018/11/8 13:18</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="fs_12 c_black6">训练记录16条</span>
                            </div>
                            <div class="arrow_box"><img src="<?=student_css_url.'image/trains/arrow.png'?>"></div>
                        </a>
                    </div>
                </div>
        </div>
    </div>
</div>