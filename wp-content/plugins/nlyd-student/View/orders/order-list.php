<div class="layui-fluid">
    <div class="layui-row">
        <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper">
            <header class="mui-bar mui-bar-nav">
                <a class="mui-pull-left nl-goback">
                    <div><i class="iconfont">&#xe610;</i></div>
                </a>
                <h1 class="mui-title"><div><?=__('我的订单', 'nlyd-student')?></div></h1>
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
                                <input type="text" class="serach-Input" placeholder="<?=__('搜索商品', 'nlyd-student')?>">
                            </div>
                        </div>
                        <div class="layui-tab layui-tab-brief" lay-filter="tabs">
                            <ul style="margin-left: 0" class="layui-tab-title">
                                <li class="layui-this" data-id="10"><?=__('全部', 'nlyd-student')?></li>
                                <li data-id="1"><?=__('待支付', 'nlyd-student')?></li>
                                <li data-id="2"><?=__('待发货', 'nlyd-student')?></li>
                                <li data-id="3"><?=__('待收货', 'nlyd-student')?></li>
                                <div class="nl-transform"><?=__('全部', 'nlyd-student')?></div>
                            </ul>
                            <div class="layui-tab-content">
                                <!-- 全部 -->
                                <div class="layui-tab-item layui-show flow-default" id="10">
                                    <!-- <div class="order-row layui-row">
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
                                    </div> -->
                                </div> 
                                <!-- 待支付 -->
                                <div class="layui-tab-item flow-default" id="1">
                                    <!-- <div class="order-row layui-row">
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
                                    </div> -->
                                </div> 
                                <!-- 代发货 -->
                                <div class="layui-tab-item flow-default" id="2">
                                    <!-- <div class="order-row layui-row">
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
                                    </div> -->
                                </div> 
                                <!-- 待收货 -->
                                <div class="layui-tab-item flow-default" id="3">
                                    <!-- <div class="order-row layui-row">
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
                                    </div> -->
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
    var isClick={'10':true,'1':false,'2':false,'3':false}
    function pagation(data_id) {
        flow.load({
                elem: '#'+data_id
                ,isAuto: false
                ,isLazyimg: true
                ,done: function(page, next){ //加载下一页
                    var postData={
                        action:'getOrderList',
                        page:page,
                        pay_status:data_id,//10全部,1待支付,2待发货,3待收货,4已完成,-1待退款,-2已退款
                    }
                    var lis = [];
                    console.log(postData)
                    $.ajax({
                        data:postData
                        ,success:function(res,ajaxStatu,xhr){  
                            console.log(res)
                            isClick[data_id]=true
                            if(res.success){
                                $.each(res.data.info,function(i,v){
                                    var list_info={}
                                    if(v.pay_status=="1"){//1待支付
                                        list_info['time']="<?=__('创建时间', 'nlyd-student')?>";
                                        list_info['statu_color']="c_orange";
                                        list_info['btn_html']='<div href="" class="order-right-btn c_blue"><?=__('支付订单', 'nlyd-student')?></div>'
                                                                +'<div class="order-left-btn"><?=__('取消订单', 'nlyd-student')?></div>';
                                    }else if(v.pay_status=="2"){//2待发货
                                        list_info['time']="<?=__('支付时间', 'nlyd-student')?>";
                                        list_info['statu_color']="c_blue";
                                        list_info['btn_html']='<a href="<?=home_url('orders/details')?>" class="order-right-btn c_blue"><?=__('查看详情', 'nlyd-student')?></a>'
                                                            +'<div class="order-left-btn"><?=__('提醒发货', 'nlyd-student')?></div>';
                                    }else if(v.pay_status=="3"){//3待收货
                                        list_info['time']="<?=__('发货时间', 'nlyd-student')?>";
                                        list_info['statu_color']="c_blue"
                                        list_info['btn_html']='<a href="" class="order-right-btn c_blue"><?=__('确认收货', 'nlyd-student')?></a>'
                                                            +'<a  href="<?=home_url('orders/logistics')?>" class="order-left-btn ml-20"><?=__('查看物流', 'nlyd-student')?></a>'
                                                            +'<a class="order-left-btn" href="<?=home_url('orders/details')?>"><?=__('查看详情', 'nlyd-student')?></a>';
                                    }else if(v.pay_status=="4"){//4已完成
                                        list_info['time']="<?=__('完成时间', 'nlyd-student')?>";
                                        list_info['statu_color']="c_black6"
                                        list_info['btn_html']='<a class="order-left-btn" href="<?=home_url('orders/details')?>"><?=__('查看详情', 'nlyd-student')?></a>';
                                    }
                                    var dom='<div class="order-row layui-row">'
                                                +'<div class="order-title layui-row width-padding width-padding-pc">'
                                                    +'<span class="pull-left">'+list_info['time']+'：'+v.created_time+'</span>'
                                                    +'<span class="pull-right '+list_info['statu_color']+'">'+v.pay_status_title+'</span>'
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
                                                            +'<p class="order-price">￥'+v.cost+'</p>'
                                                            +'<p class="order-price">x1</p>'
                                                        +'</div>'
                                                    +'</div>'
                                                    +'<p class="order-body-bottom">'    
                                                        +'共1件商品 实际支付: ￥'+v.cost
                                                    +'</p>'
                                                +'</div>'
                                                +'<div class="order-footer layui-row width-padding width-padding-pc">'
                                                +list_info['btn_html']
                                                +'</div>'
                                            +'</div>'
                                    lis.push(dom) 
                                })
                                if (res.data.info.length<50) {
                                    next(lis.join(''),false) 
                                }else{
                                    next(lis.join(''),true) 
                                }
                                
                            }else{
                                next(lis.join(''),false)
                            }
                        }
                    })       
                }
            });
    }
    
    var layid = location.hash.replace(/^#matchList=/, '');
    pagation(layid)
        if(layid.length>0){
            $('.layui-tab-title li').each(function(){
                var _this=$(this)
                var lay_id=_this.attr('data-id');
                if(lay_id==layid){
                    setTimeout(function() {
                        _this.click()
                    }, 200);
                    return false
                }
            })
        }
    element.on('tab(tabs)', function(){//tabs
        location.hash = 'matchList='+ $(this).attr('data-id');
        var left=$(this).position().left+parseInt($(this).css('marginLeft'));
        var html=$(this).html();
        var data_id=$(this).attr('data-id')
        $('.nl-transform').css({
            'transform':'translate3d('+left+'px, 0px, 0px)'
        }).html(html)

        if(!isClick[data_id]){
            pagation(data_id)
        }
    });
})

})
</script>
