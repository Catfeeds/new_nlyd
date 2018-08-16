<?php
/**
 * Microsite meta boxes.
 * @since presscore 2.2
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

$nav_menus = get_terms( 'nav_menu' );
$nav_menus_clear = array( 0 => _x('Primary location menu', 'backend metabox', 'presscore'), -1 => _x('Default menu', 'backend metabox', 'presscore') );

foreach ( $nav_menus as $nav_menu ) {
	$nav_menus_clear[ $nav_menu->term_id ] = $nav_menu->name;
}

$logo_field_title = _x('Logo', 'backend metabox', 'presscore');
$logo_hd_field_title = _x('High-DPI (retina) logo', 'backend metabox', 'presscore');

$prefix = '_dt_microsite_';

$DT_META_BOXES[] = array(
	'id'		=> 'dt_page_box-microsite',
	'title' 	=> _x('Microsite', 'backend metabox', 'presscore'),
	'pages' 	=> array( 'page' ),
	'context' 	=> 'side',
	'priority' 	=> 'default',
	'fields' 	=> array(

		// Page layout
		array(
			'name'    	=> _x('Page layout:', 'backend metabox', 'presscore'),
			'id'      	=> "{$prefix}page_layout",
			'type'    	=> 'radio',
			'std'		=> 'full',
			'options'	=> array(
				'wide' => _x('full-width', 'backend metabox', 'presscore'),
				'boxed' => _x('boxed', 'backend metabox', 'presscore')
			)
		),

		// Hide contemt
		array(
			'name' => _x('Hide:', 'backend metabox', 'presscore'),
			'id'   => "{$prefix}hidden_parts",
			'type' => 'checkbox_list',
			'options' => array(
				'header' => _x('header &amp; top bar', 'backend metabox', 'presscore'),
				'floating_menu' => _x('floating menu', 'backend metabox', 'presscore'),
				'content' => _x('content area', 'backend metabox', 'presscore'),
				'bottom_bar' => _x('bottom bar', 'backend metabox', 'presscore')
			),
			'top_divider'	=> true
		),

		// Enable beautiful page loading
		array(
			'name'    		=> _x('Beautiful loading:', 'backend metabox', 'presscore'),
			'id'      		=> "{$prefix}page_loading",
			'type'    		=> 'radio',
			'std'			=> 'accent',
			'options'		=>array(
				'disabled' => _x( 'Disabled', 'backend metabox', 'presscore' ),
				'light' => _x( 'Light', 'backend metabox', 'presscore' ),
				'accent' => _x( 'Accent', 'backend metabox', 'presscore' )
			),
			'top_divider'	=> true
		),

		// ------------------ Bottom logo
		array(
			'type' => 'heading',
			'name' => _x( 'Logo in bottom line', 'backend metabox', 'presscore' ),
			'id'   => 'bottom_logo_heading', // Not used but needed for plugin
		),

			// Regular logo
			array(
				'name'				=> $logo_field_title,
				'id'               => "{$prefix}bottom_logo_regular",
				'type'             => 'image_advanced_mk2',
				'max_file_uploads'	=> 1
			),

			// HD logo
			array(
				'name'				=> $logo_hd_field_title,
				'id'               => "{$prefix}bottom_logo_hd",
				'type'             => 'image_advanced_mk2',
				'max_file_uploads'	=> 1
			),

		// ------------------ Floating logo
		array(
			'type' => 'heading',
			'name' => _x( 'Floating menu', 'backend metabox', 'presscore' ),
			'id'   => 'floating_logo_heading', // Not used but needed for plugin
		),

			// Regular logo
			array(
				'name'				=> $logo_field_title,
				'id'               => "{$prefix}floating_logo_regular",
				'type'             => 'image_advanced_mk2',
				'max_file_uploads'	=> 1
			),

			// HD logo
			array(
				'name'				=> $logo_hd_field_title,
				'id'               => "{$prefix}floating_logo_hd",
				'type'             => 'image_advanced_mk2',
				'max_file_uploads'	=> 1
			),

		// ------------------ Favicon
		array(
			'type' => 'heading',
			'name' => _x( 'Favicon', 'backend metabox', 'presscore' ),
			'id'   => 'favicon_heading', // Not used but needed for plugin
		),

			array(
				'id'               => "{$prefix}favicon",
				'type'             => 'image_advanced_mk2',
				'max_file_uploads'	=> 1
			),

		// Link
		array(
			'name'	=> _x('Target link:', 'backend metabox', 'presscore'),
			'id'    => "{$prefix}logo_link",
			'type'  => 'text',
			'std'   => '',
			'top_divider'	=> true
		),

		// Primary menu list
		array(
			'name'     		=> _x('Primary menu:','backend metabox', 'presscore'),
			'id'       		=> "{$prefix}primary_menu",
			'type'     		=> 'select',
			'options'  		=> $nav_menus_clear,
			'std'			=> 0,
			'top_divider'	=> true
		),

		// Custom CSS
		array(
			'name'	=> _x('Custom CSS','backend metabox', 'presscore'),
			'id'	=> "{$prefix}custom_css",
			'type'	=> 'textarea',
			'cols'	=> 20,
			'rows'	=> 4,
			'top_divider'	=> true
		),

	),
	'only_on'	=> array( 'template' => array('template-microsite.php') ),
);