
<div class="answer-zoo">
    <button class="matching-btn active"><?=__('答案对比', 'nlyd-student')?></button>
        <?php if(!empty($grading_questions)):?>
        <div class="reading-answer">
            <?php
            $num = 0;
            foreach ($grading_questions as $k => $val){
                $answer_my = array();
                $answer_questions = array();
                ++$num;
                if(isset($my_answer[$k])){
                    $my_select = arr2str($my_answer[$k]);
                    foreach ($my_answer[$k] as $v){
                        $my_answer_select = $questions_answer[$k]['problem_select'][$v];
                        $answer_my[] = $v >= 0 && is_numeric($v) ? get_select($v).'.'.stripslashes($my_answer_select) : __('未作答', 'nlyd-student');
                    }
                }

                if(isset($questions_answer[$k])){
                    $questions_select = arr2str($questions_answer[$k]['problem_answer']);
                    foreach ($questions_answer[$k]['problem_answer'] as $x){
                        $questions_answer_select = $questions_answer[$k]['problem_select'][$x];
                        $answer_questions[] = $x >= 0 && is_numeric($x) ? get_select($x).'.'.stripslashes($questions_answer_select) : __('未作答', 'nlyd-student');
                    }
                }
            ?>
            <div class="one-ques">
                <p class="question"><?=$num?>、<?=stripslashes($val);?></p>
                <p class="yours"><?=__('你的答案', 'nlyd-student')?>:<span class="<?=$my_select == $questions_select ? 'yes' : 'error'; ?>"><?=!empty($answer_my) ? arr2str(' ',$answer_my) : __('未作答', 'nlyd-student');?></span></p>
                <p class="rights"><?=__('正确答案', 'nlyd-student')?>:<?=!empty($answer_questions) ? arr2str(' ',$answer_questions) :  __('未作答', 'nlyd-student');?></p>
            </div>
            <?php } ?>
        <?php endif;?>