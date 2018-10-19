


                    <div class="answer-zoo">
                        <div class="answerBtn"><?=__('你的答案', 'nlyd-student')?></div>
                        <div class="answers">
                            <div class="porker-zoo">
                                <div class="poker-window">
                                <?php if(!empty($my_answer)): ?>
                                    <div class="poker-wrapper your-answer first_wap">
                                        <?php foreach ($my_answer as $k => $v ){
                                            $val = str2arr($v,'-');
                                            switch ($val[0]){
                                                case 'club':
                                                    $ico = '&#xe635;';
                                                    break;
                                                case 'heart':
                                                    $ico = '&#xe638;';
                                                    break;
                                                case 'spade':
                                                    $ico = '&#xe636;';
                                                    break;
                                                case 'diamond':
                                                    $ico = '&#xe634;';
                                                    break;
                                            }
                                            ?>
                                            <div class="poker <?=$val[0]?> <?= in_array($k,$error_arr) ? 'active' : '';?>">
                                                <div class="poker-detail poker-top">
                                                    <div class="poker-name"><?=$val[1]?></div>
                                                    <div class="poker-type"><i class="iconfont"><?=$ico?></i></div>
                                                </div>
                                                <div class="poker-logo">
                                                    <img src="<?=student_css_url.'image/nlyd-big.png'?>">
                                                </div>
                                                <div class="poker-detail poker-bottom">
                                                    <div class="poker-name"><?=$val[1]?></div>
                                                    <div class="poker-type"><i class="iconfont"><?=$ico?></i></div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php endif;?>
                                <?php if(!empty($questions_answer)): ?>
                                    <div class="poker-wrapper right-answer second_wap">
                                        <?php foreach ($questions_answer as $k => $v ){
                                                $val = str2arr($v,'-');
                                                switch ($val[0]){
                                                    case 'club':
                                                        $ico = '&#xe635;';
                                                        break;
                                                    case 'heart':
                                                        $ico = '&#xe638;';
                                                        break;
                                                    case 'spade':
                                                        $ico = '&#xe636;';
                                                        break;
                                                    case 'diamond':
                                                        $ico = '&#xe634;';
                                                        break;
                                                }
                                        ?>
                                            <div class="poker <?=$val[0]?> <?= in_array($k,$error_arr) ? 'active' : '';?>">
                                                <div class="poker-detail poker-top">
                                                    <div class="poker-name"><?=$val[1]?></div>
                                                    <div class="poker-type"><i class="iconfont"><?=$ico?></i></div>
                                                </div>
                                                <div class="poker-logo">
                                                    <img src="<?=student_css_url.'image/nlyd-big.png'?>">
                                                </div>
                                                <div class="poker-detail poker-bottom">
                                                    <div class="poker-name"><?=$val[1]?></div>
                                                    <div class="poker-type"><i class="iconfont"><?=$ico?></i></div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <?php endif;?>
                            </div>
                        </div>
                        <div class="answerBtn porkerAnswer"><?=__('正确答案', 'nlyd-student')?></div>
                    </div>
               