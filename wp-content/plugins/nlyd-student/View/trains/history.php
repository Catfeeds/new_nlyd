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
                <div><?=__('我的训练记录', 'nlyd-student')?></div>
                </h1>
            </header>

                  <?php if(!empty($list)){ ?>
                    <div class="layui-row nl-border nl-content have-bottom">
                        <div class="width-padding width-padding-pc ta_c c_blue fs_12 his_tips"><?=__('温馨提示:训练记录最多保存100条', 'nlyd-student')?> <span class="c_black pull-right close fs_20">×</span></div>
                        <?php if($_GET['alias'] == 'grading'):?>
                        <div class="width-padding width-padding-pc ta_c mt_10">
                            <span class="fs_16">训练记录：
                                记忆<span class="c_blue"><?=$rank_row['memory'] > 0 ? $rank_row['memory'] : 0;?></span>级
                                心算<span class="c_blue"><?=$rank_row['compute'] > 0 ? $rank_row['compute'] : 0;?></span>级
                                速读<span class="c_blue"><?=$rank_row['read'] > 0 ? $rank_row['read'] : 0;?></span>级</span>
                        </div>
                        <?php endif;?>
                        <div class="width-padding width-padding-pc">
                            <?php foreach ($list as $k => $val){ ?>
                            <div class="ta_c mt_10"><?=__($k, 'nlyd-student')?></div>
                            <?php foreach ($val as $v){
                                if($_GET['alias'] == 'mental_world_cup'){
                                    $url = home_url('trains/logs/back/1/id/'.$v['id'].'/type/'.$v['project_type']);
                                }elseif ($_GET['alias'] == 'grading'){
                                    $url = home_url('grade/myAnswerLog/back/1/grad_type/'.$v['grade_type'].'/log_id/'.$v['id']);
                                }
                            ?>
                            <a class="his_row"  href="<?=$url?>">
                                <div class="bold c_black pull-left his_first"> <?=__($v['project_type_cn'], 'nlyd-student')?></div>
                                <!-- <div class="c_orange pull-left his_second"><?=$v['my_score']?>分</div> -->
                                <div class="pull-right his_thir"> 
                                    <span class="<?=$v['grade_result'] == 1 ? 'c_green' : 'c_orange';?>">
                                        <?php if($_GET['alias'] == 'mental_world_cup'):?>
                                        <?=$v['my_score']?><?=__('分', 'nlyd-student')?>
                                        <?php endif;?>
                                        <?php if($_GET['alias'] == 'grading'):?>
                                        <?=$v['grade_result_cn']?>
                                        <?php endif;?>
                                    </span>
                                    <span class="c_black6"><?=$v['created_time']?></span>
                                </div>
                                <div class="arrow_box"><img src="<?=student_css_url.'image/trains/arrow.png'?>"></div>
                            </a>
                            <?php } ?>
                            <?php } ?>
                            <a class="a-btn" href="<?=home_url('trains')?>"><?=__('马上去训练', 'nlyd-student')?></a>
                        </div>
                    </div>               
                    <?php }else{ ?>
                        <div class="layui-row nl-border nl-content">
                            <div class="width-padding width-padding-pc">
                                <div class="no-info-page">
                                    <div class="no-info-img">
                                        <img src="<?=student_css_url.'image/noInfo/noTrain1045@3x.png'?>">
                                    </div>
                                    <p class="no-info-text"><?=__('您暂无训练记录', 'nlyd-student')?></p>
                                    <a class="a-btn" href="<?=home_url('trains')?>"><?=__('马上去训练', 'nlyd-student')?></a>
                                </div>
                            </div>
                        </div>
                    <?php }?>

        </div>
    </div>
</div>

<script>
jQuery(function($) { 
    $('body').on('click','.close',function(){
        $('.his_tips').hide()
    })
})
</script>