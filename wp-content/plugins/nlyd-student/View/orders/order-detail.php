<style>
@media screen and (max-width: 1199px){
    #page{
        background-color:#f6f6f6!important;
    }
}
</style>
<div class="layui-fluid">
    <div class="layui-row">
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('订单详情', 'nlyd-student')?></div></h1>
            </header>
            <div class="layui-row nl-border nl-content have-bottom">
                <a class="orderDetail_title bg_gradient_blue dis_table" href="<?=home_url('orders/logistics')?>">
                    <span class="c_white fs_16 dis_cell">交易成功(待支付,等待发货,等待收货)&nbsp;&nbsp;&nbsp;&nbsp;2017-07-24 13:20<br>物流信息&nbsp;&nbsp;&nbsp;&nbsp;已签收</span>
                    <span class="c_white dis_cell"><i class="iconfont c_white fs_22">&#xe64f;</i></span>
                </a>
                <div class="layui-row width-padding width-padding-pc goods_row mt_0 dis_table">
                    <span class="dis_cell c_black">罗岚&nbsp;&nbsp;&nbsp;&nbsp;13982242710<br>四川省成都市武侯区丰德万瑞中心A座2楼国际脑力运动中心</span>
                </div>
               <div class="layui-row width-padding width-padding-pc goods_row">
                    <div class="order-img img-box pull-left">
                        <img src="<?=student_css_url.'image/noInfo/noOrder1096@2x.png'?>">
                    </div>
                    <div class="order-detail pull-left">
                        <p class="order-name">2018脑力世界杯总决赛（重庆）报名</p>
                        <p class="order-content">脑力世界杯是一年一度的国际大赛，汇聚了海内外很多脑力健将</p>
                    </div>
                    <div class="order-right-info  pull-left">
                        <p class="order-price">￥0.00</p><p class="order-price">x1</p>
                    </div>
                </div>
                <div class="layui-row width-padding width-padding-pc goods_row">
                    <div>
                        <span class="c_black">实际支付</span>
                        <span class="c_blue pull-right">￥392.00</span>
                    </div>
                    <div>
                        <span>商品总额</span>
                        <span class="pull-right">+￥392.00</span>
                    </div>
                    <div>
                        <span>商品运费</span>
                        <span class="pull-right">+￥392.00</span>
                    </div>
                    <div>
                        <span>脑币抵扣</span>
                        <span class="pull-right">-￥1.00</span>
                    </div>
                </div>
                <div class="layui-row width-padding width-padding-pc goods_row">
                    <div>
                        <span class="c_black">订单编号</span>
                        <span class="c_blue pull-right">566546546505471707</span>
                    </div>
                    <div>
                        <span>创建时间</span>
                        <span class="pull-right">2018-07-04 13:50</span>
                    </div>
                    <div>
                        <span>支付时间</span>
                        <span class="pull-right"><span class="c_blue">微信支付</span> 2018-07-04 13:50</span>
                    </div>
                    <div>
                        <span>完成时间</span>
                        <span class="pull-right">2018-07-04 13:50</span>
                    </div>
                </div>
                <!-- 交易成功(已发货) -->
                <div class="details_btn flex-h">
                    <div class="details-button flex1">
                        <button class="see_button" type="button" class="">删除订单</button>
                    </div>
                    <div class="details-button flex1 last-btn">
                        <button class="see_button" type="button" href="<?=home_url('orders/logistics')?>">查看物流</button>
                    </div>
                </div>
                <!-- 交易成功(快件派送中) -->
                <!-- <div class="details_btn flex-h">
                    <div class="details-button flex1">
                        <button class="see_button" type="button" class="">删除订单</button>
                    </div>
                    <div class="details-button flex1 last-btn">
                        <button class="see_button" type="button" href="<?=home_url('orders/logistics')?>">查看物流</button>
                    </div>
                </div> -->
                 <!-- 交易成功(已签收) -->
                <!-- <div class="details_btn flex-h">
                    <div class="details-button flex1 last-btn">
                        <button class="see_button" type="button" class="">删除订单</button>
                    </div>
                </div> -->
                <!-- 待发货 -->
                <!-- <div class="details_btn flex-h">
                    <div class="details-button flex1 last-btn">
                        <button class="see_button" type="button" class="">提醒发货</button>
                    </div>
                </div> -->
                <!-- 待支付 -->
                <!-- <div class="details_btn flex-h">
                    <div class="details-button flex1">
                        <button class="see_button" type="button" class="">取消订单</button>
                    </div>
                    <div class="details-button flex1 last-btn">
                        <button class="see_button" type="button">支付订单</button>
                    </div>
                </div> -->
               
            </div>
        </div>  

    </div>
</div>

<script>
jQuery(function($) {
    $('.see_button').click(function(){
        var href=$(this).attr('href');
        if(typeof(href)!==undefined){
            window.location.href=href;
        }
    })

})
</script>
