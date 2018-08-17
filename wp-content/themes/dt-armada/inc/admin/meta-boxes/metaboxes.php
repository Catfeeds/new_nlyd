<?php
/**
 * Theme metaboxes.
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Load meta box fields templates
require_once PRESSCORE_ADMIN_DIR . '/meta-boxes/metabox-fields-templates.php';

/**
 * Get advanced settings open block.
 *
 * @return string.
 */
function presscore_meta_boxes_advanced_settings_tpl( $id = 'dt-advanced' ) {
	return sprintf(
		'<div class="hide-if-no-js"><div class="dt_hr"></div><p><a href="#advanced-options" class="dt_advanced">
				<input type="hidden" name="%1$s" data-name="%1$s" value="hide" />
				<span class="dt_advanced-show">%2$s</span>
				<span class="dt_advanced-hide">%3$s</span> 
				%4$s
			</a></p></div><div class="%1$s dt_container hide-if-js"><div class="dt_hr"></div>',
		esc_attr(''.$id),
		_x('+ Show', 'backend metabox', 'presscore'),
		_x('- Hide', 'backend metabox', 'presscore'),
		_x('advanced settings', 'backend metabox', 'presscore')
	);
}

// define global metaboxes array
global $DT_META_BOXES;
$DT_META_BOXES = array();

// Get widgetareas
$widgetareas_list = presscore_get_widgetareas_options();
if ( !$widgetareas_list ) {
	$widgetareas_list = array('none' => _x('None', 'backend metabox', 'presscore'));
}

// Ordering settings
$order_options = array(
	'ASC'	=> _x('ascending', 'backend', 'presscore'),
	'DESC'	=> _x('descending', 'backend', 'presscore'),
);

$orderby_options = array(
	'ID'			=> _x('ID', 'backend', 'presscore'),
	'author'		=> _x('author', 'backend', 'presscore'),
	'title'			=> _x('title', 'backend', 'presscore'),
	'name'			=> _x('name', 'backend', 'presscore'),
	'date'			=> _x('date', 'backend', 'presscore'),
	'modified'		=> _x('modified', 'backend', 'presscore'),
	'parent'		=> _x('parent', 'backend', 'presscore'),
	'rand'			=> _x('rand', 'backend', 'presscore'),
	'comment_count'	=> _x('comment_count', 'backend', 'presscore'),
	'menu_order'	=> _x('menu_order', 'backend', 'presscore'),
);

$yes_no_options = array(
	'1'	=> _x('Yes', 'backend metabox', 'presscore'),
	'0' => _x('No', 'backend metabox', 'presscore'),
);

$enabled_disabled = array(
	'1'	=> _x('Enabled', 'backend metabox', 'presscore'),
	'0' => _x('Disabled', 'backend metabox', 'presscore'),
);

// Image settings
$repeat_options = array(
	'repeat'	=> _x('repeat', 'backend', 'presscore'),
	'repeat-x'	=> _x('repeat-x', 'backend', 'presscore'),
	'repeat-y'	=> _x('repeat-y', 'backend', 'presscore'),
	'no-repeat'	=> _x('no-repeat', 'backend', 'presscore'),
);

$position_x_options = array(
	'center'	=> _x('center', 'backend', 'presscore'),
	'left'		=> _x('left', 'backend', 'presscore'),
	'right'		=> _x('right', 'backend', 'presscore'),
);

$position_y_options = array(
	'center'	=> _x('center', 'backend', 'presscore'),
	'top'		=> _x('top', 'backend', 'presscore'),
	'bottom'	=> _x('bottom', 'backend', 'presscore'),
);

$load_style_options = array(
	'ajax_pagination'	=> _x('Pagination & filter with AJAX', 'backend metabox', 'presscore'),
	'ajax_more'			=> _x('"Load more" button & filter with AJAX', 'backend metabox', 'presscore'),
	'lazy_loading'		=> _x('Lazy loading', 'backend metabox', 'presscore'),
	'default'			=> _x('Standard (no AJAX)', 'backend metabox', 'presscore')
);

$font_size = array(
	'h1'		=> _x('h1', 'backend metabox', 'presscore'),
	'h2'		=> _x('h2', 'backend metabox', 'presscore'),
	'h3'		=> _x('h3', 'backend metabox', 'presscore'),
	'h4'		=> _x('h4', 'backend metabox', 'presscore'),
	'h5'		=> _x('h5', 'backend metabox', 'presscore'),
	'h6'		=> _x('h6', 'backend metabox', 'presscore'),
	'small'		=> _x('small', 'backend metabox', 'presscore'),
	'normal'	=> _x('medium', 'backend metabox', 'presscore'),
	'big'		=> _x('large', 'backend metabox', 'presscore')
);

$accent_custom_color = array(
	'accent'	=> _x('Accent', 'backend metabox', 'presscore'),
	'color'		=> _x('Custom color', 'backend metabox', 'presscore')
);

$proportions = presscore_meta_boxes_get_images_proportions();
$proportions_max = count($proportions);
$proportions_maybe_1x1 = array_search( 1, wp_list_pluck( $proportions, 'ratio' ) );

