<?php
/**
 * Albums helpers.
 *
 * @since   1.2.5
 */

// File Security Check.
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'presscore_mod_albums_share_buttons_link' ) ) :

	/**
	 * Point popup share buttons to attachment single page.
	 *
	 * @param  array  $args
	 * @return array
	 */
	function presscore_mod_albums_share_buttons_link( $args = array() ) {
		if ( ! empty( $args['img_id'] ) ) {
			$args['custom'] = ( isset( $args['custom'] ) ? $args['custom'] : '' );
			$args['custom'] .= ' data-dt-location="' . esc_attr( get_permalink( $args['img_id'] ) ) . '" ';
		}
		return $args;
	}

endif;