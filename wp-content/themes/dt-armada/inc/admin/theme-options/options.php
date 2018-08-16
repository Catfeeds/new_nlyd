<?php

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the "id" fields, make sure to use all lowercase and no spaces.
 *  
 */

$repeat_arr = array(
	'repeat'    => _x( 'repeat', 'backend options', 'presscore' ),
	'repeat-x'  => _x( 'repeat-x', 'backend options', 'presscore' ),
	'repeat-y'  => _x( 'repeat-y', 'backend options', 'presscore' ),
	'no-repeat' => _x( 'no-repeat', 'backend options', 'presscore' )
);

$repeat_x_arr = array(
	'no-repeat' => _x( 'no-repeat', 'backend options', 'presscore' ),
	'repeat-x'  => _x( 'repeat-x', 'backend options', 'presscore' )
);

$y_position_arr = array(
	'center'    => _x( 'center', 'backend options', 'presscore' ),
	'top'       => _x( 'top', 'backend options', 'presscore' ),
	'bottom'    => _x( 'bottom', 'backend options', 'presscore' )
);

$x_position_arr = array(
	'center'    => _x( 'center', 'backend options', 'presscore' ),
	'left'      => _x( 'left', 'backend options', 'presscore' ),
	'right'     => _x( 'right', 'backend options', 'presscore' )
);

$colour_arr = array(
	'blue'      => _x( 'blue', 'backend options', 'presscore' ),
	'green'     => _x( 'green', 'backend options', 'presscore' ),
	'orange'    => _x( 'orange', 'backend options', 'presscore' ),
	'purple'    => _x( 'purple', 'backend options', 'presscore' ),
	'yellow'    => _x( 'yellow', 'backend options', 'presscore' ),
	'pink'      => _x( 'pink', 'backend options', 'presscore' ),
	'white'     => _x( 'white', 'backend options', 'presscore' )
);

$footer_arr = array(
	'every'     => _x( 'on every page', 'backend options', 'presscore' ),
	'home'      => _x( 'front page only', 'backend options', 'presscore' ),
	'ex_home'   => _x( 'everywhere except front page', 'backend options', 'presscore' ),
	'nowhere'   => _x( 'nowhere', 'backend options', 'presscore' )
);

$homepage_arr = array(
	'every'     => _x( 'everywhere', 'backend options', 'presscore' ),
	'home'      => _x( 'only on homepage templates', 'backend options', 'presscore' ),
	'ex_home'   => _x( 'everywhere except homepage templates', 'backend options', 'presscore' ),
	'nowhere'   => _x( 'nowhere', 'backend options', 'presscore' )
);

$image_hovers = array(
	'slice'     => _x( 'slice', 'backend options', 'presscore' ),
	'fade'      => _x( 'fade', 'backend options', 'presscore' )
);

// contact fields
$contact_fields = array(
	array(
		'prefix'    => 'address',
		'desc'      => _x('Address', 'theme-options', 'presscore') 
	),
	array(
		'prefix'    => 'phone',
		'desc'      => _x('Phone', 'theme-options', 'presscore') 
	),
	array(
		'prefix'    => 'email',
		'desc'      => _x('Email', 'theme-options', 'presscore') 
	),
	array(
		'prefix'    => 'skype',
		'desc'      => _x('Skype', 'theme-options', 'presscore') 
	),
	array(
		'prefix'    => 'clock',
		'desc'      => _x('Working hours', 'theme-options', 'presscore') 
	)
);

$soc_ico_arr = array(
	'skype'	=> array(
		'img'	=> '\'\'',
		'desc'	=> 'Skype'
	),
	'working_hours'	=> array(
		'img'	=> '\'\'',
		'desc'	=> 'Working hours'
	),
	'additional_info'	=> array(
		'img'	=> '\'\'',
		'desc'	=> 'Additional info'
	)
);

// Background Defaults
$background_defaults = array(
	'image' 		=> '',
	'repeat' 		=> 'repeat',
	'position_x' 	=> 'center',
	'position_y'	=> 'center'
);

// Radio enabled/disabled
$en_dis_options = array(
	'1' => _x('Enabled', 'theme-options', 'presscore'),
	'0' => _x('Disabled', 'theme-options', 'presscore')
);

// Radio yes/no
$yes_no_options = array(
	'1'	=> _x('Yes', 'theme-options', 'presscore'),
	'0'	=> _x('No', 'theme-options', 'presscore'),
);

// Radio on/off
$on_off_options = array(
	'1'	=> _x('On', 'theme-options', 'presscore'),
	'0'	=> _x('Off', 'theme-options', 'presscore'),
);

// Radio proportional images/fixed width
$prop_fixed_options = array(
	'prop'	=> _x('Proportional images', 'theme-options', 'presscore'),
	'fixed'	=> _x('Fixed width', 'theme-options', 'presscore'),
);


// used in:
//	menu first level background
$background_dis_line_solid_mode = array(
	"disabled" => _x( 'Disabled', 'theme-options', 'presscore' ),
	"content_line" => _x( 'Content-width line', 'theme-options', 'presscore' ),
	"fullwidth_line" => _x( 'Full-width line', 'theme-options', 'presscore' ),
	"solid" => _x( 'Solid background', 'theme-options', 'presscore' ),
);

