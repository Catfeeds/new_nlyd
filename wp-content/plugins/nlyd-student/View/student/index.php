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
        <div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 layui-col-md12 detail-content-wrapper have-footer">
            <header class="mui-bar mui-bar-nav layui-bg-white">
                <div class="search-zoo">
                    <i class="iconfont search-Icon">&#xe63b;</i>
                    <input type="text" class="serach-Input nl-foucs" placeholder="<?=__('搜索名录/课程/教练等', 'nlyd-student')?>">
                </div>
            </header>
            <div class="layui-row nl-border nl-content  layui-bg-white">
                <!-- 头部导航 -->
                <div class="layui-row width-padding">
                    <div class="top-nav">
                        <div class="top-nav-btn active"><a class="fs_16 c_blue" href="<?=home_url('/student/index');?>"><?=__('首 页', 'nlyd-student')?></a></div>
                        <div class="top-nav-btn"><a class="fs_16 c_black6 disabled_a"><?=__('我们', 'nlyd-student')?></a></div>
                        <div class="top-nav-btn"><a class="fs_16 c_black6" href="<?=home_url('/directory/');?>"><?=__('名 录', 'nlyd-student')?></a></div>
                        <div class="top-nav-btn"><a class="fs_16 c_black6" href="<?=home_url('/courses/');?>"><?=__('课 程', 'nlyd-student')?></a></div>
                        <div class="top-nav-btn"><a class="fs_16 c_black6 disabled_a" href="<?=home_url('shops');?>"><?=__('商 城', 'nlyd-student')?></a></div>
                        <div class="top-nav-btn"><a class="fs_16 c_black6 disabled_a" href="<?=home_url('system/concatUs');?>"><?=__('合 作', 'nlyd-student')?></a></div>
                    </div>
                </div>
                <!-- 轮播 -->
                <div class="swiper-container layui-bg-white swiper-container1" style="margin-bottom:0">
                    <div class="swiper-wrapper" style="height:auto">
                        <div class="swiper-slide">
                            <div class="swiper-content img-box"><img src="<?=student_css_url.'image/homePage/swiper1.png'?>"></div>
                        </div>
                        <div class="swiper-slide">
                            <div class="swiper-content img-box"><img src="<?=student_css_url.'image/homePage/swiper2.png'?>"></div>
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
                <!-- 入口 -->
                <!-- <div class="layui-row width-padding  layui-bg-white">
                    <div class="entrance flex-h">
                        <div class="entrance-wrap flex1">
                            <a class="index-btn">
                                <div class="img-btn img-box">
                                    <img src="<?=student_css_url.'image/homePage/for-us-big.png'?>">
                                </div>
                                <div class="entrance-name c_black6"><?=__('关于我们', 'nlyd-student')?></div>
                            </a>
                        </div>
                        <div class="entrance-wrap flex1">
                            <a class="index-btn" href="<?=home_url('system')?>">
                                <div class="img-btn img-box">
                                    <img src="<?=student_css_url.'image/homePage/sys-big.png'?>">
                                </div>
                                <div class="entrance-name c_black6"><?=__('体系标准', 'nlyd-student')?></div>
                            </a>
                        </div>
                        <div class="entrance-wrap flex1">
                            <a class="index-btn"   href="<?=home_url('system/concatUs');?>">
                                <div class="img-btn img-box">
                                    <img src="<?=student_css_url.'image/homePage/concat-big.png'?>">
                                </div>
                                <div class="entrance-name c_black6"><?=__('合作联系', 'nlyd-student')?></div>
                            </a>
                        </div>
                    </div>
                </div> -->
                <div class="layui-row width-padding width-padding-pc">
                    <?php if(!empty($course_list)): ?>
                    <div class="layui-row index_row">
                        <div class="layui-row index_row_title">
                             <span class="bold c_black fs_16 mr_10 dis_inlineBlock"><?=__('推荐课程', 'nlyd-student')?></span>   
                             <a class="c_blue fs_12 dis_inlineBlock" href="<?=home_url('/courses/');?>"><?=__('查看更多', 'nlyd-student')?></a>   
                        </div>   
                        <div class="layui-row index_row_content">
                            <?php foreach ($course_list as $v){?>
                            <div class="index_course_item">
                                <div class="index_course_name"><?=$v['course_title']?></div>
                                <div class="index_course_detail"><?=$v['zone_city']?>（<?=$v['zone_number']?>）</div>
                                <a class="index_course_btn dis_table c_black"><div class="dis_cell"><?=__('抢占课程', 'nlyd-student')?></div></a>
                            </div>
                            <?php } ?>
                            <!--<div class="index_course_item">
                                <div class="index_course_name">高效记忆术第一期</div>
                                <div class="index_course_detail">温江训练中心（0001）</div>
                                <a class="index_course_btn dis_table c_black"><div class="dis_cell"><?/*=__('抢占课程', 'nlyd-student')*/?></div></a>
                            </div>    
                            <div class="index_course_item">
                                <div class="index_course_name">高效记忆术第一期</div>
                                <div class="index_course_detail">温江训练中心（0001）</div>
                                <a class="index_course_btn dis_table c_black"><div class="dis_cell"><?/*=__('抢占课程', 'nlyd-student')*/?></div></a>
                            </div>  
                            <div class="index_course_item">
                                <div class="index_course_name">高效记忆术第一期</div>
                                <div class="index_course_detail">温江训练中心（0001）</div>
                                <a class="index_course_btn dis_table c_black"><div class="dis_cell"><?/*=__('抢占课程', 'nlyd-student')*/?></div></a>
                            </div>-->
                        </div>     
                    </div>
                    <?php endif;?>
                    <?php if(!empty($coach_list)): ?>
                    <div class="layui-row index_row">
                        <div class="layui-row index_row_title">
                             <span class="bold c_black fs_16 dis_inlineBlock"><?=__('教练体系', 'nlyd-student')?></span>  
                        </div>   
                        <div class="swiper-container layui-bg-white swiper-container3" style="margin-bottom:0">
                            <div class="swiper-wrapper" style="height:auto">
                                <?php foreach ($coach_list as $val){?>
                                <a class="swiper-slide" href="<?=home_url('/teams/coachDetail/coach_id/'.$val['coach_id'])?>">
                                    <div class="swiper-course">
                                        <div class="swiper_course_img">
                                        <div class="item-img">
                                            <img src="<?=$val['work_photo']?>">
                                        </div>
                                        </div>
                                        <div class="swiper_course_detail">
                                            <div class="bold c_black fs_14"><?=$val['coach_name']?></div>
                                            <div class="fs_14 c_black6">国际脑力运动委员会教练</div>
                                        </div>
                                    </div>
                                </a>
                                <?php } ?>
                                <!--<a class="swiper-slide">
                                    <div class="swiper-course">
                                        <div class="swiper_course_img">
                                        <div class="item-img">
                                                <img src="<?/*=student_css_url.'image/homePage/concat-big.png'*/?>">
                                        </div>
                                        </div>
                                        <div class="swiper_course_detail">
                                            <div class="bold c_black fs_14">程玮</div>
                                            <div class="fs_14 c_black6">国际脑力运动委员会教练</div>
                                        </div>
                                    </div>
                                </a>-->
                            </div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                    <?php endif;?>
                    <div class="layui-row index_row">
                        <div class="layui-row index_row_title">
                             <span class="bold c_black fs_16 dis_inlineBlock"><?=__('赛事回顾', 'nlyd-student')?></span>  
                        </div>   
                        <div class="swiper-container swiper-container2" style="margin-bottom:0">
                            <div class="swiper-wrapper" style="height:auto">
                                <a class="swiper-slide">
                                    <div class="swiper_news_wrap">
                                        <div class="swiper-content img-box"><img src="<?=student_css_url.'image/homePage/swiper1.png'?>"></div>
                                        <div class="swiper_news_title">全球首批“国际脑力健将诞生”</div>
                                    </div>
                                </a>
                                <a class="swiper-slide">
                                    <div class="swiper_news_wrap">
                                        <div class="swiper-content img-box"><img src="<?=student_css_url.'image/homePage/swiper1.png'?>"></div>
                                        <div class="swiper_news_title">全球首批“国际脑力健将诞生1”</div>
                                    </div>
                                </a>
                                <a class="swiper-slide">
                                    <div class="swiper_news_wrap">
                                        <div class="swiper-content img-box"><img src="<?=student_css_url.'image/homePage/swiper1.png'?>"></div>
                                        <div class="swiper_news_title">全球首批“国际脑力健将诞生2”</div>
                                    </div>
                                </a>
                            </div>
                            <div class="swiper-button-prev"></div><!--左箭头-->
                            <div class="swiper-button-next"></div><!--右箭头-->
                        </div>
                    </div>

                    <div class="layui-row index_row">
                        <div class="layui-row index_row_title">  
                             <span class="bold c_black fs_16 mr_10 dis_inlineBlock"><?=__('推荐资讯', 'nlyd-student')?></span>   
                             <a class="c_blue fs_12 dis_inlineBlock"><?=__('查看更多', 'nlyd-student')?></a> 
                        </div>   
                        <div class="swiper-container swiper-container2" style="margin-bottom:0">
                            <a class="swiper_news_wrap">
                                <div class="img-box"><img src="<?=student_css_url.'image/homePage/swiper1.png'?>"></div>
                                <div class="swiper_news_title">全球首批“国际脑力健将诞生”</div>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- <div class="layui-row pt-10 layui-bg-gray">
                    <div class="layui-row">
                        <div class="head-info layui-row width-padding">
                            <span class="pull-left  c_blue fs_14"><?=__('推荐课程', 'nlyd-student')?></span>
                            <span class="pull-right fs_14"><?=__('推荐课程', 'nlyd-student')?> <i class="iconfont">&#xe640;</i></span>
                        </div>
                        <div class="nl-ad-row layui-bg-white  width-padding">
                            <div class="layui-row foot-info">
                                <div class="layui-col-lg5 layui-col-md5 layui-col-sm5 layui-col-xs5 img-box">
                                    <img src="<?=student_css_url.'image/homePage/course1.png'?>">
                                </div>
                                <div class="layui-col-lg7 layui-col-md7 layui-col-sm7 layui-col-xs7">
                                    <p class="text_1 c_black"><?=__('课程名称', 'nlyd-student')?></p>
                                    <p class="fs_12 c_orange"><?=__('抢占名额中', 'nlyd-student')?>(2/18)</p>
                                    <div class="fs_12 c_black6 text_2 nl-ad-detail"><?=__('这里是课程介绍摘要，截取课程简要介绍，不超过30个字符', 'nlyd-student')?></div>
                                </div>
                            </div>
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
                                    <p class="text_1 c_black"><?= $cat->post_title ?></p>
                                    <div class="fs_12 c_black6 text_2 nl-ad-detail"><?= msubstr(strip_tags($cat->post_content),0,35) ?></div>
                                </div>
                            </div>
                        </div>                
                        <?php
                                }
                            }
                        ?>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</div>
