
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_public_view.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper">
        <header class="mui-bar mui-bar-nav">
            <?php echo (isset($_GET['match_id']) ? '<a class="mui-pull-left nl-goback">' : '<a class="mui-pull-left nl-goback static" href="'.home_url('/account/info').'">'); ?>
        <i class="iconfont">&#xe610;</i>
        </a>
        <h1 class="mui-title">收货地址管理</h1>
        </header>
            <div class="layui-row nl-border nl-content">

                    <?php if(!empty($lists)){
                        foreach ($lists as $val){
                    ?>
                    <div class="address-row width-margin width-margin-pc"
                    <?php if(isset($_GET['match_id'])){?>
                        href="<?=home_url('/matchs/confirm/match_id/'.$_GET['match_id'].'/address_id/'.$val['id']);?>"
                    <?php } ?>
                    >
                        <div class="address-left">
                            <div class="address-title">
                                <span class="accept-name"><?=$val['fullname']?></span>
                                <span class="phone-number"><?=$val['telephone']?></span>
                                <span data-id="<?=$val['id']?>" class="default-address <?=$val['is_default'] != 1 ? '':'set-address';?>"><?=$val['is_default'] != 1 ? '设为默认':'默认地址'?></span>
                            </div>
                            <p class="address-detail"><?=$val['user_address']?></p>
                        </div>
                        <div  class="address-right">
                            <a class="address-btn edit" href="<?=home_url('/account/addAddress/address_id/'.$val['id']);?>">修改</a>
                            <div class="address-btn del" data-id="<?=$val['id']?>">删除</div>
                        </div>
                    </div>
                    <?php  } }else{ ?>
                        <p class="no-info">您未设置收货地址</p>
                    <?php } ?>
                <?php
                $add_url = home_url('/account/addAddress');
                if(isset($_GET['match_id'])) $add_url .= '/match_id/'.$_GET['match_id'];
                ?>
                <a class="a-btn" href="<?=$add_url;?>">新 增</a>
            </div>
        </div>           
    </div>
</div>
<!-- 删除地址 -->
<input type="hidden" name="_wpnonce" id="delAddress" value="<?=wp_create_nonce('student_remove_address_code_nonce');?>">
<!-- 设置默认地址 -->
<input type="hidden" name="_wpnonce" id="defaultAddress" value="<?=wp_create_nonce('student_set_default_code_nonce');?>">

<script>
jQuery(function($) { 
layui.use(['layer'], function(){
    $('.address-row').on('click','.default-address',function(){//设置默认地址
        var _this=$(this);
        if(!_this.hasClass('set-address')){
                layer.open({
                type: 1
                ,maxWidth:300
                ,title: '提示' //不显示标题栏
                ,skin:'nl-box-skin'
                ,id: 'certification' //防止重复弹出
                ,content: '<div class="box-conent-wrapper">是否确认设为默认地址</div>'
                ,btn: ['再想想', '确认', ]
                ,success: function(layero, index){
                    
                }
                ,yes: function(index, layero){
                    layer.closeAll();
                }
                ,btn2: function(index, layero){
                    var postData={
                        action:'set_default_address',
                        _wpnonce:$("#defaultAddress").val(),
                        id:_this.attr('data-id')
                    }
                    $.post(window.admin_ajax,postData,function(res){
                        $.alerts(res.data.info)
                        if(res.success){
                            $('.default-address.set-address').text('设为默认').removeClass('set-address');
                            _this.text('默认地址').addClass('set-address');
                        }    
                    })
                }
                ,closeBtn:2
                ,btnAagn: 'c' //按钮居中
                ,shade: 0.3 //遮罩
                ,isOutAnim:true//关闭动画
            });
        }
        return false;
    })
    $('body').on('click','.del',function(){//删除地址
        var _this=$(this);
        layer.open({
            type: 1
            ,maxWidth:300
            ,title: '提示' //不显示标题栏
            ,skin:'nl-box-skin'
            ,id: 'certification' //防止重复弹出
            ,content: '<div class="box-conent-wrapper">是否确认删除地址</div>'
            ,btn: ['再想想', '确认', ]
            ,success: function(layero, index){
                
            }
            ,yes: function(index, layero){
                layer.closeAll();
            }
            ,btn2: function(index, layero){
                var postData={
                    action:'remove_address',
                    _wpnonce:$("#delAddress").val(),
                    id:_this.attr('data-id')
                }
                console.log(postData)
                $.post(window.admin_ajax,postData,function(res){
                    console.log(res)
                    $.alerts(res.data.info)
                    if(res.success){
                        _this.parents('.address-row').remove()
                    }    
                })
            }
            ,closeBtn:2
            ,btnAagn: 'c' //按钮居中
            ,shade: 0.3 //遮罩
            ,isOutAnim:true//关闭动画
        });
        return false;
    })
})
$('body').on('click','.address-row',function(){
    if($(this).attr('href')){
        window.location.href=$(this).attr('href')
    }
})
$('.address-row').each(function(i){//左滑动
    var _this=$(this)
    var dom=$(this)[0]
    var hammertime = new Hammer(dom);
    hammertime.on("swipeleft", function (e) {
        _this.addClass('swipeleft')
    });
})
$('.address-row').each(function(i){//右滑动
    var _this=$(this)
    var dom=$(this)[0]
    var hammertime = new Hammer(dom);
    hammertime.on("swiperight", function (e) {
        _this.removeClass('swipeleft')
    });
})

})
</script>