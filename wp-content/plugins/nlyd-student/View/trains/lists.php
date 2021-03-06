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
                <div><?=__($post_title, 'nlyd-student')?></div>
                </h1>
            </header>
            <div class="layui-row nl-border nl-content have-footer">
                <div class="width-margin width-margin-pc">
                    <div class="lists_lists">   
                        <?php
                        if (empty($list)){ ?>
                            <div class="ta_c"><?=__('暂无专项训练', 'nlyd-student')?></div>
                        <?php }else{
                                if($project_alias == 'grading'){
                                    foreach ($list as $k =>$v){
                                        //print_r($k);
                                        $alias = get_post_meta($k,'project_alias')[0];
                                        $thumbnail_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($k), 'thumbnail');
                                    ?>
                                    <div class="lists_item_row">
                                        <div class="bold c_black fs_16"></div>
                                        <a class="item_row lists_row c_black6 kaoji"  href="<?=home_url('trains/ready/genre_id/'.$genre_id.'/type/'.$alias)?>" >
                                            <div class="item_img">
                                                <img src="<?=$thumbnail_image_url[0]?>">
                                            </div>
                                            <div class="name_wrapper dis_table"><div class="dis_cell"><?=__($v["title"], 'nlyd-student')?></div></div>
                                            <div class="arrow_box"><img src="<?=student_css_url.'image/trains/arrow.png'?>"></div>
                                        </a>
                                    </div>

                        <?php       }
                                }else{
                                     foreach ($list as $v){ ?>
                                        <div class="lists_item_row">
                                            <div class="bold c_black fs_16"><?=__($v["title"], 'nlyd-student')?></div>
                                            <?php foreach ($v["children"] as $val){ ?>
                                                <a class="item_row lists_row c_black6"  href="<?=home_url('trains/ready/type/'.$val->project_alias.'/genre_id/'.$genre_id.'/id/'.$val->ID)?>" >
                                                    <?php
                                                    $thumbnail_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($val->ID), 'thumbnail');
                                                    ?>
                                                    <div class="item_img"><img src="<?=$thumbnail_image_url[0]?>"></div>
                                                    <div class="name_wrapper dis_table"><div class="dis_cell"><?=__($val->post_title, 'nlyd-student')?></div></div>
                                                    <div class="arrow_box"><img src="<?=student_css_url.'image/trains/arrow.png'?>"></div>

                                                </a>
                                            <?php }?>
                                        </div>
                                    <?php }
                                }
                        } ?>
                    </div>

<?php if($project_alias == 'mental_world_cup'): ?>
<div class="width-padding width-padding-pc lists_row">
<span class="fs_12">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;脑力世界杯是国际脑力运动委员会（IISC）发起的一项世界顶级综合性脑力赛事，是比拼选手脑力素质的世界舞台，六大标准项目分别从不同侧面综合考查选手注意力、感知力、理解力、记忆力和创造力五大脑力素质，是全球最全面而精炼的脑力大赛。</span>
<br><span class="fs_12">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Intellectual World Cup is a world top comprehensive intellectual competition sponsored by the International Intellectual Sports Commission (IISC). It is a world stage for competitors to show  their brain. The six events comprehensively examine the five intellectual qualities of competitors from different aspects: attention, perception, comprehension, memory and creativity. It is the most comprehensive and refined mental contest in the world.</span>
<br><span class="fs_12">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;本版块供选手针对该赛事各项目开展自我训练，相关挑战指标可根据自身水平自行设定，循序渐进，以逐步提高实战能力。</span>
<br><span class="fs_12">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;This section for the athletes to carry out self-training for the events, the users can set different time and difficulty by themself to gradually improve their actual ability.</span>
</div>
<?php endif;?>
                </div>
            </div>
        </div>
    </div>
</div>
