<?php
/**
 * Fonts
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Page definition.
 */
$options[] = array(
	"page_title"    => _x( "Fonts", 'theme-options', 'presscore' ),
	"menu_title"    => _x( "Fonts", 'theme-options', 'presscore' ),
	"menu_slug"     => "of-fonts-menu",
	"type"          => "page"
);

/**
 * Heading definition.
 */
$options[] = array( "name" => _x('Fonts', 'theme-options', 'presscore'), "type" => "heading" );

/**
 * Base font.
 */
$options[] = array( "name" => _x('Basic font', 'theme-options', 'presscore'), "type" => "block_begin" );

	// select
	$options[] = array(
		"desc"      => '',
		"name"      => _x( 'Choose basic font-family', 'theme-options', 'presscore' ),
		"id"        => "fonts-font_family",
		"std"       => "Open Sans",
		"type"      => "web_fonts",
		"options"   => $merged_fonts,
	);

	// font sizes
	$font_sizes = array(
		'small_size'   => array(
			'font_std' => 11,
			'font_desc' => _x( 'Small font size', 'theme-options', 'presscore' ),

			'lh_std' => 20,
			'lh_desc' => _x( 'Small line-height', 'theme-options', 'presscore' )
		),

		'normal_size'   => array(
			'font_std' => 13,
			'font_desc' => _x( 'Medium font size', 'theme-options', 'presscore' ),

			'lh_std' => 20,
			'lh_desc' => _x( 'Medium line-height', 'theme-options', 'presscore' )
		),

		'big_size'   => array(
			'font_std' => 15,
			'font_desc' => _x( 'Large font size', 'theme-options', 'presscore' ),

			'lh_std' => 20,
			'lh_desc' => _x( 'Large line-height', 'theme-options', 'presscore' )
		)
	);

	foreach ( $font_sizes as $id=>$data ) {

		// slider
		$options[] = array(
			"desc"      => '',
			"name"      => $data['font_desc'],
			"id"        => "fonts-" . $id,
			"std"       => $data['font_std'], 
			"type"      => "slider",
			"options"   => array( 'min' => 9, 'max' => 71 ),
			"sanitize"  => 'font_size'
		);

		// slider
		$options[] = array(
			"desc"      => '',
			"name"      => $data['lh_desc'],
			"id"        => "fonts-" . $id . "_line_height",
			"std"       => $data['lh_std'], 
			"type"      => "slider",
			"options"   => array( 'min' => 9, 'max' => 71 )
		);

	}

$options[] = array( "type" => "block_end");

/**
 * Headers font.
 */
$options[] = array( "name" => _x('Headers fonts', 'theme-options', 'presscore'), "type" => "block_begin" );

	// headers
	$headers = presscore_themeoptions_get_headers_defaults();

	foreach ( $headers as $id=>$opts ) {

		// do not show divider for first header
		if ( $id != 'h1' ) {

			// divider
			$options[] = array(
				"type" => 'divider'
			);

		}

		// title
		$options[] = array(
			"type" => 'title',
			"name" => $opts['desc']
		);

		// select
		$options[] = array(
			"name"      => _x( 'Font-family', 'theme-options', 'presscore' ),
			"id"        => "fonts-" . $id . "_font_family",
			"std"       => (!empty($opts['ff']) ? $opts['ff'] : "Open Sans"),
			"type"      => "web_fonts",
			"options"   => $merged_fonts,
		);

		// slider
		$options[] = array(
			"desc"      => '',
			"name"      => _x( 'Font-size', 'theme-options', 'presscore' ),
			"id"        => "fonts-" . $id . "_font_size",
			"std"       => $opts['fs'], 
			"type"      => "slider",
			"options"   => array( 'min' => 9, 'max' => 71 ),
			"sanitize"  => 'font_size'
		);

		// slider
		$options[] = array(
			"desc"        => '',
			"name"        => _x( 'Line-height', 'theme-options', 'presscore' ),
			"id"        => "fonts-" . $id ."_line_height",
			"std"        => $opts['lh'], 
			"type"        => "slider",
		);

		// checkbox
		$options[] = array(
			"desc"      => '',
			"name"      => _x( 'Uppercase', 'theme-options', 'presscore' ),
			"id"        => 'fonts-' . $id . '_uppercase',
			"type"      => 'checkbox',
			'std'       => $opts['uc']
		);

	}

$options[] = array( "type" => "block_end");
