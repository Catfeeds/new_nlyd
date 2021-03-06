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
                <h1 class="mui-title"><div><?=__('提 现', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content" style="padding-bottom: 333px;">
                <div class="width-padding layui-row width-margin-pc">
                    <div class="bold c_black getCash_type"><?=__('选择提现方式', 'nlyd-student')?></div>
                    <form class="layui-form ">
                        <?php if(empty($zone)){ ?>
                        <div class="getCash_type_row">
                            <input type="radio" name="extract_type" value="user_bank" title="<?=__('提现至银行卡', 'nlyd-student')?><?php if(!empty($user_cheques_bank)):?>
                            <?='('.$user_cheques_bank.')'?>
                            <?php endif;?>" checked>
                            <?php if(empty($user_cheques_bank)):?> <a class="c_red dis_inlineBlock ml_10" href="<?=home_url('/zone/settingCash/')?>">未设置</a> <?php endif;?>
                        </div>
                        <div class="getCash_type_row">
                            <input type="radio" name="extract_type" value="weChat" title="<?=__('提现至微信', 'nlyd-student')?>" >
                            <?php if(empty($user_coin_code)):?> <a class="c_red dis_inlineBlock ml_10" href="<?=home_url('/zone/settingCash/')?>">未设置</a> <?php endif;?>
                        </div>
                        <div class="getCash_type_row">
                            <input type="radio" name="extract_type" value="aliPay" title="<?=__('提现至支付宝', 'nlyd-student')?>" >
                            <?php if(empty($aliPay_coin_code)):?> <a class="c_red dis_inlineBlock ml_10" href="<?=home_url('/zone/settingCash/')?>">未设置</a> <?php endif;?>
                        </div>
                        <!--<div class="getCash_type_row">
                            <input type="radio" name="extract_type" value="wallet" title="<?/*=__('提现至平台账户钱包', 'nlyd-student')*/?>">
                        </div>-->
                        <?php }else{ ?>
                        <div class="getCash_type_row">
                            <input type="radio" name="extract_type" value="bank" title="<?=__("提现至银行卡（{$zone['opening_bank']} {$bank_card_num}）", 'nlyd-student')?>" checked>
                        </div>
                        <?php } ?>

                        <!-- <div class="c_red fs_12">
                            <?=__('*充值进平台的余额只可用于消费，无法再次提现，谨慎操作', 'nlyd-student')?>
                        </div> -->
                        <div class="enter_num">
                            <div class="danwei bold c_black fs_20">￥</div>
                            <input class="radius_input_row nl-foucs" id="num" type="text" name="num" lay-verify="number" autocomplete="off" data-max="<?=$balance?>" placeholder="<?=__('输入金额，最多可提现'.$balance, 'nlyd-student')?>">
                        </div>
                        <input type="hidden" name="action" value="user_extract_apply"/>
                        <a class="a-btn a-btn-table bg_gradient_green" lay-filter="layform" lay-submit=""><div><?=__('提 现', 'nlyd-student')?></div></a>
                    </form>
                </div>  
            </div>
        </div>            
    </div>
</div>

<script>
jQuery(function($) { 
    layui.use(['form'], function(){
        var form = layui.form;
        form.render();
        form.verify($.validationLayui.allRules);
        form.on('submit(layform)', function(data){//实名认证提交
            var _this=$(this);
            var _num=parseInt($('#num').attr('data-max'))
            if (data.field.num<=_num) {//可提现金额小于max
                if(!_this.hasClass('disabled')){
                    $.ajax({
                        data:data.field,
                        beforeSend:function(XMLHttpRequest){
                            _this.addClass('disabled')
                        },
                        success:function(res){
                            $.alerts(res.data.info);
                            if(res.success){
                                setTimeout(function () {
                                    window.location.href = res.data.url;
                                },800)
                            }else{
                                _this.removeClass('disabled');
                            }
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
            }else{
                $.alerts("<?=__('余额不足', 'nlyd-student')?>")
            }
        })
    })
})
</script>
