<?php
/**
 * 比赛答题记录页面
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/8/22
 * Time: 20:28
 */



?>

<div class="layui-fluid">
    <div class="layui-row">
        <?php
        if(!is_mobile()){
            require_once leo_student_public_view.'leftMenu.php';
        }
        ?>
        <div class="nl-right-content layui-col-lg8 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper <?php if(!isset($_GET['back'])){ ?>have-bottom<?php } ?>">
            <header class="mui-bar mui-bar-nav">

                <h1 class="mui-title"><div><?=__('答题记录', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content ">
                <div class="width-margin">
                    <div class="match-subject-info">
                        <div class="subject-title">
                            <div class="c_black match_info_font"><div>1</div></div>
                            <div class="c_blue ml_10 match_info_font"><div><?=__('您的得分', 'nlyd-student')?>1<?=__('分', 'nlyd-student')?></div></div>
               
                        </div>
                        <div class="subject-row flex-h">
                            <div class="one-info flex1">
                                <div class="left-label"><?=__('答题数量', 'nlyd-student')?>:</div><span class="c_blue">0</span>
                            </div>
                            <div class="one-info flex1">
                                <div class="left-label"><?=__('正确数量', 'nlyd-student')?>:</div><span class="c_blue">0</span>
                            </div>
                        </div>
                        <div class="subject-row flex-h">
                            <div class="one-info flex1">
                                <div class="left-label"><?=__('答题用时', 'nlyd-student')?>:</div><span class="c_blue">0s</span>
                            </div>
                            <div class="one-info flex1">
                                <div class="left-label"><?=__('剩余时间', 'nlyd-student')?>:</div><span class="c_blue">0s</span>
                            </div>
                        </div>
                        <div class="subject-row flex-h">
                            <div class="one-info flex1">
                                <div class="left-label"><?=__('正确率', 'nlyd-student')?>:</div><span class="c_blue">0%</span>
                            </div>
                        </div>
                    </div>
                    <div class="answer-zoo">
    <button class="matching-btn active"><?=__('你的答案', 'nlyd-student')?></button>
    <div class="your-answer layui-row">
        <div class="matching-card">
            <div class="img-box card_img">
                <img class="_img" src="http://127.0.0.1/nlyd/wp-content/plugins/nlyd-match/upload/people/36.jpg">
            </div>
            <div class="card_detail">
                <div class="card_name c_black">leo</div>
                <div class="card_phone c_black active">18140022053</div>
            </div>
        </div>
        <div class="matching-card">
            <div class="img-box card_img">
                <img class="_img" src="http://127.0.0.1/nlyd/wp-content/plugins/nlyd-match/upload/people/36.jpg">
            </div>
            <div class="card_detail">
                <div class="card_name c_black">leo</div>
                <div class="card_phone c_black active">18140022053</div>
            </div>
        </div>
        <div class="matching-card">
            <div class="img-box card_img">
                <img class="_img" src="http://127.0.0.1/nlyd/wp-content/plugins/nlyd-match/upload/people/36.jpg">
            </div>
            <div class="card_detail">
                <div class="card_name c_black">leo</div>
                <div class="card_phone c_black active">18140022053</div>
            </div>
        </div>
    </div>
    <button class="matching-btn active"><?=__('正确答案', 'nlyd-student')?></button>
    <div class="right-answer layui-row">
        <div class="matching-card">
            <div class="img-box card_img">
                <img class="_img" src="http://127.0.0.1/nlyd/wp-content/plugins/nlyd-match/upload/people/36.jpg">
            </div>
            <div class="card_detail">
                <div class="card_name c_black">leo</div>
                <div class="card_phone c_black active">18140022053</div>
            </div>
        </div>
        <div class="matching-card">
            <div class="img-box card_img">
                <img class="_img" src="http://127.0.0.1/nlyd/wp-content/plugins/nlyd-match/upload/people/36.jpg">
            </div>
            <div class="card_detail">
                <div class="card_name c_black">leo</div>
                <div class="card_phone c_black active">18140022053</div>
            </div>
        </div>
        <div class="matching-card">
            <div class="img-box card_img">
                <img class="_img" src="http://127.0.0.1/nlyd/wp-content/plugins/nlyd-match/upload/people/36.jpg">
            </div>
            <div class="card_detail">
                <div class="card_name c_black">leo</div>
                <div class="card_phone c_black active">18140022053</div>
            </div>
        </div>
        <div class="matching-card">
            <div class="img-box card_img">
                <img class="_img" src="http://127.0.0.1/nlyd/wp-content/plugins/nlyd-match/upload/people/36.jpg">
            </div>
            <div class="card_detail">
                <div class="card_name c_black">leo</div>
                <div class="card_phone c_black active">18140022053</div>
            </div>
        </div>
        <div class="matching-card">
            <div class="img-box card_img">
                <img class="_img" src="http://127.0.0.1/nlyd/wp-content/plugins/nlyd-match/upload/people/36.jpg">
            </div>
            <div class="card_detail">
                <div class="card_name c_black">leo</div>
                <div class="card_phone c_black active">18140022053</div>
            </div>
        </div>
    </div>
</div>
                    <div class="a-btn a-btn-table a-btn-top" href="<?=$next_project_url?>"><div><?=__('距下一'.$title.'开赛', 'nlyd-student')?>&nbsp;&nbsp;&nbsp;&nbsp; <span class="count_down next_more_down" data-seconds="<?=$next_count_down?>">00:00:00</span></div></div>
                    <a class="a-btn a-btn-table" href="<?=$next_project_url?>"><div><?=__('跳过等待答题', 'nlyd-student')?></div></a>
                </div>
            </div>
        </div>
    </div>
</div>