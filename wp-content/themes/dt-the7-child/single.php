<?php
/**
 * The Template for displaying all single posts.
 *
 * @package presscore
 * @since presscore 0.1
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

$config = Presscore_Config::get_instance();
presscore_config_base_init();
get_header( 'single' );

//查询浏览量
$readRes = $wpdb->get_row('SELECT meta_id,meta_value FROM '.$wpdb->postmeta.' WHERE post_id='.$post->ID.' AND meta_key="read_num"', ARRAY_A);
if(!$readRes){
    if(!($metaId = $wpdb->insert($wpdb->postmeta,['post_id' => $post->ID, 'meta_key' => 'read_num', 'meta_value' => 1]))){
        return false;
    }
    $readNum = 1;
}else{
    $readNum = $readRes['meta_value'];
    $metaId = $readRes['meta_id'];
}
//浏览量+1
$wpdb->update($wpdb->postmeta, ['meta_value' => ++$readNum], ['meta_id' => $metaId]);
?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

		<?php get_template_part( 'header-main' ); ?>

		<?php if ( presscore_is_content_visible() ): ?>

			<?php do_action( 'presscore_before_loop' ); ?>

			<!-- !- Content -->
			<div id="content" class="content" role="main">

				<div class="layui-fluid">
					<div class="layui-row">
						<div class="layui-col-lg12 layui-col-md12 layui-col-sm12 layui-col-xs12 detail-content-wrapper have-footer">
							<header class="mui-bar mui-bar-nav">
								<a class="mui-pull-left nl-goback static" onclick="window.location.href='<?= home_url("category/new-news/"); ?>'">
									<i class="iconfont">&#xe610;</i>
								</a>
								<h1 class="mui-title">新闻详情</h1>
							</header>
							<div class="layui-row nl-border nl-content">
								<div class="width-margin width-margin-pc news-wrapper layui-row">
									<div class="news-title lauyi-row">
										<div class="layui-row">

											<?php if(get_next_post(true)->ID){ ?>
												<a class="news-prev" href="<?=get_next_post(true)->guid?>">上一篇</a>
											<?php }else{ ?>
												<a class="news-prev" href="javascript:;">无上篇</a>
											<?php }?>
											<p class="news-name"><?=$post->post_title?></p>
											<?php if(get_previous_post(true)->ID){ ?>
												<a class="news-next" href="<?=get_previous_post(true)->guid?>">下一篇</a>
											<?php }else{ ?>
												<a class="news-next" href="javascript:;">无下篇</a>
											<?php }?>
										</div>
										<div class="layui-row">
											<div class="pull-left news-info">
												<span class="news-build">发布日期：<?=explode(' ',$post->post_date)[0]?></span>
												<span class="news-scan">浏览数量：<?=$readNum?></span>
											</div>
											<div class="pull-right news-share">分享</div>
										</div>
									</div>

									<div class="new-content">
										<?php get_template_part( 'content-single', str_replace( 'dt_', '', get_post_type() ) ); ?>

										<?php
										// comments_template( '', true );
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>


			</div><!-- #content .wf-cell -->

			<?php do_action('presscore_after_content'); ?>

		<?php endif; // content is visible ?>

<?php endwhile; endif; // end of the loop. ?>

<?php get_footer(); ?>