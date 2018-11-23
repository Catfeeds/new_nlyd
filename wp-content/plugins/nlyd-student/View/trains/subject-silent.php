
<div class="two layui-row">
    <div class="c_blue left disabled pull-left"><div><?=__('上一题', 'nlyd-student')?></div></div>
    <div class="c_blue right pull-right"><div><?=__('下一题', 'nlyd-student')?></div></div>
</div>
<?php foreach ($questions_answer as $key =>$val){

    //print_r($val);
?>
<div class="answer-zoo silent <?=$key == 0 ? 'active' : '';?>" data-index="<?=$key?>">
    <button class="matching-btn active"><?=__('你的答案', 'nlyd-student')?></button>
    <div class="your-answer layui-row">
        <?php foreach ($my_answer[$key] as $x => $y ){ ?>
        <div class="matching-number grey <?= $y!=$val[$x] ? 'active' : '';?>"><?=$y?></div>
        <?php } ?>
    </div>
    <button class="matching-btn active"><?=__('正确答案', 'nlyd-student')?></button>
    <div class="right-answer layui-row">
        <?php foreach ($val as $k => $v ){ ?>
        <div class="matching-number grey <?= $v!=$my_answer[$key][$k] ? 'active' : '';?>" ><?=$v?></div>
        <?php } ?>
    </div>
</div>
<?php } ?>
<script>
    jQuery(function($) { 
    var how_ques=$('.answer-zoo').length;//多少道题目
    var n=0;
    if(how_ques<=1){
        $('.right').addClass('disabled')
    }
    new AlloyFinger($('.left')[0], {
        touchStart: function () {
            var left=$('.left');
            if(!left.hasClass('disabled')){
                left.addClass("opacity");
            }
        },
        touchMove: function () {
            $('.left').removeClass("opacity");
        },
        touchEnd: function () {
            $('.left').removeClass("opacity");
        },
        touchCancel: function () {
            $('.left').removeClass("opacity");
        },
        tap:function(){
            var left=$('.left');
            var len=$('.answer-zoo').length-1;
            if(!left.hasClass('disabled')){
                if(n>0){
                    n--
                    $('#number').text(n+1)
                    if(n==0){
                        left.addClass('disabled')
                    }
                    $('.right').removeClass('disabled')
                    $('.answer-zoo').each(function(){
                        $(this).removeClass('active')
                        if($(this).attr('data-index')==n){
                            $(this).addClass('active')
                        }
                    })
                    
                }

            }else{
                return false;
            }
        }
    });
    // mTouch('body').on('tap','.right',function(e){//下一题
    new AlloyFinger($('.right')[0], {
        touchStart: function () {
            var right=$('.right');
            if(!right.hasClass('disabled')){
                $('.right').addClass("opacity");
            }
        },
        touchMove: function () {
            $('.right').removeClass("opacity");
        },
        touchEnd: function () {
            $('.right').removeClass("opacity");
        },
        touchCancel: function () {
            $('.right').removeClass("opacity");
        },
        tap:function(){
            var right=$('.right');
            var len=$('.answer-zoo').length-1;
            if(!right.hasClass('disabled')){
                if(n<len){
                    n++
                    $('#number').text(n+1)
                    if(n==len){
                        right.addClass('disabled')  
                    }
                    $('.left').removeClass('disabled')  
                    $('.answer-zoo').each(function(){
                        $(this).removeClass('active')
                        if($(this).attr('data-index')==n){
                            $(this).addClass('active')
                        }
                    })
                }
            }else{
                return false;
            }
        }
    });
    
});
</script>