// used in:
// social buttons
$background_dis_acc_col_grad_mode = array(
	"disabled"	=> _x( 'Disabled', 'theme-options', 'presscore' ),
	"accent"	=> _x( 'Accent', 'theme-options', 'presscore' ),
	"color"		=> _x( 'Custom color', 'theme-options', 'presscore' ),
	"gradient"	=> _x( 'Custom gradient', 'theme-options', 'presscore' )
);

$background_acc_col_grad_mode = array_diff_key( $background_dis_acc_col_grad_mode, array( 'disabled' => '' ) );

$background_dis_line_solid_mode_dependency = array( "content_line" => true, "fullwidth_line" => true, "solid" => true );

// header layout
$header_layout_info_title = _x('Available areas:', 'theme-options', 'presscore');
$header_layout_palette_title = _x('Inactive elements', 'theme-options', 'presscore');

$header_layout_fields = array(
	'top' => array( 'title' => _x('Top', 'theme-options', 'presscore'), 'class' => 'field-red' ),
	'bottom' => array( 'title' => _x('Bottom', 'theme-options', 'presscore'), 'class' => 'field-blue' ),

	'top_bar_left' => array( 'title' => _x('Top bar (left)', 'theme-options', 'presscore'), 'class' => 'field-red' ),
	'top_bar_right' => array( 'title' => _x('Top bar (right)', 'theme-options', 'presscore'), 'class' => 'field-green' ),

	'logo_area' => array( 'title' => _x('Near logo', 'theme-options', 'presscore'), 'class' => 'field-purple' ),
	'nav_area' => array( 'title' => _x('Near navigation area', 'theme-options', 'presscore'), 'class' => 'field-blue' )
);

$header_layout_elements = array(
	'social_icons' => array( 'title' => _x('Social icons', 'theme-options', 'presscore'), 'class' => '' ),
	'search' => array( 'title' => _x('Search', 'theme-options', 'presscore'), 'class' => '' ),
	'cart' => array( 'title' => _x('Cart', 'theme-options', 'presscore'), 'class' => '' ),
	'custom_menu' => array( 'title' => _x('Custom menu', 'theme-options', 'presscore'), 'class' => '' ),
	'login' => array( 'title' => _x('Login', 'theme-options', 'presscore'), 'class' => '' ),
	'text_area' => array( 'title' => _x('Text area', 'theme-options', 'presscore'), 'class' => '' ),
	'skype' => array( 'title' => _x('Skype', 'theme-options', 'presscore'), 'class' => '' ),
	'email' => array( 'title' => _x('Mail', 'theme-options', 'presscore'), 'class' => '' ),
	'address' => array( 'title' => _x('Address', 'theme-options', 'presscore'), 'class' => '' ),
	'phone' => array( 'title' => _x('Phone', 'theme-options', 'presscore'), 'class' => '' ),
	'working_hours' => array( 'title' => _x('Working hours', 'theme-options', 'presscore'), 'class' => '' )
);

$header_layout_elements = apply_filters( 'header_layout_elements', $header_layout_elements );

$font_sizes = array(
	"big" => _x( 'large', 'theme-options', 'presscore' ),
	"normal" => _x( 'medium', 'theme-options', 'presscore' ),
	"small" => _x( 'small', 'theme-options', 'presscore' )
);

// Divider
$divider_html = '<div class="divider"></div>';

$backgrounds_set_1 = dt_get_images_in( 'images/backgrounds/patterns', 'images/backgrounds', trailingslashit( get_template_directory() ) );

// here we get presets images
$presets_images = dt_get_images_in( 'inc/presets/images', 'inc/presets/images', trailingslashit( get_template_directory() ) );

$id_based_presets_images = array(
	'backgrounds_bottom_bar_bg_image'				=> array(),
	'backgrounds_footer_bg_image'					=> array(),
	'backgrounds_general_bg_image'					=> array(),
	'backgrounds_general_title_bg_image'			=> array(),
	'backgrounds_general_boxed_bg_image'			=> array(),
	'backgrounds_header_bg_image'					=> array(),
	'backgrounds_header_transparent_bg_image'		=> array(),
	'backgrounds_sidebar_bg_image'					=> array(),
	'backgrounds_slideshow_bg_image'				=> array(),
	'backgrounds_background_img'					=> array(),
	'backgrounds_top_bar_bg_image'					=> array(),
	'backgrounds_stripes_stripe_1_bg_image'			=> array(),
	'backgrounds_stripes_stripe_2_bg_image'			=> array(),
	'backgrounds_stripes_stripe_3_bg_image'			=> array(),
);

// convert all
if ( $presets_images ) {
	foreach ( $presets_images as $full=>$thumb ) {
		$img_field_id = explode( '.', $full );

		// ignore
		if ( count($img_field_id) < 3 ) { continue; }

		$img_field_id = $img_field_id[1];
		$clear_key = 'backgrounds_' . str_replace( '-', '_', $img_field_id );

		if ( !isset($id_based_presets_images[ $clear_key ]) ) { continue; }

		$id_based_presets_images[ $clear_key ][ $full ] = $thumb;
	}
}

// merge all
foreach ( $id_based_presets_images as $field=>$value ) {
	$id_based_presets_images[ $field ] = array_merge( $value, $backgrounds_set_1 );
}

// extract all
extract( $id_based_presets_images );

$google_fonts = dt_get_google_fonts_list();

$web_fonts = dt_stylesheet_get_websafe_fonts();

$merged_fonts = array_merge( $web_fonts, $google_fonts );
