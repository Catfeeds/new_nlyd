<?php
/**
 * Image Hovers.
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Page definition.
 */
$options[] = array(
		"page_title"	=> _x( "Images Styling &amp; Hovers", 'theme-options', 'presscore' ),
		"menu_title"	=> _x( "Images Styling &amp; Hovers", 'theme-options', 'presscore' ),
		"menu_slug"		=> "of-imghoovers-menu",
		"type"			=> "page"
);

/**
 * Heading definition.
 */
$options[] = array( "name" => _x('Images Styling &amp; Hovers', 'theme-options', 'presscore'), "type" => "heading" );

/**
 * Styling.
 */
$options[] = array(	"name" => _x('Styling', 'theme-options', 'presscore'), "type" => "block_begin" );

	// radio
	$options[] = array(
		"name"		=> _x('Image &amp; hover decoration', 'theme-options', 'presscore'),
		// "id"		=> 'hover-style',
		"id"		=> 'image_hover-style',
		"std"		=> 'none',
		"type"		=> 'radio',
		"options"	=> presscore_themeoptions_get_hoover_options()
	);

$options[] = array(	"type" => "block_end");

/**
 * Hover color.
 */
$options[] = array(	"name" => _x('Hover color overlay', 'theme-options', 'presscore'), "type" => "block_begin" );

	// radio
	$options["image_hover-color_mode"] = array(
		"name"		=> _x( "Hovers background color", "theme-options", 'presscore' ),
		"id"		=> "image_hover-color_mode",
		"std"		=> "accent",
		"type"		=> "radio",
		"show_hide"	=> array(
			'color' 	=> "image-hover-color-mode-color",
			'gradient'	=> "image-hover-color-mode-gradient"
		),
		"options"	=> array(
			"accent"	=> _x( 'Accent', 'theme-options', 'presscore' ),
			"color"		=> _x( 'Custom color', 'theme-options', 'presscore' ),
			"gradient"	=> _x( 'Custom gradient', 'theme-options', 'presscore' )
		)
	);

	// hidden area
	$options[] = array( "type" => "js_hide_begin", "class" => "image_hover-color_mode image-hover-color-mode-color" );

		// colorpicker
		$options["image_hover-color"] = array(
			"name"	=> "&nbsp;",
			"id"	=> "image_hover-color",
			"std"	=> "#ffffff",
			"type"	=> "color"
		);

	$options[] = array( "type" => "js_hide_end" );

	// hidden area
	$options[] = array( "type" => "js_hide_begin", "class" => "image_hover-color_mode image-hover-color-mode-gradient" );

		// colorpicker
		$options["image_hover-color_gradient"] = array(
			"name"	=> "&nbsp;",
			"id"	=> "image_hover-color_gradient",
			"std"	=> array( '#ffffff', '#000000' ),
			"type"	=> "gradient"
		);

	$options[] = array( "type" => "js_hide_end" );

$options[] = array(	"type" => "block_end");

/**
 * Hover opacity.
 */
$options[] = array(	"name" => _x('Hover opacity', 'theme-options', 'presscore'), "type" => "block_begin" );

	///////////////////////////////
	// Opacity for plain hovers //
	///////////////////////////////

	$options[] = array(
		"desc"		=> '',
		"name"		=> _x( 'Background opacity for plain hovers', 'theme-options', 'presscore' ),
		"id"		=> "image_hover-opacity",
		"std"		=> 100, 
		"type"		=> "slider",
	);

	/////////////////////////////////////////////
	// Opacity for hovers with text and icons //
	/////////////////////////////////////////////

	$options[] = array(
		"desc"		=> '',
		"name"		=> _x( 'Background opacity for hovers with text and icons', 'theme-options', 'presscore' ),
		"id"		=> "image_hover-with_icons_opacity",
		"std"		=> 100, 
		"type"		=> "slider",
	);

$options[] = array(	"type" => "block_end");
