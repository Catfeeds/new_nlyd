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
            <div class="layui-row nl-border nl-content have-bottom">
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
                        <div class="shops_num_btn dis_table reduce disabled" data-id="reduce"><div class="dis_cell">-</div></div>
                        <div class="shops_num_input"><input class="shops_focus" type="tel" name="total" value="1" id="total"></div>
                        <div class="shops_num_btn dis_table add" data-id="add"><div class="dis_cell">+</div></div>
                    </div>
                </div>
                <div class="shops_row width-padding width-padding-pc">
                    <div class="fs_14 c_black"><?=__('这里是商品介绍商品介绍商品介绍商品介绍商品介绍商品介绍商品介绍商品介绍', 'nlyd-student')?></div>
                </div>

                <div class="shops_detail_footer width-padding width-padding-pc">
                    <a class="shops_buy_car bg_gradient_blue dis_table c_white" href="<?=home_url('/courses/shopsCar');?>">
                        <div class="shops_car_num">1</div>
                        <i class="iconfont">&#xe63d;</i>
                    </a>
                    <div class="shops_foot_bnts flex-h">
                        <div class="flex1">
                            <a class="go_buy_car shops_foot_bnt dis_table c_blue">
                                <div class="dis_cell fs_16"><?=__('加入购物车', 'nlyd-student')?></div>
                            </a>
                        </div>
                        <div class="flex1">
                            <a class="go_buy shops_foot_bnt dis_table c_white bg_gradient_blue">
                                <div class="dis_cell fs_16"><?=__('立即购买', 'nlyd-student')?></div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(function($) { 
    var max=100;
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
    $('body').on('focusin','.shops_focus',function(){
       $('.shops_detail_footer').addClass('shops_footer_rel')
    })
    $('body').on('focusout','.shops_focus',function(){
        var _this=$(this);
        var val=_this.val();
         if(isNaN(parseInt(val)) || parseInt(val)<=0){
            _this.val('1')
         }else{
             if(val>max){
                _this.val(max)
             }else{
                _this.val(Math.ceil(val))
             }
         }
         btnActive(_this.val(),max)
        $('.shops_detail_footer').removeClass('shops_footer_rel')
    })
    $('body').on('click','.shops_zize_btn',function(){
        var _this=$(this);
        $('.shops_zize_btn').removeClass('active');
        _this.addClass('active')
    })
    // $(".shops_focus").focusout(function(){
    //     var _this=$(this);
    //     var val=_this.val();
    //      if(isNaN(parseInt(val)) || parseInt(val)<=0){
    //         _this.val('1')
    //      }else{
    //          if(val>max){
    //             _this.val(max)
    //          }else{
    //             _this.val(Math.ceil(val))
    //          }
    //      }
    //      btnActive(_this.val(),max)
    // })
    function btnActive(val,max) {
        if(val<=1){
            $('.reduce').addClass('disabled');
        }else{
            $('.reduce').removeClass('disabled');
        }

        if(val<max){
            $('.add').removeClass('disabled');
        }else{
            $('.add').addClass('disabled');
        }
    }
    function numberPress(_this){
        var type=_this.attr('data-id');
        var val=$("#total").val();
        console.log(val)
        switch (type) {
            case 'reduce':
                if(val>1){
                    val--
                }
                break;
            case 'add':
            if(val<max){
                val++
            }
            
                break;
            default:
                break;
        }
        $("#total").val(val)
        btnActive($("#total").val(),max)
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