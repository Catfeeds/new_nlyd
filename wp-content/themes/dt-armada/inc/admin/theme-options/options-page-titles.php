<?php
/**
 * Page titles settings
 *
 * @since 1.0.0
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

$page_title = _x( "Page titles", "theme-options", 'presscore' );

/**
 * Page definition.
 */
$options[] = array(
		"page_title"	=> $page_title,
		"menu_title"	=> $page_title,
		"menu_slug"		=> "of-contentarea-menu",
		"type"			=> "page"
);

/**
 * Heading definition.
 */
$options[] = array( "name" => $page_title, "type" => "heading" );

////////////////
// Title area //
////////////////

$options[] = array( "name" => _x( "Title area layout", "theme-options", 'presscore' ), "type" => "block_begin" );

	$options[] = array(
		"name"      => _x( 'Title area layout', 'theme-options', 'presscore' ),
		"id"        => "general-title_align",
		"std"       => 'left',
		"type"      => "images",
		"options"   => array(
			'left'		=> '/inc/admin/assets/images/l-r.gif',
			'right'		=> '/inc/admin/assets/images/r-l.gif',
			'all_left'	=> '/inc/admin/assets/images/l-l.gif',
			'all_right'	=> '/inc/admin/assets/images/r-r.gif',
			'center'	=> '/inc/admin/assets/images/centre.gif'
		)
	);

	$options[] = array( "type" => "divider" );

	$options[] = array(
		"name"		=> _x( "Title area height (px)", "theme-options", 'presscore' ),
		"id"		=> "general-title_height",
		"std"		=> "170",
		"class"		=> "mini",
		"type"		=> "text",
		"sanitize"	=> "slider"
	);

$options[] = array( "type" => "block_end" );

////////////////
// Page title //
////////////////

$options[] = array(	"name" => _x( "Page title", "theme-options", 'presscore' ), "type" => "block_begin" );

	$options[] = array(
		"name"		=> _x( "Page title", "theme-options", 'presscore' ),
		"id"		=> "general-show_titles",
		"std"		=> "1",
		"type"		=> "radio",
		"show_hide"	=> array( "1" => true ),
		"options"	=> $en_dis_options
	);

	$options[] = array( 'type' => 'js_hide_begin' );

		$options[] = array( "type" => "divider" );

		$options[] = array(
			"name"		=> _x( "Title size", "theme-options", 'presscore' ),
			"id"		=> "general-title_size",
			"std"		=> "normal",
			"class"		=> "mini",
			"type"		=> "select",
			"options"	=> array(
				'h1'		=> _x('h1', 'backend metabox', 'presscore'),
				'h2'		=> _x('h2', 'backend metabox', 'presscore'),
				'h3'		=> _x('h3', 'backend metabox', 'presscore'),
				'h4'		=> _x('h4', 'backend metabox', 'presscore'),
				'h5'		=> _x('h5', 'backend metabox', 'presscore'),
				'h6'		=> _x('h6', 'backend metabox', 'presscore'),
				'small'		=> _x('small', 'backend metabox', 'presscore'),
				'normal'	=> _x('medium', 'backend metabox', 'presscore'),
				'big'		=> _x('large', 'backend metabox', 'presscore')
			)
		);

		$options[] = array( "type" => "divider" );

		$options[] = array(
			"name"	=> _x( "Title color", "theme-options", 'presscore' ),
			"id"	=> "general-title_color",
			"std"	=> "#ffffff",
			"type"	=> "color"
		);

	$options[] = array( 'type' => 'js_hide_end' );

$options[] = array(	"type" => "block_end");

/////////////////
// Breadcrumbs //
/////////////////

$options[] = array( "name" => _x( "Breadcrumbs", "theme-options", 'presscore' ), "type" => "block_begin" );

	$options[] = array(
		"name"		=> _x('Breadcrumbs', 'theme-options', 'presscore'),
		"id"		=> 'general-show_breadcrumbs',
		"std"		=> '1',
		"type"		=> 'radio',
		"show_hide"	=> array( "1" => true ),
		"options"	=> $en_dis_options
	);

	$options[] = array( 'type' => 'js_hide_begin' );

		$options[] = array( "type" => "divider" );

		$options[] = array(
			"name"	=> _x( "Breadcrumbs color", "theme-options", 'presscore' ),
			"id"	=> "general-breadcrumbs_color",
			"std"	=> "#ffffff",
			"type"	=> "color"
		);

		$options[] = array( "type" => "divider" );

		$options[] = array(
			"name"		=> _x( "Breadcrumbs background color", "theme-options", 'presscore' ),
			"id"		=> "general-breadcrumbs_bg_color",
			"std"		=> "disabled",
			"type"		=> "radio",
			"options"	=> array(
				'disabled'	=> _x('Disabled', 'backend metabox', 'presscore'),
				'black'		=> _x('Black', 'backend metabox', 'presscore'),
				'white'		=> _x('White', 'backend metabox', 'presscore')
			)
		);

	$options[] = array( 'type' => 'js_hide_end' );

$options[] = array( "type" => "block_end" );

