<?php
/**
 * This class is responsible for managing the pricing of products in the YAYDP system.
 * It contains methods for calculating prices based on various factors such as discounts, taxes, and fees
 *
 * @package YayPricing\Classes
 */

namespace YAYDP\Core\Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Declare class
 */
class YAYDP_Pricing_Manager {

	/**
	 * Constructor
	 */
	public function __construct() {
		if ( \yaydp_is_request( 'frontend' ) || \yaydp_is_request( 'ajax' ) ) {
			\YAYDP\YAYDP_Ajax::get_instance();
			\YAYDP\Core\Manager\YAYDP_Product_Pricing_Manager::get_instance();
			\YAYDP\Core\Manager\YAYDP_Cart_Discount_Manager::get_instance();
			\YAYDP\Core\Manager\YAYDP_Checkout_Fee_Manager::get_instance();
			\YAYDP\Core\Shortcode\YAYDP_Shortcode_Handler::get_instance();
			add_action( 'wp_body_open', array( $this, 'bottom_encouraged_notice_section' ), 100 );
			add_action( 'custom_wp_body_open', array( $this, 'bottom_encouraged_notice_section' ), 100 );
		}
	}

	/**
	 * This function displays the encouraged notice section on the front-end of the website
	 */
	public function bottom_encouraged_notice_section() {
		if ( ! \yaydp_is_request( 'frontend' ) ) {
			return;
		}?>
		<section id="yaydp-bottom-encouraged-notice"> 
			<?php
			$current_page       = \yaydp_current_frontend_page();
			$hide_notice_cookie = isset( $_COOKIE[ "yaydp_hide_{$current_page}_notice" ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ "yaydp_hide_{$current_page}_notice" ] ) ) : null;
			if ( empty( $hide_notice_cookie ) || 'true' !== $hide_notice_cookie ) {
				do_action( 'yaydp_bottom_product_pricing_encouraged_section' );
				do_action( 'yaydp_bottom_cart_discount_encouraged_section' );
				do_action( 'yaydp_bottom_checkout_fee_encouraged_section' );
			}
			?>
		</section>
		<?php
	}
}

new YAYDP_Pricing_Manager();
