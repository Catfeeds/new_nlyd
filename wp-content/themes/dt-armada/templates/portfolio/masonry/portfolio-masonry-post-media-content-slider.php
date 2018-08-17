<?php
/**
 * Portfolio post media content part for slider
 *
 * @since 1.0.0
 * @package vogue
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<div class="project-list-media">

	<?php
	$slider_classes = array('alignnone');
	$config = Presscore_Config::get_instance();

	if ( 'grid' == $config->get('layout') ) {
		$slider_classes[] = 'slider-simple';

	} else {
		$slider_classes[] = 'slider-masonry';

	}

	echo presscore_get_project_media_slider( $slider_classes );
	?>

</div>