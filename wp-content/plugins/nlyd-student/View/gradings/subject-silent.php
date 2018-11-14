
<div class="layui-fluid">
    <div class="layui-row">
     
        <div class="nl-right-content layui-col-lg8 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                    <a class="mui-pull-left nl-goback"><div><i class="iconfont">&#xe610;</i></div></a>
             
                <h1 class="mui-title"><div><?=__('答题记录', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content ">
                <div class="width-margin">
                    <div class="answer-zoo slient active" data-index="0">
                        <button class="matching-btn active"><?=__('你的答案', 'nlyd-student')?></button>
                        <div class="your-answer layui-row">
                            <div class="matching-number grey active">v</div>
                        </div>
                        <button class="matching-btn active"><?=__('正确答案', 'nlyd-student')?></button>
                        <div class="right-answer layui-row">
                            <div class="matching-number grey active">爱</div>
                        </div>
                    </div>
                    <div class="answer-zoo slient" data-index="1">
                        <button class="matching-btn active"><?=__('你的答案', 'nlyd-student')?></button>
                        <div class="your-answer layui-row">
                            <div class="matching-number grey active">1</div>
                        </div>
                        <button class="matching-btn active"><?=__('正确答案', 'nlyd-student')?></button>
                        <div class="right-answer layui-row">
                            <div class="matching-number grey active">2</div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>
<div class="a-btn two">
    <div class="a-two left disabled"><div><?=__('上一题', 'nlyd-student')?></div></div>
    <div class="a-two right"><div><?=__('下一题', 'nlyd-student')?></div></div>
</div>
<script>
    jQuery(function($) { 
    var how_ques=$('.answer-zoo').length;//多少道题目
    var n=0;
    if(how_ques<=1){
        $('.a-two.right').addClass('disabled')
    }
    new AlloyFinger($('.a-two.left')[0], {
        touchStart: function () {
            var left=$('.a-two.left');
            if(!left.hasClass('disabled')){
                left.addClass("opacity");
            }
        },
        touchMove: function () {
            $('.a-two.left').removeClass("opacity");
        },
        touchEnd: function () {
            $('.a-two.left').removeClass("opacity");
        },
        touchCancel: function () {
            $('.a-two.left').removeClass("opacity");
        },
        tap:function(){
            var left=$('.a-two.left');
            var len=$('.answer-zoo').length-1;
            if(!left.hasClass('disabled')){
                if(n>0){
                    n--
                    $('#number').text(n+1)
                    if(n==0){
                        left.addClass('disabled')
                    }
                    $('.a-two.right').removeClass('disabled')
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
    // mTouch('body').on('tap','.a-two.right',function(e){//下一题
    new AlloyFinger($('.a-two.right')[0], {
        touchStart: function () {
            var right=$('.a-two.right');
            if(!right.hasClass('disabled')){
                $('.a-two.right').addClass("opacity");
            }
        },
        touchMove: function () {
            $('.a-two.right').removeClass("opacity");
        },
        touchEnd: function () {
            $('.a-two.right').removeClass("opacity");
        },
        touchCancel: function () {
            $('.a-two.right').removeClass("opacity");
        },
        tap:function(){
            var right=$('.a-two.right');
            var len=$('.answer-zoo').length-1;
            if(!right.hasClass('disabled')){
                if(n<len){
                    n++
                    $('#number').text(n+1)
                    if(n==len){
                        right.addClass('disabled')  
                    }
                    $('.a-two.left').removeClass('disabled')  
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