<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package presscore
 * @since presscore 0.1
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

$config = Presscore_Config::get_instance();
$config->set( 'template', 'blog' );
$config->set( 'layout', 'list' );
$config->set( 'template.layout.type', 'list' );
$config->set( 'post.preview.media.width', 30 );
$banners = get_option('index_banner_url');
//最新咨询
$which_cat = get_category_by_slug('news');
$recentPosts = new WP_Query();
$cat_query = $recentPosts->query('showposts=1&cat='.$which_cat->cat_ID.'&paged=1');
//var_dump($cat_query);

get_header(); ?>

<?php require_once PLUGINS_PATH.'nlyd-student/View/public/student-footer-menu.php' ;?>

<div class="layui-fluid">
    <div class="layui-row">
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper have-footer">
            <header class="mui-bar mui-bar-nav layui-bg-white">
                <div class="search-zoo">
                    <i class="iconfont search-Icon">&#xe63b;</i>
                    <input type="text" class="serach-Input nl-foucs" placeholder="搜索名录/课程/教练等">
                </div>
            </header>
            <div class="layui-row nl-border nl-content  layui-bg-white">
                <!-- 头部导航 -->
                <div class="layui-row width-padding">
                    <div class="top-nav">
                        <div class="top-nav-btn active"><a href="<?=home_url();?>">首 页</a></div>
                        <div class="top-nav-btn"><a  href="<?=home_url('/student/account/directory');?>">名 录</a></div>
                        <div class="top-nav-btn"><a>课 程</a></div>
                        <div class="top-nav-btn"><a href="<?=home_url('/student/account/shop');?>">商 城</a></div>
                        <div class="top-nav-btn"><a>公 益</a></div>
                    </div>
                </div>
                <!-- 轮播 -->
                <div class="swiper-container layui-bg-white">
                    <div class="swiper-wrapper">
                        <?php
                        if($banners) {
                            foreach ($banners as $banner) {
                        ?>
                                <div class="swiper-slide">
                                    <div class="swiper-content img-box"><img src="<?=$banner?>"></div>
                                </div>
                        <?php
                            }
                        }
                        ?>

<!--                        <div class="swiper-slide">-->
<!--                            <div class="swiper-content img-box"><img src="--><?//=student_css_url.'image/homePage/ad2.png'?><!--"></div>-->
<!--                        </div>-->
<!--                        <div class="swiper-slide">-->
<!--                            <div class="swiper-content img-box"><img src="--><?//=student_css_url.'image/homePage/ad3.png'?><!--"></div>-->
<!--                        </div>-->
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
                <!-- 入口 -->
                <div class="layui-row width-padding  layui-bg-white">
                    <div class="entrance">
                        <div class="entrance-wrap">
                            <a class="index-btn">
                                <div class="img-btn img-box">
                                    <img src="<?=student_css_url.'image/homePage/for-us-big.png'?>">
                                </div>
                                <div class="entrance-name">关于我们</div>
                            </a>
                        </div>
                        <div class="entrance-wrap">
                            <a class="index-btn" href="<?=home_url('student/account/system')?>">
                                <div class="img-btn img-box">
                                    <img src="<?=student_css_url.'image/homePage/sys-big.png'?>">
                                </div>
                                <div class="entrance-name">体系标准</div>
                            </a>
                        </div>
                        <div class="entrance-wrap">
                            <a class="index-btn"   href="<?=home_url('/student/account/concatUs');?>">
                                <div class="img-btn img-box">
                                    <img src="<?=student_css_url.'image/homePage/concat-big.png'?>">
                                </div>
                                <div class="entrance-name">合作联系</div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="layui-row pt-10 layui-bg-gray">
                    <!-- 广告 -->
                    <a class="nl-ad layui-row img-box layui-bg-white">
                        <img src="<?=student_css_url.'image/homePage/ad-big.png'?>">
                    </a>
                    <!-- 课程 资讯 -->
                    <div class="layui-row">
                        <div class="head-info layui-row width-padding">
                            <span class="pull-left">推荐课程</span>
                            <span class="pull-right">推荐课程 <i class="iconfont">&#xe640;</i></span>
                        </div>
                        <div class="nl-ad-row layui-bg-white  width-padding">
                            <div class="layui-row foot-info">
                                <div class="layui-col-lg5 layui-col-md5 layui-col-sm5 layui-col-xs5 img-box">
                                    <img src="<?=student_css_url.'image/homePage/course1.png'?>">
                                </div>
                                <div class="layui-col-lg7 layui-col-md7 layui-col-sm7 layui-col-xs7">
                                    <p class="nl-ad-name">课程名称</p>
                                    <p class="nl-ad-tips">抢占名额中(2/18)</p>
                                    <div class="nl-ad-detail">这里是课程介绍摘要，截取课程简要介绍，不超过30个字符</div>
                                </div>
                            </div>
                        </div>


                        <div class="head-info layui-row  width-padding">
                            <span class="pull-left">最新资讯</span>
                            <span class="pull-right" onclick="window.location.href='<?= home_url('news/'); ?>'">全部资讯 <i class="iconfont">&#xe640;</i></span>
                        </div>
                        <?php
                        if(!empty($cat_query)) {
                            foreach ($cat_query as $cat){
                        ?>
                        <div class="nl-ad-row layui-bg-white  width-padding" onclick="window.location.href='<?= $cat->guid ?>'">
                            <div class="layui-row foot-info">
                                <div class="layui-col-lg5 layui-col-md5 layui-col-sm5 layui-col-xs5 img-box">
                                    <img src="<?= wp_get_attachment_image_src(get_post_thumbnail_id($cat->ID), 'thumbnail')[0] ?>">
                                </div>
                                <div class="layui-col-lg7 layui-col-md7 layui-col-sm7 layui-col-xs7">
                                    <p class="nl-ad-name"><?= $cat->post_title ?></p>
                                    <div class="nl-ad-detail"><?= msubstr(strip_tags($cat->post_content),0,35) ?></div>
                                </div>
                            </div>
                        </div>                
                        <?php
                                }
                            }
                        ?>
                    </div>
                </div>
                <!-- 视频 -->
                <div class="layui-row">
                    <a class="img-box">
                        <img src="<?=student_css_url.'image/homePage/sp.png'?>">
                    </a>
                </div>

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
})
</script>

<?php get_footer(); ?>