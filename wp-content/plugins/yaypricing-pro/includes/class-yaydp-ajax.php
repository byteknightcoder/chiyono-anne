<?php
/**
 * Used to handle asynchronous requests and responses between the client and server.
 *
 * @package YayPricing\Ajax
 */

namespace YAYDP;

/**
 * YAYDP_Ajax class
 */
class YAYDP_Ajax {

	use \YAYDP\Traits\YAYDP_Singleton;

	/**
	 * Constructor
	 */
	protected function __construct() {
		add_action( 'wp_ajax_yaydp-update-encouraged-notice', array( $this, 'update_encouraged_notice' ) );
		add_action( 'wp_ajax_nopriv_yaydp-update-encouraged-notice', array( $this, 'update_encouraged_notice' ) );
	}

	/**
	 * This function is responsible for displaying a notice to the user encouraging customer to buy more
	 */
	public function update_encouraged_notice() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'yaydp_frontend_nonce' ) ) {
			return wp_send_json_error( array( 'mess' => __( 'Verify nonce failed', 'yaypricing' ) ) );

		}
		
		try {
			$content_types = json_decode( isset( $_POST['content_types'] ) ? sanitize_text_field( wp_unslash( $_POST['content_types'] ) ) : '[]' );
			$product_pricing_content = '';
			$cart_discount_content = '';
			$checkout_fee_content = '';
			if ( in_array( 'product_pricing', $content_types ) ) {
				$product_pricing_content = do_shortcode( '[yaypricing-product-pricing-encouraged-notice]' );
			}
			if ( in_array( 'cart_discount', $content_types ) ) {
				$cart_discount_content = do_shortcode( '[yaypricing-cart-discount-encouraged-notice]' );
			}
			if ( in_array( 'checkout_fee', $content_types ) ) {
				$checkout_fee_content = do_shortcode( '[yaypricing-checkout-fee-encouraged-notice]' );
			}
			wp_send_json_success(
				array(
					'product_pricing' => $product_pricing_content,
					'cart_discount' => $cart_discount_content,
					'checkout_fee' => $checkout_fee_content,
				)
			);
		} catch ( \Error $error ) {
			\YAYDP\YAYDP_Logger::log_exception_message( $error, true );
		} catch ( \Exception $exception ) {
			\YAYDP\YAYDP_Logger::log_exception_message( $exception, true );
		}

	}
}
