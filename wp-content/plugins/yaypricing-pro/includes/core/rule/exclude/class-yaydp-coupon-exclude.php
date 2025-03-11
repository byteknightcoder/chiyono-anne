<?php
/**
 * Represents a class for managing coupon exclusions in YAYDP
 *
 * @package YayPricing\Classes
 */

namespace YAYDP\Core\Rule\Exclude;

defined( 'ABSPATH' ) || exit;

/**
 * Declare class
 */
class YAYDP_Coupon_Exclude extends \YAYDP\Abstracts\YAYDP_Exclude_Rule {

	/**
	 * Get list coupons for checking
	 *
	 * @return array
	 */
	public function get_coupon_condition() {
		return isset( $this->data['coupon_condition'] ) ? $this->data['coupon_condition'] : array();
	}

	/**
	 * Check if current applied coupon matching coupon condition
	 *
	 * @return bool
	 */
	public function have_coupon_applied() {
		$applied_coupons = \WC()->cart->get_applied_coupons();
		$coupon_id_list  = array_map(
			function( $item ) {
				return $item['value'];
			},
			$this->get_coupon_condition()
		);
		foreach ( $applied_coupons as $coupon_code ) {
			if ( in_array( \wc_get_coupon_id_by_code( $coupon_code ), $coupon_id_list ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Check if given rule is excluded
	 *
	 * @param object $checking_rule Given rule.
	 *
	 * @return bool
	 */
	public function check_exclude( $checking_rule, $product = null ) {

		if ( ! is_null( $product ) ) {
			return false;
		}

		if ( ! $this->have_coupon_applied() ) {
			return false;
		}

		$excluded_list = parent::get_excluded_list();

		if ( \yaydp_is_product_pricing( $checking_rule ) && in_array( 'all_product_pricing_rules', $excluded_list, true ) ) {
			return true;
		}
		if ( \yaydp_is_cart_discount( $checking_rule ) && in_array( 'all_cart_discount_rules', $excluded_list, true ) ) {
			return true;
		}
		if ( \yaydp_is_checkout_fee( $checking_rule ) && in_array( 'all_checkout_fee_rules', $excluded_list, true ) ) {
			return true;
		}

		return ( in_array( $checking_rule->get_id(), $excluded_list, true ) );
	}
}
