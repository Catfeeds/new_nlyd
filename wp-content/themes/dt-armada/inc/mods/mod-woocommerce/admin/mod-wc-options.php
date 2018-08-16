<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Page definition.
 */
$options[] = array(
	"page_title" => _x( "WooCommerce", 'theme-options', 'presscore' ),
	"menu_title" => _x( "WooCommerce", 'theme-options', 'presscore' ),
	"menu_slug" => "of-woocommerce-menu",
	"type" => "page"
);

/**
 * Heading definition.
 */
$options[] = array( "name" => _x('Item settings', 'theme-options', 'presscore'), "type" => "heading" );

/**
 * Item settings.
 */
$options[] = array( "name" => _x("Item settings", "theme-options", 'presscore'), "type" => "block_begin" );

	$options[] = array(
		"name" => _x( "Show product information", "theme-options", 'presscore' ),
		"id" => "woocommerce_display_product_info",
		"std" => "under_image",
		"type" => "radio",
		"options" => array(
			'under_image' => _x( "Under image", "theme-options", 'presscore' ),
			'on_hoover_centered' => _x( "On image hover", "theme-options", 'presscore' )
		)
	);

	$options[] = array( "type" => "divider" );

	$options[] = array(
		"name" => _x( "Product titles", "theme-options", 'presscore' ),
		"id" => "woocommerce_show_product_titles",
		"std" => 1,
		"type" => "radio",
		"options" => $en_dis_options
	);

	$options[] = array( "type" => "divider" );

	$options[] = array(
		"name" => _x( "Product price", "theme-options", 'presscore' ),
		"id" => "woocommerce_show_product_price",
		"std" => 1,
		"type" => "radio",
		"options" => $en_dis_options
	);

	$options[] = array( "type" => "divider" );

	$options[] = array(
		"name" => _x( "Product rating", "theme-options", 'presscore' ),
		"id" => "woocommerce_show_product_rating",
		"std" => 1,
		"type" => "radio",
		"options" => $en_dis_options
	);

	$options[] = array( "type" => "divider" );

	$options[] = array(
		"name" => _x( "Details icon", "theme-options", 'presscore' ),
		"id" => "woocommerce_show_details_icon",
		"std" => 1,
		"type" => "radio",
		"options" => $en_dis_options
	);

	$options[] = array( "type" => "divider" );

	$options[] = array(
		"name" => _x( "Cart icon", "theme-options", 'presscore' ),
		"id" => "woocommerce_show_cart_icon",
		"std" => 1,
		"type" => "radio",
		"options" => $en_dis_options
	);

$options[] = array( "type" => "block_end" );

/**
 * Heading definition.
 */
$options[] = array( "name" => _x('List settings', 'theme-options', 'presscore'), "type" => "heading" );

/**
 * List settings.
 */
$options[] = array( "name" => _x("List settings", "theme-options", 'presscore'), "type" => "block_begin" );

	$options[] = array(
		"name" => _x( "Layout", "theme-options", 'presscore' ),
		"id" => "woocommerce_shop_template_layout",
		"std" => "masonry",
		"type" => "radio",
		"options" => array(
			'masonry' => _x( "Masonry", "theme-options", 'presscore' ),
			'grid' => _x( "Grid", "theme-options", 'presscore' )
		)
	);

	$options[] = array( "type" => "divider" );

	$options[] = array(
		"name" => _x( "Image paddings (px)", "theme-options", 'presscore' ),
		"desc" => _x( "(e.g. 5 pixel padding will give you 10 pixel gaps between images)", "theme-options", 'presscore' ),
		"id" => "woocommerce_shop_template_gap",
		"class" => "mini",
		"std" => 20,
		"type" => "text",
		"sanitize" => "dimensions"
	);

	$options[] = array( "type" => "divider" );

	$options[] = array(
		"name" => _x( "Column minimum width (px)", "theme-options", 'presscore' ),
		"id" => "woocommerce_shop_template_column_min_width",
		"class" => "mini",
		"std" => 370,
		"type" => "text",
		"sanitize" => "dimensions"
	);

	$options[] = array( "type" => "divider" );

	$options[] = array(
		"name" => _x( "Desired columns number", "theme-options", 'presscore' ),
		"desc" => _x( "(used for defult shop page, archives, search results etc.)", "theme-options", 'presscore' ),
		"id" => "woocommerce_shop_template_columns",
		"class" => "mini",
		"std" => 3,
		"type" => "text",
		"sanitize" => "dimensions"
	);

	$options[] = array( "type" => "divider" );

	$options[] = array(
		"name" => _x( "Loading effect", "theme-options", 'presscore' ),
		"id" => "woocommerce_shop_template_loading_effect",
		"std" => "fade_in",
		"type" => "radio",
		"options" => array(
			'none'				=> _x( 'None', 'backend metabox', 'presscore' ),
			'fade_in'			=> _x( 'Fade in', 'backend metabox', 'presscore' ),
			'move_up'			=> _x( 'Move up', 'backend metabox', 'presscore' ),
			'scale_up'			=> _x( 'Scale up', 'backend metabox', 'presscore' ),
			'fall_perspective'	=> _x( 'Fall perspective', 'backend metabox', 'presscore' ),
			'fly'				=> _x( 'Fly', 'backend metabox', 'presscore' ),
			'flip'				=> _x( 'Flip', 'backend metabox', 'presscore' ),
			'helix'				=> _x( 'Helix', 'backend metabox', 'presscore' ),
			'scale'				=> _x( 'Scale', 'backend metabox', 'presscore' )
		)
	);

$options[] = array( "type" => "block_end" );
