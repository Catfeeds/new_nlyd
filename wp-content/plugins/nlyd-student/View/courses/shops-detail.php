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
                        <div class="shops_zize_btn dis_table active" data-size="1"><div class="dis_cell">规格一</div></div>
                        <div class="shops_zize_btn dis_table" data-size="2"><div class="dis_cell">规格二</div></div>
                        <div class="shops_zize_btn dis_table" data-size="3"><div class="dis_cell">规格三</div></div>
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
                            <a class="go_buy_car shops_foot_bnt dis_table" data-type="car">
                                <div class="dis_cell fs_16"><?=__('加入购物车', 'nlyd-student')?></div>
                            </a>
                        </div>
                        <div class="flex1">
                            <a class="go_buy shops_foot_bnt dis_table c_white bg_gradient_blue" data-type="buy">
                                <div class="dis_cell fs_16"><?=__('立即购买', 'nlyd-student')?></div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="_wpnonce" id="payForm" value="<?=wp_create_nonce('student_go_pay_code_nonce');?>">
<input type="hidden" name="_wpnonce" id="inputPay" value="<?=wp_create_nonce('student_get_ranking_code_nonce');?>">
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
    function submit(_this){//加入购物车
        var type=_this.attr('data-type');//加入购物车
        var total=$('#total').val();//数量
        var size=$('.shops_zize_btn.active').attr('data-size');//规格
        var _post_data={
            type:type,
            total:total,
            size:size,
            order_type:3,
            cost:'11',
            action:'entry_pay',
            _wpnonce:$('#payForm').val()
        }
        console.log(_post_data)
        $.ajax({
            data:_post_data,
            success:function(res){
                console.log(res)
                if(res.success){
                    if(type=="buy"){//立即购买
                        //不需要支付
                        if(res.data.is_pay == 0){
                            window.location.href=res.data.url;
                            return false;
                        }
                        serialnumber=res.data.serialnumber;//获取订单号
                        if(total_money>0){
                            var content='<div class="box-conent-wrapper"><?=__("本次共需支付", "nlyd-student")?>￥'+total_money+'</div>'
                                        +'<div style="text-align:left;margin:auto;width:100px;" class="fs_14"><div id="weiChat" class="layui-unselect layui-form-checkbox layui-form-checked" lay-skin="primary"><i class="layui-icon layui-icon-ok"></i></div>&nbsp;&nbsp;&nbsp;&nbsp;<?=__('微信', 'nlyd-student')?></div>'
                                        +'<div style="text-align:left;margin:auto;width:100px;margin-top:10px" class="fs_14"><div id="zfb" class="layui-unselect layui-form-checkbox" lay-skin="primary"><i class="layui-icon layui-icon-ok"></i></div>&nbsp;&nbsp;&nbsp;&nbsp;<?=__('支付宝', 'nlyd-student')?></div>'
                            layer.open({
                                type: 1
                                ,maxWidth:300
                                ,title: "<?=__('选择支付方式', 'nlyd-student')?>" //不显示标题栏
                                ,skin:'nl-box-skin'
                                ,id: 'certification' //防止重复弹出
                                ,content:content
                                ,btn: ["<?=__('取消支付', 'nlyd-student')?>", "<?=__('确认支付', 'nlyd-student')?>" ]
                                ,cancel:function(){

                                }
                                ,success: function(layero, index){
                                    
                                }
                                ,yes: function(index, layero){
                                    layer.closeAll();
                                }
                                ,btn2: function(index, layero){
                                    var id=$('.layui-form-checked').attr('id')
                                    var pay_type=''
                                    if(id=='weiChat'){//微信支付
                                        pay_type='wxh5pay'
                                    }else if(id=='zfb'){//支付宝支付
                                        pay_type='alipay'
                                    }else{
                                        pay_type=null;
                                    }

                                    var datas={
                                        action:'pay',
                                        pay_type:pay_type,
                                        _wpnonce:$('#inputPay').val(),
                                        serialnumber:serialnumber,
                                        match_id:$.Request('grad_id')
                                    }
                                    // alert(pay_type)
                                    if(pay_type){
                                        $.ajax({
                                            data:datas,success:function(response){
                                                if(response.success){
                                                    if(response.data.info){
                                                        window.location.href=response.data.info;
                                                    }else{//微信公众号支付
                                                        if(response.data.params){
                                                            prams=response.data.params;
                                                            jsApiCall()
                                                        }
                                                    }
                                                    
                                                }else{
                                                    $.alerts(response.data.info)
                                                }
                                            }
                                        })
                                    }
                                }
                                ,closeBtn:2
                                ,btnAagn: 'c' //按钮居中
                                ,shade: 0.3 //遮罩
                                ,isOutAnim:true//关闭动画
                            });
                        }else{
                            window.location.href=window.home_url+'/courses/shopsPaySuccess/';
                        }
                    }else{//加入购物车
                        alert('加入购物车')
                    }
                }else{
                    if(res.data.info=="<?=__('请先实名认证', 'nlyd-student')?>"){
                        setTimeout(function(){
                            window.location.href=window.home_url+'/courses/shopsPaySuccess/';
                        }, 1000);
                    }else{
                        $.alerts(res.data.info)
                    }

                }
            }
        })
        return false;
    }
    var serialnumber='';//订单号
    var prams=''
    function jsApiCall()
    {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest',
            prams,
            function(res){
                if(res.err_msg=='get_brand_wcpay_request:ok'){
                    window.location.href=window.home_url+'/courses/courseDetail/id/'+$.Request('id')
                }
            }
        );
    }
	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
    }
    layui.use(['layer'], function(){

      
        if('ontouchstart' in window){// 移动端
            $('.shops_num_btn').each(function(){//数字键盘
                var _this=$(this);
                new AlloyFinger(_this[0], {
                    tap:function(){
                        numberPress(_this)
                    }
                })
            })
            $('.shops_foot_bnt').each(function(){//购买｜｜加入购物车
                var _this=$(this);
                new AlloyFinger(_this[0], {
                    tap:function(){
                        submit(_this)
                    }
                })
            })
        }else{
            $('body').on('click','.shops_num_btn',function(){
                var _this=$(this);
                numberPress(_this)
            })
            $('body').on('click','.shops_foot_bnt',function(){
                var _this=$(this);
                submit(_this)
            })
        } 
    })
})
</script>