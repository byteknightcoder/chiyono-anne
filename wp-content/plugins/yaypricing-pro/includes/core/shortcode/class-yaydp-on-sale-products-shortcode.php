<?php
/**
 * This class handles the "yadyp-on-sale-products" shortcode and displays a list of on-sale products
 *
 * @since 2.4
 *
 * @package YayPricing\Shortcode
 */

namespace YAYDP\Core\Shortcode;

/**
 * Declare class
 */
class YAYDP_On_Sale_Products_Shortcode {

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
		$rule_ids            = \YAYDP\Settings\YAYDP_Product_Pricing_Settings::get_instance()->get_on_sale_products_rules();
		$on_sale_products    = self::get_on_sale_products( $rule_ids );
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

	/**
	 * Returns list on-sale products based on given ids.
	 *
	 * @param array $rule_ids List id.
	 */
	public static function get_on_sale_products( $rule_ids ) {
		$running_rules = \yaydp_get_running_product_pricing_rules();
		$products      = array();
		foreach ( $running_rules as $rule ) {
			if ( in_array( $rule->get_id(), $rule_ids, true ) || in_array( 'all', $rule_ids, true ) ) {
				$is_buy_x_get_y = \yaydp_is_buy_x_get_y( $rule );
				if ( $is_buy_x_get_y ) {
					$filters    = $rule->get_receive_filters();
					$match_type = 'any';
				} else {
					$filters    = $rule->get_buy_filters();
					$match_type = $rule->get_match_type_of_buy_filters();
				}
				$capable_products = \YAYDP\Helper\YAYDP_Matching_Products_Helper::get_raw_matching_products_by_rule( $filters, $match_type, $is_buy_x_get_y );
				$products         = array_merge( $products, $capable_products );
				$products         = \YAYDP\Helper\YAYDP_Matching_Products_Helper::simplify_product_list( $products, $match_type );
			}
		}
		\YAYDP\Helper\YAYDP_Matching_Products_Helper::sort_products_by_name( $products );
		return $products;
	}
}
