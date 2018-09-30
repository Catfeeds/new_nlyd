
<div class="answer-zoo">
    <div class="answerBtn">答案对比</div>
        <?php if(!empty($match_questions)):?>
        <div class="reading-answer">
            <?php
            $num = 0;
            foreach ($match_questions as $k => $val){
                $answer_my = array();
                $answer_questions = array();
                ++$num;
                if(isset($my_answer[$k])){
                    $my_select = arr2str($my_answer[$k]);
                    foreach ($my_answer[$k] as $v){
                        $my_answer_select = $questions_answer[$k]['problem_select'][$v];
                        $answer_my[] = $v >= 0 && is_numeric($v) ? get_select($v).'.'.$my_answer_select : '--';
                    }
                }

                if(isset($questions_answer[$k])){
                    $questions_select = arr2str($questions_answer[$k]['problem_answer']);
                    foreach ($questions_answer[$k]['problem_answer'] as $x){
                        $questions_answer_select = $questions_answer[$k]['problem_select'][$x];
                        $answer_questions[] = $x >= 0 && is_numeric($x) ? get_select($x).'.'.$questions_answer_select : '--';
                    }
                }
                global $current_user;
                if($current_user->ID == 63){

                    var_dump($my_select .'=='. $questions_select);
                }
            ?>
            <div class="one-ques">
                <p class="question"><?=$num?>、<?=$val?></p>
                <p class="yours">你的答案：<span class="<?=$my_answer_select == $questions_answer_select ? 'yes' : 'error'; ?>"><?=!empty($answer_my) ? arr2str(' ',$answer_my) : '未作答';?></span></p>
                <p class="rights">正确答案：<?=!empty($answer_questions) ? arr2str(' ',$answer_questions) : '--';?></p>
            </div>
            <?php } ?>
        <?php endif;?>