$rev_sliders = $layer_sliders = array( 'none' => _x('none', 'backend metabox', 'presscore') );

if ( class_exists('RevSlider') ) {

	$rev = new RevSlider();

	$arrSliders = $rev->getArrSliders();
	foreach ( (array) $arrSliders as $revSlider ) { 
		$rev_sliders[ $revSlider->getAlias() ] = $revSlider->getTitle();
	}
}

if ( function_exists('lsSliders') ) {

	$layerSliders = lsSliders();

	foreach ( $layerSliders as $lSlide ) {

		$layer_sliders[ $lSlide['id'] ] = $lSlide['name'];
	}
}

$slideshow_posts = array();
$slideshow_query = new WP_Query( array(
	'no_found_rows'		=> true,
	'posts_per_page'	=> -1,
	'post_type'			=> 'dt_slideshow',
	'post_status'		=> 'publish',
) );

if ( $slideshow_query->have_posts() ) {

	foreach ( $slideshow_query->posts as $slidehsow_post ) {

		$slideshow_posts[ $slidehsow_post->ID ] = wp_kses( $slidehsow_post->post_title, array() );
	}
}

////////////////
// Cusom logo //
////////////////

$prefix = '_dt_custom_header_logo_';

$DT_META_BOXES['dt_page_box-custom_header_logo'] = array(
	'id'		=> 'dt_page_box-custom_header_logo',
	'title' 	=> _x( 'Logo in header', 'backend metabox', 'presscore' ),
	'pages' 	=> array( 'page', 'post', 'dt_portfolio', 'dt_gallery', 'dt_team' ),
	'context' 	=> 'side',
	'priority' 	=> 'low',
	'fields' 	=> array(

		array(
			'name'				=> _x( 'Logo', 'backend metabox', 'presscore' ),
			'id'               	=> "{$prefix}regular",
			'type'             	=> 'image_advanced_mk2',
			'max_file_uploads'	=> 1
		),

		array(
			'name'				=> _x('High-DPI (retina) logo', 'backend metabox', 'presscore'),
			'id'               	=> "{$prefix}hd",
			'type'             	=> 'image_advanced_mk2',
			'max_file_uploads'	=> 1
		),

	)
);

/***********************************************************/
// Sidebar options
/***********************************************************/

$prefix = '_dt_sidebar_';

$DT_META_BOXES['dt_page_box-sidebar'] = array(
	'id'		=> 'dt_page_box-sidebar',
	'title' 	=> _x('Sidebar Options', 'backend metabox', 'presscore'),
	'pages' 	=> array( 'page', 'post', 'dt_portfolio', 'dt_gallery', 'dt_team' ),
	'context' 	=> 'side',
	'priority' 	=> 'low',
	'fields' 	=> array(

		// Sidebar option
		array(
			'name'    	=> _x('Sidebar position:', 'backend metabox', 'presscore'),
			'id'      	=> "{$prefix}position",
			'type'    	=> 'radio',
			'std'		=> 'right',
			'options'	=> array(
				'left' 		=> array( _x('Left', 'backend metabox', 'presscore'), array('sidebar-left.gif', 75, 50) ),
				'right' 	=> array( _x('Right', 'backend metabox', 'presscore'), array('sidebar-right.gif', 75, 50) ),
				'disabled'	=> array( _x('Disabled', 'backend metabox', 'presscore'), array('sidebar-disabled.gif', 75, 50) ),
			),
			'hide_fields'	=> array(
				'disabled'	=> array("{$prefix}widgetarea_id"),
			)
		),

		// Sidebar widget area
		array(
			'name'     		=> _x('Sidebar widget area:', 'backend metabox', 'presscore'),
			'id'       		=> "{$prefix}widgetarea_id",
			'type'     		=> 'select',
			'options'  		=> $widgetareas_list,
			'std'			=> 'sidebar_1',
			'top_divider'	=> true
		),
	)
);

/***********************************************************/
// Footer options
/***********************************************************/

$prefix = '_dt_footer_';

$DT_META_BOXES['dt_page_box-footer'] = array(
	'id'		=> 'dt_page_box-footer',
	'title' 	=> _x('Footer Options', 'backend metabox', 'presscore'),
	'pages' 	=> array( 'page', 'post', 'dt_portfolio', 'dt_gallery', 'dt_team' ),
	'context' 	=> 'side',
	'priority' 	=> 'low',
	'fields' 	=> array(

		// Footer option
		array(
			'name'    		=> _x('Show widgetized footer:', 'backend metabox', 'presscore'),
			'id'      		=> "{$prefix}show",
			'type'    		=> 'checkbox',
			'std'			=> 1,
			'hide_fields'	=> array( "{$prefix}widgetarea_id" ),
		),

		// Sidebar widgetized area
		array(
			'name'     		=> _x('Footer widget area:', 'backend metabox', 'presscore'),
			'id'       		=> "{$prefix}widgetarea_id",
			'type'     		=> 'select',
			'options'  		=> $widgetareas_list,
			'std'			=> 'sidebar_2',
			'top_divider'	=> true
		),
	)
);

/***********************************************************/
// Header options
/***********************************************************/

