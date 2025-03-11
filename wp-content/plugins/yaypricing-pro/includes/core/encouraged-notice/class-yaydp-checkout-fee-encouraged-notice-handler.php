<?php
/**
 * Managing handler for checkout fee encouraged notice
 *
 * @package YayPricing\EncouragedNotice
 *
 * @since 2.4
 */

namespace YAYDP\Core\Encouraged_Notice;

defined( 'ABSPATH' ) || exit;

/**
 * Declare class
 */
class YAYDP_Checkout_Fee_Encouraged_Notice_Handler {

	use \YAYDP\Traits\YAYDP_Singleton;

	/**
	 * Constructor
	 */
	protected function __construct() {
		add_action( 'yaydp_bottom_checkout_fee_encouraged_section', array( $this, 'add_bottom_encouraged_notice' ), 100 );
		add_shortcode( 'yaypricing-checkout-fee-encouraged-notice', [ $this, 'encouraged_notice_shortcode' ] );
	}

	/**
	 * Check whether can notice show at bottom
	 */
	public function can_show_at_bottom() {
		$is_enabled = \YAYDP\Settings\YAYDP_Checkout_Fee_Settings::get_instance()->is_enabled_encouraged_notice();
		if ( ! $is_enabled ) {
			return false;
		}

		if ( yaydp_is_request( 'ajax' ) ) {
			$nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';
			if ( ! wp_verify_nonce( $nonce, 'yaydp_frontend_nonce' ) ) {
				return false;
			}
			$notice_page = isset( $_REQUEST['notice_page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['notice_page'] ) ) : null;
			if ( 'checkout' !== $notice_page ) {
				return false;
			}
		} elseif ( ! \is_checkout() ) {
			return false;
		}
		return true;
	}

	/**
	 * Add notice at bottom of page
	 */
	public function add_bottom_encouraged_notice() {
		if ( ! yaydp_is_request( 'frontend' ) ) {
			return;
		}

		if ( ! $this->can_show_at_bottom() ) {
			return;
		}

		echo wp_kses_post( do_shortcode('[yaypricing-checkout-fee-encouraged-notice]') );
	}

	/**
	 * Get bottom encouragement
	 */
	public function get_bottom_encouragement() {
		$running_rules               = \yaydp_get_running_checkout_fee_rules();
		global $yaydp_cart;
		if ( is_null( $yaydp_cart ) ) {
			$yaydp_cart                        = new \YAYDP\Core\YAYDP_Cart();
			$product_pricing_adjustments = new \YAYDP\Core\Adjustments\YAYDP_Product_Pricing_Adjustments( $yaydp_cart );
			$product_pricing_adjustments->do_stuff();
		}
		foreach ( $running_rules as $rule ) {
			$encouragements_by_rule = $rule->get_encouragements( $yaydp_cart );
			if ( ! empty( $encouragements_by_rule ) ) {
				return $encouragements_by_rule;
			}
		}
		return null;
	}

	public function encouraged_notice_shortcode() {

		$bottom_encouragement = $this->get_bottom_encouragement();

		if ( empty( $bottom_encouragement ) ) {
			return '<div class="checkout-fee-encouraged-notice"></div>';
		}

		ob_start();
		?>
		<div class="checkout-fee-encouraged-notice">
			<?php
			\wc_get_template(
				'notice/yaydp-encouraged-notice.php',
				array(
					'encouragements' => array(
						$bottom_encouragement,
					),
				),
				'',
				YAYDP_PLUGIN_PATH . 'includes/templates/'
			);
			?>
		</div>
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
}
