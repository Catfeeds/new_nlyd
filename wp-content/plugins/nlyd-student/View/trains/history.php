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
                我的训练记录
                </h1>
            </header>

                  <?php if(!empty($list)){ ?>
                    <div class="layui-row nl-border nl-content have-bottom">
                        <div class="width-padding width-padding-pc">
                            <div class="ta_c c_yellow fs_12 his_tips">温馨提示：训练最多保存100条</div>
                            <?php foreach ($list as $v){ ?>
                            <div class="his_row">
                                <div class="bold c_black fs_16 pull-left his_first"> <?=$v['project_type_cn']?></div>
                                <div class="c_orange pull-left his_second"><?=$v['my_score']?>分</div>
                                <div class="pull-left his_third"><?=$v['created_time']?></div>
                                <a class="pull-right c_blue" href="<?=home_url('trains/logs/back/1/id/'.$v['id'].'/type/'.$v['project_type'])?>">详情</a>
                            </div>
                            <?php } ?>
                            <a class="a-btn" href="<?=home_url('trains')?>">马上去训练</a>
                        </div>
                    </div>               
                    <?php }else{ ?>
                        <div class="layui-row nl-border nl-content">
                            <div class="width-padding width-padding-pc">
                                <div class="no-info-page">
                                    <div class="no-info-img">
                                        <img src="<?=student_css_url.'image/noInfo/noTrain1045@3x.png'?>">
                                    </div>
                                    <p class="no-info-text">您暂无训练记录</p>
                                    <a class="a-btn" href="<?=home_url('trains')?>">马上去训练</a>
                                </div>
                            </div>
                        </div>
                    <?php }?>

        </div>
    </div>
</div>

