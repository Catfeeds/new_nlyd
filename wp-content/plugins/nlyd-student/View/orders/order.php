<?php
/**
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/7/16
 * Time: 17:41
 */

?>
<div class="layui-fluid">
    <div class="layui-row">
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback">
                    <i class="iconfont">&#xe610;</i>
                </a>
                <h1 class="mui-title">我的订单</h1>
            </header>
            <?php if($row){?>

            <?php }else{ ?>
                <!-- <div class="layui-row nl-border nl-content layui-bg-gray">
                    <div class="no-info-page">
                        <div class="no-info-img">
                            <img src="<?=student_css_url.'image/noInfo/noOrder1096@2x.png'?>">
                        </div>
                        <p class="no-info-text">暂无订单</p>
                    </div>
                </div> -->
                <div class="layui-row nl-border nl-content">
                    <div class="width-margin width-margin-pc">
                        <div class="search-warapper">
                            <div class="search-zoo">
                                <i class="iconfont search-Icon">&#xe63b;</i>
                                <input type="text" class="serach-Input" placeholder="搜索商品">
                            </div>
                        </div>
                        <div class="layui-tab layui-tab-brief" lay-filter="tabs">
                            <ul style="margin-left: 0" class="layui-tab-title">
                                <li class="layui-this" data-id="all">全部(8)</li>
                                <li data-id="apply">待支付(1)</li>
                                <li data-id="send">待发货(1)</li>
                                <li data-id="accept">待收货(1)</li>
                                <div class="nl-transform">全部(8)</div>
                            </ul>
                            <div class="layui-tab-content">
                                <!-- 全部 -->
                                <div class="layui-tab-item layui-show flow-default" id="all">
                                    <div class="order-row layui-row">
                                        <div class="order-title layui-row width-padding width-padding-pc">
                                            <span class="pull-left">完成时间：2017-07-24 13:20</span>
                                            <span class="pull-right">交易成功</span>
                                        </div>
                                        <div class="order-body layui-row  width-padding width-padding-pc">
                                            <div class="order-body-top layui-row">
                                                <div class="order-img img-box pull-left">
                                                    <img src="<?=student_css_url.'image/noInfo/noOrder1096@2x.png'?>">
                                                </div>
                                                <div class="order-detail pull-left">
                                                    <p class="order-name">2018脑力世界杯总决赛（重庆）报名</p>
                                                    <p class="order-content">脑力世界杯是一年一度的国际大赛，汇聚了海内外很多脑力健将</p>
                                                </div>
                                                <div class="order-right-info  pull-left">
                                                    <p class="order-price">￥380.00</p>
                                                    <p class="order-price">x1</p>
                                                </div>
                                            </div>
                                            <p class="order-body-bottom">    
                                                共1件商品 实际支付: ￥155.00
                                            </p>
                                        </div>
                                        <div class="order-footer layui-row width-padding width-padding-pc">
                                            <a href="" class="order-right-btn blue">查看详情</a>
                                            <div class="order-left-btn">删除订单</div>
                                        </div>
                                    </div>

                                     <div class="order-row layui-row">
                                        <div class="order-title layui-row width-padding width-padding-pc">
                                            <span class="pull-left">支付时间：2017-07-24 13:20</span>
                                            <span class="pull-right">交易关闭</span>
                                        </div>
                                        <div class="order-body layui-row  width-padding width-padding-pc">
                                            <div class="order-body-top layui-row">
                                                <div class="order-img img-box pull-left">
                                                    <img src="<?=student_css_url.'image/noInfo/noOrder1096@2x.png'?>">
                                                </div>
                                                <div class="order-detail pull-left">
                                                    <p class="order-name">2018脑力世界杯总决赛（重庆）报名</p>
                                                    <p class="order-content">脑力世界杯是一年一度的国际大赛，汇聚了海内外很多脑力健将</p>
                                                </div>
                                                <div class="order-right-info  pull-left">
                                                    <p class="order-price">￥380.00</p>
                                                    <p class="order-price">x1</p>
                                                </div>
                                            </div>
                                            <p class="order-body-bottom">    
                                                共1件商品 实际支付: ￥155.00
                                            </p>
                                        </div>
                                        <div class="order-footer layui-row width-padding width-padding-pc">
                                            <div class="order-left-btn">删除订单</div>
                                        </div>
                                    </div>
                                </div> 
                                <!-- 待支付 -->
                                <div class="layui-tab-item flow-default" id="apply">
                                    <div class="order-row layui-row">
                                        <div class="order-title layui-row width-padding width-padding-pc">
                                            <span class="pull-left">创建时间：2017-07-24 13:20</span>
                                            <span class="pull-right orange">待支付</span>
                                        </div>
                                        <div class="order-body layui-row  width-padding width-padding-pc">
                                            <div class="order-body-top layui-row">
                                                <div class="order-img img-box pull-left">
                                                    <img src="<?=student_css_url.'image/noInfo/noOrder1096@2x.png'?>">
                                                </div>
                                                <div class="order-detail pull-left">
                                                    <p class="order-name">2018脑力世界杯总决赛（重庆）报名</p>
                                                    <p class="order-content">脑力世界杯是一年一度的国际大赛，汇聚了海内外很多脑力健将</p>
                                                </div>
                                                <div class="order-right-info  pull-left">
                                                    <p class="order-price">￥380.00</p>
                                                    <p class="order-price">x1</p>
                                                </div>
                                            </div>
                                            <p class="order-body-bottom">    
                                                共1件商品 实际支付: ￥155.00
                                            </p>
                                        </div>
                                        <div class="order-footer layui-row width-padding width-padding-pc">
                                            <a href="" class="order-right-btn blue">支付订单</a>
                                            <div class="order-left-btn">取消订单</div>
                                        </div>
                                    </div>

                                     <div class="order-row layui-row">
                                        <div class="order-title layui-row width-padding width-padding-pc">
                                            <span class="pull-left">创建时间：2017-07-24 13:20</span>
                                            <span class="pull-right orange">待支付</span>
                                        </div>
                                        <div class="order-body layui-row  width-padding width-padding-pc">
                                            <div class="order-body-top layui-row">
                                                <div class="order-img img-box pull-left">
                                                    <img src="<?=student_css_url.'image/noInfo/noOrder1096@2x.png'?>">
                                                </div>
                                                <div class="order-detail pull-left">
                                                    <p class="order-name">2018脑力世界杯总决赛（重庆）报名</p>
                                                    <p class="order-content">脑力世界杯是一年一度的国际大赛，汇聚了海内外很多脑力健将</p>
                                                </div>
                                                <div class="order-right-info  pull-left">
                                                    <p class="order-price">￥380.00</p>
                                                    <p class="order-price">x1</p>
                                                </div>
                                            </div>
                                            <p class="order-body-bottom">    
                                                共1件商品 实际支付: ￥155.00
                                            </p>
                                        </div>
                                        <div class="order-footer layui-row width-padding width-padding-pc">
                                            <a href="" class="order-right-btn blue">支付订单</a>
                                            <div class="order-left-btn">取消订单</div>
                                        </div>
                                    </div>
                                </div> 
                                <!-- 代发货 -->
                                <div class="layui-tab-item flow-default" id="send">
                                    <div class="order-row layui-row">
                                        <div class="order-title layui-row width-padding width-padding-pc">
                                            <span class="pull-left">支付时间：2017-07-24 13:20</span>
                                            <span class="pull-right blue">代发货</span>
                                        </div>
                                        <div class="order-body layui-row  width-padding width-padding-pc">
                                            <div class="order-body-top layui-row">
                                                <div class="order-img img-box pull-left">
                                                    <img src="<?=student_css_url.'image/noInfo/noOrder1096@2x.png'?>">
                                                </div>
                                                <div class="order-detail pull-left">
                                                    <p class="order-name">2018脑力世界杯总决赛（重庆）报名</p>
                                                    <p class="order-content">脑力世界杯是一年一度的国际大赛，汇聚了海内外很多脑力健将</p>
                                                </div>
                                                <div class="order-right-info  pull-left">
                                                    <p class="order-price">￥380.00</p>
                                                    <p class="order-price">x1</p>
                                                </div>
                                            </div>
                                            <p class="order-body-bottom">    
                                                共1件商品 实际支付: ￥155.00
                                            </p>
                                        </div>
                                        <div class="order-footer layui-row width-padding width-padding-pc">
                                            <a href="" class="order-right-btn blue">查看详情</a>
                                            <div class="order-left-btn">提醒发货</div>
                                        </div>
                                    </div>

                                    <div class="order-row layui-row">
                                        <div class="order-title layui-row width-padding width-padding-pc">
                                            <span class="pull-left">支付时间：2017-07-24 13:20</span>
                                            <span class="pull-right blue">代发货</span>
                                        </div>
                                        <div class="order-body layui-row  width-padding width-padding-pc">
                                            <div class="order-body-top layui-row">
                                                <div class="order-img img-box pull-left">
                                                    <img src="<?=student_css_url.'image/noInfo/noOrder1096@2x.png'?>">
                                                </div>
                                                <div class="order-detail pull-left">
                                                    <p class="order-name">2018脑力世界杯总决赛（重庆）报名</p>
                                                    <p class="order-content">脑力世界杯是一年一度的国际大赛，汇聚了海内外很多脑力健将</p>
                                                </div>
                                                <div class="order-right-info  pull-left">
                                                    <p class="order-price">￥380.00</p>
                                                    <p class="order-price">x1</p>
                                                </div>
                                            </div>
                                            <p class="order-body-bottom">    
                                                共1件商品 实际支付: ￥155.00
                                            </p>
                                        </div>
                                        <div class="order-footer layui-row width-padding width-padding-pc">
                                            <a href="" class="order-right-btn blue">查看详情</a>
                                            <div class="order-left-btn">提醒发货</div>
                                        </div>
                                    </div>
                                </div> 
                                <!-- 待收货 -->
                                <div class="layui-tab-item flow-default" id="accept">
                                    <div class="order-row layui-row">
                                        <div class="order-title layui-row width-padding width-padding-pc">
                                            <span class="pull-left">发货时间：2017-07-24 13:20</span>
                                            <span class="pull-right blue">待收货</span>
                                        </div>
                                        <div class="order-body layui-row  width-padding width-padding-pc">
                                            <div class="order-body-top layui-row">
                                                <div class="order-img img-box pull-left">
                                                    <img src="<?=student_css_url.'image/noInfo/noOrder1096@2x.png'?>">
                                                </div>
                                                <div class="order-detail pull-left">
                                                    <p class="order-name">2018脑力世界杯总决赛（重庆）报名</p>
                                                    <p class="order-content">脑力世界杯是一年一度的国际大赛，汇聚了海内外很多脑力健将</p>
                                                </div>
                                                <div class="order-right-info  pull-left">
                                                    <p class="order-price">￥380.00</p>
                                                    <p class="order-price">x1</p>
                                                </div>
                                            </div>
                                            <p class="order-body-bottom">    
                                                共1件商品 实际支付: ￥155.00
                                            </p>
                                        </div>
                                        <div class="order-footer layui-row width-padding width-padding-pc">
                                            <a href="" class="order-right-btn blue">确认收货</a>
                                            <div class="order-left-btn ml-20">查看物流</div>
                                            <div class="order-left-btn">查看详情</div>
                                        </div>
                                    </div>
                                </div> 
                            </div> 
                        </div>    
                    </div>   
                </div>
            <?php } ?>
        </div>  

    </div>
