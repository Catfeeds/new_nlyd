
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
                <?=$post_title?>
                </h1>
            </header>
            <div class="layui-row nl-border nl-content">
                <div class="width-padding width-padding-pc">
                    <span class="fs_12">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;脑力世界杯是国际脑力运动委员会（IISC）发起的一项世界顶级综合性脑力赛事，是比拼选手脑力素质的世界舞台，六大标准项目分别从不同侧面综合考查选手注意力、感知力、理解力、记忆力和创造力五大脑力素质，是全球最全面而精炼的脑力大赛。</span>
                　　 <br><span class="fs_12">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Intellectual World Cup is a world top comprehensive intellectual competition sponsored by the International Intellectual Sports Commission (IISC). It is a world stage for competitors to show  their brain. The six events comprehensively examine the five intellectual qualities of competitors from different aspects: attention, perception, comprehension, memory and creativity. It is the most comprehensive and refined mental contest in the world.</span>
                　　 <br><span class="fs_12">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;本版块供选手针对该赛事各项目开展自我训练，相关挑战指标可根据自身水平自行设定，循序渐进，以逐步提高实战能力。</span>
                　　 <br><span class="fs_12">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;This section for the athletes to carry out self-training for the events, the users can set different time and difficulty by themself to gradually improve their actual ability.</span>
                </div>
                <div class="nl-page-form layui-form width-margin-pc have-bottom" lay-filter="nicenameForm">   

                    <div class="nl-form-tips width-padding width-padding-pc">
                        <h5>训练项目</h5>
                        <!-- <span>速记类</span> -->
                    </div>
                    <?php
                    if (empty($list)){ ?>
                        <div>暂无专项训练</div>;
                    <?php }else{ ?>
                        <?php foreach ($list as $v){ ?>
                            <span><?=$v["title"]?></span>
                            <?php foreach ($v["children"] as $val){ ?>
                                <div class="form-inputs">
                                    <a class="form-input-row" href="<?=home_url('trains/ready/type/'.$val->project_alias.'/genre_id/'.$genre_id.'/id/'.$val->ID)?>" >
                                        <div class="form-input-label"><?=$val->post_title?></div>
                                        <span class="form-input-right"><i class="iconfont">&#xe727;</i></span>
                                    </a>
                                </div>
                            <?php }?>
                        <?php }?>
                    <?php }?>
                </div>
            </div>
        </div>
    </div>
</div>
