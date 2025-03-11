<?php
/**
 * Divi theme compatibility Class.
 *
 * URL: https://www.elegantthemes.com/
 *
 * @package Iconic_PC
 */

defined( 'ABSPATH' ) || exit;

/**
 * Iconic_PC_Compat_Divi class.
 */
class Iconic_PC_Compat_Divi {

	/**
	 * Init.
	 */
	public static function run() {
		add_action( 'woocommerce_before_single_product', array( __CLASS__, 'maybe_disable_pc_gallery' ), 21 );
	}

	/**
	 * Maybe disable PC gallery.
	 *
	 * @return void
	 */
	public static function maybe_disable_pc_gallery() {
		global $themename, $jckpc, $post;

		if ( ! isset( $themename ) || 'Divi' !== $themename ) {
			return;
		}

		// Only disable Configurator default action when Divi builder is enabled for the product.
		if ( is_product() && 'on' === get_post_meta( $post->ID, '_et_pb_use_builder', true ) ) {
			remove_action( 'woocommerce_before_single_product_summary', array( $jckpc, 'display_product_image' ), 20 );
		}
	}

}
