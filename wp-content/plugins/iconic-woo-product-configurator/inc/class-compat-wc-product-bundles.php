<?php
/**
 * Class adding compatibility with WooCommerce Product Bundles.
 *
 * NOTE: the changes in this class could be be added to the regular
 * class files if we get more reports of incompatibility issues with
 * similar plugins that need to be aware of PC's stock data.
 *
 * @package iconic-woothumbs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC Product Bundles compatibility Class
 */
class Iconic_PC_Compat_WC_Product_Bundles {
	/**
	 * Init.
	 */
	public static function run() {
		add_action( 'plugins_loaded', array( __CLASS__, 'hooks' ) );
	}

	/**
	 * Add hooks.
	 */
	public static function hooks() {
		if ( ! class_exists( 'WC_Bundles' ) ) {
			return;
		}

		add_filter( 'iconic_pc_enqueue_frontend_assets', array( __CLASS__, 'maybe_enqueue_pc_frontend_assets' ), 10, 2 );
		add_filter( 'iconic_pc_inventory_products', array( __CLASS__, 'maybe_filter_inventory_data' ) );
	}

	/**
	 * Maybe enqueue PC front-end assets if we are on a
	 * bundled product.
	 *
	 * @param bool    $enqueue True to enqueue, false to not.
	 * @param WP_Post $post    WP_Post object.
	 *
	 * @return bool
	 */
	public static function maybe_enqueue_pc_frontend_assets( $enqueue, $post ) {
		if ( ! $post ) {
			return $enqueue;
		}

		$product_object = wc_get_product( $post->ID );

		if ( ! $product_object || 'bundle' !== $product_object->get_type() ) {
			return $enqueue;
		}

		return true;
	}

	/**
	 * Maybe filter the objects passed to the method that outputs
	 * inventory data as JSON, to add additional objects that need
	 * their data output e.g. products in a bundle.
	 *
	 * @param array $objects Array of WC_Product objects.
	 *
	 * @return array
	 */
	public static function maybe_filter_inventory_data( $objects ) {
		global $product;

		if ( 'bundle' !== $product->get_type() ) {
			return $objects;
		}

		$bundle_items = $product->get_bundled_data_items();
		$objects      = array();

		foreach ( $bundle_items as $bundled_item ) {
			if ( Iconic_PC_Product::is_configurator_enabled( $bundled_item->get_product_id() ) ) {
					$objects[] = wc_get_product( $bundled_item->get_product_id() );
			}
		}

		return $objects;
	}
}
