<?php
/**
 * Skins.
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Page definition.
 */
$options[] = array(
		"page_title"	=> _x( "Skins", 'theme-options', 'presscore' ),
		"menu_title"	=> _x( "Skins", 'theme-options', 'presscore' ),
		"menu_slug"		=> "of-skins-menu",
		"type"			=> "page"
);

/**
 * Heading definition.
 */
$options[] = array( "name" => _x( 'Skins', 'theme-options', 'presscore' ), "type" => "heading" );

/**
 * Skins.
 */
$options[] = array(	"name" => _x( 'Skins', 'theme-options', 'presscore' ), "type" => "block_begin" );

	$options[] = array(
		"name"      => '',
		"desc"      => '',
		"id"        => "preset",
		"std"       => 'none', 
		"type"      => "images",
		"options"   => optionsframework_get_presets_list()
	);

$options[] = array(	"type" => "block_end");
