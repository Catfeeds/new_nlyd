
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
        <h1 class="mui-title"><div>教辅商城</div></h1>
        </header>
            <div class="layui-row nl-border nl-content layui-bg-white">
                <!-- <div class="title-img img-box">
                    <img src="<?=$user_info['user_head'];?>">
                </div> -->
                <!-- 轮播 -->
                <div class="swiper-container layui-bg-white">
                    <div class="swiper-wrapper">
                        <!-- <div class="swiper-slide">
                            <div class="swiper-content img-box"><img src="<?=$user_info['user_head'];?>"></div>
                        </div> -->
                        
                        <div class="swiper-slide">
                            <div class="swiper-content img-box"><img src="<?=student_css_url.'image/homePage/ad1.png'?>"></div>
                        </div>
                        <div class="swiper-slide">
                            <div class="swiper-content img-box"><img src="<?=student_css_url.'image/homePage/ad2.png'?>"></div>
                        </div>
                        <div class="swiper-slide">
                            <div class="swiper-content img-box"><img src="<?=student_css_url.'image/homePage/ad3.png'?>"></div>
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
                <div class="width-margin width-margin-pc">
                    <div class="shop-search layui-row">
                        <i class="iconfont searchIcon">&#xe63b;</i>
                        <input type="text" class="serachInput" placeholder="搜索商品">
                        <a class="shopCar" href="<?=home_url('orders');?>">
                            <span class="layui-badge">16</span>
                            <i class="iconfont">&#xe63d;</i>
                        </a>
                    </div>
                    <?php foreach ($rows as $row){ ?>
                        <a class="goods-row layui-row">
                            <div class="goods-left-img img-box">
                                <img src="<?=empty($row['images']) ? '' : $row['images'][0]?>">
                            </div>
                            <div class="goods-right-info">
                                <div class="goods-wrap">

                                    <div class="goods-name"><?=$row['goods_title']?></div>
                                    <div class="goods-price">
                                        <span class="nl-dark-blue">￥ <?php echo ($row['price']+$row['brain']);?></span>
                                        <br />
                                        <span class="orange-color"><?=$row['price']?>+<?=$row['brain']?>脑币</span>
                                        <i class="iconfont addShopCar pull-right">&#xe673;</i>
                                    </div>
                                    <div class="goods-detail"><?=$row['goods_intro']?></div>

                                </div>
                            </div>
                        </a>

                    <?php } ?>

                    <a class="goods-row layui-row">
                        <div class="goods-left-img img-box">
                            <img src="">
                        </div>
                        <div class="goods-right-info">
                            <div class="goods-wrap">
                                <div class="goods-name">商品名字</div>
                                <div class="goods-price">
                                    <span class="c_blue">￥ 180.00</span>
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
});
 //--------------------分页-------------------------- 

})
</script>