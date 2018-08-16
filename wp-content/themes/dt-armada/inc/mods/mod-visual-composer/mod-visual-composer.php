<?php
/**
 * Visual Composer setup.
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( function_exists( 'vc_set_as_theme' ) ) {
	vc_set_as_theme(true);
}

if ( function_exists( 'vc_set_default_editor_post_types' ) ) {
	vc_set_default_editor_post_types( array( 'page', 'post', 'dt_portfolio', 'dt_benefits', 'dt_team' ) );
}

if ( function_exists( 'vc_set_shortcodes_templates_dir' ) ) {

	if ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, '4.7' ) < 0 ) {
		vc_set_shortcodes_templates_dir( PRESSCORE_THEME_DIR . '/inc/shortcodes/vc_templates-old' );
	} else {
		vc_set_shortcodes_templates_dir( PRESSCORE_THEME_DIR . '/inc/shortcodes/vc_templates' );
	}
}

require_once locate_template( '/inc/shortcodes/vc-extensions.php' );

if ( ! function_exists( 'presscore_js_composer_load_bridge' ) ) :

	/**
	 * Visual composer theme shortcodes map
	 *
	 * @since 1.0.0
	 */
	function presscore_js_composer_load_bridge() {
		require_once locate_template( '/inc/shortcodes/js_composer_bridge.php' );
	}

	add_action( 'init', 'presscore_js_composer_load_bridge', 20 );

endif;

if ( ! function_exists( 'js_composer_bridge_admin' ) ) :

	/**
	 * Visual composer custom admin styles
	 *
	 * @since 1.0.0
	 */
	function js_composer_bridge_admin() {
		wp_enqueue_style( 'dt-vc-bridge', PRESSCORE_THEME_URI . '/inc/shortcodes/css/js_composer_bridge.css' );
	}

	add_action( 'admin_enqueue_scripts', 'js_composer_bridge_admin', 15 );

endif;

if ( ! function_exists( 'presscore_vc_inline_editor_scripts' ) ) :

	/**
	 * Visual Composer custom view scripts
	 * 
	 * @since 1.0.0
	 */
	function presscore_vc_inline_editor_scripts() {
		if ( ! function_exists( 'vc_is_inline' ) || ! vc_is_inline() ) {
			return;
		}

		wp_enqueue_script( 'vc-custom-view-by-dt', get_template_directory_uri() . '/inc/shortcodes/js/vc-custom-view.js', array(), false, true );
	}

	add_action( 'admin_enqueue_scripts', 'presscore_vc_inline_editor_scripts', 20 );

endif;

if ( ! function_exists( 'vc_map_get_defaults' ) ) :

	/**
	 * Function to get defaults values for shortcode.
	 * @since 4.6
	 *
	 * @param $tag - shortcode tag
	 *
	 * @return array - list of param=>default_value
	 */
	function vc_map_get_defaults( $tag ) {
		$shortcode = vc_get_shortcode( $tag );
		$params = array();
		if ( is_array( $shortcode ) && isset( $shortcode['params'] ) && ! empty( $shortcode['params'] ) ) {
			foreach ( $shortcode['params'] as $param ) {
				if ( isset( $param['param_name'] ) && 'content' !== $param['param_name'] ) {
					$value = '';
					if ( isset( $param['std'] ) ) {
						$value = $param['std'];
					} elseif ( isset( $param['value'] ) && 'checkbox' !== $param['type'] ) {
						if ( is_array( $param['value'] ) ) {
							$value = current( $param['value'] );
							if ( is_array( $value ) ) {
								// in case if two-dimensional array provided (vc_basic_grid)
								$value = current( $value );
							}
							// return first value from array (by default)
						} else {
							$value = $param['value'];
						}
					}
					$params[ $param['param_name'] ] = $value;
				}
			}
		}

		return $params;
	}

endif;

if ( ! function_exists( 'vc_map_get_attributes' ) ) :

	/**
	 * @param $tag - shortcode tag
	 * @param $atts - shortcode attributes
	 *
	 * @return array - return merged values with provided attributes ( 'a'=>1,'b'=>2 + 'b'=>3,'c'=>4 == 'a'=>1,'b'=>3 )
	 *
	 * @see vc_shortcode_attribute_parse - return union of provided attributes ( 'a'=>1,'b'=>2 + 'b'=>3,'c'=>4 == 'a'=>1,
	 *     'b'=>3, 'c'=>4 )
	 */
	function vc_map_get_attributes( $tag, $atts = array() ) {
		return shortcode_atts( vc_map_get_defaults( $tag ), $atts );
	}

endif;
