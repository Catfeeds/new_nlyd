
                    <div class="answer-zoo">
                        <div class="answerBtn">答案对比</div>
                        <div class="reading-answer">
                            <?php if(!empty($match_questions)): ?>
                                <?php foreach ($match_questions as $k => $val){ ?>
                                    <div class="one-ques">
                                        <p class="question"><?=$k+1?>、<?=$val?></p>
                                        <p class="yours">你的答案：<span class="<?=$my_answer[$k] == $questions_answer[$k] ? 'yes' : 'error';?>"><?= !empty($my_answer[$k]) ? $my_answer[$k] : '未作答' ;?></span></p>
                                        <p class="rights">正确答案：<?=$questions_answer[$k]?></p>
                                    </div>
                                <?php } ?>
                            <?php endif; ?>
                        </div>
                    </div>
               