///////////////////////
// Title area style //
///////////////////////
$options[] = array( "name" => _x( "Title area style", "theme-options", 'presscore' ), "type" => "block_begin" );

	$options[] = array(
		"name"		=> _x( "Title background &amp; lines", "theme-options", 'presscore' ),
		"id"		=> "general-title_bg_mode",
		"std"		=> "content_line",
		"type"		=> "radio",
		"show_hide"	=> array(
			"background" => true
		),
		"options"	=> array(
			"disabled"			=> _x( 'Disabled', 'theme-options', 'presscore' ),
			"content_line"		=> _x( 'Content-width line', 'theme-options', 'presscore' ),
			"fullwidth_line"	=> _x( 'Full-width line', 'theme-options', 'presscore' ),
			"background"		=> _x( 'Background', 'theme-options', 'presscore' )
		)
	);

	$options[] = array( 'type' => 'js_hide_begin' );

		$options[] = array( "type" => "divider" );

		$options[] = array(
			'name'    		=> _x( "Title background style", "theme-options", 'presscore' ),
			'id'      		=> "header-background",
			'type'    		=> 'images',
			'std'			=> 'normal',
			'options'		=> array(
				'normal'		=> array(
					'src' => '/inc/admin/assets/images/regular.gif',
					'title' => _x( 'Normal', 'theme-options', 'presscore' ),
					'title_width' => 100
				),
				'overlap'		=> array(
					'src' => '/inc/admin/assets/images/overl.gif',
					'title' => _x( "Overlapping (doesn't work with side header &amp; Photo scroller)", 'theme-options', 'presscore' ),
					'title_width' => 100
				),
				'transparent'	=> array(
					'src' => '/inc/admin/assets/images/transp.gif',
					'title' => _x( "Transparent (doesn't work with side header)", 'theme-options', 'presscore' ),
					'title_width' => 100
				),
			),
			'show_hide'	=> array(
				'transparent' => true
			)
		);

		// hidden open
		$options[] = array( "type" => "js_hide_begin" );

			$options[] = array( "type" => "divider" );

			$options[] = array(
				"type"		=> "radio",
				"id"		=> "header-style",
				"name"		=> _x( "Transparent  background", "theme-options", 'presscore' ),
				"std"		=> "solid_background",
				"options"	=> array(
					'solid_background' => _x( "Enabled", "theme-options", 'presscore' ),
					'disabled' => _x( "Disabled", "theme-options", 'presscore' )
				),
				'show_hide'	=> array(
					'solid_background' => true
				)
			);

			// hidden open
			$options[] = array( "type" => "js_hide_begin" );

				$options[] = array(
					'name'    		=> _x( 'Transparent background color', 'backend metabox', 'presscore' ),
					'id'      		=> "header-transparent_bg_color",
					'type'    		=> 'color',
					'std'			=> '#000000',
				);

				$options[] = array(
					'name'	=> _x( 'Transparent background opacity', 'backend metabox', 'presscore' ),
					'id'	=> "header-transparent_bg_opacity",
					'type'	=> 'slider',
					'std'	=> '100',
					'options' => array(
						'min' => 0,
						'max' => 100
					)
				);

			// hidden close
			$options[] = array( "type" => "js_hide_end" );

			$options[] = array(
				"name"		=> _x( 'Transparent header text color', 'theme-options', 'presscore' ),
				"id"		=> 'header-menu_text_color_mode',
				"std"		=> 'theme',
				"type"		=> 'radio',
				"options"	=> array(
					'light' => _x( 'Light', 'theme-options', 'presscore' ),
					'dark' => _x( 'Dark', 'theme-options', 'presscore' ),
					'theme' => _x( 'From Theme Options', 'theme-options', 'presscore' )
				)
			);

			$options[] = array(
				"name"		=> _x( 'Menu hover decoration color', 'theme-options', 'presscore' ),
				"id"		=> 'header-menu_hover_color_mode',
				"std"		=> 'theme',
				"type"		=> 'radio',
				"options"	=> array(
					'light' => _x( 'Light', 'theme-options', 'presscore' ),
					'dark' => _x( 'Dark', 'theme-options', 'presscore' ),
					'theme' => _x( 'From Theme Options', 'theme-options', 'presscore' )
				)
			);

			$options[] = array(
				"name"		=> _x( 'Transparent header layout elements color', 'theme-options', 'presscore' ),
				"id"		=> 'header-menu_top_bar_color_mode',
				"std"		=> 'theme',
				"type"		=> 'radio',
				"options"	=> array(
					'light' => _x( 'Light', 'theme-options', 'presscore' ),
					'dark' => _x( 'Dark', 'theme-options', 'presscore' ),
					'theme' => _x( 'From Theme Options', 'theme-options', 'presscore' )
				)
			);

		// hidden close
		$options[] = array( "type" => "js_hide_end" );

		$options[] = array( "type" => "divider" );

		$options[] = array(
			"name"	=> _x( "Background color", "theme-options", 'presscore' ),
			"id"	=> "general-title_bg_color",
			"std"	=> "#ffffff",
			"type"	=> "color"
		);

		$options[] = array(
			'type' 			=> 'background_img',
			'id' 			=> "general-title_bg_image",
			'name' 			=> _x( 'Add background image', 'theme-options', 'presscore' ),
			'preset_images' => $backgrounds_general_title_bg_image,
			'std' 			=> array(
				'image'			=> '',
				'repeat'		=> 'repeat',
				'position_x'	=> 'center',
				'position_y'	=> 'center'
			),
		);

		$options[] = array(
			"name"      => _x( 'Fullscreen ', 'theme-options', 'presscore' ),
			"id"    	=> "general-title_bg_fullscreen",
			"type"  	=> 'checkbox',
			'std'   	=> 0
		);

		$options[] = array(
			"name"      => _x( 'Fixed ', 'theme-options', 'presscore' ),
			"id"    	=> "general-title_bg_fixed",
			"type"  	=> 'checkbox',
			'std'   	=> 0
		);

		$options[] = array(
			"name"		=> _x( "Enable parallax &amp; Parallax speed", "theme-options", 'presscore' ),
			"id"		=> "general-title_bg_parallax",
			"std"		=> "0",
			"class"		=> "mini",
			"type"		=> "text"
		);

	$options[] = array( 'type' => 'js_hide_end' );

$options[] = array( "type" => "block_end" );
