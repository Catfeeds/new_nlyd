<div class="answer-zoo">
    <button class="matching-btn active"><?=__('你的答案', 'nlyd-student')?></button>
    <div class="your-answer layui-row">
        <?php if(!empty($my_answer)):?>
            <?php foreach ($my_answer as $k => $v){ ?>
                <div class="matching-card">
                    <div class="img-box card_img">
                        <img class="_img" src="<?=leo_match_url.'/upload/people/'.$v['picture'].'.jpg';?>">
                    </div>
                    <div class="card_detail">
                        <div class="card_name c_black <?=$v['name'] == $questions_answer[$k]['name'] ? '' : 'active';?>" ><?=empty($v['name']) ? '-' : $v['name']?></div>
                        <div class="card_phone c_black <?=$v['phone'] == $questions_answer[$k]['phone'] ? '' : 'active';?>" ><?=empty($v['phone']) ? '-' : $v['phone']?></div>
                    </div>
                </div>
            <?php } ?>
        <?php endif;?>
    </div>
    <button class="matching-btn active"><?=__('正确答案', 'nlyd-student')?></button>
    <div class="right-answer layui-row">
        <?php if(!empty($questions_answer)):?>
            <?php foreach ($questions_answer as $key => $val){ ?>
                <div class="matching-card">
                    <div class="img-box card_img">
                        <img class="_img" src="<?=leo_match_url.'/upload/people/'.$val['picture'].'.jpg';?>">
                    </div>
                    <div class="card_detail">
                        <div class="card_name c_black"><?=empty($val['name']) ? '-' : $val['name']?></div>
                        <div class="card_phone c_black"><?=empty($val['phone']) ? '-' : $val['phone']?></div>
                    </div>
                </div>
            <?php } ?>
        <?php endif;?>
    </div>
</div>
<script>
    jQuery(function($) { 
        var width=$('.matching-card').width()
        $('._img').height(width).width(width)
    })
</script>