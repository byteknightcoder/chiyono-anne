<?php
/**
 * Manage encouragement for checkout fee
 *
 * @package YayPricing\Encouragement
 *
 * @since 2.4
 */

namespace YAYDP\Core\Encouragement;

/**
 * Declare class
 */
class YAYDP_Checkout_Fee_Encouragement extends  \YAYDP\Abstracts\YAYDP_Encouragement {

	/**
	 * Contains cart
	 */
	protected $cart = null;

	/**
	 * Contains rule
	 */
	protected $rule = null;

	/**
	 * Contains conditions encouragements
	 *
	 * @var array
	 */
	protected $conditions_encouragements = array();

	/**
	 * Constructor
	 *
	 * @param array $data Input.
	 */
	public function __construct( $data ) {
		if ( isset( $data['cart'] ) ) {
			$this->cart = $data['cart'];
		}
		if ( isset( $data['rule'] ) ) {
			$this->rule = $data['rule'];
		}
		if ( isset( $data['conditions_encouragements'] ) ) {
			$this->conditions_encouragements = $data['conditions_encouragements'];
		}
	}

	/**
	 * Get encouraged notice content
	 *
	 * @override
	 */
	public function get_content() {
		$raw_content      = $this->get_raw_content();
		$replaced_content = $this->replace_discount_value( $raw_content );
		$replaced_content = $this->replace_action( $replaced_content );
		return $replaced_content;
	}

	/**
	 * Get encouraged notice raw content ( before replacing variables )
	 *
	 * @override
	 */
	public function get_raw_content() {
		$encouraged_settings = \YAYDP\Settings\YAYDP_Checkout_Fee_Settings::get_instance()->get_encouraged_notice_settings();
		return empty( $encouraged_settings['text'] ) ? '' : $encouraged_settings['text'];
	}

	/**
	 * Replace [current_item] variable
	 *
	 * @param string $raw_content Raw content.
	 */
	public function replace_discount_value( $raw_content ) {
		$pricing_value = $this->rule->get_pricing_value();
		$pricing_type  = $this->rule->get_pricing_type();
		if ( ! \yaydp_is_percentage_pricing_type( $pricing_type ) ) {
			$pricing_value = \YAYDP\Helper\YAYDP_Pricing_Helper::convert_price( $pricing_value );
		}
		$formatted_discount_value = \yaydp_get_formatted_pricing_value( $pricing_value, $pricing_type );
		return str_replace( '[discount_value]', $formatted_discount_value, $raw_content );
	}

	/**
	 * Replace [discount_value] variable
	 *
	 * @param string $raw_content Raw content.
	 */
	public function replace_action( $raw_content ) {

		$action = '';

		foreach ( $this->conditions_encouragements as $condition_encouragement ) {
			$missing_value = $condition_encouragement['missing_value'];

			if ( in_array( $condition_encouragement['type'], array( 'cart_subtotal', 'cart_shipping_total' ), true ) ) {
				$missing_value = \YAYDP\Helper\YAYDP_Pricing_Helper::convert_price( $missing_value );
			}

			switch ( $condition_encouragement['type'] ) {
				case 'cart_subtotal':
					// Translators: buy more text.
					$action = sprintf( __( 'buy more %s', 'yaypricing' ), \wc_price( $missing_value ) );
					$action = apply_filters( 'yaydp_checkout_fee_encouraged_notice_action', $action, $this->get_raw_content(), $condition_encouragement['type'], $missing_value );
					break;
				case 'cart_quantity':
					// Translators: buy more text.
					$action = sprintf( __( 'buy more %1$s item%2$s', 'yaypricing' ), $missing_value, $missing_value > 1 ? 's' : '' );
					$action = apply_filters( 'yaydp_checkout_fee_encouraged_notice_action', $action, $this->get_raw_content(), $condition_encouragement['type'], $missing_value );
					break;
				case 'logged_customer':
					$action = __( 'login', 'yaypricing' );
					$action = apply_filters( 'yaydp_checkout_fee_encouraged_notice_action', $action, $this->get_raw_content(), $condition_encouragement['type'], null );
					break;
				case 'customer_order_count':
					// translators: %s value.
					$action = sprintf( __( 'make more %1$s order%2$s', 'yaypricing' ), $missing_value, $missing_value > 1 ? 's' : '' );
					$action = apply_filters( 'yaydp_checkout_fee_encouraged_notice_action', $action, $this->get_raw_content(), $condition_encouragement['type'], $missing_value );
					break;
				case 'cart_shipping_total':
					// translators: %s value.
					$action = sprintf( __( 'take more %1$s shipping fee', 'yaypricing' ), \wc_price( $missing_value ) );
					$action = apply_filters( 'yaydp_checkout_fee_encouraged_notice_action', $action, $this->get_raw_content(), $condition_encouragement['type'], $missing_value );
					break;
				case 'cart_total_weight':
					// translators: %s value.
					$action = sprintf( __( 'buy more %1$skg', 'yaypricing' ), $missing_value );
					$action = apply_filters( 'yaydp_checkout_fee_encouraged_notice_action', $action, $this->get_raw_content(), $condition_encouragement['type'], $missing_value );
					break;
				default:
					$action = __( 'buy more items', 'yaypricing' );
					$action = apply_filters( 'yaydp_checkout_fee_encouraged_notice_action', $action, $this->get_raw_content(), $condition_encouragement['type'], null );
					break;
			}
			break;
		}
		return str_replace( '[action]', $action, $raw_content );
	}

}
