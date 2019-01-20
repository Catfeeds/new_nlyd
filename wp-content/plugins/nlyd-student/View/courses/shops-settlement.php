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
        <h1 class="mui-title"><div><?=__('确认订单信息', 'nlyd-student')?></div></h1>
        </header>
            <div class="layui-row nl-border nl-content have-bottom">
                <form class="layui-form" lay-filter='layform'>
                    <div class="shops_row width-padding width-padding-pc">
                        <div class="dis_inlineBlock fs_14 c_black shops_settlement_label"><?=__('取货方式', 'nlyd-student')?></div>
                        <div class="dis_inlineBlock c_black6 shops_labelSettlement_info">
                            <div class="shops_zize_btn dis_table labelSettlement" data-type="normal"><div class="dis_cell">普通快递</div></div>
                            <div class="shops_zize_btn dis_table labelSettlement active" data-type="sf"><div class="dis_cell">顺丰快递</div></div>
                            <div class="shops_zize_btn dis_table labelSettlement" data-type="byself"><div class="dis_cell">自 提</div></div>
                        </div>
                    </div>
                    <a class="shops_row width-padding width-padding-pc c_black" href="<?=home_url('/account/address/');?>" id="kuaidi">
                        <div class="dis_inlineBlock shops_settlement_address">
                            <span class="c_black fs_14 mr_10">罗岚 13982242710</span><span class="c_orange fs_12"><?=__('默认地址', 'nlyd-student')?></span><br>
                            <span class="c_black8 fs_14">四川省成都市武侯区丰德万瑞中心A座2楼国际脑力运动中心 大萨达撒多</span>
                        </div>
                        <div class="dis_inlineBlock shops_settlement_arrow"><i class="iconfont fs_20">&#xe727;</i></div>
                    </a>
                    <div class="shops_row width-padding width-padding-pc c_black" style="display:none" id="byself">
                        <div class="dis_inlineBlock fs_14 c_black shops_settlement_label"><?=__('自提地址', 'nlyd-student')?></div>
                        <div class="dis_inlineBlock fs_14 c_black8 shops_labelSettlement_info"><?=__('四川省成都市武侯区丰德万瑞中心A座25楼国际脑力运动中心', 'nlyd-student')?></div>
                    </div>

                    <div class="shops_row width-padding width-padding-pc shopsSettlement">
                        <div class="dis_inlineBlock shops_img">
                            <div class="shops_img_box">
                                <img src="<?=student_css_url.'image/noInfo/noCourse1043@2x.png'?>">
                            </div>
                        </div>
                        <div class="dis_inlineBlock shops_details_right">
                            <div class="c_black fs_14">名字</div>
                            <div class="c_black6 fs_14">规格</div>
                            <div class="shops_details_bottom">
                                <span class="c_blue fs_14">￥180.00 </span>
                                <span class="c_orange fs_12">170+10脑币</span>
                                <span class="c_black fs_14 pull-right">X3</span>
                            </div>
                        </div>
                    </div>

                    <div class="shops_row width-padding width-padding-pc shopsSettlement">
                        <div class="dis_inlineBlock shops_img">
                            <div class="shops_img_box">
                                <img src="<?=student_css_url.'image/noInfo/noCourse1043@2x.png'?>">
                            </div>
                        </div>
                        <div class="dis_inlineBlock shops_details_right">
                            <div class="c_black fs_14">名字</div>
                            <div class="c_black6 fs_14">规格</div>
                            <div class="shops_details_bottom">
                                <span class="c_blue fs_14">￥180.00 </span>
                                <span class="c_orange fs_12">170+10脑币</span>
                                <span class="c_black fs_14 pull-right">X3</span>
                            </div>
                        </div>
                    </div>
                    <div class="shops_row width-padding width-padding-pc mt_10 line_bottom">
                        <div class="dis_inlineBlock fs_14 c_black6 shops_settlement_left"><?=__('快递费用', 'nlyd-student')?></div>
                        <div class="dis_inlineBlock fs_14 c_blue shops_labelSettlement_r ta_r">普通快递 ￥12.00
                        </div>
                    </div>
                    <div class="shops_row width-padding width-padding-pc line_bottom">
                        <div class="dis_inlineBlock fs_14 c_black6 shops_settlement_left">
                            <?=__('使用脑币抵扣', 'nlyd-student')?>
                        </div>
                        <!-- <div class="dis_inlineBlock fs_12 c_black3 shops_settlement_middle">
                            剩余脑币88，所有商品均可参与抵扣
                        </div> -->
                        <div class="dis_inlineBlock fs_12 c_red shops_settlement_middle">
                            剩余脑币8，无法参与抵扣
                        </div>
                        <div class="dis_inlineBlock c_black6 shops_labelSettlement_right ta_r">
                            <input type="checkbox" class="isNaobi" lay-skin="switch">
                        </div>
                    </div>
                    <div class="shops_row width-padding width-padding-pc line_bottom" style="border-bottom:none;">
                        <div class="dis_inlineBlock fs_14 c_black6 shops_settlement_left"><?=__('总计', 'nlyd-student')?></div>
                        <div class="dis_inlineBlock c_blue fs_14 shops_labelSettlement_r ta_r"> 2<?=__('件商品', 'nlyd-student')?> ￥325.00</div>
                    </div>
                    <div class="shops_car_footer">
                        <div class="dis_inlineBlock fs_14 c_black shops_settlement_money">
                            <span><?=__('实付款', 'nlyd-student')?></span>
                            <span class="c_blue">￥340.00+20脑币</span>
                        </div>
                        <a class="dis_inlineBlock shops_settlement c_white" id="submit"><?=__('提交订单', 'nlyd-student')?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="_wpnonce" id="payForm" value="<?=wp_create_nonce('student_go_pay_code_nonce');?>">
