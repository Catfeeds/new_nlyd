<style>
@media screen and (max-width: 1199px){
    #page{
        background-color:#f6f6f6!important;
    }
}
</style>
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper">
            
        <header class="mui-bar mui-bar-nav">
        <a class="mui-pull-left nl-goback">
        <div><i class="iconfont">&#xe610;</i></div>
        </a>
        <h1 class="mui-title"><div><?=__('教辅商城', 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content">
                <!-- 轮播 -->
                <div class="swiper-container layui-bg-white" style="margin-bottom:0">
                    <div class="swiper-wrapper">
                        
                        <div class="swiper-slide">
                            <div class="swiper-content img-box"><img src="<?=student_css_url.'image/homePage/ad1.png'?>"></div>
                        </div>
                        <div class="swiper-slide">
                            <div class="swiper-content img-box"><img src="<?=student_css_url.'image/homePage/ad2.png'?>"></div>
                        </div>
                        <div class="swiper-slide">
                            <div class="swiper-content img-box"><img src="<?=student_css_url.'image/homePage/ad3.png'?>"></div>
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>

                <div class="shops_row width-padding width-padding-pc">
                    <div>
                        <div class="shops_width2 ta_l dis_inlineBlock fs_14 c_black">商品名字</div>
                        <div class="shops_width2 ta_r dis_inlineBlock fs_14">
                            <span class="c_orange">170+10脑币</span>
                            <span class="c_blue">￥180.00</span>
                        </div>
                    </div>
                    <div class="mt_10">
                        <div class="shops_width3 ta_l dis_inlineBlock fs_14 c_black">普通快递12.00</div>
                        <div class="shops_width3 ta_c dis_inlineBlock fs_14 c_black">销量1280件</div>
                        <div class="shops_width3 ta_r dis_inlineBlock fs_14 c_black">库存55件</div>
                    </div>
                </div>
                <div class="shops_row width-padding width-padding-pc">
                    <div class="dis_inlineBlock fs_14 c_black shops_label"><?=__('规 格', 'nlyd-student')?></div>
                    <div class="dis_inlineBlock c_black6 shops_label_info">
                        <div class="shops_zize_btn dis_table"><div class="dis_cell">规格一</div></div>
                        <div class="shops_zize_btn dis_table"><div class="dis_cell">规格二</div></div>
                        <div class="shops_zize_btn dis_table"><div class="dis_cell">规格三</div></div>
                    </div>
                </div>

                <div class="shops_row width-padding width-padding-pc">
                    <div class="dis_inlineBlock fs_14 c_black shops_label"><?=__('数 量', 'nlyd-student')?></div>
                    <div class="dis_inlineBlock c_black6 shops_label_info">
                        <div class="shops_num_btn dis_table" data-id="reduce"><div class="dis_cell">-</div></div>
                        <div class="shops_num_input"><input type="tel" name="total" value="1" id="total"></div>
                        <div class="shops_num_btn dis_table" data-id="add"><div class="dis_cell">+</div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(function($) { 
    var mySwiper = new Swiper('.swiper-container', {
        loop : true,
        autoplay:{
            disableOnInteraction:false
        },//可选选项，自动滑动
        autoplayDisableOnInteraction : false,    /* 注意此参数，默认为true */ 
        initialSlide :0,//初始展示页
        pagination: {
            el: '.swiper-pagination',
            dynamicBullets: true,
            dynamicMainBullets: 2,
            clickable :true,
        },
    }); 
    $('body').on('click','.shops_zize_btn',function(){
        var _this=$(this);
        $('.shops_zize_btn').removeClass('active');
        _this.addClass('active')
    })
    $("#total").focusout(function(){
        var _this=$(this);
        var val=_this.val();
         if(isNaN(parseInt(val)) || parseInt(val)<=0){
            _this.val('1')
         }
    })
    function numberPress(_this){
        var type=_this.attr('data-id');
        var val=$("#total").val();
        console.log(val)
        switch (type) {
            case 'reduce':
                val--
                break;
            case 'add':
                val++
                break;
            default:
                break;
        }
        console.log(1)
        $("#total").val(val)
    }
    if('ontouchstart' in window){// 移动端
        $('.shops_num_btn').each(function(){//数字键盘
                var _this=$(this);
                new AlloyFinger(_this[0], {
                    tap:function(){
                        numberPress(_this)
                    }
                })
            })
    }else{
        $('body').on('click','.shops_num_btn',function(){
            var _this=$(this);
            numberPress(_this)
        })
    }
})
</script>