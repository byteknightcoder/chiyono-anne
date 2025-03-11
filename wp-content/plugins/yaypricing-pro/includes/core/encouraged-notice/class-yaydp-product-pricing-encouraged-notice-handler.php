<?php
/**
 * Managing handler for product pricing encouraged notice
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
class YAYDP_Product_Pricing_Encouraged_Notice_Handler {

	use \YAYDP\Traits\YAYDP_Singleton;

	/**
	 * Constructor
	 */
	protected function __construct() {
		add_action( 'yaydp_bottom_product_pricing_encouraged_section', array( $this, 'add_bottom_encouraged_notice' ), 100 );
		add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'add_encouraged_notice_after_product_short_description' ), 11 );
		add_shortcode( 'yaypricing-product-pricing-encouraged-notice', [ $this, 'encouraged_notice_shortcode' ] );
	}

	/**
	 * Check whether is enabled
	 */
	public function is_enabled() {
		$is_enabled = \YAYDP\Settings\YAYDP_Product_Pricing_Settings::get_instance()->is_enabled_encouraged_notice();
		if ( ! $is_enabled ) {
			return false;
		}
		return true;
	}

	/**
	 * Check whether can notice show at bottom
	 */
	public function can_show_at_bottom() {
		$show_at_bottom = \YAYDP\Settings\YAYDP_Product_Pricing_Settings::get_instance()->show_encouraged_notice_at_bottom();
		if ( ! $show_at_bottom ) {
			return false;
		}
		return true;
	}

	/**
	 * Check whether can notice show at certain page
	 */
	public function can_show_on_certain_page() {
		if ( yaydp_is_request( 'ajax' ) ) {
			$nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';
			if ( ! wp_verify_nonce( $nonce, 'yaydp_frontend_nonce' ) ) {
				return false;
			}
			$notice_page = isset( $_REQUEST['notice_page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['notice_page'] ) ) : null;
			if ( 'product' !== $notice_page && 'shop' !== $notice_page ) {
				return false;
			}
		} elseif ( ! \is_product() && ! \is_shop() ) {
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

		if ( ! $this->can_show_on_certain_page() ) {
			return;
		}

		echo wp_kses_post( do_shortcode('[yaypricing-product-pricing-encouraged-notice]') );
	}

	/**
	 * Add notice after product short description
	 */
	public function add_encouraged_notice_after_product_short_description() {
		if ( ! yaydp_is_request( 'frontend' ) ) {
			return;
		}

		if ( ! $this->is_enabled() ) {
			return;
		}

		if ( ! $this->can_show_on_certain_page() ) {
			return;
		}

		echo wp_kses_post( do_shortcode('[yaypricing-product-pricing-encouraged-notice single_product=""]') );
	}

	/**
	 * Get bottom encouragement
	 */
	public function get_bottom_encouragement() {
		$running_rules = \yaydp_get_running_product_pricing_rules();
		$cart          = new \YAYDP\Core\YAYDP_Cart();
		foreach ( $running_rules as $rule ) {
			$encouragements_by_rule = $rule->get_encouragements( $cart );
			if ( ! empty( $encouragements_by_rule ) ) {
				return $encouragements_by_rule;
			}
		}
		return null;
	}

	/**
	 * Get all encouragements of product
	 *
	 * @param \WC_Product $product Product.
	 */
	public function get_after_product_description_encouragements( $product ) {
		$running_rules  = \yaydp_get_running_product_pricing_rules();
		$cart           = new \YAYDP\Core\YAYDP_Cart();
		$encouragements = array();
		foreach ( $running_rules as $rule ) {
			$encouragements_by_rule = $rule->get_encouragements( $cart, $product );
			if ( ! empty( $encouragements_by_rule ) ) {
				$encouragements[] = $encouragements_by_rule;
			}
		}
		return $encouragements;
	}

	public function encouraged_notice_shortcode( $attrs = [] ) {

		if ( ! empty( $attrs['single_product'] ) ) {
			global $product;
			if ( empty( $product ) ) {
				return '<div class="product-pricing-encouraged-notice"></div>';
			}
	
			$bottom_encouragement = $this->get_after_product_description_encouragements( $product );
		} else {
			$bottom_encouragement = $this->get_bottom_encouragement();
		}


		if ( empty( $bottom_encouragement ) ) {
			return '<div class="product-pricing-encouraged-notice"></div>';
		}

		ob_start();
		?>
		<div class="product-pricing-encouraged-notice">
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
