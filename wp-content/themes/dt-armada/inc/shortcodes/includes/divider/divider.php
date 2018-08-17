<?php
/**
 * Divider shortcode.
 *
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'DT_Shortcode_Divider', false ) ) {

	class DT_Shortcode_Divider extends DT_Shortcode {

		static protected $instance;

		protected $shortcode_name = 'dt_divider';
		protected $plugin_name = 'dt_mce_plugin_shortcode_divider';

		public static function get_instance() {
			if ( !self::$instance ) {
				self::$instance = new DT_Shortcode_Divider();
			}
			return self::$instance;
		}

		protected function __construct() {
			// add_shortcode( $this->shortcode_name, array($this, 'shortcode') );
			presscore_add_puny_shortcode( $this->shortcode_name, array($this, 'shortcode') );
			$this->map_vc_shortcode();
			$tinymce_button = new DT_ADD_MCE_BUTTON( $this->plugin_name, basename(dirname(__FILE__)), false );
		}

		public function shortcode( $atts, $content = null ) {
			extract( shortcode_atts( array(
				'style' => 'thin'
			), $atts ) );

			switch( $style ) {
				case 'thick': $class = 'hr-thick'; break;
				default: $class = 'hr-thin';
			}

			$output = '<div class="' . esc_attr( $class ) . '"></div>';

			return $output;
		}

		protected function map_vc_shortcode() {

			if ( ! function_exists( 'vc_map' ) ) {
				return false;
			}

			vc_map( array(
				"deprecated" => '4.6.0',
				"name" => __("Divider", 'presscore'),
				"base" => "dt_divider",
				"icon" => "dt_vc_ico_divider",
				"class" => "dt_vc_sc_divider",
				"weight" => -1,
				"params" => array(
					array(
						"type" => "dropdown",
						"class" => "",
						"heading" => __("Divider style", 'presscore'),
						"admin_label" => true,
						"param_name" => "style",
						"value" => array(
							"Thin" => "thin",
							"Thick" => "thick"
						),
						"description" => ""
					)
				)
			) );
		}

	}

	// create shortcode
	DT_Shortcode_Divider::get_instance();

}