</div>

<script>
jQuery(function($) {
layui.use(['element','flow'], function(){
    var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
    var flow = layui.flow;//流加载
    var isClick={all:true,apply:false,send:false,accept:false}
    flow.load({
        elem: '#all'
        ,scrollElem: '#all'
        ,isAuto: false
        ,isLazyimg: true
        ,done: function(page, next){ //加载下一页
            var postData={
                action:'getOrderList',
                page:page,
            }
            var lis = [];
            $.post(window.admin_ajax+"?date="+new Date().getTime(),postData,function(res,ajaxStatu,xhr){
                console.log(res)
                if(res.success){
                    $.each(res.data.info,function(i,v){
                        var order_type=v.order_type;
                        var dom='<div class="order-row layui-row">'
                                    +'<div class="order-title layui-row width-padding width-padding-pc">'
                                        +'<span class="pull-left">发货时间：2017-07-24 13:20</span>'
                                        +'<span class="pull-right blue">待收货</span>'
                                    +'</div>'
                                    +'<div class="order-body layui-row  width-padding width-padding-pc">'
                                        +'<div class="order-body-top layui-row">'
                                            +'<div class="order-img img-box pull-left">'
                                                +'<img src="<?=student_css_url.'image/noInfo/noOrder1096@2x.png'?>">'
                                            +'</div>'
                                            +'<div class="order-detail pull-left">'
                                                +'<p class="order-name">2018脑力世界杯总决赛（重庆）报名</p>'
                                                +'<p class="order-content">脑力世界杯是一年一度的国际大赛，汇聚了海内外很多脑力健将</p>'
                                            +'</div>'
                                            +'<div class="order-right-info  pull-left">'
                                                +'<p class="order-price">￥380.00</p>'
                                                +'<p class="order-price">x1</p>'
                                            +'</div>'
                                        +'</div>'
                                        +'<p class="order-body-bottom">'    
                                            +'共1件商品 实际支付: ￥155.00'
                                        +'</p>'
                                    +'</div>'
                                    +'<div class="order-footer layui-row width-padding width-padding-pc">'
                                        +'<a href="" class="order-right-btn blue">确认收货</a>'
                                        +'<div class="order-left-btn ml-20">查看物流</div>'
                                        +'<div class="order-left-btn">查看详情</div>'
                                    +'</div>'
                                +'</div>'
                        lis.push(dom) 
                    })
                    if (res.data.info.length<10) {
                        next(lis.join(''),false) 
                    }else{
                        next(lis.join(''),true) 
                    }
                    
                }else{
                    if(page==1){
                        var dom='<div class="no-info">无新闻信息</div>'
                        lis.push(dom) 
                    }else{
                        $.alerts('没有更多了')
                    }
                    next(lis.join(''),false)
                }
            })       
        }
    });
    element.on('tab(tabs)', function(){//tabs
        var left=$(this).position().left+parseInt($(this).css('marginLeft'));
        var html=$(this).html();
        var data_id=$(this).attr('data-id')
        $('.nl-transform').css({
            'transform':'translate3d('+left+'px, 0px, 0px)'
        }).html(html)

        if(!isClick[data_id]){
            flow.load({
                elem: '#'+data_id
                ,scrollElem: '#'+data_id
                ,isAuto: false
                ,isLazyimg: true
                ,done: function(page, next){ //加载下一页
                    var postData={
                        action:'getOrderList',
                        page:page,
                    }
                    var lis = [];
                    $.post(window.admin_ajax+"?date="+new Date().getTime(),postData,function(res,ajaxStatu,xhr){
                        console.log(res)
                        isClick[data_id]=true
                        if(res.success){
                            $.each(res.data.info,function(i,v){
                                
                                var dom='<div class="order-row layui-row">'
                                            +'<div class="order-title layui-row width-padding width-padding-pc">'
                                                +'<span class="pull-left">发货时间：2017-07-24 13:20</span>'
                                                +'<span class="pull-right blue">待收货</span>'
                                            +'</div>'
                                            +'<div class="order-body layui-row  width-padding width-padding-pc">'
                                                +'<div class="order-body-top layui-row">'
                                                    +'<div class="order-img img-box pull-left">'
                                                        +'<img src="<?=student_css_url.'image/noInfo/noOrder1096@2x.png'?>">'
                                                    +'</div>'
                                                    +'<div class="order-detail pull-left">'
                                                        +'<p class="order-name">2018脑力世界杯总决赛（重庆）报名</p>'
                                                        +'<p class="order-content">脑力世界杯是一年一度的国际大赛，汇聚了海内外很多脑力健将</p>'
                                                    +'</div>'
                                                    +'<div class="order-right-info  pull-left">'
                                                        +'<p class="order-price">￥380.00</p>'
                                                        +'<p class="order-price">x1</p>'
                                                    +'</div>'
                                                +'</div>'
                                                +'<p class="order-body-bottom">'    
                                                    +'共1件商品 实际支付: ￥155.00'
                                                +'</p>'
                                            +'</div>'
                                            +'<div class="order-footer layui-row width-padding width-padding-pc">'
                                                +'<a href="" class="order-right-btn blue">确认收货</a>'
                                                +'<div class="order-left-btn ml-20">查看物流</div>'
                                                +'<div class="order-left-btn">查看详情</div>'
                                            +'</div>'
                                        +'</div>'
                                lis.push(dom) 
                            })
                            if (res.data.info.length<10) {
                                next(lis.join(''),false) 
                            }else{
                                next(lis.join(''),true) 
                            }
                            
                        }else{
                            if(page==1){
                                var dom='<div class="no-info">无新闻信息</div>'
                                lis.push(dom) 
                            }else{
                                $.alerts('没有更多了')
                            }
                            next(lis.join(''),false)
                        }
                    })       
                }
            });
        }
    });
})

})
</script>
