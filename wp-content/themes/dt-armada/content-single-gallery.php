<?php
/**
 * Album singla page template.
 *
 * @package vogue
 * @since  1.0.0
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('project-post'); ?>>

	<?php
	do_action('presscore_before_post_content');

	if ( !post_password_required() ) {

		$config = Presscore_Config::get_instance();
		if ( 'photo_scroller' != $config->get( 'post.media.type' ) ) {

			echo '<div class="wf-container">';
				echo '<div class="wf-cell wf-1 project-slider">';
					dt_get_template_part( 'albums/albums-post-single-media' );
				echo '</div>';
			echo '</div>';

		}

		echo '<div class="wf-container">';
			echo '<div class="wf-cell wf-1 project-content">';
				the_content();
			echo '</div>';
		echo '</div>';

	} else {
		the_content();

	}

	do_action('presscore_after_post_content');
	?>

</article>