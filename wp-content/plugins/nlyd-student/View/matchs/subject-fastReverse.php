
                    <div class="answer-zoo">
                        <div class="answerBtn">答案对比</div>
                            <div class="reading-answer">
                                <?php if(!empty($match_questions)): ?>
                                <?php foreach ($match_questions as $k => $val){ ?>
                                <div class="one-ques">
                                    <p class="question"><?=$k+1;?>、运算数字：<?=arr2str($val,'、')?></p>
                                    <p class="yours">你的答案：<span class="<?=$answer_array[$k] == 'true' ? 'yes' : 'error';?>"><?= !empty($my_answer[$k]) ? $my_answer[$k] : '' ;?></span></p>
                                    <p class="rights">正确示例：<?=!empty($questions_answer[$k]) ? $questions_answer[$k] : '本题无解'?></p>
                                </div>
                                <?php } ?>
                                <?php endif;?>
                            </div>
                        </div>
                   