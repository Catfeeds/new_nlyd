<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package presscore
 * @since presscore 0.1
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

presscore_config_base_init();
global $wp_query;

get_header(); ?>
<!-- Content -->
<div id="content" class="content" role="main" style="min-height: 500px; text-align:center">

    <article id="post-0" class="post error404 not-found">

        <h1 class="entry-title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'presscore' ); ?></h1>

        <p><?php _e( $wp_query->query_vars['error'] ); ?></p>

    </article><!-- #post-0 .post .error404 .not-found -->

</div><!-- #content .site-content -->

<?php do_action('presscore_after_content'); ?>

<?php get_footer(); ?>