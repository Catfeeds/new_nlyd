<div class="answer-zoo">
    <button class="matching-btn active"><?=__('你的答案', 'nlyd-student')?></button>
    <div class="your-answer layui-row">
        <?php if(!empty($my_answer)):?>
            <?php foreach ($my_answer as $k => $v){ ?>
                <div class="matching-number-match-word <?=in_array($k,$error_arr) ? 'active' : ''?>"><span><?=$v?></span></div>
            <?php } ?>
        <?php endif;?>
    </div>
    <button class="matching-btn active"><?=__('正确答案', 'nlyd-student')?></button>
    <div class="right-answer layui-row">
        <?php if(!empty($questions_answer)):?>
            <?php foreach ($questions_answer as $key => $val){ ?>
                <div class="matching-number-match-word <?=in_array($key,$error_arr) ? 'active' : ''?> "><span><?=$val?></span></div>
            <?php } ?>
        <?php endif;?>
    </div>
</div>