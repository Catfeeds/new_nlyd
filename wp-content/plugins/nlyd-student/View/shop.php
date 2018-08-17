
<div class="layui-fluid">
    <div class="layui-row">
        <?php
                require_once leo_student_left_path.'leftMenu.php';
            
        ?>


        <div class="nl-right-content layui-col-sm12 layui-col-xs12 detail-content-wrapper">
            
        <header class="mui-bar mui-bar-nav">
        <a class="mui-pull-left nl-goback">
        <i class="iconfont">&#xe610;</i>
        </a>
        <h1 class="mui-title">教辅商城</h1>
        </header>
            <div class="layui-row nl-border nl-content layui-bg-white">
                <!-- <div class="title-img img-box">
                    <img src="<?=$user_info['user_head'];?>">
                </div> -->
                <!-- 轮播 -->
                <div class="swiper-container layui-bg-white">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="swiper-content img-box"><img src="<?=$user_info['user_head'];?>"></div>
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
                <div class="width-margin width-margin-pc">
                    <div class="shop-search layui-row">
                        <i class="iconfont searchIcon">&#xe63b;</i>
                        <input type="text" class="serachInput" placeholder="搜索商品">
                        <a class="shopCar" href="<?=home_url('/student/account/order');?>">
                            <span class="layui-badge">16</span>
                            <i class="iconfont">&#xe63d;</i>
                        </a>
                    </div>

                    <a class="goods-row layui-row">
                        <div class="goods-left-img img-box">
                            <img src="">
                        </div>
                        <div class="goods-right-info">
                            <div class="goods-wrap">
                                
                                <div class="goods-name">商品名字</div>
                                <div class="goods-price">
                                    <span class="nl-dark-blue">￥ 180.00</span>
                                    <span class="orange-color">170+10脑币</span>
                                    <i class="iconfont addShopCar pull-right">&#xe673;</i>
                                </div>
                                <div class="goods-detail">商品介绍商品介绍商品介绍商品介绍商品介绍商品介绍商品介绍商品介绍商品介绍商品介绍商品介绍</div>
                        
                            </div>
                        </div>
                    </a>

                    <a class="goods-row layui-row">
                        <div class="goods-left-img img-box">
                            <img src="">
                        </div>
                        <div class="goods-right-info">
                            <div class="goods-wrap">
                                <div class="goods-name">商品名字</div>
                                <div class="goods-price">
                                    <span class="nl-dark-blue">￥ 180.00</span>
                                    <span class="orange-color">170+10脑币</span>
                                    <i class="iconfont addShopCar pull-right">&#xe673;</i>
                                </div>
                                <div class="goods-detail">商品介绍商品介绍商品介绍商品介绍商品介绍商品介绍商品介绍商品介绍商品介绍商品介绍商品介绍</div>
                            </div>
                        </div>
                    </a>
                </div>

                    <!-- <div class="no-info-page">
                        <div class="no-info-img">
                            <img src="<?=student_css_url.'image/noInfo/noMessage1040@2x.png'?>">
                        </div>
                        <p class="no-info-text">没有商品信息</p>
                    </div>               -->
            </div>
        </div>
    </div>
</div>

<script>
jQuery(function($) { 
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
layui.use(['element','flow','layer'], function(){
    var element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
    var flow = layui.flow;//流加载

 //--------------------分页--------------------------
    // flow.load({
    //     elem: '#flow-zoo' //流加载容器
    //     ,scrollElem: '#flow-zoo' //滚动条所在元素，一般不用填，此处只是演示需要。
    //     ,isAuto: false
    //     ,isLazyimg: true
    //     ,done: function(page, next){//加载下一页
    //             var postData={
    //                 action:'get_coach_lists',
    //                 category_id:category_id,
    //                 page:page,
    //                 user_id:user_id,
    //             }
    //             var lis = [];
    //             $.post(window.admin_ajax,postData,function(res){
    //                     if(res.success){
    //                         $.each(res.data.info,function(i,v){
    //                             var dom=
    //                             lis.push(dom) 
    //                         })
    //                         if (res.data.info.length<10) {
    //                             next(lis.join(''),false) 
    //                         }else{
    //                             next(lis.join(''),true) 
    //                         }
    //                     }else{
    //                         if(page==1){
    //                             var dom='<tr><td colspan="7">无教练信息</td></tr>'
    //                             lis.push(dom) 
    //                         }else{
    //                             $.alerts('没有更多了')
    //                         }
    //                         next(lis.join(''),false)
    //                     }
    //         })       
    //     }
    // });
});
 //--------------------分页-------------------------- 

})
</script>