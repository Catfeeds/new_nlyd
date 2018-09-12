
                    <div class="answer-zoo">
                        <div class="answerBtn">答案对比</div>
                            <div class="reading-answer">
                                <?php if(!empty($questions_answer)): ?>
                                <?php foreach ($questions_answer as $k => $val){ ?>
                                <div class="one-ques">
                                    <p class="question"><?=$k+1?>、<?=$val?></p>
                                    <p class="yours">你的答案：<span class="<?=$my_answer[$k] == $val ? 'yes' : 'error';?>"><?=$my_answer[$k]?></span></p>
                                    <p class="rights">正确答案：<?=$val?></p>
                                </div>
                                <?php } ?>
                                <?php endif; ?>
                            </div>
                    </div>
              