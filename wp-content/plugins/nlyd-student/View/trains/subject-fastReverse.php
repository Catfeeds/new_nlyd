
                    <div class="answer-zoo">
                        <button class="matching-btn active"><?=__('答案对比', 'nlyd-student')?></button>
                            <div class="reading-answer">
                                <?php if(!empty($match_questions)): ?>
                                <?php foreach ($match_questions as $k => $val){ ?>
                                <div class="one-ques">
                                    <p class="question"><?=$k+1;?>、<?=__('运算数字', 'nlyd-student')?>:<?=arr2str($val,'、')?></p>
                                    <p class="yours"><?=__('你的答案', 'nlyd-student')?>:<span class="<?=$answer_array[$k] == 'true' ? 'yes' : 'error';?>"><?= !empty($my_answer[$k]) ? $my_answer[$k] : __('未作答', 'nlyd-student') ;?></span></p>
                                    <p class="rights"><?=__('正确示例', 'nlyd-student')?>:<?=!empty($questions_answer[$k]) ? $questions_answer[$k] : '本题无解'?></p>
                                </div>
                                <?php } ?>
                                <?php endif;?>
                            </div>
                        </div>
                   