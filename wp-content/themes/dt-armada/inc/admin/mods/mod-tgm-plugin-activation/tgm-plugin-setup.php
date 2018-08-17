<?php
/**
 * TGM plugin setup.
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

require_once 'class-tgm-plugin-activation.php';

if ( ! function_exists( 'presscore_register_required_plugins' ) ) :

	function presscore_register_required_plugins() {
		$plugins = array(

			// Visual Composer
			array(
				'name' => 'WPBakery Visual Composer',
				'slug' => 'js_composer',
				'source' => '/js_composer.zip',
				'required' => false,
				'version' => '5.4.5',
				'force_activation' => false,
				'force_deactivation' => false
			),

			// Revolution slider
			array(
				'name' => 'Revolution Slider',
				'slug' => 'revslider',
				'source' => '/revslider.zip',
				'required' => false,
				'version' => '5.4.6.3',
				'force_activation' => false,
				'force_deactivation' => false
			),

			// Go Pricing config
			array(
				'name' => 'GO Pricing Tables',
				'slug' => 'go_pricing',
				'source' => '/go_pricing.zip',
				'required' => false,
				'version' => '3.3.7',
				'force_activation' => false,
				'force_deactivation' => false
			),

			array(
				'name' => 'Contact Form 7',
				'slug' => 'contact-form-7',
				'required' => false
			),

			array(
				'name' => 'Recent Tweets Widget',
				'slug' => 'recent-tweets-widget',
				'required' => false
			)
		);

		/**
		 * Array of configuration settings. Amend each line as needed.
		 * If you want the default strings to be available under your own theme domain,
		 * leave the strings uncommented.
		 * Some of the strings are added into a sprintf, so see the comments at the
		 * end of each line for what each argument will be.
		 */
		$config = array(
			'default_path' 		=> PRESSCORE_PLUGINS_DIR,
			'parent_slug' 	=> 'themes.php',
			'menu'         		=> 'install-required-plugins',
			'capability'   => 'edit_theme_options',
			'has_notices'      	=> true,
			'is_automatic'    	=> false,
			'message' 			=> '',
			'strings'      		=> array(
				'page_title'                       			=> __( 'Install Required Plugins', 'presscore' ),
				'menu_title'                       			=> __( 'Install Plugins', 'presscore' ),
				'installing'                       			=> __( 'Installing Plugin: %s', 'presscore' ), // %1$s = plugin name
				'oops'                             			=> __( 'Something went wrong with the plugin API.', 'presscore' ),
				'notice_can_install_required'     			=> _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'presscore' ), // %1$s = plugin name(s)
				'notice_can_install_recommended'			=> _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'presscore' ), // %1$s = plugin name(s)
				'notice_cannot_install'  					=> false,
				'notice_can_activate_required'    			=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'presscore' ), // %1$s = plugin name(s)
				'notice_can_activate_recommended'			=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'presscore' ), // %1$s = plugin name(s)
				'notice_cannot_activate' 					=> false,
				'notice_ask_to_update' 						=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'presscore' ), // %1$s = plugin name(s)
				'notice_cannot_update' 						=> false,
				'install_link' 					  			=> _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'presscore' ),
				'activate_link' 				  			=> _n_noop( 'Activate installed plugin', 'Activate installed plugins', 'presscore' ),
				'return'                           			=> __( 'Return to Required Plugins Installer', 'presscore' ),
				'plugin_activated'                 			=> __( 'Plugin activated successfully.', 'presscore' ),
				'complete' 									=> __( 'All plugins installed and activated successfully. %s', 'presscore' ), // %1$s = dashboard link
				'nag_type'									=> 'updated'
			)
		);

		tgmpa( $plugins, $config );

	}
	add_action( 'tgmpa_register', 'presscore_register_required_plugins' );

endif;

/**
 * This function prevents plugin update api modification, so tgmpa can do its job.
 */
function presscore_remove_plugins_update_filters() {
	$tgmpa_update = ( isset( $_GET['tgmpa-update'] ) ? $_GET['tgmpa-update'] : '' );

	if ( 'update-plugin' !== $tgmpa_update ) {
		return;
	}

	$tags_to_wipe = array(
		'pre_set_site_transient_update_plugins',
		'update_api',
		'upgrader_pre_download',
	);

	// Wipe out filters.
	foreach ( $tags_to_wipe as $tag ) {
		remove_all_filters( $tag );
	}
}
add_action( 'load-appearance_page_install-required-plugins', 'presscore_remove_plugins_update_filters' );