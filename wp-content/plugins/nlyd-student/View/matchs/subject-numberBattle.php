
                    <div class="answer-zoo">
                        <div class="answerBtn">你的答案</div>
                        <div class="your-answer layui-row">
                            <?php if(!empty($my_answer)):?>
                            <?php foreach ($my_answer as $k => $v){ ?>
                            <div class="matching-number grey <?=in_array($k,$error_arr) ? 'active' : ''?>"><?=$v?></div>
                            <?php } ?>
                            <?php endif;?>
                        </div>
                        <div class="answerBtn">正确答案</div>
                        <div class="right-answer layui-row">
                            <?php if(!empty($questions_answer)):?>
                                <?php foreach ($questions_answer as $key => $val){ ?>
                                    <div class="matching-number grey <?=in_array($key,$error_arr) ? 'active' : ''?> "><?=$val?></div>
                                <?php } ?>
                            <?php endif;?>
                        </div>
                    </div>