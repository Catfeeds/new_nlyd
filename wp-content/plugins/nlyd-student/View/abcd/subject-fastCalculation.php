
                    <div class="answer-zoo">
                        <div class="answerBtn"><?=__('答案对比', 'nlyd-student')?></div>
                        <div class="reading-answer">
                            <?php if(!empty($match_questions)): ?>
                                <?php foreach ($match_questions as $k => $val){ ?>
                                    <div class="one-ques">
                                        <p class="question"><?=$k+1?>、<?=$val?></p>
                                        <p class="yours"><?=__('你的答案', 'nlyd-student')?>：<span class="<?=$my_answer[$k] == $questions_answer[$k] ? 'yes' : 'error';?>"><?= !empty($my_answer[$k]) ? $my_answer[$k] : __('未作答', 'nlyd-student') ;?></span></p>
                                        <p class="rights"><?=__('正确答案', 'nlyd-student')?>：<?=$questions_answer[$k]?></p>
                                    </div>
                                <?php } ?>
                            <?php endif; ?>
                        </div>
                    </div>
               