$prefix = '_dt_header_';

$DT_META_BOXES['dt_page_box-header_options'] = array(
	'id'		=> 'dt_page_box-header_options',
	'title' 	=> _x('Page Header Options', 'backend metabox', 'presscore'),
	'pages' 	=> array( 'page', 'post', 'dt_portfolio', 'dt_gallery', 'dt_team' ),
	'context' 	=> 'normal',
	'priority' 	=> 'high',
	'fields' 	=> array(

		// Header options
		array(
			'id'      	=> "{$prefix}title",
			'type'    	=> 'radio',
			'std'		=> 'enabled',
			'options'	=> array(
				'enabled'	=> array( _x('Show page title', 'backend metabox', 'presscore'), array('regular-title.gif', 100, 60) ),
				'disabled'	=> array( _x('Hide page title', 'backend metabox', 'presscore'), array('no-title.gif', 100, 60) ),
				'fancy'		=> array( _x('Fancy title', 'backend metabox', 'presscore'), array('fancy-title.gif', 100, 60) ),
				'slideshow'	=> array( _x('Slideshow', 'backend metabox', 'presscore'), array('slider.gif', 100, 60) ),
			),
			'hide_fields'	=> array(
				'enabled'	=> array( "{$prefix}background_settings" ),
				'disabled'	=> array( "{$prefix}background_settings" ),
			)
		),

		// Header overlapping
		array(
			// container begin !!!
			'before'		=> '<div class="rwmb-flickering-field ' . "rwmb-input-{$prefix}background_settings" . '">',

			'name'    		=> '',
			'id'      		=> "{$prefix}background",
			'type'    		=> 'radio',
			'std'			=> 'normal',
			'top_divider'	=> true,
			'options'		=> array(
				'normal'		=> array( _x('Normal', 'backend metabox', 'presscore'), array('regular.gif', 100, 60) ),
				'overlap'		=> array( _x("Overlapping (doesn't work with side header & Photo scroller)", 'backend metabox', 'presscore'), array('overl.gif', 100, 61) ),
				'transparent'	=> array( _x("Transparent (doesn't work with side header)", 'backend metabox', 'presscore'), array('transp.gif', 100, 60) ),
			),
			'hide_fields'	=> array(
				'normal'	=> array( "{$prefix}transparent_settings" ),
				'overlap'	=> array( "{$prefix}transparent_settings" )
			)
		),

		array(
			// container begin !!!
			'before'		=> '<div class="rwmb-flickering-field ' . "rwmb-input-{$prefix}transparent_settings" . '">',

			"type"		=> "radio",
			"id"		=> "{$prefix}transparent_bg_style",
			"name"		=> _x( "Transparent  background:", "theme-options", 'presscore' ),
			"std"		=> "solid_background",
			"options"	=> array(
				'solid_background' => _x( "Enabled", "theme-options", 'presscore' ),
				'disabled' => _x( "Disabled", "theme-options", 'presscore' )
			),
			'hide_fields'	=> array(
				'disabled'	=> array( "{$prefix}transparent_solid_bg_settings" ),
			),
			'top_divider'	=> true
		),

		array(
			// container begin !!!
			'before'		=> '<div class="rwmb-flickering-field ' . "rwmb-input-{$prefix}transparent_solid_bg_settings" . '">',

			'name'    		=> _x('Transparent background color:', 'backend metabox', 'presscore'),
			'id'      		=> "{$prefix}transparent_bg_color",
			'type'    		=> 'color',
			'std'			=> '#000000'
		),

		array(
			'name'	=> _x('Transparent background opacity:', 'backend metabox', 'presscore'),
			'id'	=> "{$prefix}transparent_bg_opacity",
			'type'	=> 'slider',
			'std'	=> '100',
			'js_options' => array(
				'min'   => 0,
				'max'   => 100,
				'step'  => 1,
			),

			'after' => '</div>'
		),

		Presscore_Meta_Box_Field_Template::get_as_array( 'transparent header color mode', array(
			'name' => _x( 'Transparent header text color:', 'theme-options', 'presscore' ),
			'id' => "{$prefix}transparent_menu_text_color_mode"
		) ),

		Presscore_Meta_Box_Field_Template::get_as_array( 'transparent header color mode', array(
			'name' => _x( 'Menu hover decoration color:', 'theme-options', 'presscore' ),
			'id' => "{$prefix}transparent_menu_hover_color_mode"
		) ),

		Presscore_Meta_Box_Field_Template::get_as_array( 'transparent header color mode', array(
			'name' => _x( 'Transparent header layout elements color:', 'theme-options', 'presscore' ),
			'id' => "{$prefix}transparent_menu_top_bar_color_mode",

			// container end x 2
			'after'	=> '</div></div>'
		) ),

	)
);

/***********************************************************/
// Slideshow Options
/***********************************************************/

$prefix = '_dt_slideshow_';

