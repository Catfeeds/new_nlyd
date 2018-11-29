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
            <?php if ( empty($list)){ ?>
                <div class="layui-row nl-border nl-content">
                    <div class="width-padding width-padding-pc">
                        <div class="no-info-page">
                            <div class="no-info-img">
                                <img src="<?=student_css_url.'image/noInfo/noTrain1045@3x.png'?>">
                            </div>
                            <p class="no-info-text"><?=__('暂无考级自测训练', 'nlyd-student')?></p>
                        </div>
                    </div>
                </div>
            <?php }else{ ?>
                <div class="layui-row nl-border nl-content have-footer">
                    <div class="width-padding width-padding-pc">
                        <?php foreach ($list as $v){ ?>
                            <a class="train_row his_list c_black " href="<?=home_url('trains/lists/id/'.$v['id'])?>">
                                <div class="train-img" style="background:#<?=$v['highlight']?>"></div>
                                <div class="train_name">

                                    <span class="bold fs_16"><?=__($v['post_title'], 'nlyd-student')?></span>
                                    <br>
                                    <span class="fs_12 c_black6">上次训练时间</span>：
                                    <span class="fs_12 c_black6"><?= !empty($v['last_time']) ? $v['last_time'] : '-'?></span>
                                </div>
                                <div class="arrow_box"><img src="<?=student_css_url.'image/trains/arrow.png'?>"></div>
                            </a>
                        <?php }?>
                    </div>
                </div>
            <?php }?>
        </div>
    </div>
</div>