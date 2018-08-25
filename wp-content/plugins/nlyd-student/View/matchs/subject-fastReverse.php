
<div class="layui-fluid">
    <div class="layui-row">
        <?php
            if(!is_mobile()){
                require_once leo_student_public_view.'leftMenu.php';
            }
        ?>
        <div class="layui-col-lg8 layui-col-md8 layui-col-sm12 layui-col-xs12 detail-content-wrapper">
            
        <header class="mui-bar mui-bar-nav">
            <?php if(isset($_GET['type'])){ ?>
                <a class="mui-pull-left nl-goback"><i class="iconfont">&#xe610;</i></a>
            <?php } ?>
        <h1 class="mui-title">本轮答题记录</h1>
        </header>
            <div class="layui-row nl-border nl-content ">
                <div class="width-margin">
                    <div class="match-subject-info">
                        <div class="subject-title">
                            <div class="c_black"><?=$project_title?> 第<?=$match_more_cn?>轮</div>
                            <div class="c_blue ml_10">您的得分<?=$my_score?>分</div>
                            <div class="subject-title-info"><a <?= !empty($ranking) ? "href='{$record_url}'" :'class="disabled-a"';?> >全部排名</a></div>
                        </div>
                        <div class="subject-row">
                            <div class="one-info">
                                <div class="left-label">答题数量：</div><span class="c_blue"><?=$str_len;?></span>
                            </div>
                            <div class="one-info">
                                <div class="left-label">正确数量：</div><span class="c_blue"><?=$success_length;?></span>
                            </div>
                        </div>
                        <div class="subject-row">
                            <div class="one-info">
                                <div class="left-label">答题用时：</div><span class="c_blue"><?=$use_time;?>s</span>
                            </div>
                            <div class="one-info">
                                <div class="left-label">剩余时间：</div><span class="c_blue"><?=$surplus_time;?>s</span>
                            </div>
                        </div>
                        <div class="subject-row">
                            <div class="one-info">
                                <div class="left-label">&nbsp;&nbsp;&nbsp;正确率：</div><span class="c_blue"><?=$accuracy;?>%</span>
                            </div>
                            <?php if(!empty($ranking)):?>
                            <div class="one-info">
                                <div class="left-label">本轮排名：</div><span class="c_blue"><?=$ranking?></span>
                            </div>
                            <?php endif;?>
                        </div>
                    </div>
                    <div class="answer-zoo">
                        <div class="answerBtn">答案对比</div>
                            <div class="reading-answer">
                                <?php if(!empty($match_questions)): ?>
                                <?php foreach ($match_questions as $k => $val){ ?>
                                <div class="one-ques">
                                    <p class="question"><?=$k+1;?>、运算数字：<?=arr2str($val,'、')?></p>
                                    <p class="yours">你的答案：<span class="<?=$my_answer[$k] == $questions_answer[$k] ? 'yes' : 'error';?>"><?= $my_answer[$k] == 'unsolvable' ? '本题无解' : $my_answer[$k]?></span></p>
                                    <p class="rights">正确示例：<?=!empty($questions_answer[$k]) && $questions_answer[$k] != 'unsolvable' ? $questions_answer[$k] : '本题无解'?></p>
                                </div>
                                <?php } ?>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                <?php if($next_type == 1 && !isset($_GET['type'])): ?>
                <div class="a-btn" href="<?=$next_project_url?>">距下一轮开赛&nbsp;&nbsp;&nbsp;&nbsp; <span class="count_down next_more_down" data-seconds="<?=$next_count_down?>">00:00:00</span></div>
                <a href="<?=$next_project_url?>">下一轮</a>
                <?php endif;?>
                <?php if($next_type == 2 && !isset($_GET['type'])): ?>
                <div class="a-btn" href="<?=$next_project_url?>">距下一项目开赛 <span class="count_down next_project_down" data-seconds="<?=$next_count_down?>">00:00:00</span></div>
                <a href="<?=$next_project_url?>">下一项目</a>
                <?php endif;?>
                <?php if($next_type == 3):?>
                <a class="a-btn" href="<?=$next_project_url?>">下一项已开赛,等待开赛</a>
                <?php endif;?>
                <?php if($next_type == 4):?>
                    <a class="a-btn" href="<?=$next_project_url?>">比赛结束,查看详情</a>
                <?php endif;?>
            </div>
        </div>           
    </div>
</div>

<input style="display:none;" type="file" name="meta_val" id="file" class="file" value="" accept="image/jpg,image/jpeg,image/png,image/bmp" multiple/>
<input  type="hidden" name="meta_key" value="user_head"/>
<input  type="hidden" name="action" value="student_saveInfo"/>
<input type="hidden" name="_wpnonce" value="<?=wp_create_nonce('student_saveInfo_code_nonce');?>">

<script>
jQuery(function($) {
<?php if(!isset($_GET['type'])): ?>
$('.count_down').countdown(function(S, d){//倒计时
    var _this=$(this);
    var D=d.day>0 ? d.day+'天' : '';
    var h=d.hour<10 ? '0'+d.hour : d.hour;
    var m=d.minute<10 ? '0'+d.minute : d.minute;
    var s=d.second<10 ? '0'+d.second : d.second;
    var time=D+h+':'+m+':'+s;
     _this.text(time);
    if(S<=0){
         window.location.href=_this.parents('.a-btn').attr('href')
    }
});
<?php endif;?>
})
</script>