<script>
jQuery(function($) {   
    var mySwiper1 = new Swiper('.swiper-container1', {//乐学乐
        loop : true,
        autoplay:{
            disableOnInteraction:false
        },//可选选项，自动滑动
        autoplayDisableOnInteraction : false,    /* 注意此参数，默认为true */ 
        initialSlide :0,//初始展示页
        pagination: {
            el: '.swiper-pagination',
            dynamicBullets: true,
            dynamicMainBullets: 1,
            clickable :true,
            // bulletActiveClass:'layui-bg-white',//分页器active
        },
       
    }); 
    var mySwiper2 = new Swiper('.swiper-container2', {//赛事回顾
        loop : true,
        autoplay:{
            disableOnInteraction:false
        },//可选选项，自动滑动
        autoplayDisableOnInteraction : false, 
        initialSlide :0,//初始展示页
        navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
        },
  });
  var mySwiper3 = new Swiper('.swiper-container3', {//教练
        loop : true,
        autoplay:{
            disableOnInteraction:false
        },//可选选项，自动滑动
        autoplayDisableOnInteraction : true,    /* 注意此参数，默认为true */ 
        initialSlide :0,//初始展示页
        pagination: {
            el: '.swiper-pagination',
            // dynamicBullets: true,
            dynamicMainBullets: 1,
            clickable :true,
            bulletActiveClass:'yellowActive',//分页器active
        },
        // pagination:{
        //     el: '.swiper-pagination',
        //     bulletActiveClass: 'yellowActive',
        // },
    }); 
    console.log('.swiper-container3 .'+mySwiper3.params.pagination.bulletClass)
    $('.swiper-container3 .'+mySwiper3.params.pagination.bulletClass).addClass('yellow_circle'); //为分页器增加样式
})
</script>

<?php get_footer(); ?>