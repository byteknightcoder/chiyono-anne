<?php
/**
 * This class handles the "yadyp-matching-products" shortcode and displays a list of on-sale products
 *
 * @since 2.4
 *
 * @package YayPricing\Shortcode
 */

namespace YAYDP\Core\Shortcode;

/**
 * Declare class
 */
class YAYDP_Matching_Products_Shortcode {

	use \YAYDP\Traits\YAYDP_Singleton;

	/**
	 * Constructor
	 */
	protected function __construct() {

	}

	/**
	 * Function that display the shortcode content
	 *
	 * @param array $atts Attributes passed in shortcode.
	 */
	public static function render_shortcode( $atts ) {
		$matching_rule         = null;
		$args                  = shortcode_atts(
			array(
				'id'    => 'yaydp_12345678',
				'image' => false,
				'link'  => false,
			),
			$atts
		);
		$product_pricing_rules = \yaydp_get_product_pricing_rules();
		foreach ( $product_pricing_rules as $rule ) {
			if ( $rule->get_rule_id() === $args['id'] ) {
				$matching_rule = $rule;
				break;
			}
		}
		if ( empty( $matching_rule ) ) {
			return '';
		}
		$on_sale_products    = \YAYDP\Core\Shortcode\YAYDP_On_Sale_Products_Shortcode::get_on_sale_products( array( $matching_rule->get_id() ) );
		$on_sale_product_ids = array_map(
			function( $product ) {
				return $product->get_id();
			},
			$on_sale_products
		);
		if ( empty( $on_sale_product_ids ) ) {
			$on_sale_product_ids = array( -1 );
		}
		$query_products = new \WP_Query(
			array(
				'post_type'   => array( 'product', 'product_variation' ),
				'post_status' => 'publish',
				'post__in'    => $on_sale_product_ids,
				'orderby'     => 'name',
				'order'       => 'asc',
			)
		);
		ob_start();
		if ( $query_products->have_posts() ) {
			do_action( 'woocommerce_before_shop_loop' );
			\woocommerce_product_loop_start();
			while ( $query_products->have_posts() ) {
				$query_products->the_post();
				wc_get_template_part( 'content', 'product' );
			}
			\woocommerce_product_loop_end();
			do_action( 'woocommerce_after_shop_loop' );
			\wp_reset_postdata();
		} else {
			do_action( 'woocommerce_no_products_found' );
		}
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
}
