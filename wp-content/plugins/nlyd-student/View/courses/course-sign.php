<style>
@media screen and (max-width: 1199px){
    .layui-fluid>.layui-row>.layui-bg-white:first-child,#page{
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
                <a class="mui-pull-left nl-goback nl-goback">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('课程报名信息确认', 'nlyd-student')?></div></h1>
            </header>    
            <div class="layui-row nl-border nl-content have-bottom">
                <div class="course_detail_row width-padding width-padding-pc">
                    <div class="detail_table_row"><span class="c_blue bold fs_16">课程信息</span></div>
                    <div class="detail_table_row">
                        <div class="detail_label c_black6 fs_14"><?=__('课程名称：', 'nlyd-student')?></div>
                        <div class="detail_detail c_black fs_14"><?=$course_title?>·<?=$city?></div>
                    </div>
                    <div class="detail_table_row">
                        <div class="detail_label c_black6 fs_14"><?=__('课程类型：', 'nlyd-student')?></div>
                        <div class="detail_detail c_black fs_14"><?=$category_title?></div>
                    </div>
                    <div class="detail_table_row">
                        <div class="detail_label c_black6 fs_14"><?=__('开课时间：', 'nlyd-student')?></div>
                        <div class="detail_detail c_black fs_14"><?=$start_time?></div>
                    </div>
                    <div class="detail_table_row">
                        <div class="detail_label c_black6 fs_14"><?=__('开课地点：', 'nlyd-student')?></div>
                        <div class="detail_detail c_black fs_14"><?=!empty($address) ? $address : '-';?></div>
                    </div>
                    <div class="detail_table_row">
                        <div class="detail_label c_black6 fs_14"><?=__('授课教练：', 'nlyd-student')?></div>
                        <div class="detail_detail c_black fs_14"><?=!empty($coach_name) ? $coach_name : '-';?></div>
                    </div>
                    <div class="detail_table_row">
                        <div class="detail_label c_black6 fs_14"><?=__('课程费用：', 'nlyd-student')?></div>
                        <div class="detail_detail c_blue fs_14">￥ <?=$const?></div>
                    </div>
                </div>
                <?php if($user_ID > 0):?>
                <div class="course_detail_row width-padding width-padding-pc">
                    <div class="detail_table_row"><span class="c_blue bold fs_16">学员信息</span></div>
                    <div class="detail_table_row">
                        <div class="detail_label c_black6 fs_14"><?=__('姓名：', 'nlyd-student')?></div>
                        <div class="detail_detail c_black fs_14"><?=!empty($user_name) ? $user_name : $user_mobile?></div>
                    </div>
                    <div class="detail_table_row">
                        <div class="detail_label c_black6 fs_14"><?=__('ID：', 'nlyd-student')?></div>
                        <div class="detail_detail c_black fs_14">
                            <?=$user_ID?>
                            <?php if(!empty($user_name)):?>
                            <div class="nl-match-rz img-box"><img src="<?=student_css_url.'image/confirm/rz.png'?>"></div>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
                <?php endif;?>
            </div>
            <input type="hidden" id="cost" value="<?=$const?>">
            <a class="a-btn a-btn-table go" id="goPay" lay-filter="pay-formbtn" lay-submit=""><div><?=__('确认支付'.$const, 'nlyd-student')?></div></a>
        </div>           
    </div>
</div>
<input type="hidden" name="_wpnonce" id="payForm" value="<?=wp_create_nonce('student_go_pay_code_nonce');?>">
<input type="hidden" name="_wpnonce" id="inputPay" value="<?=wp_create_nonce('student_get_ranking_code_nonce');?>">
<script>
jQuery(function($) { 
    $('body').on('click','.layui-form-checkbox',function(){
        var _this=$(this);
        $('.layui-form-checkbox').each(function(){
            var __this=$(this);
            if(__this.hasClass('layui-form-checked')){
                __this.removeClass('layui-form-checked');
            }
        })
        _this.toggleClass('layui-form-checked');
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
                    window.location.href=window.home_url+'/courses/courseDetail/center_id/'+$.Request('center_id')+'/id/'+$.Request('id')
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
    layui.use(['form'], function(){
        var form = layui.form
        form.render();
        // 自定义验证规则
        form.verify($.validationLayui.allRules); 
        // 监听提交
        form.on('submit(pay-formbtn)', function(data){
            var total=$('#cost').val();
            var _post_data={
                order_type:3,
                match_id:$.Request('id'),
                cost:total,
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
                            
                        if(total>0){
                            // $('.selectBottom').addClass('selectBottom-show')
                            var content='<div class="box-conent-wrapper"><?=__('本次共需支付', 'nlyd-student')?>￥'+total+'</div>'
                                        +'<div style="text-align:left;margin:auto;width:100px;" class="fs_14"><div id="weiChat" class="layui-unselect layui-form-checkbox layui-form-checked" lay-skin="primary"><i class="layui-icon layui-icon-ok"></i></div>&nbsp;&nbsp;&nbsp;&nbsp;<?=__('微信', 'nlyd-student')?></div>'
                                        +'<div style="text-align:left;margin:auto;width:100px;margin-top:10px" class="fs_14"><div id="zfb" class="layui-unselect layui-form-checkbox" lay-skin="primary"><i class="layui-icon layui-icon-ok"></i></div>&nbsp;&nbsp;&nbsp;&nbsp;<?=__('支付宝', 'nlyd-student')?></div>'
                                        //    +'<div style="text-align:left;margin:auto;width:100px;" class="fs_14 c_orange"><div id="visa" class="layui-unselect layui-form-checkbox" lay-skin="primary"><i class="layui-icon layui-icon-ok"></i></div>&nbsp;&nbsp;&nbsp;&nbsp;银联支付</div>'
                            layer.open({
                                type: 1
                                ,maxWidth:300
                                ,title: '<?=__('选择支付方式', 'nlyd-student')?>' //不显示标题栏
                                ,skin:'nl-box-skin'
                                ,id: 'certification' //防止重复弹出
                                ,content:content
                                ,btn: ['<?=__('取消支付', 'nlyd-student')?>', '<?=__('确认支付', 'nlyd-student')?>' ]
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
                                        match_id:$.Request('id'),
                                        center_id:$.Request('center_id')
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
                            window.location.href=window.home_url+'/courses/courseDetail/center_id/'+$.Request('center_id')+'/id/'+$.Request('id');
                        }
                    }else{
                        // if(res.data.info=="请先实名认证"){
                        if(res.data.info=="<?=__('请先实名认证', 'nlyd-student')?>"){
                            setTimeout(function(){
                                window.location.href=window.home_url+'/account/info/courses_id/'+$.Request('id');
                            }, 1000);
                        }else{
                            $.alerts(res.data.info)
                        }
                        if(res.data.url){
                            setTimeout(function(){
                                window.location.href=res.data.url;
                            }, 1000);
                        }

                    }
                }
            })
            return false;
        });
    })
})
</script>
