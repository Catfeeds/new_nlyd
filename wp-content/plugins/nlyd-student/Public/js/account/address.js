
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
                        $.ajax({
                            data: postData,success:function(res,ajaxStatu,xhr){
                                $.alerts(res.data.info)
                                if(res.success){
                                    $('.default-address.set-address').text('设为默认').removeClass('set-address');
                                    _this.text('默认地址').addClass('set-address');
                                }
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
                    $.ajax({
                        data: postData,success:function(res,ajaxStatu,xhr){
                            $.alerts(res.data.info)
                            if(res.success){
                                _this.parents('.address-row').remove()
                            }
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
    // $('.address-row').each(function(i){//左滑动
    //     var _this=$(this)
    //     var dom=$(this)[0]
    //     var hammertime = new Hammer(dom);
    //     hammertime.on("swipeleft", function (e) {
    //         _this.addClass('swipeleft')
    //     });
    // })
    mTouch('body').on('swipeleft', '.address-row', function (e) {
        var _this=$(this)
        _this.addClass('swipeleft')
    })
    mTouch('body').on('swiperight', '.address-row', function (e) {
        var _this=$(this)
        _this.removeClass('swipeleft')
    })
    // $('.address-row').each(function(i){//右滑动
    //     var _this=$(this)
    //     var dom=$(this)[0]
    //     var hammertime = new Hammer(dom);
    //     hammertime.on("swiperight", function (e) {
    //         _this.removeClass('swipeleft')
    //     });
    // })

})