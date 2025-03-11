<?php
/**
 * Class adding compatibility with Be theme.
 *
 * @package Iconic_PC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Be Theme compatibility Class
 */
class Iconic_PC_Compat_Be_Theme {
	/**
	 * Init.
	 */
	public static function run() {
		/*
		This code comment checks whether the global $product variable is a string or not,
		and if so, it converts it to a WC_Product. Since this solution is intended to solve
		a common problem that could arise with any theme that alters the $product data variable,
		we did not include a check for the specific theme.
		*/
		add_action( 'woocommerce_before_single_product', array( __CLASS__, 'fix_global_product_var' ), 10 );
	}

	/**
	 * Fix global $product variable.
	 *
	 * @return void
	 */
	public static function fix_global_product_var() {
		global $product;

		if ( empty( $product ) || ! is_string( $product ) ) {
			return;
		}

		$args = array(
			'name'        => $product,
			'post_type'   => 'product',
			'numberposts' => 1,
		);

		$posts = get_posts( $args );
		if ( count( $posts ) && ! empty( $posts[0] ) ) {
			$product = wc_get_product( $posts[0]->ID );
		}
	}
}
