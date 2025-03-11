<?php
/**
 * Handle Custom Fee rule
 *
 * @package YayPricing\Rule\CheckoutFee
 */

namespace YAYDP\Core\Rule\Checkout_Fee;

defined( 'ABSPATH' ) || exit;

/**
 * Declare class
 */
class YAYDP_Custom_Fee extends \YAYDP\Abstracts\YAYDP_Checkout_Fee_Rule {

	/**
	 * Calculate all possible adjustment created by the rule.
	 *
	 * @override
	 *
	 * @param \YAYDP\Core\YAYDP_Cart $cart Cart.
	 */
	public function create_possible_adjustment_from_cart( \YAYDP\Core\YAYDP_Cart $cart ) {

		if ( \YAYDP\Core\Manager\YAYDP_Exclude_Manager::check_coupon_exclusions( $this ) ) {
			return null;
		}

		if ( $this->check_conditions( $cart ) ) {
			return array(
				'rule' => $this,
			);
		}
		return null;
	}

	/**
	 * Calculate the adjustment amount based on current shipping fee
	 *
	 * @override
	 */
	public function get_adjustment_amount() {
		$pricing_type              = $this->get_pricing_type();
		$pricing_value             = $this->get_pricing_value();
		$maximum_adjustment_amount = $this->get_maximum_adjustment_amount();
		$cart_shipping_fee         = \yaydp_get_shipping_fee();
		$adjustment_amount         = \YAYDP\Helper\YAYDP_Pricing_Helper::calculate_adjustment_amount( $cart_shipping_fee, $pricing_type, $pricing_value, $maximum_adjustment_amount );
		return $adjustment_amount;
	}

	/**
	 * Calculate total discount amount per order
	 */
	public function get_total_discount_amount() {
		$adjustment_amount = $this->get_adjustment_amount();
		$pricing_type      = $this->get_pricing_type();
		if ( \yaydp_is_percentage_pricing_type( $pricing_type ) ) {
			return $adjustment_amount;
		}
		if ( \yaydp_is_fixed_pricing_type( $pricing_type ) ) {
			return $adjustment_amount;
		}
		return 0;
	}

	/**
	 * Add fee to the cart
	 */
	public function add_fee() {

		$fee_amount = abs( $this->get_total_discount_amount() );

		if ( empty( $fee_amount ) ) {
			return;
		}

		$fee_data = array(
			'id'     => $this->get_id(),
			'name'   => $this->get_name(),
			'amount' => \YAYDP\Helper\YAYDP_Pricing_Helper::convert_fee( $fee_amount ),
		);
		\WC()->cart->fees_api()->add_fee( $fee_data );

	}

	/**
	 * Calculate all encouragements can be created by rule ( include condition encouragements )
	 *
	 * @override
	 *
	 * @param \YAYDP\Core\YAYDP_Cart $cart Cart.
	 */
	public function get_encouragements( \YAYDP\Core\YAYDP_Cart $cart ) {
		return null;
	}
}
