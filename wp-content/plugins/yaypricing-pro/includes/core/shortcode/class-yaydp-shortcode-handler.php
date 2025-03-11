<?php
/**
 * The YAYDP_Shortcode_Handler class handles the shortcodes used by the YAYDP plugin
 *
 * @since 2.4
 *
 * @package YayPricing\Shortcode
 */

namespace YAYDP\Core\Shortcode;

/**
 * Declare
 */
class YAYDP_Shortcode_Handler {

	use \YAYDP\Traits\YAYDP_Singleton;

	/**
	 * Constructor
	 *
	 * Declare add shortcode hooks
	 */
	protected function __construct() {
		add_shortcode( 'yaydp-matching-products', array( \YAYDP\Core\Shortcode\YAYDP_Matching_Products_Shortcode::get_instance(), 'render_shortcode' ) );
		add_shortcode( 'yaydp-on-sale-products', array( \YAYDP\Core\Shortcode\YAYDP_On_Sale_Products_Shortcode::get_instance(), 'render_shortcode' ) );
	}
}
