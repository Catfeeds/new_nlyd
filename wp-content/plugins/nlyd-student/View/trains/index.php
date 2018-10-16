

<?php require_once PLUGINS_PATH.'nlyd-student/View/public/student-footer-menu.php' ;?>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
            require_once leo_student_public_view.'leftMenu.php';
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12  detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback">
                    <i class="iconfont">&#xe610;</i>
                </a>
                <h1 class="mui-title">
                训练类型
                </h1>
            </header>

                    <?php if (empty($list)){ ?>
                        <div class="layui-row nl-border nl-content">
                            <div class="width-padding width-padding-pc">
                                <div class="no-info-page">
                                    <div class="no-info-img">
                                        <img src="<?=student_css_url.'image/noInfo/noTrain1045@3x.png'?>">
                                    </div>
                                    <p class="no-info-text">暂无专项比賽训练</p>
                                </div>
                            </div>
                        </div>
                    <?php }else{ ?>
                        <div class="layui-row nl-border nl-content have-footer">
                            <div class="width-padding width-padding-pc">
                                <?php foreach ($list as $v){ ?>
                                    <a class="train_row c_black" href="<?=home_url('trains/lists/id/'.$v->ID)?>">
                                        <div class="train-img">
                                            <img src="<?=student_css_url.'image/sjb.png'?>">
                                        </div>
                                        <div class="train_name fs_16"><?=$v->post_title?></div>
                                    </a>
                                <?php }?>
                                <a class="train_row disable" href="" onclick="return false;">
                                    <div class="train-img">
                                        <img src="<?=student_css_url.'image/sjb.png'?>">
                                    </div>
                                    <div class="train_name fs_16">自测考级专项训练</div>
                                    <div class="train_tips">暂未开放</div>
                                </a>
                                <a class="train_row disable" href="" onclick="return false;">
                                    <div class="train-img">
                                        <img src="<?=student_css_url.'image/sjb.png'?>">
                                    </div>
                                    <div class="train_name fs_16">自测考级专项训练</div>
                                    <div class="train_tips">暂未开放</div>
                                </a>
                                <a class="train_row disable" href="" onclick="return false;">
                                    <div class="train-img">
                                        <img src="<?=student_css_url.'image/sjb.png'?>">
                                    </div>
                                    <div class="train_name fs_16">自测考级专项训练</div>
                                    <div class="train_tips">暂未开放</div>
                                </a>
                            </div>
                        </div>
                    <?php }?>

        </div>
    </div>
</div>