<input type="hidden" name="_wpnonce" id="inputPay" value="<?=wp_create_nonce('student_get_ranking_code_nonce');?>">
<script>
jQuery(function($) { 
    $('body').on('click','.shops_zize_btn',function(){
        var _this=$(this);
        $('.shops_zize_btn').removeClass('active');
        _this.addClass('active');
        if(_this.attr('data-type')=="byself"){//显示取货地址
            $('#kuaidi').hide()
            $('#byself').show()

        }else{//显示默认地址
            $('#byself').hide()
            $('#kuaidi').show()
        }
    })
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
    layui.use(['layer','form'], function(){
        var form = layui.form
        form.render();
        // 监听提交
        function submit(_this){//加入购物车
            var isNaobi=$('.layui-form-onswitch').length>0 ? true : false;//是否脑币抵扣
            if(!_this.hasClass('disabled')){
                var _post_data={
                    order_type:3,
                    cost:'11',
                    action:'entry_pay',
                    _wpnonce:$('#payForm').val()
                }
                console.log(_post_data)
                $.ajax({
                    data:_post_data,
                    beforeSend:function(XMLHttpRequest){
                        _this.addClass('disabled')
                    },
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
                         
                        }else{
                            if(res.data.info=="<?=__('请先实名认证', 'nlyd-student')?>"){
                                setTimeout(function(){
                                    window.location.href=window.home_url+'/courses/shopsPaySuccess/';
                                }, 1000);
                            }else{
                                $.alerts(res.data.info)
                            }
                        }
                        _this.removeClass('disabled');
                    },
                    complete: function(jqXHR, textStatus){
                        if(textStatus=='timeout'){
                            $.alerts("<?=__('网络质量差', 'nlyd-student')?>")
                            _this.removeClass('disabled');
                        }
                        
                    }
                })
            }else{
                $.alerts("<?=__('正在处理您的请求..', 'nlyd-student')?>")
            }
            return false;
        }
        if('ontouchstart' in window){// 移动端
            var _this=$("#submit");
            new AlloyFinger(_this[0], {
                tap:function(){
                    submit(_this)
                }
            })
        }else{
            $('body').on('click','#submit',function(){
                var _this=$(this);
                submit(_this)
            })
        } 
    })
});
</script>