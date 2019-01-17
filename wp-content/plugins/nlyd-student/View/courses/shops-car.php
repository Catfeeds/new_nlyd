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
        <h1 class="mui-title"><div><?=__('购物车（3）', 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content have-bottom">
                    <div class="shops_row width-padding width-padding-pc">
                        <div  class="layui-unselect layui-form-checkbox shops_checkBox single" data-price="180" lay-skin="primary"><i class="layui-icon layui-icon-ok"></i></div>
                        <div class="dis_inlineBlock shops_img">
                            <div class="shops_img_box">
                                <img src="<?=student_css_url.'image/noInfo/noCourse1043@2x.png'?>">
                            </div>
                        </div>
                        <div class="dis_inlineBlock shops_details_right">
                            <div class="c_black fs_14">名字</div>
                            <div class="c_black6 fs_14">规格</div>
                            <div class="shops_details_bottom"><span class="c_blue fs_14">￥180.00 </span><span class="c_orange fs_14">170+10脑币</span></div>
                            <div class="shops_add_compent" data-max="10">
                                <div class="shops_num_btn dis_table reduce disabled fs_14" data-id="reduce"><div class="dis_cell">-</div></div>
                                <div class="shops_num_input fs_14"><input class="shops_focus total" type="tel" name="total" value="1"></div>
                                <div class="shops_num_btn dis_table add fs_14" data-id="add"><div class="dis_cell">+</div></div>
                            </div>
                        </div>
                    </div>

                    <div class="shops_row width-padding width-padding-pc">
                        <div  class="layui-unselect layui-form-checkbox shops_checkBox single" data-price="180" lay-skin="primary"><i class="layui-icon layui-icon-ok"></i></div>
                        <div class="dis_inlineBlock shops_img">
                            <div class="shops_img_box">
                                <img src="<?=student_css_url.'image/noInfo/noCourse1043@2x.png'?>">
                            </div>
                        </div>
                        <div class="dis_inlineBlock shops_details_right">
                            <div class="c_black fs_14">名字</div>
                            <div class="c_black6 fs_14">规格</div>
                            <div class="shops_details_bottom"><span class="c_blue fs_14">￥180.00 </span><span class="c_orange fs_14">170+10脑币</span></div>
                            <div class="shops_add_compent"  data-max="10">
                                <div class="shops_num_btn dis_table reduce disabled fs_14" data-id="reduce"><div class="dis_cell">-</div></div>
                                <div class="shops_num_input fs_14"><input class="shops_focus total" type="tel" name="total" value="1"></div>
                                <div class="shops_num_btn dis_table add fs_14" data-id="add"><div class="dis_cell">+</div></div>
                            </div>
                        </div>
                    </div>
                    <div class="shops_car_footer">
                        <div class="layui-unselect layui-form-checkbox shops_checkBox all" lay-skin="primary"><i class="layui-icon layui-icon-ok"></i></div>
                        <div class="dis_inlineBlock fs_14 c_black6 shops_all_check"><?=__('全 选', 'nlyd-student')?></div>
                        <div class="dis_inlineBlock fs_14 c_black shops_compute"><?=__('合计', 'nlyd-student')?>￥<span id="total_money">0.00</span></div>
                        <a class="dis_inlineBlock shops_settlement bg_gradient_blue c_white"><?=__('结 算', 'nlyd-student')?></a>
                    </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="_wpnonce" id="payForm" value="<?=wp_create_nonce('student_go_pay_code_nonce');?>">
<input type="hidden" name="_wpnonce" id="inputPay" value="<?=wp_create_nonce('student_get_ranking_code_nonce');?>">
<script>
jQuery(function($) { 
    // var max=100;
    // var total_money=0;//总价
    $('body').on('focusin','.shops_focus',function(){
       $('.shops_car_footer').addClass('shops_footer_rel')
    })
    $("body").on('focusout','.total',function(){
        var _this=$(this);
        var val=_this.val();
        var max=parseInt(_this.parents('.shops_add_compent').attr('data-max'));
         if(isNaN(parseInt(val)) || parseInt(val)<=0){
            _this.val('1')
         }else{
             if(val>max){
                _this.val(max)
             }else{
                _this.val(Math.ceil(val))
             }
         }

         btnActive(_this.val(),max,_this)
         getTotalPrice()
         $('.shops_car_footer').removeClass('shops_footer_rel')
    })
    function btnActive(val,max,_this) {//+-显示
        if(val<=1){
            _this.parent('div').find('.reduce').addClass('disabled');
        }else{
            _this.parent('div').find('.reduce').removeClass('disabled');
        }

        if(val<max){
            _this.parent('div').find('.add').removeClass('disabled');
        }else{
            _this.parent('div').find('.add').addClass('disabled');
        }
    }
    function numberPress(_this){
        var type=_this.attr('data-id');
        var val=_this.parent('div').find('.total').val()
        var max=parseInt(_this.parents('.shops_add_compent').attr('data-max'));
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
        _this.parent('div').find('.total').val(val)
        var newVal=_this.parent('div').find('.total').val();
        btnActive(newVal,max,_this)
        getTotalPrice()
    }
    function getTotalPrice(){//获取总价
        var total_moneys=0;
        $('.single').each(function(){
            var that=$(this);
            var price=parseInt(that.attr('data-price'));
            var num=that.parent('.shops_row').find('.total').val();
            if (that.hasClass('layui-form-checked')) {
                total_moneys+=price*num
            }
        })
        $('#total_money').text(total_moneys.toFixed(2))
        return total_moneys
    }
    function checkbox(_this){
        _this.toggleClass('layui-form-checked');
        var type=_this.hasClass('single');
        if(type){//单选
            var price=parseInt(_this.attr('data-price'));//单价
            var check=_this.hasClass('layui-form-checked');
            if(check){
                if($('.single').length==$('.single.layui-form-checked').length){//全选
                    $('.all').addClass('layui-form-checked');
                }
                // total_money+=price;
            }else{
                // total_money-=price;
                $('.all').removeClass('layui-form-checked');
            }
        }else{//全选
            var check=_this.hasClass('layui-form-checked');
            if(check){
                $('.single').each(function(){
                    var that=$(this);
                    var price=parseInt(that.attr('data-price'));
                    // total_money+=price
                })
                
                $('.layui-form-checkbox').addClass('layui-form-checked');
            }else{
                // total_money=0
                $('.layui-form-checkbox').removeClass('layui-form-checked');
            }
        }
        getTotalPrice();
        
    }
    if('ontouchstart' in window){// 移动端
        $('.shops_num_btn').each(function(){//+-
            var _this=$(this);
            new AlloyFinger(_this[0], {
                tap:function(){
                    numberPress(_this);
                }
            })
        })
        $('.layui-form-checkbox').each(function(){//复选
            var _this=$(this);
            new AlloyFinger(_this[0], {
                tap:function(){
                    checkbox(_this)
                }
            })
        })
    }else{
        $('body').on('click','.shops_num_btn',function(){//+-
            var _this=$(this);
            numberPress(_this)
        })
        $('body').on('click','.layui-form-checkbox',function(){//复选
            var _this=$(this);
            checkbox(_this)
        })
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
            // 监听提交
            $('body').on('click','.shops_settlement',function(){
                var total_money=getTotalPrice()
                if($('.layui-form-checked').length>0){
                    var _post_data={
                        order_type:3,
                        cost:total_money,
                        action:'entry_pay',
                        _wpnonce:$('#payForm').val()
                    }
                    console.log(_post_data)
                    $.ajax({
                        data:_post_data,
                        success:function(res){
                            console.log(res)
                            if(res.success){
                                //不需要支付
                                if(res.data.is_pay == 0){
                                    window.location.href=res.data.url;
                                    return false;
                                }
                                serialnumber=res.data.serialnumber;//获取订单号
                                
                                if(total_money>0){
                                    // $('.selectBottom').addClass('selectBottom-show')
                                    var content='<div class="box-conent-wrapper"><?=__("本次共需支付", "nlyd-student")?>￥'+total_money+'</div>'
                                                +'<div style="text-align:left;margin:auto;width:100px;" class="fs_14"><div id="weiChat" class="layui-unselect layui-form-checkbox layui-form-checked" lay-skin="primary"><i class="layui-icon layui-icon-ok"></i></div>&nbsp;&nbsp;&nbsp;&nbsp;<?=__('微信', 'nlyd-student')?></div>'
                                                +'<div style="text-align:left;margin:auto;width:100px;margin-top:10px" class="fs_14"><div id="zfb" class="layui-unselect layui-form-checkbox" lay-skin="primary"><i class="layui-icon layui-icon-ok"></i></div>&nbsp;&nbsp;&nbsp;&nbsp;<?=__('支付宝', 'nlyd-student')?></div>'
                                                //    +'<div style="text-align:left;margin:auto;width:100px;" class="fs_14 c_orange"><div id="visa" class="layui-unselect layui-form-checkbox" lay-skin="primary"><i class="layui-icon layui-icon-ok"></i></div>&nbsp;&nbsp;&nbsp;&nbsp;银联支付</div>'
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
                            }else{
                                // if(res.data.info=="请先实名认证"){
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
                }else{
                    $.alerts("<?=__('请选择您要结算的商品', 'nlyd-student')?>")
                }
            });
        })
    });
</script>