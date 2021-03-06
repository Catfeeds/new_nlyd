
<div class="two layui-row">
    <div class="c_grey lefts disabled pull-left"><div><?=__('上一题', 'nlyd-student')?></div></div>
    <div class="c_blue rights pull-right"><div><?=__('下一题', 'nlyd-student')?></div></div>
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
</div>
<script>
    jQuery(function($) { 
    var how_ques=$('.answer-zoo').length;//多少道题目
    var n=0;
    if(how_ques<=1){
        $('.rights').addClass('disabled').removeClass('c_blue').addClass('c_grey');
    }
    function left() {
        var left=$('.lefts');
        var len=$('.answer-zoo').length-1;
        if(!left.hasClass('disabled')){
            if(n>0){
                n--
                $('#number').text(n+1)
                if(n==0){
                    left.addClass('disabled').removeClass('c_blue').addClass('c_grey')
                }
                $('.rights').removeClass('disabled').removeClass('c_grey').addClass('c_blue')
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
    function right() {
        var right=$('.rights');
        var len=$('.answer-zoo').length-1;
        if(!right.hasClass('disabled')){
            if(n<len){
                n++
                $('#number').text(n+1)
                if(n==len){
                    right.addClass('disabled').removeClass('c_blue').addClass('c_grey')
                }
                $('.lefts').removeClass('disabled').removeClass('c_grey').addClass('c_blue')
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
    if('ontouchstart' in window){// 移动端
        new AlloyFinger($('.lefts')[0], {
            touchStart: function () {
                var left=$('.lefts');
                if(!left.hasClass('disabled')){
                    left.addClass("opacity");
                }
            },
            touchMove: function () {
                $('.lefts').removeClass("opacity");
            },
            touchEnd: function () {
                $('.lefts').removeClass("opacity");
            },
            touchCancel: function () {
                $('.lefts').removeClass("opacity");
            },
            tap:function(){
                left()
            }
        });
        new AlloyFinger($('.rights')[0], {
            touchStart: function () {
                var right=$('.rights');
                if(!right.hasClass('disabled')){
                    $('.rights').addClass("opacity");
                }
            },
            touchMove: function () {
                $('.rights').removeClass("opacity");
            },
            touchEnd: function () {
                $('.rights').removeClass("opacity");
            },
            touchCancel: function () {
                $('.rights').removeClass("opacity");
            },
            tap:function(){
                right()
            }
        });
    }else{
        $('body').on('click','.lefts',function(){
            left()
        })
        $('body').on('click','.rights',function(){
            right()
        })
    }
});
</script>