$DT_META_BOXES['dt_page_box-slideshow_options'] = array(
	'id'		=> 'dt_page_box-slideshow_options',
	'title' 	=> _x('Slideshow Options', 'backend metabox', 'presscore'),
	'pages' 	=> array( 'page', 'post', 'dt_portfolio', 'dt_gallery', 'dt_team' ),
	'context' 	=> 'normal',
	'priority' 	=> 'high',
	'fields' 	=> array(

		// Slideshow mode
		array(
			'id'      	=> "{$prefix}mode",
			'type'    	=> 'radio',
			'std'		=> 'porthole',
			'options'	=> array(
				'porthole' => array( _x('Porthole slider', 'backend metabox', 'presscore'), array('portholeslider.gif', 75, 50) ),
				// 'metro' => array( _x('Metro slider', 'backend metabox', 'presscore'), array('metro.png', 75, 50) ),
				'photo_scroller' => array( _x('Photo scroller', 'backend metabox', 'presscore'), array('photoscroller.gif', 75, 50) ),
				'3d' => array( _x('3D slideshow', 'backend metabox', 'presscore'), array('3dslider.gif', 75, 50) ),
				'revolution' => array( _x('Slider Revolution', 'backend metabox', 'presscore'), array('sliderrevolution.gif', 75, 50) ),
				'layer' => array( _x('LayerSlider', 'backend metabox', 'presscore'), array('layerslider.gif', 75, 50) ),
			),
			'hide_fields'	=> array(
				'porthole' => array( "{$prefix}photo_scroller_container", "{$prefix}revolution_slider", "{$prefix}layer_container", "{$prefix}3d_layout_container" ),
				// 'metro' => array( "{$prefix}3d_layout_container", "{$prefix}porthole_container", "{$prefix}revolution_slider", "{$prefix}layer_container" ),
				'photo_scroller' => array( "{$prefix}3d_layout_container", "{$prefix}porthole_container", "{$prefix}revolution_slider", "{$prefix}layer_container" ),
				'3d' => array( "{$prefix}porthole_container", "{$prefix}revolution_slider", "{$prefix}layer_container", "{$prefix}photo_scroller_container" ),
				'revolution' => array( "{$prefix}porthole_container", "{$prefix}3d_layout_container", "{$prefix}sliders", "{$prefix}layer_container", "{$prefix}photo_scroller_container" ),
				'layer' => array( "{$prefix}porthole_container", "{$prefix}3d_layout_container", "{$prefix}sliders", "{$prefix}revolution_slider", "{$prefix}photo_scroller_container" ),
			)
		),

		// Sldeshows
		array(
			'name'    		=> _x('Slideshow(s):', 'backend metabox', 'presscore'),
			'id'      		=> "{$prefix}sliders",
			'type'    		=> 'checkbox_list',

			'desc'  		=> _x('if non selected, all slideshows will be displayed.', 'backend metabox', 'presscore') . ' <a href="' . esc_url( add_query_arg( 'post_type', 'dt_slideshow', get_admin_url() . 'edit.php' ) ) . '" target="_blank">' . _x('Edit slideshows', 'backend metabox', 'presscore') . '</a>',

			'options'		=> $slideshow_posts,

			'top_divider'	=> true,
		),

		// Slideshow layout
		array(
			// container begin !!!
			'before'		=> '<div class="rwmb-input-' . $prefix . '3d_layout_container rwmb-flickering-field">',

			'name'		=> _x('Layout:', 'backend metabox', 'presscore'),
			'id'      	=> "{$prefix}3d_layout",
			'type'    	=> 'radio',
			'std'		=> 'fullscreen-content',
			'options'	=> array(
				'fullscreen-content'	=> _x('full-screen', 'backend metabox', 'presscore'),
				'fullscreen+content'	=> _x('full-screen with content', 'backend metabox', 'presscore'),
				'prop-fullwidth'		=> _x('proportional, full-width', 'backend metabox', 'presscore'),
				'prop-content-width'	=> _x('proportional, content-width', 'backend metabox', 'presscore'),
			),
			'hide_fields'	=> array(
				'fullscreen-content'	=> array( "{$prefix}3d_slider_proportions" ),
				'fullscreen+content'	=> array( "{$prefix}3d_slider_proportions" ),
			),
			'top_divider'	=> true,
		),

		// Slider proportions
		array(
			'name'			=> _x('Slider proportions:', 'backend metabox', 'presscore'),
			'id'    		=> "{$prefix}3d_slider_proportions",
			'type'  		=> 'simple_proportions',
			'std'   		=> array('width' => 500, 'height' => 500),
			'top_divider'	=> true,

			// container end !!!
			'after'			=> '</div>'
		),

		// Slideshow layout
		array(
			// container begin !!!
			'before'		=> '<div class="rwmb-input-' . $prefix . 'porthole_container rwmb-flickering-field">',

			'name'			=> _x('Slider layout:', 'backend metabox', 'presscore'),
			'id'      	=> "{$prefix}layout",
			'type'    	=> 'radio',
			'std'		=> 'fullwidth',
			'options'	=> array(
				'fullwidth'		=> _x('full-width', 'backend metabox', 'presscore'),
				'fixed'			=> _x('content-width', 'backend metabox', 'presscore'),
			),
			'top_divider'	=> true,
		),

		// Slider proportions
		array(
			'name'			=> _x('Slider proportions:', 'backend metabox', 'presscore'),
			'id'    		=> "{$prefix}slider_proportions",
			'type'  		=> 'simple_proportions',
			'std'   		=> array('width' => 1200, 'height' => 500),
		),

		// Scaling
		array(
			'name'			=> _x('Images sizing: ', 'backend metabox', 'presscore'),
			'id'      		=> "{$prefix}scaling",
			'type'    		=> 'radio',
			'std'			=> 'fill',
			'options'	=> array(
				'fit'		=> _x('fit (preserve proportions)', 'backend metabox', 'presscore'),
				'fill'		=> _x('fill the viewport (crop)', 'backend metabox', 'presscore'),
			),
			'top_divider'	=> true,
		),

		// Autoplay
		array(
			'name'			=> _x('On page load slideshow is: ', 'backend metabox', 'presscore'),
			'id'      		=> "{$prefix}autoplay",
			'type'    		=> 'radio',
			'std'			=> 'paused',
			'options'	=> array(
				'play'		=> _x('playing', 'backend metabox', 'presscore'),
				'paused'	=> _x('paused', 'backend metabox', 'presscore'),
			),
			'top_divider'	=> true,
		),

		// Autoslide interval
		array(
			'name'			=> _x('Autoslide interval (in milliseconds):', 'backend metabox', 'presscore'),
			'id'    		=> "{$prefix}autoslide_interval",
			'type'  		=> 'text',
			'std'   		=> '5000'
		),

		// Hide captions
		array(
			'name'    		=> _x('Hide captions:', 'backend metabox', 'presscore'),
			'id'      		=> "{$prefix}hide_captions",
			'type'    		=> 'checkbox',
			'std'			=> 0,

			// container end
			'after'			=> '</div>'
		),

		//////////////////////
		// Photo scroller //
		//////////////////////

		array(
			// container begin !!!
			'before'	=> '<div class="rwmb-input-' . $prefix . 'photo_scroller_container rwmb-flickering-field">',

			'name'		=> _x( 'Layout:', 'backend metabox', 'presscore' ),
			'id'		=> "{$prefix}photo_scroller_layout",
			'type'		=> 'radio',
			'std'		=> 'fullscreen',
			'options'	=> array(
				'fullscreen'	=> _x( 'Fullscreen slideshow', 'backend metabox', 'presscore' ),
				'with_content'	=> _x( 'Fullscreen slideshow + text area', 'backend metabox', 'presscore' )
			),
			'divider'	=> 'top'
		),

		array(
			'name'     		=> _x( 'Background under slideshow:', 'backend metabox', 'presscore' ),
			'id'       		=> "{$prefix}photo_scroller_bg_color",
			'type'     		=> 'color',
			'std'			=> '#000000',
			'divider'		=> 'top'
		),

		Presscore_Meta_Box_Field_Template::get_as_array( 'radio yes no', array(
			'id'		=> "{$prefix}photo_scroller_overlay",
			'name'		=> _x( 'Show pixel overlay:', 'backend metabox', 'presscore' ),
			'divider'	=> 'top'
		) ),

		array(
			'name'			=> _x('Top padding:', 'backend metabox', 'presscore'),
			'id'			=> "{$prefix}photo_scroller_top_padding",
			'type'			=> 'text',
			'std'			=> '0',
			'divider'		=> 'top'
		),

		array(
			'name'			=> _x('Bottom padding:', 'backend metabox', 'presscore'),
			'id'			=> "{$prefix}photo_scroller_bottom_padding",
			'type'			=> 'text',
			'std'			=> '0',
			'divider'		=> 'top'
		),

		array(
			'name'			=> _x('Side paddings:', 'backend metabox', 'presscore'),
			'id'			=> "{$prefix}photo_scroller_side_paddings",
			'type'			=> 'text',
			'std'			=> '0',
			'divider'		=> 'top'
		),

		Presscore_Meta_Box_Field_Template::get_as_array( 'opacity slider', array(
			'name'		=> _x( 'Inactive image transparency (%):', 'backend metabox', 'presscore' ),
			'id'		=> "{$prefix}photo_scroller_inactive_opacity",
			'std' => 15,
			'divider'	=> 'top'
		) ),

		array(
			'name'     	=> _x( 'Thumbnails:', 'backend metabox', 'presscore' ),
			'id'       	=> "{$prefix}photo_scroller_thumbnails_visibility",
			'type'     	=> 'radio',
			'std'		=> 'show',
			'options'  	=> array(
				'show'		=> _x( 'Show by default', 'backend metabox', 'presscore' ),
				'hide'		=> _x( 'Hide by default', 'backend metabox', 'presscore' ),
				'disabled'	=> _x( 'Disable', 'backend metabox', 'presscore' )
			),
			'divider'	=> 'top'
		),

		array(
			'name'		=> _x( 'Thumbnails width:', 'backend metabox', 'presscore' ),
			'id'		=> "{$prefix}photo_scroller_thumbnails_width",
			'type'		=> 'text',
			'std'		=> '',
			'divider'	=> 'top'
		),

		array(
			'name'		=> _x( 'Thumbnails height:', 'backend metabox', 'presscore' ),
			'id'		=> "{$prefix}photo_scroller_thumbnails_height",
			'type'		=> 'text',
			'std'		=> 85,
			'divider'	=> 'top'
		),

		array(
			'name'     	=> _x( 'Autoplay:', 'backend metabox', 'presscore' ),
			'id'       	=> "{$prefix}photo_scroller_autoplay",
			'type'     	=> 'radio',
			'std'		=> 'play',
			'options'  	=> array(
				'play'		=> _x( 'Play', 'backend metabox', 'presscore' ),
				'paused'	=> _x( 'Paused', 'backend metabox', 'presscore' ),
			),
			'divider'	=> 'top'
		),

		array(
			'name'		=> _x( 'Autoplay speed:', 'backend metabox', 'presscore' ),
			'id'		=> "{$prefix}photo_scroller_autoplay_speed",
			'type'		=> 'text',
			'std'		=> '4000',
			'divider'	=> 'top'
		),

		array(
			'type' => 'heading',
			'name' => _x( 'Landscape images', 'backend metabox', 'presscore' ),
			'id' => 'fake_id',
		),

		// Landscape images settings

		Presscore_Meta_Box_Field_Template::get_as_array( 'photoscroller max width', array(
			'id' => "{$prefix}photo_scroller_ls_max_width",
		) ),

		Presscore_Meta_Box_Field_Template::get_as_array( 'photoscroller min width', array(
			'id' => "{$prefix}photo_scroller_ls_min_width",
		) ),

		Presscore_Meta_Box_Field_Template::get_as_array( 'photoscroller filling mode desktop', array(
			'id' => "{$prefix}photo_scroller_ls_fill_dt",
		) ),

		Presscore_Meta_Box_Field_Template::get_as_array( 'photoscroller filling mode mobile', array(
			'id' => "{$prefix}photo_scroller_ls_fill_mob",
		) ),

		// Portrait iamges settings

		array(
			'type' => 'heading',
			'name' => _x( 'Portrait images', 'backend metabox', 'presscore' ),
			'id' => 'fake_id',
		),

		Presscore_Meta_Box_Field_Template::get_as_array( 'photoscroller max width', array(
			'id' => "{$prefix}photo_scroller_pt_max_width",
		) ),

		Presscore_Meta_Box_Field_Template::get_as_array( 'photoscroller min width', array(
			'id' => "{$prefix}photo_scroller_pt_min_width",
		) ),

		Presscore_Meta_Box_Field_Template::get_as_array( 'photoscroller filling mode desktop', array(
			'id' => "{$prefix}photo_scroller_pt_fill_dt",
		) ),

		Presscore_Meta_Box_Field_Template::get_as_array( 'photoscroller filling mode mobile', array(
			'id' => "{$prefix}photo_scroller_pt_fill_mob",

			// container end !!!
			'after' => '</div>',
		) ),

/*
		// Number of slides in a row
		array(
			// container begin !!!
			'before'		=> '<div class="rwmb-input-' . $prefix . 'metro_container rwmb-flickering-field">',

			'name'			=> _x('Number of slides in a row:', 'backend metabox', 'presscore'),
			'id'    		=> "{$prefix}slides_in_raw",
			'type'  		=> 'text',
			'std'   		=> '5',
			'top_divider'	=> true,
		),

		// Number of slides in a column
		array(
			// container end !!!
			'after'			=> '</div>',

			'name'			=> _x('Number of slides in a column:', 'backend metabox', 'presscore'),
			'id'    		=> "{$prefix}slides_in_column",
			'type'  		=> 'text',
			'std'   		=> '3'
		),
*/
		// Revolution slider
		array(
			'name'     		=> _x('Choose slider: ', 'backend metabox', 'presscore'),
			'id'       		=> "{$prefix}revolution_slider",
			'type'     		=> 'select',
			'std'			=>'none',
			'options'  		=> $rev_sliders,
			'multiple' 		=> false,
			'top_divider'	=> true
		),

		// LayerSlider
		array(
			// container begin !!!
			'before'		=> '<div class="rwmb-input-' . $prefix . 'layer_container rwmb-flickering-field">',

			'name'     		=> _x('Choose slider:', 'backend metabox', 'presscore'),
			'id'       		=> "{$prefix}layer_slider",
			'type'     		=> 'select',
			'std'			=>'none',
			'options'  		=> $layer_sliders,
			'multiple' 		=> false,
			'top_divider'	=> true
		),

		// Fixed background
		array(
			// container end !!!
			'after'			=> '</div>',

			'name'    		=> _x('Enable slideshow background and paddings:', 'backend metabox', 'presscore'),
			'id'      		=> "{$prefix}layer_show_bg_and_paddings",
			'type'    		=> 'checkbox',
			'std'			=> 0
		),

	)
);

