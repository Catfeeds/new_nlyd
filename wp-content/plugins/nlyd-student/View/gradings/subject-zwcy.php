<div class="answer-zoo">
    <button class="matching-btn active"><?=__('你的答案', 'nlyd-student')?></button>
    <div class="your-answer layui-row">
        <?php if(!empty($my_answer)):?>
            <?php foreach ($my_answer as $k => $v){ ?>
                <div class="matching-number-match-word <?=in_array($k,$error_arr) ? 'active' : ''?>"><span><?=$v?></span></div>
            <?php } ?>
        <?php endif;?>
        <!--<div class="matching-number-match-word active">1</div>
        <div class="matching-number-match-word">22</div>
        <div class="matching-number-match-word">22</div>
        <div class="matching-number-match-word">22</div>
        <div class="matching-number-match-word">22</div>
        <div class="matching-number-match-word active">55</div>-->
        <!-- <div class="matching-number grey active">8</div> -->
    
    </div>
    <button class="matching-btn active"><?=__('正确答案', 'nlyd-student')?></button>
    <div class="right-answer layui-row">
        <?php if(!empty($questions_answer)):?>
            <?php foreach ($questions_answer as $key => $val){ ?>
                <div class="matching-number-match-word <?=in_array($key,$error_arr) ? 'active' : ''?> "><span><?=$val?></span></div>
            <?php } ?>
        <?php endif;?>
        <!--<div class="matching-number-match-word active">22</div>
        <div class="matching-number-match-word">22</div>
        <div class="matching-number-match-word">22</div>
        <div class="matching-number-match-word">22</div>
        <div class="matching-number-match-word">22</div>
        <div class="matching-number-match-word active">56</div>-->
        <!-- <div class="matching-number grey active">9</div> -->
    </div>
</div>