/***********************************************************/
// Fancy title options
/***********************************************************/

$prefix = '_dt_fancy_header_';

$DT_META_BOXES['dt_page_box-fancy_header_options'] = array(
	'id'		=> 'dt_page_box-fancy_header_options',
	'title' 	=> _x('Fancy Title Options', 'backend metabox', 'presscore'),
	'pages' 	=> array( 'page', 'post', 'dt_portfolio', 'dt_gallery', 'dt_team' ),
	'context' 	=> 'normal',
	'priority' 	=> 'high',
	'fields' 	=> array(

		///////////////////////
		// Title alignment //
		///////////////////////

		array(
			'name'    	=> _x('Fancy title layout:', 'backend metabox', 'presscore'),
			'id'      	=> "{$prefix}title_aligment",
			'type'    	=> 'radio',
			'std'		=> 'left',
			'options'	=> array(
				'left'		=> array( _x('Left title + right breadcrumbs', 'backend metabox', 'presscore'), array('l-r.gif', 100, 60) ),
				'right'		=> array( _x('Right title + left breadcrumbs', 'backend metabox', 'presscore'), array('r-l.gif', 100, 60) ),
				'all_left'	=> array( _x('Left title + left breadcrumbs', 'backend metabox', 'presscore'), array('l-l.gif', 100, 60) ),
				'all_right'	=> array( _x('Right title + right breadcrumbs', 'backend metabox', 'presscore'), array('r-r.gif', 100, 60) ),
				'center'	=> array( _x('Centred title + centred breadcrumbs', 'backend metabox', 'presscore'), array('centre.gif', 100, 60) )
			)
		),

		///////////////////
		// Breadcrumbs //
		///////////////////

		array(
			'name'			=> _x('Breadcrumbs:', 'backend metabox', 'presscore'),
			'id'			=> "{$prefix}breadcrumbs",
			'type'	 		=> 'radio',
			'std'			=> 'enabled',
			'top_divider'	=> true,
			'hide_fields'	=> array('disabled'	=> array( "{$prefix}breadcrumbs_settings" ) ),
			'options'		=> array(
				'enabled'	=> _x('Enabled', 'backend metabox', 'presscore'),
				'disabled'	=> _x('Disabled', 'backend metabox', 'presscore'),
			)
		),

		// Breadcrumbs text color
		array(
			// container begin !!!
			'before'		=> '<div class="rwmb-flickering-field ' . "rwmb-input-{$prefix}breadcrumbs_settings" . '">',

			'name'    		=> _x('Breadcrumbs text color:', 'backend metabox', 'presscore'),
			'id'      		=> "{$prefix}breadcrumbs_text_color",
			'type'    		=> 'color',
			'std'			=> '#000000'
		),

		// Breadcrumbs background color
		array(
			'name'			=> _x('Breadcrumbs background color:', 'backend metabox', 'presscore'),
			'id'			=> "{$prefix}breadcrumbs_bg_color",
			'type'	 		=> 'radio',
			'std'			=> 'disabled',
			'options'		=> array(
				'disabled'	=> _x('Disabled', 'backend metabox', 'presscore'),
				'black'		=> _x('Black', 'backend metabox', 'presscore'),
				'white'		=> _x('White', 'backend metabox', 'presscore'),
			),

			// container end
			'after'	=> '</div>'
		),

		//////////////////////
		// Title settings //
		//////////////////////

		// Title
		array(
			'name'    	=> _x('Title:', 'backend metabox', 'presscore'),
			'id'      	=> "{$prefix}title",
			'type'    	=> 'text',
			'std'		=> '',
			'top_divider'	=> true,
		),

		// Title font size
		array(
			'name'     	=> _x('Title font size:', 'backend metabox', 'presscore'),
			'id'       	=> "{$prefix}title_size",
			'type'     	=> 'select',
			'options'  	=> $font_size,
			'std'		=> 'h2'
		),

		// Title font color
		array(
			'name'			=> _x('Title font color:', 'backend metabox', 'presscore'),
			'id'			=> "{$prefix}title_color_mode",
			'type'	 		=> 'radio',
			'std'			=> 'accent',
			'hide_fields'	=> array( 'accent' => array( "{$prefix}title_color_settings" ) ),
			'options'		=> $accent_custom_color
		),

		// Title color
		array(
			// container begin !!!
			'before'		=> '<div class="rwmb-flickering-field ' . "rwmb-input-{$prefix}title_color_settings" . '">',

			'name'    		=> '&nbsp;',
			'id'      		=> "{$prefix}title_color",
			'type'    		=> 'color',
			'std'			=> '#000000',

			// container end
			'after'			=> '</div>'
		),

		/////////////////////////
		// Subtitle settings //
		/////////////////////////

		// Subtitle
		array(
			'name'    	=> _x('Subtitle:', 'backend metabox', 'presscore'),
			'id'      	=> "{$prefix}subtitle",
			'type'    	=> 'text',
			'std'		=> '',
			'top_divider'	=> true,
		),

		// Subtitle font size
		array(
			'name'     	=> _x('Subtitle font size:', 'backend metabox', 'presscore'),
			'id'       	=> "{$prefix}subtitle_size",
			'type'     	=> 'select',
			'options'  	=> $font_size,
			'std'		=> 'h2'
		),

		// Subtitle font color
		array(
			'name'			=> _x('Subtitle font color:', 'backend metabox', 'presscore'),
			'id'			=> "{$prefix}subtitle_color_mode",
			'type'	 		=> 'radio',
			'std'			=> 'accent',
			'hide_fields'	=> array( 'accent' => array( "{$prefix}subtitle_color_settings" ) ),
			'options'		=> $accent_custom_color
		),

		// Subtitle color
		array(
			// container begin !!!
			'before'		=> '<div class="rwmb-flickering-field ' . "rwmb-input-{$prefix}subtitle_color_settings" . '">',

			'name'    		=> '&nbsp;',
			'id'      		=> "{$prefix}subtitle_color",
			'type'    		=> 'color',
			'std'			=> '#000000',

			// container end
			'after'			=> '</div>'
		),

		///////////////////////////
		// Background settings //
		///////////////////////////

		// Background color
		array(
			'name'    		=> _x('Background color:', 'backend metabox', 'presscore'),
			'id'      		=> "{$prefix}bg_color",
			'type'    		=> 'color',
			'std'			=> '#ffffff',
			'top_divider'	=> true,
		),

		// Background image
		array(
			'name'             	=> _x('Background image:', 'backend metabox', 'presscore'),
			'id'               	=> "{$prefix}bg_image",
			'type'             	=> 'image_advanced_mk2',
			'max_file_uploads'	=> 1,
		),

		// Repeat options
		array(
			'name'     	=> _x('Repeat options:', 'backend metabox', 'presscore'),
			'id'       	=> "{$prefix}bg_repeat",
			'type'     	=> 'select',
			'options'  	=> $repeat_options,
			'std'		=> 'no-repeat'
		),

		// Position x
		array(
			'name'     	=> _x('Position x:', 'backend metabox', 'presscore'),
			'id'       	=> "{$prefix}bg_position_x",
			'type'     	=> 'select',
			'options'  	=> $position_x_options,
			'std'		=> 'center'
		),

		// Position y
		array(
			'name'     	=> _x('Position y:', 'backend metabox', 'presscore'),
			'id'       	=> "{$prefix}bg_position_y",
			'type'     	=> 'select',
			'options'  	=> $position_y_options,
			'std'		=> 'center'
		),

		// Fullscreen
		array(
			'name'    		=> _x('Fullscreen:', 'backend metabox', 'presscore'),
			'id'      		=> "{$prefix}bg_fullscreen",
			'type'    		=> 'checkbox',
			'std'			=> 1,
		),

		// Fixed background
		array(
			'name'    		=> _x('Fixed background:', 'backend metabox', 'presscore'),
			'id'      		=> "{$prefix}bg_fixed",
			'type'    		=> 'checkbox',
			'std'			=> 0
		),

		// Enable parallax & Parallax speed
		array(
			'name'    	=> _x('Parallax speed:', 'backend metabox', 'presscore'),
			'desc'  	=> _x('if field is empty, parallax disabled', 'backend metabox', 'presscore'),
			'id'      	=> "{$prefix}parallax_speed",
			'type'    	=> 'text',
			'std'		=> '0',
		),

		// Height
		array(
			'name'    	=> _x('Height (px):', 'backend metabox', 'presscore'),
			'id'      	=> "{$prefix}height",
			'type'    	=> 'text',
			'std'		=> '100'
		),

	)
);

/***********************************************************/
// Content area options
/***********************************************************/

$prefix = '_dt_content_';

$DT_META_BOXES[] = array(
	'id'		=> 'dt_page_box-page_content',
	'title' 	=> _x('Content Area Options', 'backend metabox', 'presscore'),
	'pages' 	=> array( 'page' ),
	'context' 	=> 'normal',
	'priority' 	=> 'high',
	'fields' 	=> array(

		// Display content area
		array(
			'name'    	=> _x('Display content area:', 'backend metabox', 'presscore'),
			'id'      	=> "{$prefix}display",
			'type'    	=> 'radio',
			'std'		=> 'no',
			'options'	=> array(
				'no' 			=> _x('no', 'backend metabox', 'presscore'),
				'on_first_page'	=> _x('first page', 'backend metabox', 'presscore'),
				'on_all_pages'	=> _x('all pages', 'backend metabox', 'presscore'),
			),
			'hide_fields'	=> array('no'	=> "{$prefix}position")
		),

		// Content area position
		array(
			'name'    	=> _x('Content area position', 'backend metabox', 'presscore'),
			'id'      	=> "{$prefix}position",
			'type'    	=> 'radio',
			'std'		=> 'before_items',
			'options'	=> array(
				'before_items'	=> array( _x('Before items', 'backend metabox', 'presscore'), array( 'before-posts.gif', 60, 67 ) ),
				'after_items'	=> array( _x('After items', 'backend metabox', 'presscore'), array( 'under-posts.gif', 60, 67 ) ),
			),
		),

	),
	'only_on'	=> array( 'template' => array(
		'template-portfolio-list.php',
		'template-portfolio-masonry.php',
		'template-portfolio-jgrid.php',
		'template-blog-list.php',
		'template-blog-masonry.php',
		'template-albums.php',
		'template-albums-jgrid.php',
		'template-media.php',
		'template-media-jgrid.php',
		'template-team.php',
		'template-testimonials.php',
	) ),
);
